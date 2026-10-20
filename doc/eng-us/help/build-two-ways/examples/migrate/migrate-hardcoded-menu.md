---
title: "Move a hard-coded menu into settings"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1526
summary: An old template with links typed into it becomes a MainMenuID[] list read by the theme's main_menu.tpl.
keywords: hard-coded menu, settings, MainMenuID, migration
type: help
---
# Move a hard-coded menu into settings

Editors and administrators can change a menu that was code.

*Life cycle: Migrate, example 27 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With settings](#with-settings)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

Not in the Layouts editor.

## With settings

1. Collect the node IDs of the linked pages; write them with `exp:ini add` into `MainMenuID[]`; remove your override of `main_menu.tpl` so the theme's template reads the list.

## Which to choose

Settings or the tree, not code, for menu entries.

## Check

- The menu is unchanged for visitors.

## Related pages

- [Header menu by command line](../../header-menu-by-cli.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
