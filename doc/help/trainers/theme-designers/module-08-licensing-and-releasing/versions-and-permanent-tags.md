---
title: "Lesson 8.2: Versions, and why a pushed tag is permanent"
audience: trainer, designer, developer
category: training
order: 82
summary: Learners choose the next version number from what is already published, sort tags correctly (.10 after .9), bump every version field in a commit before the tag, make an annotated tag in their local repository, and explain why a pushed tag is never moved. 45 minutes.
keywords: theme design, version number, release, tag, annotated tag, permanent tag, Packagist, sort version refname, sort -V, only the last position counts, bump before tag, when a release goes wrong, keeping version fields in sync
type: help
---
# Lesson 8.2: Versions, and why a pushed tag is permanent

The moment a theme's tag is pushed, Packagist reads it once and users install it. Moving or re-cutting it afterwards
creates two different packages under one version number. This lesson teaches the release routine that avoids it:
check what is published, choose the number, write it everywhere, commit, then tag. Learners practice it in their local
repository, where nothing can go wrong.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 8: Licensing and releasing](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who publish themes |
| Prerequisites | [Lesson 7.4](../module-07-packaging-a-theme/a-theme-under-git.md): `training_theme` in a local Git repository |

## Learning objectives

After this lesson, learners can:

- explain why a pushed tag is never deleted, moved or re-cut, and what to do when a release goes wrong;
- list existing tags in the right order and choose the next number (after `.9` comes `.10`);
- write the version into every field and commit it before tagging;
- make an annotated tag and check it before it would be pushed.

## Trainer's notes

### How to present it

Sources: [A pushed tag is permanent](../../../designers/distribution/a-pushed-tag-is-permanent.md),
[After .9 comes .10](../../../designers/distribution/only-the-last-position-counts.md),
[Bumping the version before the tag](../../../designers/distribution/bumping-versions-before-the-tag.md) and
[When a release goes wrong](../../../designers/distribution/when-a-release-goes-wrong.md).

1. **Why permanent** (5 minutes). Packagist reads a tag once and caches the version it found; users put it in lock
   files and deploy it. Git lets you move a tag, but that does not reach those copies. A "fixed" tag gives two packages
   with one version number.
2. **The rules** (4 minutes). Never delete, move, re-point or re-cut a pushed tag; never replace a published release's
   assets; check what is published before choosing a number; verify before you push; if a bad version is out, leave it
   and publish the next one, saying in its notes what was wrong. Only the text of release notes may be corrected.
3. **Numbers** (4 minutes). Only the last position increments: `0.0.4.9` is followed by `0.0.4.10`. A plain sort by
   name puts `.10` before `.8`; use `git tag --sort=version:refname` or `sort -V`.
4. **The order of work** (4 minutes). Write the number into `ezinfo.php`, `extension.xml` (and `composer.json` if it
   has a `version`), commit ("Updated: Version 0.1.1"), then tag that commit. Never tag first.
5. **The checklist before pushing** (3 minutes). Clean working tree, the release commit on top, the version fields
   agree, the number is higher than every published one, the theme was installed and viewed from this commit, the tag
   message is right.

### Common questions

**I pushed a tag with a typo in its message. Can I fix it?** No. Leave it. The next release can say what was wrong.

**When does the first or second position move?** When the release's meaning calls for it (for example an incompatible
change), decided on purpose, never because the last position reached nine
([Choosing a version number](../../../designers/distribution/choosing-a-version-number.md)).

**Where do I check what is published?** The repository's tags and releases and the Packagist page; a tag can exist
without a release, and the local clone may not have fetched all tags
([Checking what is already published](../../../designers/distribution/checking-what-is-already-published.md)).

### Pitfalls

- **Practicing on a real repository.** The exercise is local; there is no remote, so nothing can be pushed.
- **Picking "the newest tag" with `tail -1` on an unsorted list.** Always sort by version.
- **Forgetting one version field.** The About page reads `extension.xml` first; a forgotten field is visible.

## Hands-on exercise

In `extension/training_theme` on your own installation, the local repository from lesson 7.4. Do not add a remote.

1. Check that the repository has no remote: `git remote -v` prints nothing.
2. To practice sorting, create five throwaway local tags on the current commit:
   `for v in 0.0.8 0.0.9 0.0.10 0.0.11 0.0.2; do git tag "practice-$v"; done`.
3. List them by name: `git tag -l 'practice-*' | tail -1`. Then by version: `git tag -l 'practice-*' --sort=version:refname | tail -1`.
4. Delete the practice tags (they were never pushed, so this is allowed):
   `git tag -l 'practice-*' | xargs git tag -d`.
5. The theme is at `0.1.0` (lesson 7.1). Choose the next number for a release that only changes the breadcrumb style.
6. Write that number into `ezinfo.php` and `extension.xml`. Check: `grep -n "Version\|<version>" ezinfo.php extension.xml`.
7. Run `php -l ezinfo.php` and `xmllint --noout extension.xml`.
8. Commit: `git commit -am "Updated: Version 0.1.1"` (with your number).
9. Make an annotated tag on that commit: `git tag -a v0.1.1 -m "Training theme 0.1.1"`.
10. Check it as you would before a push: `git status --short` (empty), `git log --oneline -1`, `git show v0.1.1 | head -5`.

## Expected result

- Step 3: the plain list ends with `practice-0.0.9`, the sorted list with `practice-0.0.11`.
- Step 5: the learners choose `0.1.1`.
- Steps 6 to 10: both files say `0.1.1`, the release commit is on top, and the annotated tag `v0.1.1` points at it with
  its message.
- The trainer asks: "You find a typo in the tag message after pushing. What do you do?" Expected answer: leave it, and
  mention it in the next release's notes.

## Quiz

1. What comes after version `2.3.9` for a bug-fix release?
   A. `2.4.0` B. `2.3.10` C. `3.0.0` D. `2.3.9.1`
2. Why must a pushed tag never be moved?
3. Which comes first: the commit that bumps the version, or the tag?
4. Which command lists tags in the right version order?
5. A release went out with a missing file. What do you do?

<details>
<summary>Answers</summary>

1. **B.** Only the last position counts
   ([After .9 comes .10](../../../designers/distribution/only-the-last-position-counts.md)).
2. Packagist and users already have the version it pointed at; a moved tag makes two packages with one number
   ([A pushed tag is permanent](../../../designers/distribution/a-pushed-tag-is-permanent.md)).
3. The commit that bumps the version; then the tag on that commit.
4. `git tag -l --sort=version:refname` (or a list piped through `sort -V`).
5. Leave the release as it is and publish the next version with the file, saying in its notes what was missing.

</details>

## Further reading

- [A pushed tag is permanent](../../../designers/distribution/a-pushed-tag-is-permanent.md).
- [Sorting tags correctly](../../../designers/distribution/sorting-tags-correctly.md).
- [Annotated tags](../../../designers/distribution/annotated-tags.md).
- [Keeping every version field in sync](../../../designers/extensions/keeping-version-fields-in-sync.md).

## Related pages

- [Module 8: Licensing and releasing](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
