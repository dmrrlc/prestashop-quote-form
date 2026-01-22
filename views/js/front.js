document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('amc-quote-form');
    
    if (!form) return;

    const submitBtn = form.querySelector('.btn-quote-submit');
    const successMsg = form.querySelector('.quote-success');
    const errorMsg = form.querySelector('.quote-error');
    const wrapper = form.closest('.amc-quote-form-wrapper');
    const ajaxUrl = wrapper.dataset.ajaxUrl;

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
            body: formData
        })
        .then(response => response.json())
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
            console.error('Error:', error);
            errorMsg.innerHTML = 'Erreur de connexion. Veuillez réessayer ou nous appeler au 026 675 15 75.';
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
});
