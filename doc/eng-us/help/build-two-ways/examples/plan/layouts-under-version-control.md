---
title: "Plan how layouts are kept and versioned"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1014
summary: Layouts live in the database; plan regular exports (exp:layouts export) so they can be compared, restored and moved.
keywords: version control, backup, export, layouts zip, restore, planning
type: help
---
# Plan how layouts are kept and versioned

A layout can always be put back.

*Life cycle: Plan, example 15 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Decide when exports are taken: before every larger change, and on a schedule.

## On the command line

1. `./console exp:layouts export --all --output=layouts-<date>.zip` writes every layout, shared layout and mapping; keep the files with the project.

## Which to choose

The export is the only way back to a published version; see [Roll a layout back](../../roll-back-a-layout.md).

## Check

- An export exists from before the current version.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
