---
title: "The header menu of a second language"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1215
summary: A German siteaccess (bold_ger, starter_ger on this installation) has its own menu.ini; write its list in its own scope.
keywords: language, siteaccess, menu.ini, bold_ger, header menu, translation
type: help
---
# The header menu of a second language

The German site's menu lists its own pages.

*Life cycle: Launch, example 16 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With settings](#with-settings)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

Not in the Layouts editor. In **Setup > Ini settings**, choose `menu.ini` and the German siteaccess, **Edit** `MainMenuID[]`.

## With settings

1. `./console exp:ini where menu.ini/SiteInfo/MainMenuID bold_ger`, then `exp:ini add ... <id> siteaccess:bold_ger`.

## Which to choose

One list per siteaccess keeps languages independent.

## Check

- The German site shows the German menu, the English one is unchanged.

## Related pages

- [Header menu by command line](../../header-menu-by-cli.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
