---
title: CSP reports
audience: government, admin, developer
category: public-sector
order: 60
summary: Collecting Content Security Policy violation reports in csp.log, reading them in the log viewer and error digest, and using them to move a public-sector siteaccess from report-only to an enforced policy without breaking pages.
keywords: Content Security Policy, CSP, csp.log, report-only, enforce, ReportEndpoint, violation reports, XSS, cross-site scripting, browser security, monitoring
type: help
---
# CSP reports

This page is for administrators and developers who want the protection of a Content Security Policy
([CSP Level 3](https://www.w3.org/TR/CSP3/)) on a public website and need evidence before enforcing it. Exponential
collects the browsers' reports itself. Reference: [Content Security Policy](../../../../features/6.0/content-security-policy.md);
hosting view: [Content Security Policy (hosting)](../../../hosting/security/content-security-policy.md).

## Contents
1. [What ships](#what-ships)
2. [Switching reports on](#switching-reports-on)
3. [Reading the reports](#reading-the-reports)
4. [From reports to enforcement](#from-reports-to-enforcement)
5. [Related pages](#related-pages)

## What ships

`site.ini [ContentSecurityPolicy] Mode=auto` (the default) sends the policy as **report-only** on administration
siteaccesses (designs beginning with `admin`) and **no policy** on public sites. The shipped directives allow the
site's own files only (`script-src 'self'`, `style-src 'self'`, `object-src 'none'`, `frame-ancestors 'self'` ...),
with a nonce per request. So, as shipped, nothing is blocked for anyone. The security standard counts the missing
public-site CSP among its known gaps ([Security standard](../../../../standards/security.md)).

## Switching reports on

Per siteaccess:

```ini
# settings/siteaccess/<siteaccess>/site.ini.append.php
[ContentSecurityPolicy]
ReportEndpoint=enabled
```

Clear the INI cache. The view `csp/report` takes the browsers' reports and writes one line per violation to
`var/log/csp.log`. It is open to every visitor (browsers send reports without signing in), answers with no body, is
never cached, and is rate limited per client (`ReportsPerMinute=60`) and in total (`ReportsPerMinuteTotal=600`); a
flood becomes one `abuse/csp-report-flood` line a minute in `security.log`.

## Reading the reports

- **Setup > Log files**, log **Content Security Policy reports** (key `csp`).
- **Error digest** groups `csp.log` by directive and blocked source: one group per thing that would break.
- Addresses are masked as in the request logs. Rotation: 5 archives, compressed, 10 MB, pruned after 30 days.

Typical sources: an inline script in a template or an extension, an external analytics or map script, an older
editor component. Each is either fixed in the template, or allowed deliberately with the policy's additional sources.

## From reports to enforcement

1. Run report-only on the siteaccess for a period that covers all page types and editor tasks.
2. Work through the digest groups with developers until only accepted sources remain.
3. Switch the siteaccess to `Mode=enforce` on staging, test, then on production through your change process
   ([Change management and advisory boards](../upgrades-and-change/change-management-and-advisory-boards.md)).
4. Keep `ReportEndpoint=enabled`: after enforcement, the reports show what was blocked.
5. Rollback is a settings change back to `report-only`.

The step-by-step is in the feature page's "Switching to enforce, step by step".

## Related pages
- [Request logs: 404 and security](request-logs-404-and-security.md)
- [Error digest and log health](error-digest-and-log-health.md)
- [Security standard](../../../../standards/security.md)
- [CSP checklist for theme designers](../../../designers/theme-design/checklist-csp.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
