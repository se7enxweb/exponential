---
title: Measuring and improving
audience: government, project-manager, website-creator, admin
category: public-sector
order: 90
summary: What Exponential can tell you about how service pages are used - search statistics, view counts, a "was this useful" form, request logs - what needs an analytics service, and how to measure with respect for visitors' privacy.
keywords: measurement, analytics, search statistics, view counts, feedback form, was this page useful, request logs, privacy, consent, continuous improvement
type: help
---
# Measuring and improving

This page is for project managers, website creators and administrators who want to know whether the service catalog
works and how to improve it. It lists what Exponential records itself, what needs a separate analytics service, and
how to measure without collecting more about visitors than you need.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What Exponential records](#what-exponential-records)
2. [A "Was this page useful?" form](#a-was-this-page-useful-form)
3. [Analytics services](#analytics-services)
4. [Turning findings into changes](#turning-findings-into-changes)

## What Exponential records

From [Statistics and analytics](../../../creators/website-creators/statistics-and-analytics.md):

| Source | What it answers | Where |
|---|---|---|
| Search statistics (off by default) | what people search, how often, with how many results | **Setup > Search statistics**, with `LogSearchStats=enabled` |
| View counts | which pages are viewed most, from the access log | the `updateviewcount` cronjob script, `fetch( 'content', 'view_top_list', ... )` |
| Request logs | requests with timing, for operations | [Request logs](../../../../features/6.0/request-logs.md) |
| Collected information | what people tell you in forms | **Setup > Collected information** |

These answer operational questions. They are not a replacement for an analytics service.

## A "Was this page useful?" form

A small information collector form below each service page gives direct feedback:

| Attribute | Datatype | Collector |
|---|---|---|
| Was this page useful? | Selection (`ezselection`): yes, no | yes |
| What was missing? | Text block (`eztext`), optional | yes |
| Page | Text line, filled by the template with the page's address in a hidden field | yes |

One form object, shown on every service page by the template, keeps all answers in one list. Export them as CSV
from **Setup > Collected information** ([Collected information](../../../../guides/collected-information.md)). Tell
people not to type personal details in the text field, and remove answers once read.

## Analytics services

| Option | Note |
|---|---|
| `bcwebsitestatistics` | adds the Google Analytics script ([bcwebsitestatistics](../../../../features/6.0/extensions/bcwebsitestatistics.md)); a third party |
| Another service, possibly self-hosted | its script through your design's script list ([Stylesheets and scripts](../../../creators/website-creators/stylesheets-and-scripts.md)) |
| None | search statistics, view counts and feedback may be enough for a small site |

Analytics usually needs consent in many jurisdictions; Exponential ships no cookie banner
([Privacy standards](../../../../standards/privacy.md)). In the EU, the
[ePrivacy Directive](https://eur-lex.europa.eu/eli/dir/2002/58/oj) and the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj)
apply. A Content Security Policy must allow the analytics host if you use one
([Content Security Policy](../../../../features/6.0/content-security-policy.md)).

## Turning findings into changes

| Finding | Change |
|---|---|
| A search phrase without results | add the word to a summary or a tag synonym; add a missing service |
| Many "not useful" answers on a page | rewrite it with the feedback; test with a few users |
| A service page rarely viewed but often asked about at the desk | improve its title and its place in the menus |
| High views of a page with a phone number | the online alternative may be unclear |

Review the findings monthly together with the overdue list ([Keeping services current](keeping-services-current.md)).

## Related pages

- [Building a service catalog](README.md)
- [Finding services](finding-services.md)
- [Statistics and analytics](../../../creators/website-creators/statistics-and-analytics.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
