<div class="amc-quote-form-wrapper" id="amc-quote-form-wrapper" data-ajax-url="{$ajax_url|escape:'htmlall':'UTF-8'}">
    <div class="quote-form-header">
        <h3 class="quote-form-title">
            <i class="material-icons">mail_outline</i>
            Demander un Devis Gratuit
        </h3>
        <p class="quote-form-subtitle">Réponse sous 24h garantie</p>
    </div>

    <form id="amc-quote-form" class="amc-quote-form" method="post">
        <div class="quote-form-messages">
            <div class="alert alert-success quote-success" style="display:none;"></div>
            <div class="alert alert-danger quote-error" style="display:none;"></div>
        </div>

        <input type="hidden" name="product_id" value="{$product_id|intval}">
        <input type="hidden" name="product_name" value="{$product_name|escape:'htmlall':'UTF-8'}">
        <input type="hidden" name="token" value="{$amc_quote_token|escape:'htmlall':'UTF-8'}">

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="quote_prenom">Prénom <span class="required">*</span></label>
                <input type="text" 
                       class="form-control" 
                       id="quote_prenom" 
                       name="prenom" 
                       required
                       placeholder="Votre prénom">
            </div>
            <div class="form-group col-md-6">
                <label for="quote_nom">Nom <span class="required">*</span></label>
                <input type="text" 
                       class="form-control" 
                       id="quote_nom" 
                       name="nom" 
                       required
                       placeholder="Votre nom">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="quote_entreprise">Entreprise <span class="required">*</span></label>
                <input type="text" 
                       class="form-control" 
                       id="quote_entreprise" 
                       name="entreprise" 
                       required
                       placeholder="Nom de votre entreprise">
            </div>
            <div class="form-group col-md-6">
                <label for="quote_quantite">Quantité désirée <span class="required">*</span></label>
                <input type="number" 
                       class="form-control" 
                       id="quote_quantite" 
                       name="quantite" 
                       min="1" 
                       required
                       placeholder="Ex: 100">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="quote_email">Email <span class="required">*</span></label>
                <input type="email" 
                       class="form-control" 
                       id="quote_email" 
                       name="email" 
                       required
                       placeholder="votre@email.ch">
            </div>
            <div class="form-group col-md-6">
                <label for="quote_telephone">Téléphone</label>
                <input type="tel" 
                       class="form-control" 
                       id="quote_telephone" 
                       name="telephone"
                       placeholder="+41 26 XXX XX XX">
            </div>
        </div>

        <div class="form-group">
            <label for="quote_message">Message (optionnel)</label>
            <textarea class="form-control" 
                      id="quote_message" 
                      name="message" 
                      rows="3"
                      placeholder="Informations complémentaires..."></textarea>
        </div>

        <div class="form-group form-gdpr">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" 
                       class="custom-control-input" 
                       id="quote_gdpr" 
                       name="gdpr_consent" 
                       required>
                <label class="custom-control-label" for="quote_gdpr">
                    J'accepte que mes données soient utilisées pour me recontacter 
                    (<a href="{$urls.pages.privacy}" target="_blank">politique de confidentialité</a>)
                    <span class="required">*</span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-quote-submit">
            <i class="material-icons">send</i>
            Envoyer ma demande de devis
        </button>
    </form>
</div>
