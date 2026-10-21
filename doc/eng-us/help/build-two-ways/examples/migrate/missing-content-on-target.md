---
title: "When the target lacks some content"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1503
summary: --unmapped=drop|keep|fail decides what happens to references to content the target does not have (default drop).
keywords: unmapped references, missing content, import, drop, keep, fail
type: help
---
# When the target lacks some content

Know what happens to a hand-picked article that does not exist on the target.

*Life cycle: Migrate, example 4 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. After an import with `drop`, check hand-picked lists for gaps.

## On the command line

1. `--unmapped=fail` stops the import and shows what is missing; `drop` (default) leaves those items out; `keep` keeps the reference as it is.
2. `./console exp:layouts import layouts.zip --dry-run --unmapped=fail` and read the States (`new`, `identical`, `changed`, `conflicting`).
3. Run it again without `--dry-run`, with the `--strategy` you chose and `--publish` if the result should be live at once.
4. Nothing to clear by hand: since explayouts_core 1.0.10 an import with `--publish` purges the page cache itself.

## Which to choose

`fail` first, to see the list; then import the content or choose `drop`.

## Check

- Manual collections have the expected items.

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
