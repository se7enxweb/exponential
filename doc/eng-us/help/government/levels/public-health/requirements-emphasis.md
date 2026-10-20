---
title: Requirements emphasis for public health
audience: government, project-manager, admin
category: public-sector
order: 50
summary: The requirements that weigh most for a public health site - accuracy and clinical review, accessibility, languages and plain language, availability under sudden load, privacy of health data - and what Exponential provides.
keywords: requirements, public health, clinical review, accuracy, accessibility, plain language, multilingual, availability, load, static cache, privacy, health data
type: help
---
# Requirements emphasis for public health

This page is for project managers and administrators of a public health site.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Accuracy and review](#accuracy-and-review)
2. [Accessibility and plain language](#accessibility-and-plain-language)
3. [Languages](#languages)
4. [Availability under load](#availability-under-load)
5. [Privacy](#privacy)

## Accuracy and review

| Requirement | Exponential | Read |
|---|---|---|
| Sign-off before publishing | *Approve* event with clinical approvers | [Workflows](../../../../guides/workflows.md) |
| Who approved what, when | collaboration items and the audit trail | [Audit trail](../../../../features/6.0/audit-trail.md) |
| What changed between versions | **Compare** on the versions page | [Versions of an object](../../../../guides/content-history.md) |
| Advice reviewed on schedule | a review date attribute; a template list of overdue topics | [Content model for public health](content-model.md) |
| Outdated advice removed from view | hide; `hide.php` for dated content | [Cronjobs](../../../../guides/cronjobs.md) |

There is no built-in review reminder mail; a template list checked weekly is the usual pattern.

## Accessibility and plain language

Health information must reach everyone. In the EU, [Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)
applies to public sector bodies; in the United States, [Section 508](https://www.section508.gov/) to federal agencies
and the [Plain Writing Act guidance](https://www.plainlanguage.gov/) to federal writing.

Exponential's status: admin4 administration **partly complies** with WCAG 2.2 AA; EN 301 549 and Section 508
**unverified**; public designs not audited; no VPAT ([Accessibility standards](../../../../standards/accessibility.md)).
For health content:

| Practice | Why |
|---|---|
| Key messages first, in a short summary field | people under stress read the first lines |
| Headings that are questions people ask | scanning and screen reader navigation |
| No advice only in images or video | alternative text, captions, transcripts |
| Data shown as tables with a text summary | charts alone exclude many readers |

## Languages

| Need | Exponential |
|---|---|
| Alerts in several languages within hours | translations per object; translators with a Language limitation |
| A page shown even if not yet translated | "always available" decides whether the main language is shown in other siteaccesses |
| Right-to-left languages | supported ([Right-to-left languages](../../../../features/6.0/right-to-left.md)) |
| Which pages exist in which language | the **Translations** tab of an object; `/content/translations` lists the languages |

See [Content languages](../../../../guides/content-languages.md) and
[Alerts in many languages and plain language](alerts-multilingual-and-plain-language.md).

## Availability under load

An outbreak can multiply traffic suddenly. Exponential's tools:

| Tool | Effect | Read |
|---|---|---|
| View cache | rendered pages reused | [Caches](../../../../guides/caches.md) |
| Static cache | pages as plain files served by the web server | [Static cache generator](../../../../features/6.0/static-cache-generator.md) |
| Velocity response cache | responses served from memory | [Velocity response cache](../../../../features/6.0/velocity-response-cache.md) |
| A CDN in front | traffic absorbed elsewhere | [HTTP caching and CDNs](../../../creators/website-creators/http-caching-and-cdns.md) |

Static pages and caches must be refreshed when an alert changes: publishing clears the view cache of the object and
related pages; a static cache or CDN needs its own refresh. Rehearse this.

## Privacy

Health data is a special category under the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) in the EU. Keep it
out of website forms; use clinical and booking systems. Exponential masks IP addresses in logs and pseudonymizes audit
records ([Privacy standards](../../../../standards/privacy.md)); it has no cookie banner, and third-party analytics on
health pages reveal what people look up, so choose statistics with care
([Statistics and analytics](../../../creators/website-creators/statistics-and-analytics.md)).

## Related pages

- [Public health](README.md)
- [The first 90 days for public health](first-90-days.md)
- [Performance and caching](../../../creators/website-creators/performance-and-caching.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
