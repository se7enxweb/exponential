---
title: Review CSP reports
audience: government, admin, developer
category: public-sector
order: 80
summary: Step by step - collect Content Security Policy violation reports with Exponential's own csp/report collector, read them in Setup > Log files, tell real problems from noise, and decide per source what to fix or allow.
keywords: CSP reports, csp.log, csp/report, ReportEndpoint, ReportURI, violations, log viewer, blocked inline script, report-sample, extensions, browser extensions, triage, public sector
type: help
---
# Review CSP reports

For administrators and developers preparing to enforce a Content Security Policy, and for the weekly log review after
enforcing. The kernel collects reports itself: `csp/report` writes one line per violation to `var/log/csp.log`
([Content Security Policy: Reports](../../../../features/6.0/content-security-policy.md#reports)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Reading the lines](#reading-the-lines)
4. [Deciding](#deciding)
5. [Related pages](#related-pages)

## Before you start

- The policy of the siteaccess points at the collector: `[ContentSecurityPolicy] ReportEndpoint=enabled` (or
  `ReportURI=/csp/report`) in that siteaccess's `site.ini.append.php`; INI cache cleared, PHP reloaded.
- Rights to read logs (`setup/logs`).

## Steps

1. Use the siteaccess as editors do for some days, including dialogs and context menus.
2. Open **Setup > Log files** > "Content Security Policy reports" (log key `csp`).
3. Group by directive and blocked source (the viewer groups `csp.log` by directive and blocked origin).
4. For each group, find the page and the template or extension that causes it.
5. Record each decision in a short table: source, decision, owner, date.

## Reading the lines

- The directive (`script-src`, `style-src`, `img-src` ...) and what was blocked (`inline`, or an origin such as
  `https://maps.example.org`).
- With `'report-sample'` in `script-src` or `style-src`, the first 40 characters of a blocked inline script or style
  are included as `sample="..."`, which helps find the template.
- Floods are rate limited; a flood is one `abuse/csp-report-flood` line a minute in `security.log`.

## Deciding

| Source | Usual decision |
|---|---|
| a template of your design or extension with inline code | fix it: script into a file, values into `data-*` |
| a third-party service you use on that siteaccess | `AdditionalSources[]=<directive> <host>` for that siteaccess only |
| a browser extension of one user (unknown origins, `chrome-extension:`) | ignore |
| a third-party service you did not know about | find who added it; remove or decide |

## Related pages

- [Content Security Policy for public sites](../../security/content-security-policy.md)
- [Switch the administration's CSP to enforce](switch-the-admin-csp-to-enforce.md)
- [Log review routine](../../security/log-review-routine.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
