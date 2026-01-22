<?php

class ProductController extends ProductControllerCore
{
    public function initContent()
    {
        parent::initContent();
        
        // Ajouter une variable pour indiquer qu'on veut afficher le formulaire
        $this->context->smarty->assign([
            'show_amc_quote_form' => true,
        ]);
    }
}
