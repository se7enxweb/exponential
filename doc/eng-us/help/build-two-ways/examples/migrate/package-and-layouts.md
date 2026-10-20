---
title: "Site packages and layouts"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1517
summary: The site packages of this installation carry content but no layouts; install the layouts with exp:layouts import after the content.
keywords: packages, content packages, layouts, install, order, migration
type: help
---
# Site packages and layouts

A new installation from packages gets its layouts too.

*Life cycle: Migrate, example 18 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Check the mappings after the import.

## On the command line

1. Install the content packages first, then `exp:layouts import` the layouts (content found by remote ID and path), then **Template & content**.

## Which to choose

Content first, layouts second.

## Check

- The dry run finds the content references.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
