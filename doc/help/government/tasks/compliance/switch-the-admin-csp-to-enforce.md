---
title: Switch the administration's CSP to enforce
audience: government, admin, developer
category: public-sector
order: 70
summary: Step by step - after the template audit and a period of clean reports, change the Content Security Policy of the administration siteaccess from report-only to enforce, deploy it to every process, and check the header.
keywords: Content Security Policy, CSP, enforce, report-only, Mode, administration siteaccess, site.ini, ReportEndpoint, ezcache clear-tag ini, Velocity deploy, rollback, public sector
type: help
---
# Switch the administration's CSP to enforce

For administrators. As shipped, the administration sends a **report-only** policy (`Mode=auto`). Enforcing it makes the
browser block injected scripts. Do it after the steps in
[From report-only to an enforced CSP](../../security/csp-from-report-only-to-enforce.md): the template audit and a period
of reports.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [If something goes wrong](#if-something-goes-wrong)
5. [Related pages](#related-pages)

## Before you start

- The audit test ran with every extension:
  `CSP_ALL_EXTENSIONS=1 php vendor/bin/phpunit tests/tests/design/AdminContentSecurityPolicyTest.php`.
- A week of reports without unexplained violations ("Review CSP reports").
- Ideally a second administration siteaccess enforced first.
- A change request with the rollback ("Roll back the CSP to report-only").

## Steps

1. In `settings/siteaccess/<admin siteaccess>/site.ini.append.php`:

   ```ini
   [ContentSecurityPolicy]
   Mode=enforce
   ReportEndpoint=enabled
   ```

2. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
3. Reload PHP-FPM, and restart Velocity: `./console exp:velocity deploy --allow-root-user` does all of it.

## Check that it worked

```bash
curl -sI https://admin.example.gov/user/login | grep -i '^content-security-policy'
```

shows `Content-Security-Policy:` (not `-Report-Only`) with `script-src 'self' 'nonce-...'`. Then use the main editor
tasks (edit, upload, context menus, dialogs) and watch **Setup > Log files** > "Content Security Policy reports".

## If something goes wrong

- Context menu entries that do nothing: an extension with inline scripts; convert it or allow `'unsafe-inline'` for
  that siteaccess.
- Users of the classic design keep a report-only policy (`ReportOnlyDesigns[]`).
- Anything blocking work: roll back at once (set `Mode=report-only`, clear the INI cache, reload).

## Related pages

- [Content Security Policy](../../../../features/6.0/content-security-policy.md)
- [Content Security Policy for public sites](../../security/content-security-policy.md)
- [Review CSP reports](review-csp-reports.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
