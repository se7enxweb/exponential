---
title: "Freeze a Layouts site into templates"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1514
summary: When a site must stop changing (an archive), its layouts can stay; freezing means fewer editors, not rewriting.
keywords: archive, freeze, layouts, templates, permissions, migration
type: help
---
# Freeze a Layouts site into templates

An old campaign site kept online unchanged.

*Life cycle: Migrate, example 15 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Export all layouts as the record; take the policy `explayouts_ui_api/edit` (editing in the Layouts editor) from the editors' roles in the role editor (`/role/list` in the administration); `explayouts_ui_api/read` lets them still look.

## With templates

1. Rewriting layouts as templates is rarely worth it; the page cache serves unchanged pages fast.

## Which to choose

Freeze by policies and exports.

## Check

- The archive renders as before.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
