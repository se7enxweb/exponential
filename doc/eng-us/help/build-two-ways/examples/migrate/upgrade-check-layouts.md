---
title: "Check layouts after an upgrade"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1518
summary: After upgrading Exponential, export, list, open the main layouts in the editor and compare the pages.
keywords: upgrade, check, layouts, export, compare
type: help
---
# Check layouts after an upgrade

The upgrade did not change how pages render.

*Life cycle: Migrate, example 19 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open Header / Footer and the home layout in the editor; check the preview.

## On the command line

1. Before: `exp:layouts export --all`; after: the same; compare `layouts.json`.
2. Clear **Template & content** after the upgrade.

## Which to choose

Compare exports; open the editor for the visual check.

## Check

- The page markers (zones, block counts) are as before.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Caches after a change](../../caches-after-a-change.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
