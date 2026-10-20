---
title: "Reuse last year's campaign"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1327
summary: Import last year's export as a copy (--strategy=copy), change the texts, map it.
keywords: reuse, campaign, import copy, strategy copy, template
type: help
---
# Reuse last year's campaign

This year's campaign starts from last year's layout.

*Life cycle: Maintain, example 28 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open the copy in the editor, change the Hero and lists, **Publish layout**, map it to the new page.

## On the command line

1. `./console exp:layouts import campaign-2026.zip --strategy=copy --dry-run`, then without `--dry-run`; the copy stays a draft without `--publish`.

## Which to choose

Copy, so last year's layout stays untouched.

## Check

- Two layouts exist; the old one is unchanged.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
