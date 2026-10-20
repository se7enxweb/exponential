---
title: "Plan for speed"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1033
summary: Cached pages answered in 22 to 102 ms on the test copy, a page after a full template clear in about 3 s; plan launches and cache clears with that in mind.
keywords: performance, speed, page cache, template compile, cache clear, launch
type: help
---
# Plan for speed

No surprise slow first visits after a deploy.

*Life cycle: Plan, example 34 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [After template changes](#after-template-changes)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Publishing a layout refreshed the affected pages on the test copy (`X-Exp-Cache: MISS (purged)`) without a full clear.

## After template changes

1. After a full **Template & content** clear the next requests recompile templates (2.9 to 3.4 s for the first page on the test copy).
2. Warm the main pages after a clear (request them once).

## Which to choose

Prefer targeted actions (publish, Content clear) over **All caches**.

## Check

- The main pages answer from the page cache (`X-Exp-Cache: HIT`) after the warm-up.

## Related pages

- [Measured steps and times](../../measured-steps-and-times.md)
- [Caches after a change](../../caches-after-a-change.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
