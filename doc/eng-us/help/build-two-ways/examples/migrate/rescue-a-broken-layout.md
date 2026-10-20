---
title: "Rescue a layout broken by an import"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1525
summary: Import the previous export with overwrite and publish; clear Content.
keywords: rescue, broken layout, import, rollback, migration
type: help
---
# Rescue a layout broken by an import

Undo an import that went wrong.

*Life cycle: Migrate, example 26 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. If the import only made drafts (no `--publish`), **Discard** them in the editor.

## On the command line

1. `./console exp:layouts import before-import.zip --dry-run --strategy=overwrite --publish` and read the States (`new`, `identical`, `changed`, `conflicting`).
2. Run it again without `--dry-run`, with the `--strategy` you chose and `--publish` if the result should be live at once.
3. `./console exp:cache clear --tag=content` (a command-line import does not purge the page cache).

## Which to choose

Always export before importing.

## Check

- The layout is as before the import.

## Related pages

- [Roll a layout back](../../roll-back-a-layout.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
