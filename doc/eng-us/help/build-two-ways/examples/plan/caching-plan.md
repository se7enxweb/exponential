---
title: "Plan which cache each change needs"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1013
summary: Write down per kind of change which cache to clear, so launches and updates do not end in "it does not show".
keywords: caches, cache plan, template cache, content cache, page cache, Velocity
type: help
---
# Plan which cache each change needs

No change is called broken when it is only cached.

*Life cycle: Plan, example 14 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [Which cache](#which-cache)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Publishing in the Layouts editor and publishing content refreshed the pages by itself on the test copy.

## Which cache

1. Settings read in templates (menus): **Template & content**. New templates: **Template & content**. Stylesheets: **Template & content** (packed files). Command-line layout imports: **Content**.

## Which to choose

Keep [Caches after a change](../../caches-after-a-change.md) with the plan.

## Check

- The team knows the Clear cache box and `./console exp:cache clear --tag=template,content`.

## Related pages

- [Caches after a change](../../caches-after-a-change.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
