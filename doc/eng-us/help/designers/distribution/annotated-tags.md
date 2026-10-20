---
title: "Annotated tags"
audience: designer, developer
category: theme-distribution
order: 120
summary: Tag releases with annotated tags (git tag -a) that carry a message, an author and a date; how to write the message, check the tag before pushing, and push only that tag.
keywords: annotated tag, git tag -a, tag message, lightweight tag, git show, push tag, release tag
type: help
---
# Annotated tags

This page is for designers creating a release tag. An **annotated** tag is an object of its own with a message, the
tagger and a date; a **lightweight** tag is only a name for a commit. Use annotated tags for releases.

The published 7x themes do: `git cat-file -t v0.1.6` in `cjw_themes_jumper` prints `tag`, and its message reads
"v0.1.6: the starter design without inline scripts".

## Step by step

1. Make sure you are on the release commit ([Bumping the version before the tag](bumping-versions-before-the-tag.md)):
   `git log --oneline -1`.
2. Create the tag with a message of the form `v<version>: <what it is about>`:

   ```bash
   git tag -a v1.0.25 -m "v1.0.25: the event pages in dark mode"
   ```

3. Check it:

   ```bash
   git cat-file -t v1.0.25        # tag
   git show v1.0.25 --stat | head -20
   ```

   Expected: `tag`, then the message, then the release commit with its files.
4. Push only this tag (never `git push --tags`, which pushes every local tag, including experiments):

   ```bash
   git push origin v1.0.25
   ```

## Writing the message

- Start with the version and a colon, then a short phrase about the release.
- Use the product name "Exponential", and no internal paths or private names.
- The same phrase makes a good release title ([GitHub releases](github-releases.md)).

## If you made a mistake before pushing

A tag that has **not** been pushed can be deleted and made again: `git tag -d v1.0.25`. Once pushed, it stays
([A pushed tag is permanent](a-pushed-tag-is-permanent.md)).

## Related pages

- [Writing release notes](writing-release-notes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
