<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class AmcQuoteForm extends Module
{
    public function __construct()
    {
        $this->name = 'amcquoteform';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'AMC Pub';
        $this->need_instance = 1;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('AMC Quote Form');
        $this->description = $this->l('Ajoute un formulaire de demande de devis sur les pages produits');

        $this->confirmUninstall = $this->l('Êtes-vous sûr de vouloir désinstaller ce module?');
    }
    
    public function getContent()
    {
        $output = '';
        
        if (Tools::isSubmit('submit' . $this->name)) {
            $autoInject = (bool)Tools::getValue('AMC_QUOTE_AUTO_INJECT');
            Configuration::updateValue('AMC_QUOTE_AUTO_INJECT', $autoInject);
            
            $output .= $this->displayConfirmation($this->l('Paramètres sauvegardés'));
        }
        
        return $output . $this->displayForm();
    }
    
    private function displayForm()
    {
        $fieldsForm = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cogs'
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Injection automatique'),
                        'name' => 'AMC_QUOTE_AUTO_INJECT',
                        'desc' => $this->l('Active l\'injection automatique du formulaire via JavaScript si les hooks ne fonctionnent pas'),
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Oui')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Non')
                            ]
                        ],
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Enregistrer'),
                    'class' => 'btn btn-default pull-right'
                ]
            ],
        ];
        
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->submit_action = 'submit' . $this->name;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        
        $helper->fields_value['AMC_QUOTE_AUTO_INJECT'] = Configuration::get('AMC_QUOTE_AUTO_INJECT', true);
        
        return $helper->generateForm([$fieldsForm]);
    }

    public function install()
    {
        Configuration::updateValue('AMC_QUOTE_AUTO_INJECT', true);
        
        return parent::install()
            && $this->registerHook('displayFooterProduct')
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->registerHook('displayProductButtons')
            && $this->registerHook('displayBeforeBodyClosingTag')
            && $this->registerHook('actionFrontControllerSetMedia')
            && $this->createQuoteTable();
    }

    public function uninstall()
    {
        Configuration::deleteByName('AMC_QUOTE_AUTO_INJECT');
        
        return parent::uninstall()
            && $this->dropQuoteTable();
    }

    private function createQuoteTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'amc_quote_requests` (
            `id_quote` int(11) NOT NULL AUTO_INCREMENT,
            `id_product` int(11) NOT NULL,
            `product_name` varchar(255) NOT NULL,
            `nom` varchar(100) NOT NULL,
            `prenom` varchar(100) NOT NULL,
            `entreprise` varchar(200) NOT NULL,
            `email` varchar(150) NOT NULL,
            `telephone` varchar(20),
            `quantite` int(11) NOT NULL,
            `message` text,
            `date_add` datetime NOT NULL,
            `status` varchar(20) DEFAULT "new",
            PRIMARY KEY (`id_quote`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

        return Db::getInstance()->execute($sql);
    }

    private function dropQuoteTable()
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'amc_quote_requests`';
        return Db::getInstance()->execute($sql);
    }

    public function hookActionFrontControllerSetMedia()
    {
        // Ajouter CSS et JS personnalisés
        $this->context->controller->registerStylesheet(
            'amc-quote-form-css',
            'modules/' . $this->name . '/views/css/front.css',
            ['media' => 'all', 'priority' => 200]
        );
        
        $this->context->controller->registerJavascript(
            'amc-quote-form-js',
            'modules/' . $this->name . '/views/js/front.js',
            ['position' => 'bottom', 'priority' => 200]
        );
    }

    public function hookDisplayFooterProduct($params)
    {
        // Hook qui s'affiche après les informations produit
        $product = $this->getProductFromParamsOrContext($params);
        if (!$product) {
            return '';
        }

        $this->assignTemplateVarsForProduct($product);

        return $this->display(__FILE__, 'views/templates/hook/quote_form.tpl');
    }
    
    public function hookDisplayProductAdditionalInfo($params)
    {
        // Hook généralement proche du bloc d'informations produit (souvent sous la description courte)
        return $this->hookDisplayFooterProduct($params);
    }

    public function hookDisplayProductButtons($params)
    {
        // Alternative hook pour les thèmes qui utilisent displayProductButtons
        return $this->hookDisplayFooterProduct($params);
    }
    
    public function hookDisplayBeforeBodyClosingTag($params)
    {
        // Ne s'exécute que si l'injection auto est activée
        if (!Configuration::get('AMC_QUOTE_AUTO_INJECT', true)) {
            return '';
        }
        
        // Injection JavaScript pour positionner le formulaire automatiquement
        // si les hooks standards ne fonctionnent pas
        if (!isset($this->context->controller->php_self) || $this->context->controller->php_self !== 'product') {
            return '';
        }

        $product = $this->getProductFromParamsOrContext([]);
        if (!$product) {
            return '';
        }
        
        $this->assignTemplateVarsForProduct($product);
        
        return $this->display(__FILE__, 'views/templates/hook/quote_form_inject.tpl');
    }

    private function assignTemplateVarsForProduct($product)
    {
        $this->context->smarty->assign([
            'product_id' => $this->extractProductId($product),
            'product_name' => $this->extractProductName($product),
            'ajax_url' => $this->context->link->getModuleLink($this->name, 'ajax'),
            'amc_quote_token' => Tools::getToken(false),
        ]);
    }

    private function getProductFromParamsOrContext($params)
    {
        if (isset($params['product']) && $params['product']) {
            return $params['product'];
        }

        // Sur les pages produit PS 8, le produit est généralement disponible dans les variables Smarty
        $product = $this->context->smarty->getTemplateVars('product');
        if ($product) {
            return $product;
        }

        return null;
    }

    private function extractProductId($product)
    {
        if (is_array($product)) {
            if (isset($product['id_product'])) {
                return (int) $product['id_product'];
            }
            if (isset($product['id'])) {
                return (int) $product['id'];
            }
        }

        if (is_object($product)) {
            if (isset($product->id_product)) {
                return (int) $product->id_product;
            }
            if (isset($product->id)) {
                return (int) $product->id;
            }
        }

        return 0;
    }

    private function extractProductName($product)
    {
        $name = '';

        if (is_array($product) && isset($product['name'])) {
            $name = $product['name'];
        } elseif (is_object($product) && isset($product->name)) {
            $name = $product->name;
        }

        // Le nom peut être un tableau [id_lang => name]
        if (is_array($name)) {
            $langId = (int) $this->context->language->id;
            if (isset($name[$langId])) {
                return (string) $name[$langId];
            }
            return (string) reset($name);
        }

        return (string) $name;
    }
}
