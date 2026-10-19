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
4. `./console exp:cache clear --tag=content` (a command-line import does not purge the page cache).

## Which to choose

Small exports are easier to review in the dry run.

## Check

- The dry run lists the campaign layout and Header / Footer.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
