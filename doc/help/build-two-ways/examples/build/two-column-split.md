---
title: "A two-thirds and one-third split"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1129
summary: The Two columns container in view two_columns_66_33 (or 33_66) with blocks in left and right.
keywords: two columns, 66/33, sidebar, Two columns, container, placeholders left right
type: help
---
# A two-thirds and one-third split

Main text left, a list of links right.

*Life cycle: Build, example 30 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Two columns** (group *Containers*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **View type** `two_columns_66_33` (or `two_columns_33_66`).
4. Drag blocks into its placeholders `left` and `right`.
5. **Publish layout**. The pages that use the layout show the block on their next view.

For a whole page with a sidebar, the layout type **Header / Left / Right / Footer** (`layout_4`, used by Full / Recipe) is the other way.

## With templates

1. Write the two columns in the template with the design's grid classes (the media design uses Bootstrap rows: `row`, `col-lg-8`, `col-lg-4`, as in `explayouts/layout.tpl` for `layout_4`).

## Which to choose

Within one page section: Two columns. The whole page: layout type `layout_4`.

## Check

- The columns stack on a phone.

## Related pages

- [The Design tab](../../design-tab-options.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
