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
3. Nothing to clear by hand: since explayouts_core 1.0.10 the import with `--publish` purges the page cache itself.

## Which to choose

Plan content first (same remote IDs on both, for example from the same package), then layouts.

## Check

- The dry run on production shows `new` or `changed`, not `conflicting`.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
