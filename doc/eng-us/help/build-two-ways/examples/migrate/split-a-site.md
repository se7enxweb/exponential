---
title: "Split one installation into two sites"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1523
summary: Export the layouts of the site's subtree, import them on the new installation after its content.
keywords: split site, export, import, subtree, migration
type: help
---
# Split one installation into two sites

Bold Agency moves to its own installation.

*Life cycle: Migrate, example 24 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Remove the Bold Agency mappings on the old installation when the new one is live.

## On the command line

1. `./console exp:layouts export --only=<Bold Agency identifiers> --output=bold.zip` (their shared layouts come along).
2. On the new installation, after the content: dry run, import (with `--publish` it purges the page cache itself).

## Which to choose

Identifiers make the selection exact.

## Check

- The new installation shows the Bold Agency pages with their layouts.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
