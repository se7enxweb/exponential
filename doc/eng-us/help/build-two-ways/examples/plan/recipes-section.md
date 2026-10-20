---
title: "Plan a recipes section"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1023
summary: Recipes have their own layout (Full / Recipe, layout_4 with a sidebar) and their own mapping; plan the category page and its lists.
keywords: recipes, ng_recipe, Full / Recipe, layout_4, category, planning
type: help
---
# Plan a recipes section

Recipe pages with ingredients beside the method, and a recipe index.

*Life cycle: Plan, example 24 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Full / Recipe (type `layout_4`, zones `left` and `right`) takes `ng_recipe` below the site root at priority 160.
2. The index is Recipes (node 144, class Category).

## With templates

1. The recipe data (time, ingredients) is shown by template blocks mapped to `content/parts/full_recipe_info.tpl` in the theme.

## Which to choose

Keep the recipe layout; change the parts by template overrides if needed.

## Check

- A recipe shows the sidebar layout.

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
