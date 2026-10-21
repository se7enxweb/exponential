---
title: "Move all layouts to another installation"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1500
summary: exp:layouts export --all on the source, import with a dry run on the target; content is matched by remote ID and path.
keywords: migrate layouts, export all, import, another installation, remote ID
type: help
---
# Move all layouts to another installation

The layouts of one installation on a second one with the same content.

*Life cycle: Migrate, example 1 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. The same with **Layouts > Export** and **Layouts > Import** in the administration.

## On the command line

1. On the source: `./console exp:layouts export --all --output=all-layouts.zip`.
2. `./console exp:layouts import all-layouts.zip --dry-run` and read the States (`new`, `identical`, `changed`, `conflicting`).
3. Run it again without `--dry-run`, with the `--strategy` you chose and `--publish` if the result should be live at once.
4. Nothing to clear by hand: since explayouts_core 1.0.10 an import with `--publish` purges the page cache itself.

## Which to choose

The console for repeatable moves; the administration for a one-off.

## Check

- `exp:layouts list` on the target shows the layouts, mappings and links.

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
