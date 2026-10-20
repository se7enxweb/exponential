---
title: "Merge layouts from two installations"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1524
summary: Import the second site's layouts with --strategy=copy where identifiers clash, then rename and remap.
keywords: merge sites, import copy, identifiers, clash, migration
type: help
---
# Merge layouts from two installations

Two sites on one installation.

*Life cycle: Migrate, example 25 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Rename the copies, link their zones to the right shared layouts, add mappings with Subtree targets for the new site.

## On the command line

1. Dry run: `conflicting` or `changed` items share identifiers; import those with `--item=<key>=copy`.

## Which to choose

Copy on clash; never overwrite another site's layouts.

## Check

- Both sites render their own layouts.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
