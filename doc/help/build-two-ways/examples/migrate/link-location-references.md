---
title: "Update stored block links to remote IDs"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1511
summary: ext:explayouts:updatelinklocationreferences converts block links from ibexa-location://<id> to exp-remote-location://<remote id>.
keywords: block links, location references, remote ID, upgrade, migration
type: help
---
# Update stored block links to remote IDs

Links in blocks survive moving to another installation.

*Life cycle: Migrate, example 12 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Links typed as addresses are not affected.

## On the command line

1. `./console ext:explayouts:updatelinklocationreferences --allow-root-user` (its help: *Converts the block links stored as ibexa-location://<id> to exp-remote-location://<remote id>*).

## Which to choose

Run before exporting layouts to another installation.

## Check

- Links in blocks still open the right pages.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
