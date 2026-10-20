---
title: "Move a site's settings into its extension"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1507
summary: exp:ini move / move-all move blocks or whole siteaccess scopes into an extension (--create-extension --activate), with a dry run.
keywords: exp:ini move, move-all, settings migration, extension, version control
type: help
---
# Move a site's settings into its extension

The menu lists and theme settings live in the design extension's repository.

*Life cycle: Migrate, example 8 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With settings](#with-settings)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

Not in the Layouts editor.

## With settings

1. `./console exp:ini move menu.ini/SiteInfo siteaccess:site extension:mysite:siteaccess:site --dry-run` shows the diff (on the test copy: *would move 1 block, 7 variables, 1 file*).
2. Without `--dry-run` it moves the block; the tool keeps the move only if the values in effect stay the same (it rolls back otherwise, unless `--force`). `--only=MainMenuID,FooterMenuID` limits it to some variables.
3. On the test copy, with `mysite` first in `ActiveExtensions[]`, its siteaccess file was read after the theme's and the header menu list in effect was unchanged after the move.

## Which to choose

Version the settings with the templates that read them.

## Check

- `exp:ini where menu.ini/SiteInfo/MainMenuID site` shows the extension's file; the value in effect is unchanged.

## Related pages

- [exp:ini for site builders](../../exp-ini-for-site-builders.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
