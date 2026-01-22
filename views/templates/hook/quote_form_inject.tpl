<script type="text/javascript">
// Script d'injection automatique du formulaire AMC Quote Form
(function() {
    // Le HTML du formulaire est rendu côté serveur (Smarty), puis injecté côté client si nécessaire.
    
    function injectQuoteForm() {
        // Vérifier si le formulaire n'est pas déjà présent
        if (document.getElementById('amc-quote-form-wrapper')) {
            console.log('AMC Quote Form: Formulaire déjà présent via hook');
            // Même si le formulaire est présent via hook, on tente de le repositionner au bon endroit
            if (window.amcQuoteFormMoveToPreferredLocation) {
                window.amcQuoteFormMoveToPreferredLocation();
            }
            return;
        }
        
        // Chercher l'élément cible
        var targetElement =
            // Thème TV: fin du bloc (juste après le reassurance)
            document.querySelector('#block-reassurance') ||
            // Thème TV: conteneur sticky de la colonne droite
            document.querySelector('.tv-product-page-content .theiaStickySidebar') ||
            // Classique (PS Classic): description courte
            document.querySelector('.product-description-short') ||
            // Fallbacks
            document.querySelector('.product-information') ||
            document.querySelector('.product-actions') ||
            document.querySelector('#add-to-cart-or-refresh');
        
        if (!targetElement) {
            console.log('AMC Quote Form: Élément cible non trouvé, tentative avec sélecteurs alternatifs');
            
            return;
        }
        
        // Récupérer le HTML pré-rendu depuis un <template>
        var tpl = document.getElementById('amc-quote-form-template');
        if (!tpl) {
            console.log('AMC Quote Form: Template HTML introuvable');
            return;
        }
        var formHTML = tpl.innerHTML;
        if (!formHTML || !formHTML.trim()) {
            console.log('AMC Quote Form: Template HTML vide');
            return;
        }
        
        // Insérer après l'élément cible
        targetElement.insertAdjacentHTML('afterend', formHTML);

        // Initialiser le JS du formulaire si le HTML a été injecté après coup
        if (window.amcQuoteFormInit) {
            window.amcQuoteFormInit(document);
        }
        
        console.log('AMC Quote Form: Formulaire injecté avec succès via JavaScript');
    }

    // Exécuter dès que possible + après chargement (safe)
    injectQuoteForm();
    document.addEventListener('DOMContentLoaded', injectQuoteForm);
})();
</script>

<template id="amc-quote-form-template">
    {include file='module:productquoteform/views/templates/hook/quote_form.tpl'}
</template>
