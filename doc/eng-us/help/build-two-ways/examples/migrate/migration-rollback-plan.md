---
title: "Plan the way back"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1532
summary: Exports of the layouts, backups of the INI files (exp:ini keeps them in var/backup/ini/) and the previous extension release.
keywords: rollback plan, backups, var/backup/ini, exports, migration
type: help
---
# Plan the way back

Any step of the migration can be undone.

*Life cycle: Migrate, example 33 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [Backups](#backups)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. An `exp:layouts export --all` from just before the migration.

## Backups

1. `exp:ini` writes a backup before each change to `var/backup/ini/<timestamp>/`; keep the previous release tag of your extension.

## Which to choose

Three backups, one per kind of change.

## Check

- Each backup can be found and has been read once.

## Related pages

- [Roll a layout back](../../roll-back-a-layout.md)
- [exp:ini for site builders](../../exp-ini-for-site-builders.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
