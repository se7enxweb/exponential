---
title: "A last-minute header menu change on launch day"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1222
summary: The quickest safe path: exp:ini add with --dry-run first, then the cache clear; about two commands.
keywords: launch day, header menu, quick change, exp:ini, dry run
type: help
---
# A last-minute header menu change on launch day

Add the forgotten section to the menu in a minute.

*Life cycle: Launch, example 23 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With settings](#with-settings)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Or in **Setup > Ini settings** (ten actions on the test copy).

## With settings

1. `./console exp:ini add menu.ini/SiteInfo/MainMenuID[] <id> siteaccess:site --dry-run`, check the diff, run it without `--dry-run`.
2. `./console exp:cache clear --tag=template,content`.

## Which to choose

The console: two commands, about 4 seconds on the test copy.

## Check

- The item shows; `exp:ini` made a backup under `var/backup/ini/`.

## Related pages

- [Header menu by command line](../../header-menu-by-cli.md)
- [Measured steps and times](../../measured-steps-and-times.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
