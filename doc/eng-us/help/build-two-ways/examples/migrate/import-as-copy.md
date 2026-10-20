---
title: "Import beside the existing layouts"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1504
summary: --strategy=copy imports items that already exist as new copies, leaving the originals.
keywords: import copy, strategy copy, side by side, migration
type: help
---
# Import beside the existing layouts

Compare an incoming layout with the current one before switching.

*Life cycle: Migrate, example 5 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open the copy in the editor, compare, then switch the mapping to it with **Link other layout** or a new mapping.

## On the command line

1. `./console exp:layouts import incoming.zip --strategy=copy` (without `--publish` the copies are drafts).

## Which to choose

Copy when unsure; overwrite when the import is the truth.

## Check

- Both layouts exist.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
