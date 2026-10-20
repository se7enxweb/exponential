---
title: Start CSP reporting on a public site
audience: government, admin, developer
category: public-sector
order: 100
summary: Step by step - public siteaccesses get no Content Security Policy by default; switch one to report-only with Exponential's own report collector, so you learn what your design, content and embeds would need before enforcing.
keywords: Content Security Policy, CSP, public site, report-only, Mode auto, ReportEndpoint, csp.log, embeds, analytics, rich text styles, AdditionalSources, public sector
type: help
---
# Start CSP reporting on a public site

For administrators and developers. With `Mode=auto`, public siteaccesses send **no** policy; their designs and the
rich text they render (styles from content, embedded players) are not checked by the project's test
([Content Security Policy](../../../../features/6.0/content-security-policy.md#switching-to-enforce-step-by-step)). Report-only
is the safe first step: nothing is blocked.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Next](#next)
5. [Related pages](#related-pages)

## Before you start

- Know your public siteaccesses (one per language or site).
- Expect many reports at first; the collector is rate limited per client and in total.

## Steps

1. In `settings/siteaccess/<public siteaccess>/site.ini.append.php`:

   ```ini
   [ContentSecurityPolicy]
   Mode=report-only
   ReportEndpoint=enabled
   ```

2. If you know your third-party hosts already (analytics, maps, video), add them so they do not flood the reports:

   ```ini
   AdditionalSources[]=script-src https://<analytics host>
   AdditionalSources[]=frame-src https://<video host>
   ```

3. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`; reload PHP-FPM or deploy on Velocity.

## Check that it worked

```bash
curl -sI https://www.example.gov/ | grep -i 'content-security-policy-report-only'
```

The header is present and names `report-uri` pointing at the siteaccess's `csp/report`. Within a day, "Content Security
Policy reports" in **Setup > Log files** shows the first lines.

## Next

Work through the reports ("Review CSP reports"): inline scripts in your design, styles in content, third-party hosts.
Enforce when they are quiet, as for the administration.

## Related pages

- [Content Security Policy for public sites](../../security/content-security-policy.md)
- [Review CSP reports](review-csp-reports.md)
- [Visitor statistics and analytics: Under a CSP](../../../creators/website-creators/statistics-and-analytics.md#under-a-content-security-policy)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
