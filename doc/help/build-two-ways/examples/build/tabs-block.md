---
title: "Tabs with short texts"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1114
summary: The Tabs block (Tabs "Title|Content" per line, Active tab index).
keywords: tabs, Tabs block, tab panel, active tab
type: help
---
# Tabs with short texts

Three tabs: Beginners, Advanced, Pros.

*Life cycle: Build, example 15 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Tabs** (group *Basic*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Tabs (Title|Content per line)** and **Active tab index (0-based)**.
4. **Publish layout**. The pages that use the layout show the block on their next view.

## With templates

1. Copy `extension/explayouts/design/standard/templates/explayouts/block/tabs.tpl` to `extension/mysite/design/media/templates/explayouts/block/tabs.tpl` and change your copy; every block of that type changes on every layout ([How a block maps to its templates](../../block-to-template-map.md)). Use it to change the tab markup for every Tabs block.

## Which to choose

The block for the content; the override for accessible markup site-wide.

## Check

- The tab given in Active tab index is open first.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
