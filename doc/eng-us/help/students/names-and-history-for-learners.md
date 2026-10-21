---
title: Names and history for learners
audience: student
category: learning
order: 49
summary: The names a learner meets and how to use them - Exponential, 7x, the Exponential Foundation, Velocity, Exponential Layouts, Exponential Intelligence, Exponential Basic - the short history from 1999 to today, why kernel identifiers still start with eZ, and how to write about it in reports.
keywords: names, history, Exponential, 7x, Exponential Foundation, eZ Publish, Velocity, Layouts, Intelligence, Exponential Basic, naming, prefixes, ez, exp, sevenx, learner
type: help
---
# Names and history for learners

You will meet several names while you learn: in the administration, in the code, in old forum posts. This page tells
you what each one means and how to write about them in your reports.

## Contents

1. [The names](#the-names)
2. [The history in one paragraph](#the-history-in-one-paragraph)
3. [Why the code still says eZ](#why-the-code-still-says-ez)
4. [Writing about it](#writing-about-it)

## The names

| Name | What it is | Source |
|---|---|---|
| **Exponential** | The product: a free and open source PHP content management system and application framework | [Press: start here](../press/press-start-here.md#exponential-in-one-paragraph) |
| **7x** | The company that leads its development | [Who is 7x?](../who-is-7x.md) |
| **the Exponential Foundation** | The nonprofit, founded in 2026, that operates and funds the project | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) |
| **Exponential Velocity** | The application server, part of Exponential as of 6.0.15; inside settings its engine is called `qbix` | [Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md), [Glossary](../../glossary.md#q) |
| **Exponential Layouts** | The visual page builder with layouts, zones and blocks | [The layouts editor](../layouts-editor.md) |
| **Exponential Intelligence** | The AI features of the editor and administration | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-intelligence) |
| **Exponential Basic** | A small CMS "for everyone, educational purposes, personal use" | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-basic) |

The product family has more members (Exponential Platform Legacy, Nexus, DXP and others); see
[The product family at a glance](../who-is-the-exponential-foundation.md#the-product-family-at-a-glance). Before you
describe one, check what it really contains.

## The history in one paragraph

The code base has been developed since 1999, first under the name eZ Publish, a product that its creator
discontinued after almost 18 years. 7x's project kept it alive and launched it in December 2023, and the product has
been called Exponential since August 2025. The Exponential Foundation was founded in 2026
([Press: start here](../press/press-start-here.md#exponential-in-one-paragraph)). That long history is why you find
twenty years of forum posts, books and extensions; much of it still applies, but check against the current release.

## Why the code still says eZ

Kernel identifiers keep their real names: `eZContentObject`, `eZINI`, `eZTemplate` and many more are never renamed.
New code from 7x uses the prefixes `exp` or `sevenx` (`sevenx` because a PHP identifier cannot start with a digit)
([Glossary](../../glossary.md#in-short), [Contributors guidelines](../../../../CONTRIBUTING.md#making-changes)).

| You see | Means |
|---|---|
| `eZ...` class, `ez...` extension | An older kernel identifier or extension, name kept |
| `exp...` | Work 7x assigns to the Exponential project and Foundation |
| `sevenx...` | 7x's public PHP prefix |

When you write new code yourself, use your own prefix, not `ez`, `exp` or `sevenx`, unless you contribute to those
projects.

## Writing about it

- Write **Exponential** for the product, **7x** for the company (with the digit), **the Exponential Foundation** for
  the nonprofit, in full at first mention.
- Use the old name only for history, in the past tense: "Exponential continues the code base of eZ Publish."
- Do not mix up the company and the nonprofit.
- Do not claim facts the sources do not give. The press pages list what is not documented
  ([Press: start here](../press/press-start-here.md)).

The full rules: [Brand and naming rules](../business/brand-and-naming-rules.md).

## Related pages

- [Who is 7x?](../who-is-7x.md)
- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Glossary for learners](glossary-for-learners.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
