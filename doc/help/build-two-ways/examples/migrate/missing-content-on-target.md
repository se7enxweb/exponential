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
4. `./console exp:cache clear --tag=content` (a command-line import does not purge the page cache).

## Which to choose

`fail` first, to see the list; then import the content or choose `drop`.

## Check

- Manual collections have the expected items.

## Related pages

- [Export and import layouts](../../export-and-import-layouts.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
