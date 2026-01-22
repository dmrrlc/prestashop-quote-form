# Quick Install - Quote Request Form (v1.0)

## What the module does
The module can display the form using multiple approaches (depending on theme support):
1. Hook `displayProductAdditionalInfo` (recommended)
2. Hook `displayProductButtons` (theme-dependent fallback)
3. Hook `displayFooterProduct` (fallback placement)
4. Optional JavaScript injection (fallback if hooks are not rendered by the theme)

## Install in 3 steps

### Step 1: Install the module
1. PrestaShop Back Office > **Modules > Module Manager**
2. Click **Upload a module**
3. Select `amcquoteform.zip`
4. Click **Install**

### Step 2: Clear caches
1. **Advanced Parameters > Performance**
2. Click **Clear cache**
3. If enabled, also clear Smarty cache

### Step 3: Test on a product page
1. Open any product page
2. The form should appear near the product information area
3. Submit a test request

## If the form is displayed in the wrong place
Recommended approach: disable auto-injection and place the hook exactly where you want it.

1. Back Office: **Modules > Module Manager**
2. Find `amcquoteform` and click **Configure**
3. Disable **Automatic injection**
4. Save

Then edit your theme template:
`/themes/[YOUR_THEME]/templates/catalog/product.tpl`

Example (place after the short description block):
```smarty
{* Place the hook call where you want the form to appear *}
{hook h='displayProductAdditionalInfo'}
```

## Configuration options
- **Automatic injection**: enable/disable automatic positioning via JS
- **Recipient email**: notification recipient (leave empty to use the shop default email)

## Data storage
Requests are stored in `ps_amc_quote_requests`.

Example query:
```sql
SELECT * FROM ps_amc_quote_requests ORDER BY date_add DESC;
```

## Google Ads tracking (optional)
Edit `/modules/amcquoteform/views/js/front.js` and replace:
```javascript
'send_to': 'AW-XXXXXXXXX/XXXXX'
```

## Troubleshooting (fast)
- **Not showing**: verify module is installed/enabled, clear caches, check **Design > Positions** for the hooks.
- **Duplicate form**: both a hook and auto-injection are active. Disable auto-injection in module configuration.
- **Emails not received**: verify Back Office email configuration and check the configured recipient email in module settings.

## Security notes
- GDPR consent checkbox
- Server-side validation
- CSRF token verification
