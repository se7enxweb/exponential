---
title: "Move a page type to a sidebar layout"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1408
summary: A layout's type cannot be switched in place: create a layout of the new type, rebuild, remap.
keywords: layout type, sidebar, layout_4, rebuild, remap, redesign
type: help
---
# Move a page type to a sidebar layout

Articles get a sidebar like recipes.

*Life cycle: Redesign, example 9 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **New layout** of type **Header / Left / Right / Footer**.
2. Link `header`, `footer` (and `pre_footer` if wanted) to the shared layouts; build `left` and `right`.
3. Publish, then point the article mapping at it (**Link other layout** on the mapping) or create a new mapping above it.

## With templates

1. On the media design, `explayouts/layout.tpl` already arranges `left` and `right` in a Bootstrap row for `layout_4`.

## Which to choose

Keep the old layout until the new one is checked; switching the mapping is one step back.

## Check

- Articles show the sidebar.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
