---
title: "Plan the mapping priorities"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1010
summary: The first enabled mapping by priority whose targets and conditions match wins; plan priorities so specific mappings come first.
keywords: mapping priority, rules order, layout mappings, specific before general
type: help
---
# Plan the mapping priorities

No page gets the wrong layout because a general mapping comes first.

*Life cycle: Plan, example 11 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Fit & Healthy: home (Location 89) 210, recipes 160, articles 130, topics 120, Default Layout (path prefix `/`) 10.
2. Give single pages (Location, Path) high numbers, class rules middle numbers, catch-alls low numbers.

## With templates

1. Without any mapping the page uses `[ResolverSettings] DefaultLayout` of `explayouts.ini` (on this site the identifier of Default Layout), or no layout at all.

## Which to choose

Leave gaps between numbers (10, 20 ...) so new mappings fit in.

## Check

- For three sample pages, going down the list by priority leads to the expected layout.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
