# 🚀 INSTALLATION RAPIDE - AMC Quote Form v1.0

## ✅ CE QUI A ÉTÉ AMÉLIORÉ

Le module utilise maintenant **3 méthodes** pour s'afficher :

1. **Hook `displayFooterProduct`** - Méthode standard
2. **Hook `displayProductButtons`** - Alternative selon thème
3. **Injection JavaScript automatique** - Solution de secours si les hooks ne fonctionnent pas

## 📦 INSTALLATION EN 3 ÉTAPES

### ÉTAPE 1 : Installer le module

1. Back-Office PrestaShop > **Modules > Module Manager**
2. Cliquer sur **"Téléverser un module"**
3. Sélectionner **amcquoteform.zip**
4. Cliquer sur **"Installer"**
5. ✅ Le module est installé avec **injection automatique activée par défaut**

### ÉTAPE 2 : Vider le cache

1. Aller dans **Paramètres avancés > Performances**
2. Cliquer sur **"Vider le cache"**
3. Décocher **"Cache Smarty"** et cliquer sur **"Vider le cache"** à nouveau

### ÉTAPE 3 : Tester

1. Ouvrir **n'importe quelle page produit** de votre site
2. Le formulaire devrait apparaître **après** la section produit
3. Tester en soumettant une demande

## 🎯 SI LE FORMULAIRE S'AFFICHE AU MAUVAIS ENDROIT

### Solution : Désactiver l'injection auto et positionner manuellement

1. **Aller dans** : Modules > Module Manager
2. **Chercher** "AMC Quote Form"
3. **Cliquer sur** "Configurer"
4. **Désactiver** "Injection automatique"
5. **Enregistrer**

Puis **éditer votre template** `/themes/[VOTRE_THEME]/templates/catalog/product.tpl` :

```smarty
<div class="product-information tvproduct-special-desc">
    <div class="product-actions">
        <form action="..." method="post" id="add-to-cart-or-refresh">
            ...
        </form>
    </div>
</div>

{* AJOUTER CETTE LIGNE ICI *}
{hook h='displayFooterProduct'}
```

## 🔧 CONFIGURATION

### Accéder à la configuration du module :
1. Modules > Module Manager
2. Chercher "AMC Quote Form"
3. Cliquer sur **"Configurer"**

### Options disponibles :
- **Injection automatique** : Active/Désactive le positionnement automatique via JavaScript

## 📊 GESTION DES DEMANDES

Les demandes sont dans la table **`ps_amc_quote_requests`**

### Consulter via phpMyAdmin :
```sql
SELECT * FROM ps_amc_quote_requests ORDER BY date_add DESC;
```

### Colonnes disponibles :
- `id_quote` - ID unique
- `product_name` - Nom du produit
- `nom`, `prenom`, `entreprise`
- `email`, `telephone`
- `quantite` - Quantité demandée
- `message` - Message optionnel
- `date_add` - Date de la demande
- `status` - Statut (new, processed, etc.)

## ⚡ TRACKING GOOGLE ADS

Pour activer le tracking des conversions :

1. Éditer `/modules/amcquoteform/views/js/front.js`
2. Ligne 30, remplacer :
```javascript
'send_to': 'AW-XXXXXXXXX/XXXXX',
```
Par votre ID de conversion Google Ads

## 🎨 PERSONNALISATION

### Couleurs et design :
Éditer `/modules/amcquoteform/views/css/front.css`

### Couleur principale (bleu) :
Remplacer `#0066cc` par votre couleur

## 🆘 DÉPANNAGE RAPIDE

### Le formulaire ne s'affiche pas ?

**1. Vérifier l'installation :**
- Modules > Module Manager
- "AMC Quote Form" doit être vert "Activé"

**2. Vérifier les hooks :**
- Design > Positions
- Chercher "displayFooterProduct"
- AMC Quote Form doit être listé

**3. Activer le debug :**
- Paramètres avancés > Performances
- Activer "Mode debug"
- Ouvrir Console navigateur (F12) sur page produit
- Regarder les erreurs

**4. Vérifier la console JavaScript :**
Ouvrez F12 sur une page produit, vous devriez voir :
```
AMC Quote Form: Formulaire injecté avec succès via JavaScript
```

### Le formulaire s'affiche en double ?

C'est que les hooks ET l'injection auto fonctionnent.

**Solution :**
1. Configurer le module
2. Désactiver "Injection automatique"
3. Sauvegarder

## 📧 EMAILS

### Email de notification (à AMC) :
Configuré automatiquement avec l'email de :
**Paramètres boutique > Contact > Email de contact**

### Email de confirmation (au client) :
Envoyé automatiquement avec coordonnées AMC Pub

### Tester l'envoi d'emails :
1. Paramètres avancés > Email
2. "Tester votre configuration email"

## 🔒 SÉCURITÉ

✅ Protection RGPD intégrée
✅ Validation des données côté serveur
✅ Protection contre injection SQL
✅ Validation email
✅ Protection CSRF

## 📞 SUPPORT

Si vous avez besoin d'aide :

**Email :** info@amc-pub.ch

**Je peux vous aider avec :**
- Positionnement exact du formulaire
- Personnalisation du design
- Création d'une interface admin
- Intégration CRM
- Problèmes d'affichage

---

**Développé par d-side solutions Sàrl pour AMC Pub**
Version 1.0 - Janvier 2026
