---
title: "After .9 comes .10: only the last position counts"
audience: designer, developer
category: theme-distribution
order: 80
summary: A version at .9 goes to .10, not to the next higher position - why that is safe for Composer, Packagist and GitHub, and why positions above the last move only by decision.
keywords: version, .9, .10, last position, increment, Packagist ordering, composer, tilde, version_compare
type: help
---
# After .9 comes .10: only the last position counts

This page is for designers who reach a version ending in `.9` and wonder whether the next one is `.10` or a jump to the
next higher position.

## The rule

**Only the last position increments. `0.0.4.9` is followed by `0.0.4.10`, then `0.0.4.11`, never `0.0.5.0`.**

The last position is a whole number and keeps counting. There is no running out of digits. Moving a position above
it is a decision about what the release means ([Choosing a version number](choosing-a-version-number.md)), never a
consequence of reaching nine.

## Why it is safe

| Tool | How it orders `x.10` after `x.9` |
|---|---|
| Composer and Packagist | They normalize versions and compare each position as a number |
| GitHub Releases | Ordered by the date of the tag, not by its name |
| `git tag --sort=version:refname`, `sort -V` | Compare positions as numbers |
| A constraint `~1.0.9` | Means `>=1.0.9 <1.1.0`, so `1.0.10` is inside it |

The Exponential project checked this on its own releases: a `.10` release after a `.9` was ordered after it on both
Packagist and GitHub.

## The one trap

A **plain sort by name** puts `.10` before `.8`:

```bash
git tag -l 'v1.0.*' | tail -1                   # can print v1.0.9    wrong
git tag -l 'v1.0.*' --sort=version:refname       # ends with v1.0.10  right
git tag -l 'v1.0.*' | sort -V | tail -1          # v1.0.10            right
```

Any script of yours that picks "the newest tag" needs `--sort=version:refname` or `sort -V`
([Sorting tags correctly](sorting-tags-correctly.md)).

## Step by step at a .9

1. Confirm the highest published version is `x.y.9` (both tags and releases).
2. Write `x.y.10` into the version fields.
3. Commit, tag `vx.y.10`, push, release.
4. Check Packagist: the new version is listed first.

## Related pages

- [Version constraints your users write](version-constraints-for-users.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
