---
title: "Breaking changes and the next major version"
audience: designer, developer
category: theme-lifecycle
order: 240
summary: When a theme change forces users to act - removed templates or settings, a raised minimum version, a redesign - group it, move a higher version position deliberately, write an upgrade guide, and keep the previous line alive for a while.
keywords: breaking changes, major version, upgrade guide, migration, minimum version, redesign, version position
type: help
---
# Breaking changes and the next major version

This page is for designers who must change something users depend on. A breaking change is one after which a user's
site does not work, or looks different, unless they act.

## Examples

| Breaking | Not breaking |
|---|---|
| Removing a template users override | Adding a template |
| Renaming a setting without reading the old name | Adding a setting with a default |
| Raising the minimum Exponential or PHP version | Supporting a newer version too |
| A redesign of the layout | A fix of a visual bug |

## Step by step

1. **Avoid it if you can**: keep the old way working ([A deprecation policy](deprecation-policy.md)).
2. **Group** breaking changes into one release rather than spreading them.
3. **Choose the version deliberately**: move a higher position (for example `1.x` to `2.0.0`), so users with a
   constraint such as `~1.0.24` do not receive it by accident ([Version constraints your users write](../distribution/version-constraints-for-users.md)).
4. **Write an upgrade guide** (`UPGRADE-2.0.md`): every change, what the user must do, in order.
5. **Keep the previous line** in security-only maintenance for a stated time ([A support policy](support-policy-for-your-theme.md)).
6. **Announce** before and at the release.

## Related pages

- [Choosing a version number](../distribution/choosing-a-version-number.md)
- [Declaring compatibility](declaring-compatibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
