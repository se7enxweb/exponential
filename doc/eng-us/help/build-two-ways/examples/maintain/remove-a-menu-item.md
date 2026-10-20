---
title: "Take an item out of the header menu"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1307
summary: Delete its line from MainMenuID[] (administration) or exp:ini rem (console), then Template & content.
keywords: remove menu item, MainMenuID, exp:ini rem, header menu, maintenance
type: help
---
# Take an item out of the header menu

The Tour item leaves the menu; the page stays.

*Life cycle: Maintain, example 8 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With settings](#with-settings)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

Not in the Layouts editor: **Setup > Ini settings**, `menu.ini`, `site`, **Edit** `MainMenuID[]`, delete the `=26210` line, **Save**, **Template & content**.

## With settings

1. `./console exp:ini rem menu.ini/SiteInfo/MainMenuID[] 26210 siteaccess:site`, then `./console exp:cache clear --tag=template,content`.

## Which to choose

Either; the console shows the diff first with `--dry-run`.

## Check

- The page is still reachable by its address.

## Related pages

- [Reorder or remove header menu items](../../reorder-or-remove-header-menu-items.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
