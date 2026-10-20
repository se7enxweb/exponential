---
title: "Clear the caches once before launch"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1211
summary: After the last template and settings changes, one Template & content clear, then a warm-up of the main pages.
keywords: launch, cache clear, warm-up, Template & content, exp:cache
type: help
---
# Clear the caches once before launch

Launch day pages show the final state and answer fast.

*Life cycle: Launch, example 12 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Administration: **Clear cache > Template & content > Clear**.

## On the command line

1. `./console exp:cache clear --tag=template,content` (ends with `PASS`), then request the main pages once.

## Which to choose

Avoid **All caches** unless needed; it also clears what does not need clearing.

## Check

- A second request of the front page answers with `X-Exp-Cache: HIT`.

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
