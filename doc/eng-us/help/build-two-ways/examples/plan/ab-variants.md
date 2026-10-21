---
title: "Plan A/B variants of a page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1018
summary: A Query parameter condition gives one address a second layout: since explayouts 1.4.42 /showcase and /showcase?variant=b each get their own layout on every request.
keywords: A/B test, variant, query parameter, resolver cache, two URLs, experiment
type: help
---
# Plan A/B variants of a page

Compare two versions of a landing page.

*Life cycle: Plan, example 19 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Add a mapping *Path prefix `showcase` + Query parameter `variant`* (priority 3000) beside the regular Showcase mapping. Since explayouts 1.4.42 the resolver does not cache an answer that depends on a Query parameter condition, so `/showcase` and `/showcase?variant=b` each get their own layout on every request. Verified on the live installation: a mapping with Query parameter `p643b=b` on one article showed `layout_4` with `?p643b=b` and the regular `layout_2` without it, on both Apache and Velocity, in any request order.
2. Split the traffic outside (campaign links, newsletter halves). The page cache does not keep an address whose query has a parameter outside `[HttpCacheSettings] QueryStringParameters`, so the variant is rendered on every view.

## With templates

1. Two full view templates, chosen by `override.ini` per node or remote ID, are the template way to the same.

## Which to choose

One address with a Query parameter for a test; two pages with their own address (for example `/offer-a` and `/offer-b`) when both variants should come from the page cache.

## Check

- Each variant address shows its own layout in a private window.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Known editor behaviors](../../known-editor-behaviors.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
