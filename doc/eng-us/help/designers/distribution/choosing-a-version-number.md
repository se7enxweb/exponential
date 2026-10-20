---
title: "Choosing a version number"
audience: designer, developer
category: theme-distribution
order: 70
summary: How to pick the next version number of a theme - from what is already published, raising only the last position for normal releases, and moving a higher position only as a deliberate decision about what the release means.
keywords: version number, versioning, next version, release number, semantic versioning, major, minor, patch, last position
type: help
---
# Choosing a version number

This page is for designers about to release. A version number is a promise to your users about what changed. Choose
it with care: once a tag is pushed, the number is used up forever.

## The procedure

1. **Look at what is published**, not at your memory or your working copy
   ([Checking what is already published](checking-what-is-already-published.md)):

   ```bash
   git fetch --tags
   git tag -l 'v*' --sort=version:refname | tail -5
   gh release list --limit 10
   ```

2. **Take the highest published number** from both lists. A tag can exist without a release, and a release can
   exist for a tag your clone has not fetched.
3. **Raise the last position by one**: `1.0.23` becomes `1.0.24`; `0.1.9` becomes `0.1.10`
   ([After .9 comes .10](only-the-last-position-counts.md)).
4. **Move a higher position only on purpose**, when the release means something different:

   | Move | When | Example |
   |---|---|---|
   | The last position | Fixes, improvements, new templates, anything that keeps working for existing users | `1.0.23` to `1.0.24` |
   | A middle position | A deliberate step, for example a new minimum Exponential or PHP version, or a changed API for template authors | `1.4.x` to `1.5.0` |
   | The first position | A redesign or a break that needs users to act | `1.x` to `2.0.0` |

5. Write the number into every version field ([Keeping every version field in sync](../extensions/keeping-version-fields-in-sync.md)).

## What the published themes do

Read `gh release list` for a real pattern: `sevenx_themes_simple` counts `1.0.20`, `1.0.21` ... `1.0.24`;
`sevenx_themes_media` reached `1.5.70`; `cjw_themes_jumper` counts `0.1.2` to `0.1.6`. In each, only the last position
moves from one release to the next.

## Things to avoid

- Reusing a number, even one whose release was deleted: Packagist and users may already have it.
- Skipping numbers to "look bigger".
- Letting a release script pick "the newest tag" by plain name sorting ([Sorting tags correctly](sorting-tags-correctly.md)).

## Related pages

- [Bumping the version before the tag](bumping-versions-before-the-tag.md)
- [Breaking changes and the next major version](../lifecycle/breaking-changes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
