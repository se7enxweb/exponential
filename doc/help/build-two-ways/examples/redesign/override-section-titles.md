---
title: "A new look for section titles"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1401
summary: The Title block dispatches by view type to explayouts/block/title/<view type>.tpl; override section_centered for the Prefooter style.
keywords: title, section_centered, override, block template, redesign
type: help
---
# A new look for section titles

Centered section titles get a line on each side.

*Life cycle: Redesign, example 2 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Titles with the view `section_centered` (the Prefooter's) change everywhere.

## With templates

1. Copy `extension/sevenx_themes_media/design/media/templates/explayouts/block/title/section_centered.tpl` to `extension/mysite/design/media/templates/explayouts/block/title/section_centered.tpl`.
2. Change the heading markup (keep the tag from `$block.parameters.tag`).
3. Clear **Template & content** and check a page that uses it.

## Which to choose

Override per view type, so other titles keep their look.

## Check

- The Prefooter title shows the new look; other titles do not.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
