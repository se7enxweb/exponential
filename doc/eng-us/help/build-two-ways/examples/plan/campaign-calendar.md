---
title: "Plan campaigns with start and end dates"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1017
summary: A mapping can have a Time condition (from, to); plan campaign layouts with it: since explayouts 1.4.42 the layout switches at the start and the end, without lag.
keywords: campaign, time condition, start date, end date, seasonal, mapping, resolver cache, page cache, MaxAge
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
2. The resolver checks the time against `from` and `to`. Since explayouts 1.4.42 it remembers that answer only until the condition's next start or end, not for `[ResolverSettings] CacheTTL`; when that moment is within the page cache's `[HttpCacheSettings] MaxAge` (3600 seconds), the page is kept out of the page caches (`Cache-Control: private, no-store`). A range that is over is cached normally.

## With templates

1. The template way is a condition in the template (`currentdate()` against a date) and a cache clear at the switch.

## Which to choose

Plan the switch with the Time condition: it switches at its start and end, without lag.

## Check

- The campaign mapping has a higher priority than the regular one.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
