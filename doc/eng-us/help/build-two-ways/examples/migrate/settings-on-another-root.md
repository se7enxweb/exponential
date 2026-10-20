---
title: "Change the settings files of another root"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1509
summary: exp:ini --root=<dir> works on the settings tree of another root (a copy of the installation); the INI cache there is not cleared, and the scope must exist there.
keywords: exp:ini --root, another installation, checkout, settings
type: help
---
# Change the settings files of another root

Prepare the menu list in a copy of the installation before changing the live one.

*Life cycle: Migrate, example 10 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With settings](#with-settings)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

Not in the Layouts editor.

## With settings

1. `./console exp:ini add menu.ini/SiteInfo/MainMenuID[] 192 siteaccess:site --root=/path/to/copy --dry-run`.
2. The scope must exist in that root: in a fresh checkout of the repository, which has no `settings/siteaccess/site/` of the installation, the test answered *Refused: Unknown siteaccess 'site' (not in settings/siteaccess/ nor in AvailableSiteAccessList)*. Extension scopes work in a checkout of the extension.

## Which to choose

Useful for preparing releases.

## Check

- The copy's file has the change; the running site is unchanged.

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
