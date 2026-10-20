---
title: "Take a baseline export at launch"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1212
summary: Export every layout, shared layout and mapping on launch day, to compare and restore later.
keywords: baseline, export, launch, backup, exp:layouts export --all
type: help
---
# Take a baseline export at launch

A known-good state of all layouts on file.

*Life cycle: Launch, example 13 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Or **Layouts > Export** in the administration.

## On the command line

1. `./console exp:layouts export --all --output=layouts-launch.zip`.

## Which to choose

The console for automation; the administration for a one-off.

## Check

- The file's `manifest.json` counts match `exp:layouts list`.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
