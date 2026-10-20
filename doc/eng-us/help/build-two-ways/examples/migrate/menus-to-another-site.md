---
title: "Copy the menu settings to another siteaccess"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1508
summary: exp:ini copy copies a setting (a whole array with its reset line) from one scope to another.
keywords: exp:ini copy, menus, siteaccess, migration
type: help
---
# Copy the menu settings to another siteaccess

The German site starts with the English menu list.

*Life cycle: Migrate, example 9 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With settings](#with-settings)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

Not in the Layouts editor.

## With settings

1. `./console exp:ini copy menu.ini/SiteInfo/MainMenuID siteaccess:site siteaccess:bold_ger --dry-run`, then without `--dry-run`; then **Template & content**.

## Which to choose

Copy, then change the copy.

## Check

- `exp:ini where menu.ini/SiteInfo/MainMenuID bold_ger` shows the list.

## Related pages

- [Header menu by command line](../../header-menu-by-cli.md)
- [exp:ini for site builders](../../exp-ini-for-site-builders.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
