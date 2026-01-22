# INSTALLATION COMPLÈTE - AMC Quote Form

## ÉTAPE 1 : Installer le module

1. Dans PrestaShop Back-Office : **Modules > Module Manager**
2. Cliquer sur **"Téléverser un module"**
3. Sélectionner **amcquoteform.zip**
4. Cliquer sur **"Installer"**

## ÉTAPE 2 : Positionner le formulaire (IMPORTANT)

Le module s'installe avec le hook `displayFooterProduct`, mais pour le positionner exactement après `<div class="product-information tvproduct-special-desc">`, vous devez modifier le template de votre thème.

### Option A : Via FTP (Recommandé)

1. **Identifier votre thème** : 
   - Aller dans BO > Apparence > Thème et logo
   - Noter le nom du thème (ex: "classic", "warehouse", etc.)

2. **Localiser le fichier produit** :
   - Chemin : `/themes/[NOM_DU_THEME]/templates/catalog/product.tpl`

3. **Éditer le fichier** :
   - Chercher cette ligne :
   ```html
   <div class="product-information tvproduct-special-desc">
   ```
   
   - Chercher la div de fermeture correspondante (juste après `</form></div></div>`)
   
   - Ajouter JUSTE APRÈS cette div fermante :
   ```smarty
   {hook h='displayFooterProduct'}
   ```

   Exemple complet :
   ```html
   <div class="product-information tvproduct-special-desc">
       <div class="product-actions">
           <form action="https://amc-pub.ch/ch/panier" method="post" id="add-to-cart-or-refresh">
               ...
           </form>
       </div>
   </div>
   
   {* AJOUTER CETTE LIGNE *}
   {hook h='displayFooterProduct'}
   ```

4. **Sauvegarder** le fichier

5. **Vider le cache** :
   - BO > Paramètres avancés > Performances
   - Cliquer sur **"Vider le cache"**

### Option B : Via Back-Office (Si votre thème le permet)

1. Aller dans **Apparence > Thème et logo**
2. Trouver votre thème actif
3. Cliquer sur **"Configurer"** ou **"Personnaliser"**
4. Chercher l'option pour modifier les templates
5. Éditer `catalog/product.tpl`
6. Suivre les mêmes instructions que l'Option A

## ÉTAPE 3 : Si le hook ne s'affiche toujours pas

### Solution alternative : Créer un enfant de thème (child theme)

Si vous ne trouvez pas l'emplacement exact ou si votre thème est très personnalisé :

1. **Créer un dossier** : `/themes/[VOTRE_THEME]/modules/amcquoteform/views/templates/hook/`

2. **Copier dedans** le fichier `displayFooterProduct.tpl` :

```smarty
{* Fichier : /themes/[VOTRE_THEME]/modules/amcquoteform/views/templates/hook/displayFooterProduct.tpl *}

<div class="amc-quote-form-wrapper" id="amc-quote-form-wrapper" data-ajax-url="{$ajax_url}">
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

        <input type="hidden" name="product_id" value="{$product.id_product}">
        <input type="hidden" name="product_name" value="{$product.name}">

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="quote_prenom">Prénom <span class="required">*</span></label>
                <input type="text" class="form-control" id="quote_prenom" name="prenom" required placeholder="Votre prénom">
            </div>
            <div class="form-group col-md-6">
                <label for="quote_nom">Nom <span class="required">*</span></label>
                <input type="text" class="form-control" id="quote_nom" name="nom" required placeholder="Votre nom">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="quote_entreprise">Entreprise <span class="required">*</span></label>
                <input type="text" class="form-control" id="quote_entreprise" name="entreprise" required placeholder="Nom de votre entreprise">
            </div>
            <div class="form-group col-md-6">
                <label for="quote_quantite">Quantité désirée <span class="required">*</span></label>
                <input type="number" class="form-control" id="quote_quantite" name="quantite" min="1" required placeholder="Ex: 100">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="quote_email">Email <span class="required">*</span></label>
                <input type="email" class="form-control" id="quote_email" name="email" required placeholder="votre@email.ch">
            </div>
            <div class="form-group col-md-6">
                <label for="quote_telephone">Téléphone</label>
                <input type="tel" class="form-control" id="quote_telephone" name="telephone" placeholder="+41 26 XXX XX XX">
            </div>
        </div>

        <div class="form-group">
            <label for="quote_message">Message (optionnel)</label>
            <textarea class="form-control" id="quote_message" name="message" rows="3" placeholder="Informations complémentaires..."></textarea>
        </div>

        <div class="form-group form-gdpr">
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="quote_gdpr" name="gdpr_consent" required>
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
```

## ÉTAPE 4 : Vérification

1. Aller sur n'importe quelle **page produit** de votre site
2. Le formulaire doit apparaître **après** la section `<div class="product-information tvproduct-special-desc">`
3. Tester en remplissant et envoyant une demande
4. Vérifier la réception de l'email

## DÉPANNAGE

### Le formulaire ne s'affiche pas ?

**1. Vérifier que le module est installé et actif :**
- BO > Modules > Module Manager
- Chercher "AMC Quote Form"
- Doit être en vert avec "Activé"

**2. Vider tous les caches :**
```bash
# Via SSH si vous avez accès
cd /var/www/html  # ou votre chemin PrestaShop
rm -rf var/cache/*
```

Ou via BO :
- Paramètres avancés > Performances > Vider le cache
- Paramètres avancés > Performances > Vider le cache de Smarty

**3. Vérifier les hooks :**
- BO > Design > Positions
- Chercher "displayFooterProduct"
- Vérifier que "AMC Quote Form" est listé

**4. Activer le mode debug :**
- BO > Paramètres avancés > Performances
- Activer "Mode debug"
- Recharger une page produit
- Vérifier les erreurs PHP

### Le formulaire s'affiche mais au mauvais endroit ?

Éditer le fichier `/themes/[VOTRE_THEME]/templates/catalog/product.tpl` et déplacer le hook `{hook h='displayFooterProduct'}` à l'endroit désiré.

### Le JavaScript ne fonctionne pas ?

1. Ouvrir la Console du navigateur (F12)
2. Vérifier s'il y a des erreurs JavaScript
3. Vérifier que jQuery est chargé (PrestaShop 8 devrait l'avoir)

### Les emails ne sont pas envoyés ?

1. Vérifier la configuration email :
   - BO > Paramètres avancés > Email
   - Tester l'envoi d'un email de test

2. Vérifier le fichier `/modules/amcquoteform/controllers/front/ajax.php`
3. Vérifier que l'email de la boutique est configuré

## BESOIN D'AIDE ?

Si vous rencontrez des problèmes :

1. **Envoyez-moi** :
   - Le nom de votre thème PrestaShop
   - Une capture d'écran de la page produit
   - Les éventuelles erreurs dans les logs

2. **Contact** :
   - Email : info@amc-pub.ch
   - Ou via le système de tickets PrestaShop

Je peux vous aider à :
- Positionner exactement le formulaire
- Personnaliser le design
- Débugger les problèmes d'affichage
- Créer une page d'administration pour gérer les demandes
