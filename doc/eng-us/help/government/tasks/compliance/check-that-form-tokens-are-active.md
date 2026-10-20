---
title: Check that form tokens are active
audience: government, admin
category: public-sector
order: 390
summary: Step by step - confirm that the ezformtoken extension protecting every Exponential form against cross-site request forgery is active on all siteaccesses, and what to do if it is missing.
keywords: form tokens, CSRF, ezformtoken, ActiveExtensions, exp:ini where, Setup Extensions, form expired, public sector
type: help
---
# Check that form tokens are active

For administrators, at go-live and after upgrades or settings changes. Without `ezformtoken`, another site could make a
signed-in editor's browser publish, delete or change roles ([CSRF and form tokens](../../security/csrf-and-form-tokens.md)).

## Contents

1. [Steps](#steps)
2. [If it is missing](#if-it-is-missing)
3. [Related pages](#related-pages)

## Steps

1. Which files set the active extensions, and the value in effect:

   ```bash
   php bin/php/console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user
   ```

   `ezformtoken` must be in the list.
2. Or in the administration: **Setup > Extensions** shows it as active.
3. Look at a form's HTML (for example the sign-in page or an edit form) in the browser: it carries a hidden form token
   field.
4. Check every siteaccess that sets its own `ActiveExtensions[]` or `ActiveAccessExtensions[]`.

## If it is missing

1. Add it in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=ezformtoken
   ```

2. Regenerate extension autoloads and clear caches: `php bin/php/ezpgenerateautoloads.php -e` and
   `php bin/php/ezcache.php --clear-all --allow-root-user`, then reload PHP (or deploy on Velocity).
3. Find out how it got lost (an override that starts with an empty `ActiveExtensions[]` line replaces the whole list),
   and record the incident.

## Related pages

- [CSRF and form tokens](../../security/csrf-and-form-tokens.md)
- [13.8 Form tokens](../../../../install/13-security-hardening.md#138-form-tokens-against-cross-site-request-forgery)
- [Form expired page](../../../../features/6.0/form-expired-page.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
