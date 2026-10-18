---
title: "Sorting tags correctly"
audience: designer, developer
category: theme-distribution
order: 90
summary: List the tags of a theme in version order, not name order - git tag --sort=version:refname, sort -V and git describe - with the mistake a plain sort makes and how to fix release scripts.
keywords: git tag, sort, version:refname, sort -V, git describe, newest tag, release script, lexical sort
type: help
---
# Sorting tags correctly

This page is for designers and developers who list tags by hand or in scripts.

## The problem

Sorted as text, `v1.0.10` comes before `v1.0.8`, because the character `1` sorts before `8`. A script that takes the
last line of a plain `git tag -l` then believes `v1.0.9` is the newest release, and the next release gets a number
that is already taken.

## The right commands

| Command | Gives |
|---|---|
| `git tag -l 'v*' --sort=version:refname` | Every tag in version order, oldest first |
| `git tag -l 'v*' --sort=-version:refname \| head -1` | The highest version |
| `git tag -l 'v*' \| sort -V \| tail -1` | The highest version, with GNU sort |
| `git describe --tags --abbrev=0` | The newest tag reachable from the current commit (follows history, not names) |

Show five, not one, so a surprise is visible: `git tag -l 'v*' --sort=version:refname | tail -5`.

## Repositories that tag without "v"

Some repositories tag `4.1.5` instead of `v4.1.5`. Use a pattern that matches what the repository really uses, or
none: `git tag -l --sort=version:refname | tail -5`.

## Step by step: fix a release script

1. Search the script for `git tag` and `tail -1`.
2. Add `--sort=version:refname` (or pipe through `sort -V`).
3. Test it against a repository that has a `.9` and a `.10` tag.
4. Also compare with `gh release list`: a release can exist for a tag your clone has not fetched; run
   `git fetch --tags` first.

## Related pages

- [After .9 comes .10](only-the-last-position-counts.md)
- [Checking what is already published](checking-what-is-already-published.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
