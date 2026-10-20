---
title: Roll back the CSP to report-only
audience: government, admin
category: public-sector
order: 90
summary: Step by step - when an enforced Content Security Policy blocks work, return the siteaccess to report-only in minutes, make every process send the new header, and keep collecting reports to fix the cause.
keywords: CSP rollback, report-only, Mode, enforce, emergency change, ezcache clear-tag ini, PHP-FPM reload, Velocity restart, Content Security Policy, public sector
type: help
---
# Roll back the CSP to report-only

For administrators on duty when an enforced Content Security Policy breaks a page editors need. The rollback changes one
setting; nothing else changes ([Content Security Policy: rollback](../../../../features/6.0/content-security-policy.md#enforcing-nonce-more-sources-a-stricter-companion-rollback)).

## Contents

1. [Steps](#steps)
2. [Check that it worked](#check-that-it-worked)
3. [Afterwards](#afterwards)
4. [Related pages](#related-pages)

## Steps

1. In the siteaccess's `site.ini.append.php` (where `Mode=enforce` was set):

   ```ini
   [ContentSecurityPolicy]
   Mode=report-only
   ```

2. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.
3. Reload PHP-FPM and restart Velocity: `./console exp:velocity deploy --allow-root-user`.

If settings come from version control (`ManagedByDeployment`), make the change there and deploy as an emergency change.

## Check that it worked

```bash
curl -sI https://admin.example.gov/user/login | grep -i 'content-security-policy'
```

shows `Content-Security-Policy-Report-Only:` for the policy (the separate `frame-ancestors 'self'` header of
`SecurityHeaders[]` stays). Ask the editor to reload the page with a hard reload.

## Afterwards

- Keep `ReportEndpoint=enabled`; the same policy is now reported, not enforced, so the violation shows in
  "Content Security Policy reports".
- Fix the cause (see "Review CSP reports") and plan the next attempt.
- Record the incident and the times.

## Related pages

- [From report-only to an enforced CSP](../../security/csp-from-report-only-to-enforce.md)
- [Review CSP reports](review-csp-reports.md)
- [Content Security Policy](../../../../features/6.0/content-security-policy.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
