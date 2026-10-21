---
title: "Swap a seasonal layout by date"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1313
summary: A Time condition (from, to) on the seasonal mapping switches it at its start and end without anyone clicking.
keywords: time condition, seasonal, schedule, campaign dates, mapping
type: help
---
# Swap a seasonal layout by date

The Black Friday layout shows from Friday 00:00 to Monday 23:59.

*Life cycle: Maintain, example 14 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. On the seasonal mapping, **Add condition** of type **Time** with the start and end.
2. Keep its priority above the regular mapping.

Since explayouts 1.4.42 the resolver remembers that answer only until the condition's next start or end, and while that moment is within the page cache's `[HttpCacheSettings] MaxAge` (3600 seconds) the page is kept out of the page caches (`Cache-Control: private, no-store`), so the layout switches on time.

## With templates

1. A template condition on `currentdate()` plus a cache clear at the boundary.

## Which to choose

The Time condition for planned campaigns: it switches at the start and the end, without lag.

## Check

- After the end date the regular layout is back.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
