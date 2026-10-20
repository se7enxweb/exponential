---
title: "Check that pages come from the page cache"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1214
summary: The response header X-Exp-Cache tells HIT, MISS or MISS (purged); check the main pages after launch.
keywords: page cache, HTTP cache, X-Exp-Cache, HIT, MISS, launch
type: help
---
# Check that pages come from the page cache

Visitors get cached pages.

*Life cycle: Launch, example 15 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. After publishing a layout, the next request said `MISS (purged)` on the test copy.

## On the command line

1. `curl -s -D - -o /dev/null https://www.example.com/fitness | grep -i x-exp-cache` twice: the first may say `MISS`, the second should say `HIT`.

## Which to choose

Check after launch and after big changes.

## Check

- `HIT` on the second request.

## Related pages

- [Caches after a change](../../caches-after-a-change.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
