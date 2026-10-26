---
title: Version numbers in print
audience: press
category: press-media
order: 42
summary: How to write version numbers of Exponential and its family in published copy - full numbers without "v", major lines, releases versus development, extension and Velocity numbers whose last position keeps counting, sorting and comparing versions, and the mistakes to avoid.
keywords: version, version numbers, release number, 6.0.14, 6.0.15, Exponential 6, semantic versioning, tag, v prefix, Velocity version, extension version, sorting, 0.0.4.10
type: help
---
# Version numbers in print

This page is for reporters and editors who put version numbers in copy. Exponential's numbers follow a few simple
rules, and one of them surprises people: the last position keeps counting, so `0.0.4.10` comes after `0.0.4.9`.

## Contents

1. [Writing a version](#writing-a-version)
2. [Lines and releases](#lines-and-releases)
3. [Released or in development](#released-or-in-development)
4. [Velocity and extension numbers](#velocity-and-extension-numbers)
5. [Comparing and sorting versions](#comparing-and-sorting-versions)
6. [Common mistakes](#common-mistakes)

## Writing a version

| Write | Not |
|---|---|
| Exponential 6.0.14 | Exponential v6.0.14, Exponential 6.0.14.0 |
| version 6.0.14 | release v6.0.14 (the "v" belongs to the Git tag only) |
| Exponential 6 | Exponential 6.0 (unless you mean the 6.0.x line) |
| the 6.0.x line | the 6.x series (no 6.1 exists) |

Source: [Brand and naming rules](../business/brand-and-naming-rules.md#the-product-exponential).

## Lines and releases

| Term | Meaning |
|---|---|
| Exponential 6 | The major line |
| 6.0.x | All releases of the line, 6.0.0 to 6.0.14 as of 18 October 2026 |
| 6.0.14 | One release, a Git tag `v6.0.14` |
| 6.0.15 | The release in development |

All releases are listed on [The 6.0.x releases at a glance](the-6-0-x-releases-at-a-glance.md).

## Released or in development

A version is released when its Git tag is published. Until then:

- write "6.0.15, which is in development" or "the next release";
- do not give a date unless the project has published one;
- describe features as "in the 6.0.15 changelog", not as available in a release
  ([Covering 6.0.15 while it is in development](covering-6-0-15-while-in-development.md)).

Once published, a version is never changed: "a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next
release, not as a corrected copy of the old one" ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md);
[Published versions never change](published-versions-never-change.md)).

## Velocity and extension numbers

Velocity and the extensions have their own numbers, independent of the kernel's:

- Velocity uses four positions, for example `0.0.4.54` (the first version `composer.json` required, on 20 October 2026).
- "After `0.1.9` the next release is `0.1.10`; only the last position counts up unless you decide otherwise"
  ([Extensions, "Release it"](../../guides/extensions.md#7-release-it)).
- Extensions shipped inside the kernel carry the kernel's version; separate extensions carry their own.

Write the product with its number: "Velocity 0.0.4.54", "the expai extension 0.0.1.12". Do not mix them with the
kernel's number.

## Comparing and sorting versions

Compare position by position as numbers: `6.0.10` is newer than `6.0.9`. A plain alphabetical sort gets this wrong
and puts `6.0.10` before `6.0.2`. On a clone of the repository:

```bash
git tag -l 'v6.0.*' --sort=version:refname    # right order
git tag -l 'v6.0.*' | sort -V                  # right order
git tag -l 'v6.0.*' | tail -1                  # WRONG: alphabetical, may not be the newest
```

## Common mistakes

| Mistake | Correct |
|---|---|
| "Exponential 7" | No such version exists |
| "6.0.15 (released October 2026)" | 6.0.15 is in development |
| "Velocity 0.0.5" as the next after 0.0.4.9 | The next after 0.0.4.9 is 0.0.4.10 |
| "the latest version, 6.0.9" | Check the tags; 6.0.14 is the newest as of 18 October 2026 |
| "eZ Publish 6.0" for the 2024 release | "6.0.0, released in January 2024, while the software was still called eZ Publish" |

## Related pages

- [How version numbers work](how-version-numbers-work.md)
- [The 6.0.x releases at a glance](the-6-0-x-releases-at-a-glance.md)
- [Verifying a release yourself](verifying-a-release-yourself.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
