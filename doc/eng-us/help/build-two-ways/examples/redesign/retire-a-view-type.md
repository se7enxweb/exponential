---
title: "Retire a view type after a redesign"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1417
summary: Remove it from ViewTypes[] only after no block uses it; find them in an export.
keywords: retire view type, cleanup, ViewTypes, export, redesign
type: help
---
# Retire a view type after a redesign

Clean up a look the new design does not have.

*Life cycle: Redesign, example 18 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With an export and settings](#with-an-export-and-settings)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Change each block found to another view type (on explayouts_ui_api before 1.3.29, mind the save quirk).

## With an export and settings

1. Export all layouts and search `layouts.json` for `"view_type": "<name>"`; switch those blocks first.
2. Then remove the `ViewTypes[]` line (with `exp:ini rem`) and the template.

## Which to choose

Blocks keep their stored view type; a removed one would point at a missing template.

## Check

- A new export has no block with the old view type.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Add a view type](../../add-a-view-type.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
