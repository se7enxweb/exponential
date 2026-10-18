---
title: "A pushed tag is permanent"
audience: designer, developer
category: theme-distribution
order: 130
summary: Why a tag and the release built from it can never be deleted, moved or re-cut once pushed - Packagist caches what it read, users keep what they installed - and the rules that follow for every theme author.
keywords: tag immutability, permanent release, do not move tags, force push, Packagist cache, re-cut, published release, fix forward
type: help
---
# A pushed tag is permanent

This page is for every designer who publishes releases. Read it before your first tag.

## Why

When you push a tag of a public Composer package:

- **Packagist reads it once and caches the version it found.** Anything that resolved that version keeps the
  contents it saw.
- **Users install it**, mirror it, put it into lock files and deploy it.

Git will let you delete or move a tag, but that does not reach those copies. A "fixed" tag produces two different
packages wearing the same version number: worse than the original mistake, and impossible to diagnose from outside.

## The rules

1. **Never delete, move, re-point or re-cut a pushed tag.** Not for a typo in the message, not for a forgotten file,
   not for a stale archive.
2. **Never replace or edit the assets of a published GitHub release.** A corrected asset under the same version is
   the same problem as a moved tag.
3. **Check what is already published before choosing a number**
   ([Checking what is already published](checking-what-is-already-published.md)).
4. **Verify before you push.** Clean working tree, the theme renders, version fields agree, built files match their
   sources.
5. **If a bad version is published, leave it** and publish the next version with the fix, saying plainly in its
   notes what was wrong ([When a release goes wrong](when-a-release-goes-wrong.md)).

## A checklist before `git push origin v<version>`

- [ ] `git status --short` prints nothing.
- [ ] `git log --oneline -1` is the release commit.
- [ ] The version fields show the new number ([Keeping every version field in sync](../extensions/keeping-version-fields-in-sync.md)).
- [ ] The number is higher than every published tag and release.
- [ ] The theme was installed and viewed on a test installation from this commit.
- [ ] The tag message is right (`git show v<version> | head -5`).

## What may still be changed

The **text** of a GitHub release (its notes) can be corrected when it is wrong, as long as the tag and the assets stay.
Say in the notes that they were corrected.

## Related pages

- [Annotated tags](annotated-tags.md)
- [Release and upgrade planning](../../leadership/release-and-upgrade-planning.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
