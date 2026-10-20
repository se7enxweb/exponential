---
title: "Do the redesign on a copy"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1418
summary: Templates in a branch of your extension, layouts in a sandbox copy; then deploy the templates and import the layouts.
keywords: redesign, sandbox, branch, staging, export import
type: help
---
# Do the redesign on a copy

The live site keeps working while the new design is built.

*Life cycle: Redesign, example 19 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [In a branch](#in-a-branch)
3. [At go-live](#at-go-live)
4. [Which to choose](#which-to-choose)
5. [Check](#check)

## In the Layouts editor

1. Build the new layouts on the copy; export them (`exp:layouts export --only=...`).

## In a branch

1. Develop the templates in a branch; test on the copy.

## At go-live

1. Deploy the extension, import the layouts (`--strategy=overwrite --publish` or `copy`), switch mappings, clear Template & content.

## Which to choose

Both halves move together.

## Check

- The dry-run import on live shows the expected items.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
