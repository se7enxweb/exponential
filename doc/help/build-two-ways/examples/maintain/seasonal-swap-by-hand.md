---
title: "Swap a seasonal layout by hand"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1312
summary: Two mappings for the same page, the seasonal one with a higher priority: tick or untick Enabled to switch.
keywords: seasonal, swap, mapping, enabled, priority, campaign
type: help
---
# Swap a seasonal layout by hand

The summer home page from 1 June, the regular one again in September.

*Life cycle: Maintain, example 13 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Create the summer layout and a mapping for the home page with **Priority** above the regular one and **Enabled** unticked.
2. On 1 June open the mapping, tick **Enabled**; in September untick it.

## With templates

1. The template way is two full view templates and a deploy at each switch.

## Which to choose

Mappings switch in one click.

## Check

- The home page shows the summer layout (after at most `[ResolverSettings] CacheTTL` for addresses the resolver remembered).

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
