---
title: Is Exponential a fit?
audience: adopter, businesses, project-manager, customer
category: adoption
order: 11
summary: Signals that Exponential fits an adopter's needs and signals that it may not, based on the features the README lists, the supported platforms and the product family - with the pages to check each signal.
keywords: fit, suitability, requirements, content model, multisite, multilingual, shop, permissions, workflows, REST API, Symfony, signals
type: help
---
# Is Exponential a fit?

## Contents

1. [Signals that it fits](#signals-that-it-fits)
2. [Signals to look closer](#signals-to-look-closer)
3. [If another product of the family fits better](#if-another-product-of-the-family-fits-better)

## Signals that it fits

| You need | Exponential has | Source |
|---|---|---|
| Structured content of your own design | "User defined content classes and objects", a "fully customizable and extendable content model" | `README.md` |
| Several sites or languages from one installation | "Multi-site support", "Advanced multi-lingual support" | `README.md` |
| An online store | "Advanced e-Commerce Stores (Built in)" | `README.md` |
| Staff-only areas and approval | Role-based permissions, "Content approval and scheduled publication" | `README.md` |
| Apps or other systems that read content | "A headless CRUD REST API" | `README.md` |
| Editors who lay out pages | Exponential Layouts | [The layouts editor](../../layouts-editor.md) |
| Help with writing and translating | Exponential Intelligence, drafts only | [expai](../../../features/6.0/extensions/expai.md) |
| Low running cost and few moving parts | No license fee; SQLite without a database server | [What you do not pay for](../../businesses/costs/what-you-do-not-pay-for.md) |

## Signals to look closer

| Situation | What to check |
|---|---|
| Your team only knows Symfony and Twig | The Symfony-based Platform line may fit better ([Choosing a product](../../ecosystem/products/choosing-a-product.md)) |
| You must use Microsoft SQL Server | No driver in 6.0.x; "known to be possible with additional work" ([Database support](../../database-support.md)) |
| Your host does not allow long-lived processes | Use Apache or LiteSpeed instead of Velocity ([Web servers and engines](../../ecosystem/php-stack/web-servers-and-engines.md)) |
| You need a feature you cannot find in the help | Assume it does not exist until a proof of concept shows it; check the extensions ([Finding an extension](../../ecosystem/packages/finding-an-extension.md)) |
| You need guaranteed response times | Plan paid support from 7x or a partner ([Longevity and support](../deciding/longevity-and-support.md)) |

## If another product of the family fits better

The family includes Exponential Basic for very small sites and the Platform line (Legacy, Nexus, DXP) for Symfony
projects ([Products](../../ecosystem/products/README.md)). This section describes adopting Exponential 6; for the
Platform line, read its own documentation at <https://platform.exponential.earth>.

## Related pages

- [Evaluating Exponential](README.md)
- [Who Exponential is for](../../business/who-exponential-is-for.md)
- [Feature highlights for customers](../../business/feature-highlights-for-customers.md)
- [Which type of site are you](../../customers/types/which-type-are-you.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
