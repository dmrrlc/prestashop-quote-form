<script type="text/javascript">
// Script d'injection automatique du formulaire AMC Quote Form
(function() {
    // Attendre que le DOM soit chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', injectQuoteForm);
    } else {
        injectQuoteForm();
    }
    
    function injectQuoteForm() {
        // Vérifier si le formulaire n'est pas déjà présent
        if (document.getElementById('amc-quote-form-wrapper')) {
            console.log('AMC Quote Form: Formulaire déjà présent via hook');
            return;
        }
        
        // Chercher l'élément cible
        var targetElement = document.querySelector('.product-information.tvproduct-special-desc');
        
        if (!targetElement) {
            console.log('AMC Quote Form: Élément cible non trouvé, tentative avec sélecteurs alternatifs');
            
            // Essayer d'autres sélecteurs
            targetElement = document.querySelector('.product-information') ||
                           document.querySelector('.product-actions') ||
                           document.querySelector('#add-to-cart-or-refresh');
            
            if (targetElement) {
                targetElement = targetElement.closest('div');
            }
        }
        
        if (!targetElement) {
            console.log('AMC Quote Form: Impossible de trouver l\'élément cible');
            return;
        }
        
        // Créer le conteneur du formulaire
        var formHTML = `{include file='module:amcquoteform/views/templates/hook/quote_form.tpl'}`;
        
        // Insérer après l'élément cible
        targetElement.insertAdjacentHTML('afterend', formHTML);
        
        console.log('AMC Quote Form: Formulaire injecté avec succès via JavaScript');
    }
})();
</script>
