# Full Installation Guide - Quote Request Form

## Step 1: Install the module
1. PrestaShop Back Office: **Modules > Module Manager**
2. Click **Upload a module**
3. Select `amcquoteform.zip`
4. Click **Install**

## Step 2: Place the form exactly where you want (recommended)
Depending on your theme, hooks may be rendered in different positions. If you want the form **immediately after the short description**, place a hook call in your theme template.

1. Identify your active theme:
   - Back Office: **Design > Theme & Logo**
   - Note the theme name (e.g. `classic`)

2. Edit the product template:
   - Path: `/themes/[YOUR_THEME]/templates/catalog/product.tpl`

3. Find the short description block (commonly `div.product-description-short` in Classic).

4. Add a hook call right after it:
```smarty
{hook h='displayProductAdditionalInfo'}
```

5. Save the file and clear cache:
   - **Advanced Parameters > Performance > Clear cache**

## Step 3: If hooks are not rendered by the theme
As a fallback, the module can inject the form via JavaScript (toggle **Automatic injection** in module configuration).

## Step 4: Verify
1. Open any product page
2. Confirm the form is displayed in the desired position
3. Submit a test request
4. Confirm notification email delivery

## Troubleshooting
### The form does not show
- Verify the module is installed and enabled (Module Manager)
- Clear caches
- Check **Design > Positions** and verify your theme outputs the relevant hook(s)

### The form shows in the wrong place
Disable auto-injection and place the hook manually in `product.tpl` where you want it.

### Emails are not received
- Check Back Office email settings (**Advanced Parameters > Email**)
- In the module configuration, verify **Recipient email** (or leave empty to use the shop default email)

## Need help?
Provide:
- Theme name
- Screenshot of the product page
- Any PHP/JS errors in logs or browser console
