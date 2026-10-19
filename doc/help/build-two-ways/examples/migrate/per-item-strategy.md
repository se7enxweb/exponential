---
title: "Choose the strategy per layout"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1505
summary: --item=<key>=<strategy>,... sets skip, overwrite or copy per item, with the keys the dry run prints.
keywords: item strategy, --item, per layout, import
type: help
---
# Choose the strategy per layout

Overwrite the Prefooter but keep the local Header / Footer.

*Life cycle: Migrate, example 6 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

Not available in the editor.

## On the command line

1. Dry run first; it prints keys like `layout:d14c82715a0c5f1da1e6df4d0b77e638:published`.
2. `./console exp:layouts import release.zip --item=layout:d14c82715a0c5f1da1e6df4d0b77e638:published=overwrite --publish`.

## Which to choose

Per-item strategies make mixed releases safe.

## Check

- Only the chosen item changed (a second dry run shows it `identical`).

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
