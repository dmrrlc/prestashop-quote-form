# Quote Request Form - PrestaShop Module (8.0+)

## Overview
This module adds a quote request contact form on product pages (typically used when the shop is in catalog mode).

## Features
- Quote request form on product pages
- Fields: first name, last name, company, email, phone, quantity, message
- GDPR consent checkbox
- AJAX submission (no page reload)
- Automatic emails (shop notification + customer confirmation)
- Stores requests in the database
- Optional Google Ads / GA4 tracking hooks in JS (placeholders to configure)
- Responsive styling

## Installation
1. Upload the folder `amcquoteform` into `/modules/`
2. In the Back Office: **Modules > Module Manager**
3. Find the module (search by **technical name** `amcquoteform` if needed)
4. Click **Install**

## Configuration
In the Back Office: **Modules > Module Manager > amcquoteform > Configure**

### Recipient email
- **Recipient email**: the address that receives quote notifications.
- Leave it empty to use the shop default email (`PS_SHOP_EMAIL`).

### Google Ads conversion tracking (optional)
Edit `/modules/amcquoteform/views/js/front.js` and replace the placeholder:
```javascript
'send_to': 'AW-XXXXXXXXX/XXXXX'
```

### Styling
Edit `/modules/amcquoteform/views/css/front.css` to match your theme colors.

## Where the form is displayed (hooks)
The module tries multiple approaches depending on theme support:
- `displayProductAdditionalInfo` (recommended placement, usually near the product information block)
- `displayProductButtons` (fallback for themes using this hook)
- `displayFooterProduct` (fallback placement)
- Optional JavaScript injection (if enabled in configuration)

If you need **exact placement** (e.g., right after the short description), you can place a hook call in your theme template where you want it:
```smarty
{hook h='displayProductAdditionalInfo'}
```

## Data storage
Requests are stored in the table `ps_amc_quote_requests`.

## Uninstall
Uninstalling the module removes:
- The module hooks
- The database table

Warning: stored requests will be lost. Export them first if needed.

## Version
1.0.0 (January 2026)

## Compatibility
PrestaShop 8.0.0 to 8.1.x+
