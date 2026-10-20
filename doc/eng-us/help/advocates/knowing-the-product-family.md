---
title: Knowing the product family
audience: advocate
category: advocacy
order: 6
summary: Every product of the Exponential family in one sentence an advocate can say, who it is for, and the source to cite, plus a quiz to check yourself.
keywords: product family, Exponential 6, Exponential Basic, Exponential Platform, Legacy, Nexus, DXP, Velocity, Exponential Intelligence, Exponential Layouts, AdminUI, help system, extensions, which product
type: help
---
# Knowing the product family

This page is for advocates who are asked "which Exponential do you mean?" It gives each product of the family in one
sentence you can say, who it is for, and where the fact is written. The full descriptions, with their sources, are on
[Who is the Exponential Foundation?, The products one by one](../who-is-the-exponential-foundation.md#the-products-one-by-one).

## Contents

1. [The family in one table](#the-family-in-one-table)
2. [Sentences you can say](#sentences-you-can-say)
3. [Which product for which person](#which-product-for-which-person)
4. [Common mix-ups](#common-mix-ups)
5. [Check yourself](#check-yourself)

## The family in one table

| Product | One sentence | For | Source |
|---|---|---|---|
| **Exponential 6** | The CMS, CMF and shop kernel: "a professional PHP application framework with advanced CMS functionality" | Everyone who builds or runs a site with it | [README](https://github.com/se7enxweb/exponential/blob/main/README.md), [Exponential 6](../who-is-the-exponential-foundation.md#exponential-6-the-kernel) |
| **Exponential Basic** | The simple CMS of the 2.x line, "for everyone, educational purposes, personal use" | Learners, small sites | [Exponential Basic](../who-is-the-exponential-foundation.md#exponential-basic) |
| **Exponential Platform 3.2.9** | A Symfony-only CMS based on the last free versions of eZ Platform | Teams on that stack | [Exponential Platform 3.2.9](../who-is-the-exponential-foundation.md#exponential-platform-329) |
| **Exponential Platform Legacy** | A Symfony platform with Exponential 6 included: a dual kernel | Exponential 6 sites that add Symfony | [Exponential Platform Legacy](../who-is-the-exponential-foundation.md#exponential-platform-legacy) |
| **Exponential Platform Nexus** | Platform plus Exponential 6, Netgen Layouts and Media Site, with demo content | Agencies that want a complete site fast | [Exponential Platform Nexus](../who-is-the-exponential-foundation.md#exponential-platform-nexus) |
| **Exponential Platform DXP** | A free, open source Symfony DXP, the community continuation of the eZ Platform and Ibexa OSS line | New Symfony projects | [Exponential Platform DXP](../who-is-the-exponential-foundation.md#exponential-platform-dxp) |
| **Exponential Velocity** | A web and application server written in PHP that keeps the application loaded between requests | Everyone who serves Exponential | [Exponential Velocity](../who-is-the-exponential-foundation.md#exponential-velocity) |
| **Exponential Intelligence** | A writing assistant in the administration that writes drafts and never publishes on its own | Editors and content teams | [Exponential Intelligence](../who-is-the-exponential-foundation.md#exponential-intelligence) |
| **Exponential Layouts** | A visual page builder: layouts, zones, blocks and rules | Editors and designers | [Exponential Layouts](../who-is-the-exponential-foundation.md#exponential-layouts) |
| **AdminUI** | An alternative administration interface, a port of the Netgen AdminUI | Editors who prefer it | [AdminUI](../who-is-the-exponential-foundation.md#adminui) |
| **The help system** | The documentation inside the administration, searchable, with context help | Every user of the administration | [Help system](../../features/6.0/help-system.md) |
| **The extensions** | Packages that add features; the kernel's `composer.json` requires 66 se7enxweb packages | Site builders | [The extension ecosystem](../who-is-the-exponential-foundation.md#the-extension-ecosystem) |

## Sentences you can say

- "**Exponential** is a PHP CMS and application framework with a fully customizable content model, built-in shop,
  multisite and many languages. It is free software under the GNU GPL."
- "If you want Symfony, the family has **Exponential Platform**: DXP for Symfony only, Legacy and Nexus if you also
  want Exponential 6 beside it."
- "**Velocity** is Exponential's own PHP web server; the README calls it the recommended engine for every stage."
- "**Exponential Layouts** lets editors arrange pages themselves; **Exponential Intelligence** helps them write, but
  only drafts."
- "**Exponential Basic** is the small, simple one, good for learning."

## Which product for which person

Use the table [Which product is for you](../who-is-the-exponential-foundation.md#which-product-is-for-you). In
conversation, ask two questions first:

1. "Do you want to stay on PHP only, or do you work with Symfony?"
2. "Do you need a complete site with demo content quickly, or a framework to build on?"

| Answers | Point to |
|---|---|
| PHP only, build on it | Exponential 6 ([Getting started](../getting-started.md)) |
| Symfony only | Exponential Platform DXP ([DXP skeleton](../../features/6.0/platform-dxp-skeleton.md)) |
| Symfony, keep an Exponential 6 site | Exponential Platform Legacy ([Legacy bridge](../../features/6.0/legacy-bridge.md)) |
| Complete demo site with layouts, Symfony | Exponential Platform Nexus ([Nexus starter](../../features/6.0/platform-nexus-starter.md)) |
| Learning, a tiny site | Exponential Basic ([basic.exponential.earth](https://basic.exponential.earth)) |

## Common mix-ups

| Mix-up | Correct |
|---|---|
| "DXP has a layouts editor." | It does not ship a layouts editor, an approval workflow, a scheduler or the legacy kernel ([Exponential Platform DXP](../who-is-the-exponential-foundation.md#exponential-platform-dxp)). For Exponential 6 beside Symfony, choose Legacy or Nexus. |
| "Legacy means old." | "Legacy" is the Exponential 6 kernel when it runs beside a Symfony platform ([Glossary](../who-is-the-exponential-foundation.md#glossary)). Exponential 6 itself is the maintained line. |
| "Velocity is Qbix." | Velocity began as a fork of the Qbix web server engine; the product name is Exponential Velocity ([Brand and naming rules](../business/brand-and-naming-rules.md#the-product-family)). |
| "The AI publishes content." | Exponential Intelligence writes drafts only ([expai](../../features/6.0/extensions/expai.md#in-short)). |
| "request-shield is part of Exponential." | It is a project of CJW Network; Exponential documents how to use it ([Companion projects](../who-is-the-exponential-foundation.md#companion-projects)). |

## Check yourself

Answer without looking, then check with the table above.

```text
1. Which product would you suggest to a team that wants Symfony only?          ______________
2. What does Exponential Intelligence never do on its own?                     ______________
3. Which two distributions run Exponential 6 beside Symfony?                   ______________
4. What is Velocity, in one sentence?                                          ______________
5. Which product is for learning and personal sites?                           ______________
6. Does Exponential Platform DXP ship a layouts editor?                        ______________
```

## Related pages

- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Naming rules quick card](naming-rules-quick-card.md)
- [Talking points: the core set](talking-points-core-set.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
