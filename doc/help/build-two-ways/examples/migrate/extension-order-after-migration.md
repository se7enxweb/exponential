---
title: "Check the extension order after moving settings"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1520
summary: After moving settings or adding extensions, check DesignExtensions[] and AvailableBlocks[] in effect.
keywords: extension order, ActiveExtensions, DesignExtensions, AvailableBlocks, migration
type: help
---
# Check the extension order after moving settings

Your design and block types still win.

*Life cycle: Migrate, example 21 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. The palette still lists your block types.

## With templates

1. `./console exp:ini get design.ini/ExtensionSettings/DesignExtensions -s site` (yours after the theme) and `exp:ini where explayouts.ini/BlockSettings/AvailableBlocks admin` (your block types still there).

## Which to choose

Check after every change of `ActiveExtensions[]`.

## Check

- Both lists as expected.

## Related pages

- [Your own design extension](../../your-own-design-extension.md)
- [Add a block type](../../add-a-block-type.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
