---
title: "When a release goes wrong"
audience: designer, developer
category: theme-distribution
order: 160
summary: What to do when a published theme release is broken - leave it in place, fix forward with the next version, say plainly what was wrong, and tell users which version to move to.
keywords: broken release, fix forward, bad release, hotfix, next version, do not delete tag, release mistake
type: help
---
# When a release goes wrong

This page is for designers who discover a mistake in a release that is already published: a missing file, a broken
template, the wrong version in `ezinfo.php`.

## What not to do

- Do not delete, move or re-cut the tag.
- Do not replace files of the GitHub release.
- Do not reuse the version number.

Each of these leaves some users with one content and others with another under the same number
([A pushed tag is permanent](a-pushed-tag-is-permanent.md)).

## Step by step: fix forward

1. **Reproduce** the problem on a test installation with the published version.
2. **Fix it** on `main`, with a commit message that names the problem (`Fixed: ...`).
3. **Choose the next number** ([Choosing a version number](choosing-a-version-number.md)), bump, commit, tag, push.
4. **Write the notes plainly**:

   ```markdown
   ## Fixed: the event template missing from 1.0.25

   Release 1.0.25 did not contain `design/mytheme/templates/full/event.tpl`; event pages showed the standard
   template. This release adds it back. Update from 1.0.25 directly; nothing else changed.
   ```

5. **Tell users** where they look: the release, the project's news on
   [projects.exponential.earth](listing-on-projects-exponential-earth.md) or your announcement thread, and the issue
   that reported it.
6. **Close the issue** with a link to the new release.

## When the mistake is a security problem

Follow [Publishing a security release](../lifecycle/publishing-a-security-release.md): fix, release, then describe.

## Users who pinned the bad version

A user who wrote an exact version (`1.0.25`) does not get the fix automatically. Say in the notes which constraint
picks it up (`~1.0.25` or `^1.0`) ([Version constraints your users write](version-constraints-for-users.md)).

## Related pages

- [Writing release notes](writing-release-notes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
