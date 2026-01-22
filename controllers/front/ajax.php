<?php

class AmcQuoteFormAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
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
        $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'amc_quote_requests` 
                (id_product, product_name, nom, prenom, entreprise, email, telephone, quantite, message, date_add, status)
                VALUES 
                (' . (int)$product_id . ', 
                 "' . pSQL($product_name) . '", 
                 "' . pSQL($nom) . '", 
                 "' . pSQL($prenom) . '", 
                 "' . pSQL($entreprise) . '", 
                 "' . pSQL($email) . '", 
                 "' . pSQL($telephone) . '", 
                 ' . (int)$quantite . ', 
                 "' . pSQL($message) . '", 
                 NOW(), 
                 "new")';

        if (Db::getInstance()->execute($sql)) {
            // Envoyer un email de notification
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

            // Envoyer un email de confirmation au client
            $this->sendCustomerConfirmation($email, $prenom, $product_name);

            $this->ajaxRender(json_encode([
                'success' => true,
                'message' => '✓ Votre demande de devis a été envoyée avec succès ! Nous vous répondrons sous 24h.'
            ]));
        } else {
            $this->ajaxRender(json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer ou nous appeler au 026 675 15 75.'
            ]));
        }
    }

    private function sendQuoteNotification($data)
    {
        $product_link = $this->context->link->getProductLink($data['product_id']);
        
        $subject = 'Nouvelle demande de devis - ' . $data['product_name'];
        
        $message = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0066cc; color: white; padding: 20px; text-align: center; }
                .content { background: #f8f9fa; padding: 20px; }
                .info-row { margin: 10px 0; padding: 10px; background: white; border-left: 4px solid #0066cc; }
                .label { font-weight: bold; color: #555; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Nouvelle Demande de Devis</h2>
                </div>
                <div class="content">
                    <div class="info-row">
                        <span class="label">Produit:</span> 
                        <a href="' . $product_link . '">' . $data['product_name'] . '</a>
                    </div>
                    <div class="info-row">
                        <span class="label">Contact:</span> 
                        ' . $data['prenom'] . ' ' . $data['nom'] . '
                    </div>
                    <div class="info-row">
                        <span class="label">Entreprise:</span> 
                        ' . $data['entreprise'] . '
                    </div>
                    <div class="info-row">
                        <span class="label">Email:</span> 
                        <a href="mailto:' . $data['email'] . '">' . $data['email'] . '</a>
                    </div>
                    <div class="info-row">
                        <span class="label">Téléphone:</span> 
                        ' . ($data['telephone'] ? $data['telephone'] : 'Non fourni') . '
                    </div>
                    <div class="info-row">
                        <span class="label">Quantité désirée:</span> 
                        <strong>' . $data['quantite'] . '</strong>
                    </div>
                    ' . ($data['message'] ? '
                    <div class="info-row">
                        <span class="label">Message:</span><br>
                        ' . nl2br($data['message']) . '
                    </div>
                    ' : '') . '
                </div>
                <div class="footer">
                    Date de la demande: ' . date('d/m/Y à H:i') . '
                </div>
            </div>
        </body>
        </html>';

        // Email du magasin (configuré dans PrestaShop)
        $to = Configuration::get('PS_SHOP_EMAIL');
        
        // Utiliser la classe Mail de PrestaShop
        return Mail::Send(
            (int)$this->context->language->id,
            null,
            $subject,
            [],
            $to,
            null,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . 'amcquoteform/mails/',
            false,
            null,
            $message
        );
    }

    private function sendCustomerConfirmation($email, $prenom, $product_name)
    {
        $subject = 'Confirmation de votre demande de devis - AMC Pub';
        
        $message = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #0066cc; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .highlight { background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
                .footer { text-align: center; padding: 20px; background: #f8f9fa; color: #666; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>AMC Pub - Articles Publicitaires</h2>
                </div>
                <div class="content">
                    <p>Bonjour ' . $prenom . ',</p>
                    
                    <p>Nous avons bien reçu votre demande de devis pour :</p>
                    <p><strong>' . $product_name . '</strong></p>
                    
                    <div class="highlight">
                        <strong>✓ Votre demande sera traitée sous 24h</strong><br>
                        Notre équipe reviendra vers vous rapidement avec une offre personnalisée.
                    </div>
                    
                    <p>En attendant, si vous avez des questions urgentes, n\'hésitez pas à nous contacter :</p>
                    <ul>
                        <li>Téléphone : <strong>026 675 15 75</strong></li>
                        <li>Email : <strong>info@amc-pub.ch</strong></li>
                    </ul>
                    
                    <p>Cordialement,<br>
                    L\'équipe AMC Pub</p>
                </div>
                <div class="footer">
                    AMC Pub - Grand Rhain 1 - 1564 Domdidier<br>
                    Plus de 40 ans d\'expérience | +2000 articles disponibles
                </div>
            </div>
        </body>
        </html>';

        return Mail::Send(
            (int)$this->context->language->id,
            null,
            $subject,
            [],
            $email,
            $prenom,
            null,
            null,
            null,
            null,
            _PS_MODULE_DIR_ . 'amcquoteform/mails/',
            false,
            null,
            $message
        );
    }
}
