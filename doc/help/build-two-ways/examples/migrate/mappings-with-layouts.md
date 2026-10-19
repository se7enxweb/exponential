---
title: "Move the mappings with the layouts"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1502
summary: --rules=none|selected|all decides which layout mappings travel; selected (the default) takes those of the chosen layouts.
keywords: mappings, rules, export rules, migration
type: help
---
# Move the mappings with the layouts

The campaign page gets its mapping on the target too.

*Life cycle: Migrate, example 3 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Check the mappings in **Layouts > Layout mappings** after the import.

## On the command line

1. `--rules=selected` (default) exports the mappings of the chosen layouts; `--rules=all` every mapping; `--rules=none` none.
2. Mappings refer to content (Location, Subtree targets) by node: the archive's `references.json` adds remote IDs and paths.

## Which to choose

Move mappings only when the target has the same content.

## Check

- The target's Layout mappings list the imported mapping.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Which layout does a page get?](../../layout-mappings.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
