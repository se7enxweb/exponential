---
title: "A new look for one class in lists"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1416
summary: Add content/views/<view>/<class>.tpl in your extension; lists with that item view type use it for that class.
keywords: item view, content views, card, class template, redesign
type: help
---
# A new look for one class in lists

Recipes in card view show the cooking time.

*Life cycle: Redesign, example 17 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Lists with **Item view type** Card show recipes with the new template.

## With templates

1. The theme's `itemViewTemplate()` prefers `content/views/<view>/<class>.tpl` and falls back to `content/views/<view>.tpl`; add `extension/mysite/design/media/templates/content/views/card/ng_recipe.tpl`.

## Which to choose

Per class, so other classes keep the generic card.

## Check

- A recipe card shows the cooking time; an article card is unchanged.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
