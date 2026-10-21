---
title: "Undo a published layout change"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1321
summary: Import the export taken before the change with --strategy=overwrite --publish; the import purges the page cache itself.
keywords: undo, rollback, published layout, import overwrite, maintenance
type: help
---
# Undo a published layout change

Last week's Prefooter back.

*Life cycle: Maintain, example 22 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Or **Layouts > Import** in the administration with the same file.

## On the command line

1. `./console exp:layouts import prefooter-before.zip --dry-run --strategy=overwrite --publish`, then without `--dry-run`; the import purges the page cache itself (since explayouts_core 1.0.10).

## Which to choose

Only possible with an export from before; take one before every larger change.

## Check

- The layout has its earlier blocks; ID and identifier are unchanged.

## Related pages

- [Roll a layout back](../../roll-back-a-layout.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
