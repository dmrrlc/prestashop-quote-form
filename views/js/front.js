function amcQuoteFormInit(root) {
    const scope = root || document;
    const form = scope.getElementById ? scope.getElementById('amc-quote-form') : document.getElementById('amc-quote-form');

    if (!form) return;
    if (form.dataset && form.dataset.amcQuoteBound === '1') return;
    if (form.dataset) form.dataset.amcQuoteBound = '1';

    const submitBtn = form.querySelector('.btn-quote-submit');
    const successMsg = form.querySelector('.quote-success');
    const errorMsg = form.querySelector('.quote-error');
    const wrapper = form.closest('.amc-quote-form-wrapper');
    const ajaxUrl = wrapper && wrapper.dataset ? wrapper.dataset.ajaxUrl : null;

    if (!ajaxUrl) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Désactiver le bouton et afficher loading
        submitBtn.disabled = true;
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="material-icons">hourglass_empty</i> Envoi en cours...';

        // Cacher les messages précédents
        successMsg.style.display = 'none';
        errorMsg.style.display = 'none';

        // Récupérer les données du formulaire
        const formData = new FormData(form);

        // Envoyer via AJAX
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(async (response) => {
            // Some failures return HTML (404/500) which would make response.json() throw.
            const text = await response.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error('Réponse serveur invalide (HTTP ' + response.status + '). Vérifiez que le contrôleur AJAX du module est bien accessible.');
            }
            if (!response.ok) {
                throw new Error(data && data.message ? data.message : ('Erreur HTTP ' + response.status));
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Succès
                successMsg.innerHTML = data.message;
                successMsg.style.display = 'block';
                form.reset();

                // Scroll vers le message de succès
                successMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });

                // Tracking Google Ads (si configuré)
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'conversion', {
                        'send_to': 'AW-XXXXXXXXX/XXXXX', // À REMPLACER par votre ID de conversion
                        'value': 1.0,
                        'currency': 'CHF'
                    });
                }

                // Tracking GA4
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'generate_lead', {
                        'event_category': 'Quote Form',
                        'event_label': formData.get('product_name')
                    });
                }
            } else {
                // Erreur
                errorMsg.innerHTML = data.message || 'Une erreur est survenue. Veuillez réessayer.';
                errorMsg.style.display = 'block';
                errorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        })
        .catch(error => {
            console.error('Quote form error:', error);
            errorMsg.innerHTML = (error && error.message)
                ? error.message
                : 'Erreur de connexion. Veuillez réessayer.';
            errorMsg.style.display = 'block';
        })
        .finally(() => {
            // Réactiver le bouton
            submitBtn.disabled = false;
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = '<i class="material-icons">send</i> Envoyer ma demande de devis';
        });
    });

    // Validation temps réel
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (!this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });

        field.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    });

    // Validation email
    const emailField = form.querySelector('#quote_email');
    if (emailField) {
        emailField.addEventListener('blur', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.value)) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
    }

    // Validation téléphone (format suisse)
    const phoneField = form.querySelector('#quote_telephone');
    if (phoneField) {
        phoneField.addEventListener('input', function() {
            // Autoriser seulement les chiffres, espaces, +, -, (, )
            this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
        });
    }

    // Validation quantité
    const qtyField = form.querySelector('#quote_quantite');
    if (qtyField) {
        qtyField.addEventListener('blur', function() {
            if (this.value < 1) {
                this.value = 1;
            }
        });
    }
}

function amcQuoteFormMoveToPreferredLocation() {
    const wrapper = document.getElementById('amc-quote-form-wrapper') || document.querySelector('.amc-quote-form-wrapper');
    if (!wrapper) return;

    // Avoid doing the work multiple times.
    if (wrapper.dataset && wrapper.dataset.amcQuoteMoved === '1') return;

    // Preferred destination for the user's theme:
    // - after the reassurance block (so the form is at the end of the right column content)
    // - otherwise, append to the sticky sidebar container if present
    // - otherwise, fallback to common PrestaShop selectors
    const reassurance = document.querySelector('#block-reassurance');
    if (reassurance && reassurance.parentNode) {
        reassurance.insertAdjacentElement('afterend', wrapper);
    } else {
        const sidebar =
            document.querySelector('.tv-product-page-content .theiaStickySidebar') ||
            document.querySelector('.tv-product-page-content') ||
            document.querySelector('.product-actions') ||
            document.querySelector('.product-information') ||
            document.querySelector('#add-to-cart-or-refresh');

        if (sidebar) {
            sidebar.appendChild(wrapper);
        }
    }

    if (wrapper.dataset) wrapper.dataset.amcQuoteMoved = '1';
}

// Exposer l'init pour l'injection auto
window.amcQuoteFormInit = amcQuoteFormInit;
window.amcQuoteFormMoveToPreferredLocation = amcQuoteFormMoveToPreferredLocation;

document.addEventListener('DOMContentLoaded', function() {
    amcQuoteFormMoveToPreferredLocation();
    amcQuoteFormInit(document);
});
