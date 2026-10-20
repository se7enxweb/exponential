---
title: "A monthly look at the caches"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1331
summary: exp:cache list shows every cache; look for growth and make sure page caching still works.
keywords: cache, exp:cache list, sizes, maintenance, page cache
type: help
---
# A monthly look at the caches

Caches work and do not fill the disk.

*Life cycle: Maintain, example 32 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

Nothing in the Layouts editor.

## On the command line

1. `./console exp:cache list --sizes`, then `curl -s -D - -o /dev/null https://www.example.com/ | grep -i x-exp-cache` twice.

## Which to choose

Monthly, or after big changes.

## Check

- The second request says `HIT`.

## Related pages

- [Caches after a change](../../caches-after-a-change.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
