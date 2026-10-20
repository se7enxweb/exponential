---
title: "Use exp:layouts instead of the old helper scripts"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1512
summary: Several ext:explayouts:* helpers (block_list, layout_info, layout_copy, layout_list, rule_list, share_create, collection_list) stopped with a class-not-found error; exp:layouts covers listing and transfer.
keywords: helper scripts, exp:layouts, layout_copy, migration, known issues
type: help
---
# Use exp:layouts instead of the old helper scripts

Scripts written against the old helpers keep working.

*Life cycle: Migrate, example 13 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Copying a layout by hand in the editor is building it again; the import copy is faster.

## On the command line

1. Listing: `exp:layouts list`. Details: `exp:layouts export --only=<identifier>` and read `layouts.json`. Copying: export and `import --strategy=copy`.

## Which to choose

Move scripts to `exp:layouts` now.

## Check

- The scripts run without the old helpers.

## Related pages

- [Known editor behaviors](../../known-editor-behaviors.md)
- [Export and import layouts](../../export-and-import-layouts.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
