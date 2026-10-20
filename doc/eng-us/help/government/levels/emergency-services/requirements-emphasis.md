---
title: Requirements emphasis for emergency services
audience: government, project-manager, admin
category: public-sector
order: 50
summary: The requirements that weigh most for an emergency service site - availability under load and during failures, speed of publishing, accuracy, accessibility under stress, languages, security - and what Exponential provides.
keywords: requirements, emergency services, availability, load, failover, static cache, speed, accuracy, accessibility, plain language, languages, security, NIS2
type: help
---
# Requirements emphasis for emergency services

This page is for project managers and administrators of an emergency service site.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Availability](#availability)
2. [Speed and accuracy](#speed-and-accuracy)
3. [Accessibility under stress](#accessibility-under-stress)
4. [Languages](#languages)
5. [Security](#security)

## Availability

| Requirement | Exponential | Your work |
|---|---|---|
| Serve many readers at once | view cache, static cache, Velocity's response cache ([Alerts, availability, maintenance mode and static cache](alerts-availability-maintenance-and-static-cache.md)) | a load test at the expected peak |
| Keep serving if PHP or the database fails | static cache files are served by the web server without starting Exponential | static cache configured and generated |
| Planned work without silence | maintenance mode with a clear page, status 503 and `Retry-After` ([Maintenance mode](../../../../guides/maintenance-mode.md)) | maintenance outside risk periods |
| Survive a failure of the hosting site | none in the product | a second site, DNS failover, or a CDN that keeps serving copies; backups tested by restoring ([Backups and restores](../../../creators/website-creators/backups-and-restores.md)) |

## Speed and accuracy

| Requirement | Exponential |
|---|---|
| Publish in a minute | a short class, an on-call role outside approval ([Structure](structure-sections-and-roles.md)) |
| Show when information was last updated | an `updated` date attribute shown at the top |
| Correct quickly, keep the trail | edit and publish; versions and the audit trail keep the earlier text ([Versions of an object](../../../../guides/content-history.md)) |
| Remove outdated information automatically | `hide.php` with an end time ([Cronjobs](../../../../guides/cronjobs.md)) |

## Accessibility under stress

People reading during an incident may be frightened, on a small screen, on a weak connection, or using assistive
technology. In the EU, [Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj) covers public sector
bodies' websites, and [WCAG 2.2](https://www.w3.org/TR/WCAG22/) is the reference. Exponential's status: admin4
administration **partly complies** with WCAG 2.2 AA; EN 301 549 and Section 508 **unverified**; public designs not
audited ([Accessibility standards](../../../../standards/accessibility.md)).

| Practice | Why |
|---|---|
| Instructions as text, first | images, maps and video fail on weak connections and for screen reader users |
| Light pages | static, cached pages load fastest |
| Plain language ([plainlanguage.gov](https://www.plainlanguage.gov/)) | people under stress read less |
| No information only in color | warning levels as words as well as colors |

## Languages

Incident instructions in the languages spoken in your area save lives. Translations per object and a siteaccess per
language ([Content languages](../../../../guides/content-languages.md)); prepare translated templates of common
instructions in advance, approved before any incident.

## Security

| Measure | Read |
|---|---|
| Two-step sign-in for publishers | [Two-step sign-in](../../../../guides/two-factor-login.md) |
| Lockout after failed sign-ins (off by default) and a request shield | [Security hardening](../../../security-hardening.md) |
| Audit alerts on suspicious sign-in activity | [Audit trail](../../../../features/6.0/audit-trail.md) |
| A false incident published by an attacker is a serious risk | least privilege, monitoring, a procedure to take it down at once |

Emergency services may be essential entities under NIS2, [Directive (EU) 2022/2555](https://eur-lex.europa.eu/eli/dir/2022/2555/oj),
depending on national law; [NIST CSF 2.0](https://www.nist.gov/cyberframework) is a common framework elsewhere.
Exponential's own status against OWASP: **Partly complies** ([Security standards](../../../../standards/security.md)).

## Related pages

- [Emergency services](README.md)
- [The first 90 days for emergency services](first-90-days.md)
- [HTTP caching and CDNs](../../../creators/website-creators/http-caching-and-cdns.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
