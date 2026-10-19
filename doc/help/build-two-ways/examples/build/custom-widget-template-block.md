---
title: "A custom widget only a developer can build"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1133
summary: Anything the block types cannot do (a calculator, a data feed) is a template; the Template block lets editors place it.
keywords: custom widget, template block, developer, editor, bridge, tpl_block
type: help
---
# A custom widget only a developer can build

A "calories per workout" table computed from content, placed by editors where they want.

*Life cycle: Build, example 34 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [With templates](#with-templates)
2. [Then in the Layouts editor](#then-in-the-layouts-editor)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## With templates

1. Write `extension/mysite/design/media/templates/explayouts/tpl_block/calories_table.tpl` in your design extension, clear the template cache, and place it with a **Template block** whose **Template name** is `calories_table` ([Your own template blocks](../../template-blocks.md)).
2. Fetch the data and compute in the template, or add a template operator in your extension for real logic.

## Then in the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Template block** (group *Placeholders*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Template name** `calories_table`.
4. **Publish layout**. The pages that use the layout show the block on their next view.

## Which to choose

This is where the two ways meet: code by the developer, placement by the editor.

## Check

- The block reads "Template block: **calories_table**" in the editor, and the site shows the table.

## Related pages

- [Your own template blocks](../../template-blocks.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
