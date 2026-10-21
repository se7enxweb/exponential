---
title: When to choose Exponential, and when a plain framework
audience: web-framework-developer, developer, project-manager
category: web-frameworks
order: 480
summary: A fair decision guide for teams choosing between Exponential and a general web framework such as Symfony, Laravel, Django or Rails - the project shapes where Exponential saves months, the shapes where a plain framework fits better, the costs on each side, and questions to answer before deciding.
keywords: decision, choose, evaluation, framework vs CMS, build vs buy, Symfony, Laravel, Django, Rails, trade-offs, content-heavy, multisite, multilingual, permissions, when not to use, web framework developer
type: help
---
# When to choose Exponential, and when a plain framework

This page is for developers and leads deciding how to build a project. Exponential and general web frameworks are good
at different things; the question is which one starts closer to your project. Nothing here says one is better in
general.

## Contents

1. [Exponential starts closer when](#exponential-starts-closer-when)
2. [A plain framework starts closer when](#a-plain-framework-starts-closer-when)
3. [Costs on each side](#costs-on-each-side)
4. [Questions to answer first](#questions-to-answer-first)
5. [Both at once](#both-at-once)

## Exponential starts closer when

| Your project has | Exponential gives you without code |
|---|---|
| Content that people edit, review and publish | content classes, drafts, versions, history, an administration |
| Several languages, including right-to-left | translated content, per-language addresses, RTL ([Translations](i18n-l10n-and-rtl.md)) |
| Several sites or brands on one content base | siteaccesses ([Siteaccesses](siteaccesses-hosts-and-tenants.md)) |
| Fine-grained permissions that administrators manage | roles and policies with limitations ([Authorization](authorization-roles-and-policies.md)) |
| Approval processes | workflows with approval ([Workflows](workflows-triggers-and-domain-events.md)) |
| Pages that editors build | Exponential Layouts ([The layouts editor](../layouts-editor.md)) |
| A shop next to the content | the built-in shop ([Shop overview](../creators/website-creators/shop-overview.md)) |
| Many database choices, or none to run | SQLite by default; PostgreSQL, MySQL/MariaDB, Oracle, MongoDB ([Database support](../database-support.md)) |

In these projects a framework team would spend its first months building an admin, revisions, translations and
permissions; Exponential has them on day one ([Exponential as an application platform](../creators/app-creators/exponential-as-an-application-platform.md)).

## A plain framework starts closer when

- The core is **high-volume transactional data** with no editorial life (a ledger, telemetry, a booking engine's
  inventory).
- The product is an **API-only service** with a team that wants the framework's container, ORM and tooling end to end.
- The team wants **the newest PHP idioms everywhere**: attributes, a DI container, PSR-7 and PSR-15 middleware,
  typed configuration. Exponential's kernel has a long history and its own conventions ([Dependency injection](dependency-injection-and-services.md)).
- **Hiring** must draw from the largest pool of developers for one framework.
- You need a framework-specific ecosystem package with no counterpart.

## Costs on each side

| | Exponential | Plain framework |
|---|---|---|
| Learning | siteaccesses, INI layers, the template language, overrides | the framework plus every package you add for CMS features |
| Building editorial features | mostly configuration | code and maintenance |
| Custom application logic | extensions with modules and classes | native |
| Testing | PHPUnit, no in-process HTTP client ([Testing](testing.md)) | usually richer test tooling |
| Upgrades | one product, Composer updates of the kernel and extensions | the framework plus each package |

## Questions to answer first

1. Who edits the data, and do they need drafts, history, translations and approval?
2. How many sites, languages and audiences share the content?
3. Who decides permissions: developers in code, or administrators at run time?
4. Is most data content or transactions?
5. What does the team know, and what will it maintain in five years?

If the answers to 1 to 3 are "editors", "several" and "administrators", Exponential is usually the shorter path. If 4 is
"transactions", keep those in your own tables or a separate service, whatever you choose for the rest.

## Both at once

You do not have to pick one for everything: keep content in Exponential and a transactional service in a framework, or
use the Symfony-based platform products ([Mixing Exponential and a framework](mixing-exponential-and-a-framework.md),
[The platform products](the-platform-products.md)).

## Related pages

- [Exponential for web framework developers](exponential-for-framework-developers.md)
- [What Exponential is: the product family](../leadership/what-exponential-is.md)
- [Checklists for framework developers](checklists-for-framework-developers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
