---
title: "Different node IDs on staging and production"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1521
summary: Node IDs differ between installations; menus (MainMenuID) need the target's IDs, layouts are matched by remote ID and path.
keywords: node IDs, staging, production, remote ID, menu IDs, migration
type: help
---
# Different node IDs on staging and production

Menus and layouts work after moving to production.

*Life cycle: Migrate, example 22 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With settings](#with-settings)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Layout imports resolve content through `references.json` (remote ID and path).

## With settings

1. Write the production node IDs into the production `menu.ini` (not a copy of staging's list).

## Which to choose

Never copy ID lists blindly between installations.

## Check

- Each menu entry opens the right page on production.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Header menu by command line](../../header-menu-by-cli.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
