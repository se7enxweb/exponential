---
title: "Move a header menu item to the front"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1308
summary: Reorder the lines of MainMenuID[] in the administration, or exp:ini clear and add in the new order.
keywords: reorder menu, MainMenuID, exp:ini clear, menu order, maintenance
type: help
---
# Move a header menu item to the front

Recipes first in the menu.

*Life cycle: Maintain, example 9 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With settings](#with-settings)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **Setup > Ini settings**, **Edit** `MainMenuID[]`, move the `=144` line to the top (after the empty first line), **Save**, **Template & content**.

## With settings

1. `exp:ini clear`, then `exp:ini add` for each ID in order ([Reorder or remove header menu items](../../reorder-or-remove-header-menu-items.md)).

## Which to choose

The administration for one move; the console for scripted orders.

## Check

- The header starts with Recipes.

## Related pages

- [Reorder or remove header menu items](../../reorder-or-remove-header-menu-items.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
