---
title: "Inventory layouts and settings from the console"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1015
summary: exp:layouts list and exp:ini where give a complete, scriptable picture of an installation's layouts and menu settings.
keywords: exp:layouts list, exp:ini where, inventory, console, audit
type: help
---
# Inventory layouts and settings from the console

Start a redesign or an audit with facts, not memory.

*Life cycle: Plan, example 16 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [On the command line](#on-the-command-line)
2. [Which to choose](#which-to-choose)
3. [Check](#check)

## On the command line

1. `./console exp:layouts list` (add `--drafts`, `--json`).
2. `./console exp:ini where menu.ini/SiteInfo/MainMenuID site` and the same for `FooterMenuID`.
3. `./console exp:layouts export --all --output=inventory.zip` for the full detail (`layouts.json`).

## Which to choose

The console output can be kept with the project and compared later.

## Check

- The inventory names every layout, its mappings and its links.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [exp:ini for site builders](../../exp-ini-for-site-builders.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
