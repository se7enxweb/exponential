---
title: "Rename layouts without breaking imports"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1522
summary: Imports match layouts by identifier; keep identifiers stable and change names freely.
keywords: identifier, layout name, import matching, migration
type: help
---
# Rename layouts without breaking imports

Rename "Spring campaign" to "Spring 2027" safely.

*Life cycle: Migrate, example 23 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Change the name with the pencil in the editor's top bar; leave the identifier.

## With templates

1. Scripts refer to identifiers (`--only=spring_campaign`).

## Which to choose

Names for people, identifiers for tools.

## Check

- A dry-run import of an older export shows `changed`, not `new`.

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
