---
title: "Plan moving layouts from staging to production"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1027
summary: Build on a staging installation, then export and import the layouts; content is matched by remote ID and path.
keywords: staging, production, export, import, remote ID, deployment, planning
type: help
---
# Plan moving layouts from staging to production

Layouts reach production without being built twice.

*Life cycle: Plan, example 28 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. The same through **Layouts > Export** and **Layouts > Import** in the administration.

## On the command line

1. On staging: `./console exp:layouts export --only=<identifiers> --output=release.zip`.
2. On production: `./console exp:layouts import release.zip --dry-run`, then with the chosen `--strategy` and `--publish`.
3. Then `./console exp:cache clear --tag=content`.

## Which to choose

Plan content first (same remote IDs on both, for example from the same package), then layouts.

## Check

- The dry run on production shows `new` or `changed`, not `conflicting`.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
