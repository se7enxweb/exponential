---
title: Tags and releases are permanent
audience: developer, designer, app-creator
category: extension-development
order: 30
summary: Why a pushed tag and a published release can never be moved, replaced or re-cut - Packagist and every installation keep what they first saw - and what to do instead when a release went out wrong.
keywords: tag, release, permanent, immutable, Packagist, re-tag, force push, mistake, next version, release notes
type: help
---
# Tags and releases are permanent

Part 7, chapter 3. This is the most important rule of releasing, and the one with no exceptions: **a tag that has
been pushed, and a release built from it, is never changed.** Not to fix a typo, not to add a forgotten file, not to
rebuild an archive. The only correction for a bad version is the next version.

## Why

Git lets you delete a tag and push a new one with the same name (`git push --force`). The trouble is everything that
already saw the old one:

- **Packagist** reads a tag once and keeps the version it found.
- **Every installation** that ran `composer update` in between has the old files, and its `composer.lock` records them.
- **Every clone** of your repository keeps its old tag; Git does not replace a tag on fetch.
- **Mirrors and caches** keep the old archive.

After a moved tag, two different sets of files carry the same version number. A user reporting a bug in "1.2.3"
might have either. That is worse than the original mistake, and impossible to diagnose from the outside.

The same goes for a GitHub release: replacing an attached archive with a corrected one has the same effect as moving
the tag.

## What to do instead

When a release is wrong:

1. **Leave it.** Do not delete the tag or the release.
2. **Fix the problem** in a new commit.
3. **Release the next version**, for example 1.2.4 after a broken 1.2.3.
4. **Say what was wrong** in the new version's notes: "1.2.3 left out the translation files; this release adds them.
   Update from 1.2.3." Users with ranges such as `~1.2.3` get the fix with their next update.

A gap in the history (a version that was skipped or withdrawn) helps nobody; a clear note does.

## Get it right before you push

Because a tag cannot be corrected, check before you push it. A tag is cheap to get right and impossible to fix:

- [ ] The working tree is clean (`git status` shows nothing).
- [ ] The tests pass ([Testing before a release](testing-before-a-release.md)).
- [ ] The version in `ezinfo.php`, `extension.xml` and (if present) `composer.json` is the version you are about to tag,
      in the commit the tag points at ([Bumping ezinfo.php and extension.xml](bumping-ezinfo-and-extension-xml.md)).
- [ ] The number is free: checked against tags and releases ([Choosing the next version number](choosing-the-next-version.md)).
- [ ] Generated files (a built JavaScript bundle, a minified stylesheet, `share/filelist.md5`) match their sources.

Then:

```bash
git tag -a v1.2.4 -m "Version 1.2.4"
git push origin v1.2.4
```

An annotated tag (`-a`) records who tagged and when. Push the tag by name, not with `--tags`, so you never push a
local experiment by accident.

## Local tags are free

Until you push, a tag is only in your clone. If you tagged the wrong commit and have not pushed:

```bash
git tag -d v1.2.4
```

and tag again. The moment it is pushed, it is permanent.

## Related pages

- Previous: [Choosing the next version number](choosing-the-next-version.md)
- Next: [Bumping ezinfo.php and extension.xml](bumping-ezinfo-and-extension-xml.md)
- [The release checklist](the-release-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
