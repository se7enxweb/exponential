---
title: "A list of the newest articles of a section"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1117
summary: A List block with a dynamic collection (parent location, sort by published, limit), or fetch() in a template.
keywords: latest articles, newest, dynamic collection, List block, parent location, sort, fetch content list
type: help
---
# A list of the newest articles of a section

The five newest articles of Fitness, always up to date.

*Life cycle: Build, example 18 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **List** (group *Listing*) into the zone `main`.
3. On the **Content** tab, **Change** the collection type to **Dynamic collection**, query type **Exponential**, **Apply**.
4. **Parent location**: Fitness; **Sort type** Published, **Sort direction** Descending; under **Offset & number of items** the number 5.
5. **Publish layout**. The pages that use the layout show the block on their next view.

Step by step with screenshots: [Manual and dynamic collections](../../manual-and-dynamic-collections.md).

## With templates

1. Write `extension/mysite/design/media/templates/explayouts/tpl_block/latest_fitness.tpl` in your design extension, clear the template cache, and place it with a **Template block** whose **Template name** is `latest_fitness` ([Your own template blocks](../../template-blocks.md)). The tested template is on that page.

## Which to choose

Editors can retarget the dynamic list themselves; the template fixes it in code.

## Check

- Publishing a new article in Fitness puts it first.

## Related pages

- [Manual and dynamic collections](../../manual-and-dynamic-collections.md)
- [Your own template blocks](../../template-blocks.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
