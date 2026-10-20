---
title: "Update stored component block identifiers"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1510
summary: Layouts from before the rename store components as ibexa_component_<type>; ext:explayouts:updatecomponentblockidentifiers renames them to exp_component_<type>.
keywords: component blocks, identifiers, rename, upgrade, exp_component
type: help
---
# Update stored component block identifiers

Old imported layouts render with the current templates.

*Life cycle: Migrate, example 11 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Afterwards the editor lists them under their new identifiers.

## On the command line

1. `./console ext:explayouts:updatecomponentblockidentifiers --allow-root-user` (its help: *Renames the stored component blocks from ibexa_component_<type> to exp_component_<type>*).
2. The media design's `block.tpl` already shows a block stored as `ibexa_component_<type>` with the `exp_component_` template.

## Which to choose

Run once after importing old layouts.

## Check

- A new export has no `ibexa_component_`.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
