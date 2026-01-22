<?php

// IMPORTANT: Class name must match the module name (`productquoteform`) and controller name (`ajax`)
// so that PrestaShop can route /module/productquoteform/ajax correctly.
class ProductquoteformAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        try {
            parent::initContent();

            // Ensure JSON responses (prevents fetch().json() failing with HTML output).
            header('Content-Type: application/json; charset=utf-8');
        
            // Sécurité: vérifier que c'est bien une requête POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                $this->ajaxRender(json_encode([
                    'success' => false,
                    'message' => 'Méthode non autorisée'
                ]));
                return;
            }

            // CSRF token (simple token de session PrestaShop)
            $token = (string) Tools::getValue('token');
            if (!$token || $token !== Tools::getToken(false)) {
                $this->ajaxRender(json_encode([
                    'success' => false,
                    'message' => 'Jeton de sécurité invalide, veuillez recharger la page.'
                ]));
                return;
            }

            // Récupérer et valider les données
            $product_id = (int)Tools::getValue('product_id');
            $product_name = pSQL(Tools::getValue('product_name'));
            $nom = pSQL(Tools::getValue('nom'));
            $prenom = pSQL(Tools::getValue('prenom'));
            $entreprise = pSQL(Tools::getValue('entreprise'));
            $email = pSQL(Tools::getValue('email'));
            $telephone = pSQL(Tools::getValue('telephone'));
            $quantite = (int)Tools::getValue('quantite');
            $message = pSQL(Tools::getValue('message'));
            $gdpr_consent = Tools::getValue('gdpr_consent');

            // Validation
            if (empty($nom) || empty($prenom) || empty($entreprise) || empty($email) || $quantite < 1) {
                $this->ajaxRender(json_encode([
                    'success' => false,
                    'message' => 'Veuillez remplir tous les champs obligatoires.'
                ]));
                return;
            }

            // Valider l'email
            if (!Validate::isEmail($email)) {
                $this->ajaxRender(json_encode([
                    'success' => false,
                    'message' => 'L\'adresse email n\'est pas valide.'
                ]));
                return;
            }

            // Vérifier le consentement RGPD
            if (!$gdpr_consent) {
                $this->ajaxRender(json_encode([
                    'success' => false,
                    'message' => 'Vous devez accepter la politique de confidentialité.'
                ]));
                return;
            }

            // Enregistrer dans la base de données
            // Backward compatible: try new table first, fallback to old table if the shop wasn't reinstalled.
            $data = [
                'id_product' => (int) $product_id,
                'product_name' => pSQL($product_name),
                'nom' => pSQL($nom),
                'prenom' => pSQL($prenom),
                'entreprise' => pSQL($entreprise),
                'email' => pSQL($email),
                'telephone' => pSQL($telephone),
                'quantite' => (int) $quantite,
                'message' => pSQL($message, true),
                'date_add' => date('Y-m-d H:i:s'),
                'status' => 'new',
            ];

            $saved = Db::getInstance()->insert('product_quote_requests', $data);
            if (!$saved) {
                $saved = Db::getInstance()->insert('amc_quote_requests', $data);
            }

            if (!$saved) {
                $this->ajaxRender(json_encode([
                    'success' => false,
                    'message' => 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer.'
                ]));
                return;
            }

            // Envoyer un email de notification (best effort)
            $this->sendQuoteNotification([
                'product_id' => $product_id,
                'product_name' => $product_name,
                'nom' => $nom,
                'prenom' => $prenom,
                'entreprise' => $entreprise,
                'email' => $email,
                'telephone' => $telephone,
                'quantite' => $quantite,
                'message' => $message
            ]);

            // Envoyer un email de confirmation au client (best effort)
            $this->sendCustomerConfirmation($email, $prenom, $product_name);

            $this->ajaxRender(json_encode([
                'success' => true,
                'message' => '✓ Votre demande de devis a été envoyée avec succès ! Nous vous répondrons sous 24h.'
            ]));
            return;
        } catch (\Throwable $e) {
            // Log in PrestaShop if available (so we can inspect in back-office logs)
            if (class_exists('PrestaShopLogger')) {
                PrestaShopLogger::addLog(
                    '[productquoteform] AJAX error: ' . $e->getMessage(),
                    3,
                    (int) $e->getCode(),
                    'ProductquoteformAjaxModuleFrontController',
                    0,
                    true
                );
            }

            http_response_code(500);
            $msg = 'Erreur serveur.';
            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
                $msg .= ' ' . $e->getMessage();
            }
            $this->ajaxRender(json_encode([
                'success' => false,
                'message' => $msg,
            ]));
            return;
        }
    }

    private function sendQuoteNotification($data)
    {
        $productLink = $this->context->link->getProductLink((int) $data['product_id']);
        $subject = 'Nouvelle demande de devis - ' . (string) $data['product_name'];

        // Email destinataire (config module) sinon email boutique
        $to = (string) Configuration::get('PRODUCTQUOTEFORM_RECIPIENT_EMAIL', '');
        if (!$to) {
            // backward compatibility
            $to = (string) Configuration::get('AMC_QUOTE_RECIPIENT_EMAIL', '');
        }
        if (!$to) {
            $to = (string) Configuration::get('PS_SHOP_EMAIL');
        }

        $templateVars = [
            '{product_name}' => (string) $data['product_name'],
            '{product_link}' => $productLink,
            '{prenom}' => (string) $data['prenom'],
            '{nom}' => (string) $data['nom'],
            '{entreprise}' => (string) $data['entreprise'],
            '{email}' => (string) $data['email'],
            '{telephone}' => (string) ($data['telephone'] ? $data['telephone'] : 'Non fourni'),
            '{quantite}' => (string) $data['quantite'],
            '{message}' => (string) ($data['message'] ? $data['message'] : ''),
        ];

        // Use PrestaShop mail templates shipped with this module (avoids invalid Mail::Send() usage)
        return (bool) Mail::Send(
            (int) $this->context->language->id,
            'quote_request',
            $subject,
            $templateVars,
            $to,
            null,
            null,
            null,
            null,
            null,
            $this->module->getLocalPath() . 'mails/',
            false
        );
    }

    private function sendCustomerConfirmation($email, $prenom, $product_name)
    {
        $subject = 'Confirmation de votre demande de devis';

        $templateVars = [
            '{prenom}' => (string) $prenom,
            '{product_name}' => (string) $product_name,
        ];

        return (bool) Mail::Send(
            (int) $this->context->language->id,
            'quote_customer_confirmation',
            $subject,
            $templateVars,
            (string) $email,
            (string) $prenom,
            null,
            null,
            null,
            null,
            $this->module->getLocalPath() . 'mails/',
            false
        );
    }
}
