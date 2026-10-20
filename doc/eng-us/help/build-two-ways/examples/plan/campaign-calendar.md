---
title: "Plan campaigns with start and end dates"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1017
summary: A mapping can have a Time condition (from, to); plan campaign layouts with it, knowing the resolver remembers answers for up to an hour.
keywords: campaign, time condition, start date, end date, seasonal, mapping, resolver cache, CacheTTL
type: help
---
# Plan campaigns with start and end dates

A campaign layout that takes over a page from one date to another.

*Life cycle: Plan, example 18 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **New mapping** offers the condition **Time** next to Siteaccess, Class, Query parameter and Route parameter.
2. The resolver checks the time against `from` and `to`, then remembers the answer per path and siteaccess for `[ResolverSettings] CacheTTL` (3600 seconds on this installation).

## With templates

1. The template way is a condition in the template (`currentdate()` against a date) and a cache clear at the switch.

## Which to choose

Plan the switch with up to an hour of overlap, or switch by hand (enable the new mapping, disable the old one) at the moment it must happen.

## Check

- The campaign mapping has a higher priority than the regular one.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
