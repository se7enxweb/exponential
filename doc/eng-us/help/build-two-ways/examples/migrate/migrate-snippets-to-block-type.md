---
title: "Replace copied HTML snippets by a block type"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1527
summary: Many HTML snippet blocks with the same markup become one block type; replace them one by one.
keywords: HTML snippet, block type, cleanup, migration
type: help
---
# Replace copied HTML snippets by a block type

No more raw HTML copies of the same box.

*Life cycle: Migrate, example 28 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Find the snippets in an export (`"definition_identifier": "html"` in `layouts.json`); replace each by the new block; publish.

## With templates

1. Define the block type ([Add a block type](../../add-a-block-type.md)).

## Which to choose

Do it once the same markup appears three times.

## Check

- A new export has fewer `html` blocks.

## Related pages

- [Add a block type](../../add-a-block-type.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
