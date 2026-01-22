# AMC Quote Form - Module PrestaShop 8.1.15

## Description
Module de formulaire de demande de devis pour les pages produits PrestaShop en mode catalogue.

Créé pour AMC Pub - Articles Publicitaires Suisse

## Fonctionnalités

✅ Formulaire de devis sur chaque page produit
✅ Champs: Nom, Prénom, Entreprise, Email, Téléphone, Quantité, Message
✅ Validation RGPD obligatoire
✅ Soumission AJAX sans rechargement
✅ Emails automatiques (notification + confirmation client)
✅ Stockage en base de données
✅ Tracking Google Ads / GA4 intégré
✅ Design responsive

## Installation

1. Uploader le dossier `amcquoteform` dans `/modules/`
2. Aller dans Back-Office > Modules > Module Manager
3. Chercher "AMC Quote Form"
4. Cliquer sur "Installer"

## Configuration

### 1. Vérifier l'email de réception
Les demandes sont envoyées à l'email configuré dans :
**Back-Office > Configurer > Paramètres de la boutique > Contact > Email de contact**

### 2. Configurer le tracking Google Ads
Éditer `/modules/amcquoteform/views/js/front.js` ligne 40 :
```javascript
'send_to': 'AW-XXXXXXXXX/XXXXX', // Remplacer par votre ID de conversion
```

### 3. Personnaliser les couleurs
Éditer `/modules/amcquoteform/views/css/front.css` pour adapter à vos couleurs.

## Gestion des demandes

Les demandes sont stockées dans la table `ps_amc_quote_requests`.

Pour consulter les demandes, utilisez phpMyAdmin ou créez une page admin personnalisée.

### Structure de la table:
- id_quote (ID unique)
- id_product (ID produit PrestaShop)
- product_name (Nom du produit)
- nom, prenom, entreprise
- email, telephone
- quantite (quantité demandée)
- message (optionnel)
- date_add (date de la demande)
- status (statut : "new" par défaut)

## Désinstallation

La désinstallation supprime automatiquement :
- Le module
- La table de base de données
- Tous les hooks

⚠️ Les demandes en base seront perdues. Exportez-les avant si nécessaire.

## Support

Développé par d-side solutions Sàrl
Contact: info@amc-pub.ch

## Version

1.0.0 - Janvier 2026

## Compatibilité

PrestaShop 8.0.0 à 8.1.15+
