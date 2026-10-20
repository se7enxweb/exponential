---
title: "Fill the header menu for launch"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1206
summary: Write the final MainMenuID[] list in one edit (administration) or a clear plus adds (console).
keywords: header menu, launch, MainMenuID, exp:ini clear, exp:ini add
type: help
---
# Fill the header menu for launch

The header menu has the launch sections in the right order.

*Life cycle: Launch, example 7 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With settings](#with-settings)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

Not in the Layouts editor: the header menu is `menu.ini`. In **Setup > Ini settings**, `menu.ini`, siteaccess `site`, **Edit** `MainMenuID[]`: one `=<node id>` line per item after the empty first line, **Save**, then **Template & content**.

## With settings

1. `./console exp:ini clear menu.ini/SiteInfo/MainMenuID[] siteaccess:site`, then one `exp:ini add ... <id> siteaccess:site` per item in order, then `./console exp:cache clear --tag=template,content`.

## Which to choose

The administration for a one-time edit; the console when the list is scripted for several sites.

## Check

- The header shows the items in the planned order.

## Related pages

- [Header menu in the administration](../../header-menu-in-the-admin.md)
- [Header menu by command line](../../header-menu-by-cli.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
