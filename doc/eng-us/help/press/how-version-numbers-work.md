---
title: How version numbers work
audience: press
category: press-media
order: 103
summary: What the numbers in Exponential versions mean, why 6.0.10 comes after 6.0.9, why the last position keeps counting (0.0.4.9 then 0.0.4.10), how tags, branches and Composer constraints relate, and how to sort versions correctly.
keywords: version, version number, tag, branch, 6.0.x, sorting, sort -V, version:refname, Composer, tilde, dev-main, Velocity, extension versions
type: help
---
# How version numbers work

This page is for reporters, editors and creators who need to write version numbers correctly. Exponential and the
packages around it use numbers whose last position keeps counting upward, which surprises people used to other
schemes. Getting the order wrong is one of the easiest mistakes to make in release coverage.

## Contents

1. [The parts of a version](#the-parts-of-a-version)
2. [The last position keeps counting](#the-last-position-keeps-counting)
3. [Tags, branches and the development line](#tags-branches-and-the-development-line)
4. [Sorting versions correctly](#sorting-versions-correctly)
5. [Composer constraints in one table](#composer-constraints-in-one-table)

## The parts of a version

| Example | Where you see it | How to write it in text |
|---|---|---|
| `v6.0.14` | The Git tag of a release | "Exponential 6.0.14" (no `v` in running text) |
| `6.0.14` | Changelogs, Packagist | "Exponential 6.0.14" |
| `6.0.15stable` | `lib/version.php` on the `main` branch | "the 6.0.15 development line", never "6.0.15 stable" |
| `dev-main`, `6.0.x-dev` | Composer names for the `main` branch | "the development branch" |
| `v0.0.4.50` | A Velocity release tag | "Exponential Velocity 0.0.4.50" |

The rule "Versions have no `v` in running text; the `v` belongs to the Git tag" is from the
[Brand and naming rules](../business/brand-and-naming-rules.md#the-product-exponential). The `6.0.15stable` and
`6.0.x-dev` details are from [Getting the code](../../install/03-getting-the-code.md#31-versions-tags-and-branches).

## The last position keeps counting

Exponential versions do not roll over at nine. After 6.0.9 came 6.0.10 (28 August 2025), not 6.1.0
([release overview](../../release-overview)). The same holds for extensions and for Velocity:

- "After `0.1.9` the next release is `0.1.10`; only the last position counts up unless you decide otherwise"
  ([Extensions guide, Release it](../../guides/extensions.md#7-release-it)).
- "The last position of a Velocity version counts on past 9 (`0.0.4.9`, `0.0.4.10`, ...)"
  ([Getting the code, the Velocity package](../../install/03-getting-the-code.md#34-the-velocity-package)).
- "Versions count in the last position: `0.0.4.9` is followed by `0.0.4.10`, never `0.0.5.0`"
  ([Velocity release notes](../../../changelogs/extensions/exponential-velocity.md)).

So do not read a jump in the last position as a small or large release. The number says the order, not the size.
What a release contains is in its changelog ([Reading a changelog](reading-a-changelog.md)).

## Tags, branches and the development line

| Name | What it is | Source |
|---|---|---|
| Release tags `v6.0.0` to `v6.0.14` | Exact, permanent releases of the 6.0 line | [Getting the code](../../install/03-getting-the-code.md#31-versions-tags-and-branches) |
| Older tags such as `4.4.0` | Lines from the eZ Publish years, tagged without `v` | Same page |
| Branch `main` | "the current development of the 6.0 line, today 6.0.15" | Same page |
| Release branches `6.0.7` to `6.0.14` | "the state of each release line" | Same page |

Only a tag is a release. A branch moves every day; a tag never does
([Published versions never change](published-versions-never-change.md)).

## Sorting versions correctly

A plain alphabetical sort puts `v6.0.10` before `v6.0.8`, so a list sorted by name shows the wrong newest version.
The documentation warns: "Always sort by version (`--sort=version:refname` or `sort -V`), never by name"
([Getting the code](../../install/03-getting-the-code.md#31-versions-tags-and-branches)).

```bash
git tag -l 'v6.0.*'                              # by name: v6.0.10 appears before v6.0.8 - misleading
git tag -l 'v6.0.*' --sort=version:refname       # by version: v6.0.14 is last - correct
```

The same applies to your own tables: order rows by release date or by version, never alphabetically.

## Composer constraints in one table

You will meet these in `composer.json` and in changelogs that list required packages.

| Constraint | Means | Example and source |
|---|---|---|
| `~0.0.4.55` | `>=0.0.4.55 <0.0.5.0`, so every later 0.0.4.x | Velocity, [Getting the code](../../install/03-getting-the-code.md#34-the-velocity-package) |
| `~2.4.11` | `>=2.4.11 <2.5.0` | eztags, [Getting the code](../../install/03-getting-the-code.md#35-extensions-are-composer-packages) |
| `^8.0` | PHP 8.0 or any later 8.x | The 6.0.15 line's PHP requirement, same page, section 3.1 |

When a changelog says a package "requires `~1.4.39`", write "requires version 1.4.39 or a later 1.4.x", not "uses
version 1.4.39".

## Related pages

- [Naming rules quick card](../advocates/naming-rules-quick-card.md)
- [Published versions never change](published-versions-never-change.md)
- [Verifying a release yourself](verifying-a-release-yourself.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
