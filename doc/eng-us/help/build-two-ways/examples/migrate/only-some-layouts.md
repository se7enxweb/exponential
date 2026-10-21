---
title: "Move only some layouts"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1501
summary: --only with the layout identifiers; the shared layouts they link to come along.
keywords: export only, identifiers, shared layouts included, partial migration
type: help
---
# Move only some layouts

Move the new campaign layouts, nothing else.

*Life cycle: Migrate, example 2 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Or tick the layouts in **Layouts > Export**.

## On the command line

1. `./console exp:layouts export --only=spring_campaign --output=campaign.zip` (the shared layouts it links to are always included; add more with `--shared=`).
2. `./console exp:layouts import campaign.zip --dry-run` and read the States (`new`, `identical`, `changed`, `conflicting`).
3. Run it again without `--dry-run`, with the `--strategy` you chose and `--publish` if the result should be live at once.
4. Nothing to clear by hand: since explayouts_core 1.0.10 an import with `--publish` purges the page cache itself.

## Which to choose

Small exports are easier to review in the dry run.

## Check

- The dry run lists the campaign layout and Header / Footer.

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
