---
title: Layouts core in the DXP
audience: dxp-developer
category: dxp-development
order: 44
summary: What se7enxweb/layouts-core gives a DXP project and what it does not - the Layouts model and rendering core without an editor, admin or standard blocks - why the fork exists (PHP 8.4 and Twig), and how to get a full page builder.
keywords: layouts, layouts-core, se7enxweb/layouts-core, Netgen Layouts, page builder, editor, zones, blocks, rules, PHP 8.4, private(set), Twig, Nexus
type: help
---
# Layouts core in the DXP

This page is for developers who see `se7enxweb/layouts-core` in a DXP project and wonder whether the DXP has a page
builder. It does not ship one; it ships the core that a page builder is built on.

## Contents

1. [What the package is](#what-the-package-is)
2. [What the DXP does not include](#what-the-dxp-does-not-include)
3. [Why the fork](#why-the-fork)
4. [If you need a page builder](#if-you-need-a-page-builder)

## What the package is

From [Layouts on the platform](../../features/6.0/platform-layouts-core-fork.md#what-it-is): the core package "holds
the model (layouts, zones, blocks, collections, rules, targets, conditions), the parameter system, the block
definitions and the rendering". It is the fork of `netgen/layouts-core`, tagged `2.0.0-se7enx.1` on top of upstream
`2.0.0`, and needs PHP 8.4 (`^8.4`).

## What the DXP does not include

The metapackage requires only the core. These Netgen Layouts packages are not part of the DXP:

| Not included | What it would add |
|---|---|
| The Layouts editor app and admin | The visual editor (`/nglayouts/app`) and the layouts admin (`/nglayouts/admin`) on Nexus |
| The standard blocks | Ready block types |
| The integration with the content repository and the Site API | Content items as block items, content-aware rules |

So editors of a plain DXP project cannot arrange pages with blocks. Say so in proposals
([Claims to avoid](claims-to-avoid.md)).

## Why the fork

PHP 8.4 added asymmetric visibility (`private(set)`). Twig checks `ReflectionProperty::isInitialized()` before it looks
for a getter; for such properties the check fails, and a block template that reads a parameter stops with "Call to
undefined method". The fork adds explicit getters to `Netgen\Layouts\Parameters\Parameter` (`getName()`,
`getParameterDefinition()`, `getValue()`, `isEmpty()`, `getValueObject()`), and declares `replace:
netgen/layouts-core`, so every Netgen Layouts package that requires the upstream name gets the fork.

## If you need a page builder

| Option | What you get |
|---|---|
| Exponential Platform Nexus (1.3 or the v5 starter) | Netgen Layouts 2.0 with its editor, standard blocks and a demo site ([Nexus starter](../../features/6.0/platform-nexus-starter.md)) |
| Exponential 6 | Exponential Layouts, the port to the Exponential 6 kernel ([The layouts editor](../layouts-editor.md)) |
| Your own DXP project plus the missing Netgen Layouts packages | Possible in principle; test it on a copy, with PHP 8.4 or newer, and check every package against the fork table |

The concepts are the same everywhere: "layout types with named zones, blocks with parameters, collections of items,
and rules with targets that pick the layout of a page. If you know one, you know the other."

## Related pages

- [Layouts on the platform: the layouts-core fork](../../features/6.0/platform-layouts-core-fork.md)
- [What the DXP ships and what it does not](what-the-dxp-ships-and-what-it-does-not.md)
- [Talking points: Exponential Layouts](../advocates/talking-points-exponential-layouts.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
