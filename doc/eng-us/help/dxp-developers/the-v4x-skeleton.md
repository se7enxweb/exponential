---
title: The 4.6.x skeleton
audience: dxp-developer
category: dxp-development
order: 43
summary: When to use se7enxweb/exponential-platform-v4x-dxp-skeleton instead of the v5 skeleton - Platform 4.6.x on Symfony 5.4 with PHP 7.4 to 8.5 - and how it differs from the v5 DXP described in the rest of this section.
keywords: 4.6, v4x skeleton, exponential-platform-v4x-dxp-skeleton, Symfony 5.4, PHP 7.4, Ibexa 4.6, older PHP, migration step
type: help
---
# The 4.6.x skeleton

This page is for developers who cannot run PHP 8.3 yet, or who move an Ibexa 4.6 project one generation at a time.
Most of this section describes the v5 DXP; the 4.6.x skeleton is its older sibling.

## Contents

1. [The two skeletons](#the-two-skeletons)
2. [When to use the 4.6.x skeleton](#when-to-use-the-46x-skeleton)
3. [What differs from v5](#what-differs-from-v5)
4. [Moving on to v5 later](#moving-on-to-v5-later)

## The two skeletons

| Skeleton | Platform | Symfony | PHP | Node.js |
|---|---|---|---|---|
| `se7enxweb/exponential-platform-dxp-skeleton` | v5 | 7.4 LTS | 8.3 or newer | 20 |
| `se7enxweb/exponential-platform-v4x-dxp-skeleton` | 4.6.x | 5.4 (`extra.symfony.require` pinned to `5.4.*`) | 7.4 to 8.5 | 20 |

Source: [DXP skeleton](../../features/6.0/platform-dxp-skeleton.md#what-it-is).

## When to use the 4.6.x skeleton

- Your servers run PHP 7.4 to 8.2 and cannot move yet.
- You migrate an Ibexa 4.0 to 4.6 project: chapter 16 moves it to 4.6.latest first, then to this skeleton
  ([16.5.1](../../install/16-migrating-from-ez-platform-and-ibexa.md#1651-pick-the-generation)).

For a new project, prefer v5.

## What differs from v5

| Area | 4.6.x | v5 |
|---|---|---|
| Core tables | `ez*` names, plus a few `ibexa_*` tables | every core table `ibexa_*` |
| Field type identifiers | `ez*` (for example `ezstring`) | `ibexa_*` (for example `ibexa_string`) |
| Symfony | 5.4 | 7.4 |
| Command prefix | `exponential:` with `ibexa:` aliases, from the kernel fork's 2026-04-07 change | the same |

Commands, settings and package names on the other pages of this section are those of v5. On 4.6.x, check each one with
`php bin/console list` and `composer show` before you use it.

## Moving on to v5 later

The move from 4.6 to 5.0 includes the table renames and the new field type identifiers
([16.5.6](../../install/16-migrating-from-ez-platform-and-ibexa.md#1656-the-database-generation-by-generation)), PHP 8.3,
and Symfony 7.4. Plan it as its own project, with a timed dry run on a copy.

## Related pages

- [Coming from Ibexa OSS or eZ Platform](coming-from-ibexa-oss-or-ez-platform.md)
- [PHP, Symfony and Node.js versions](php-symfony-and-node-versions.md)
- [Platform package map](../../specifications/6.0/platform-package-map.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
