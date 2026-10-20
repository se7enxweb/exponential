---
title: "Keep layouts in the project repository"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1506
summary: Commit exports next to the design extension; layouts.json in the archive can be compared between versions.
keywords: git, version control, layouts.json, export, diff
type: help
---
# Keep layouts in the project repository

See what changed in a layout between two releases.

*Life cycle: Migrate, example 7 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [In the repository](#in-the-repository)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Editors keep working in the editor; the export records their work.

## In the repository

1. Export after each release; unzip `layouts.json` and commit it, or commit the `.zip`.
2. Compare two versions of `layouts.json` with a JSON-aware diff.

## Which to choose

Templates and layouts both versioned; one history for the site.

## Check

- The repository has an export per release.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
