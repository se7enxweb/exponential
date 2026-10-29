---
title: Versions, tags and pinning in a bundle
audience: bundler, developer
category: bundling
order: 70
summary: How Exponential and its extensions are versioned, why a published release tag never changes and what that means for a bundle, how to find the newest release correctly (version sort, not name sort), how the tilde constraints in composer.json behave, and how to version your own bundle.
keywords: version, tag, release, pin, immutable, Packagist, sort -V, version:refname, tilde, ~, branch alias, dev-main, 6.0.x-dev, bundle version
type: help
---
# Versions, tags and pinning in a bundle

This page is for bundlers who decide which versions go into a bundle and how the bundle itself is numbered.

## Contents

1. [How Exponential is versioned](#how-exponential-is-versioned)
2. [A published release never changes](#a-published-release-never-changes)
3. [Find the newest release correctly](#find-the-newest-release-correctly)
4. [Tilde constraints](#tilde-constraints)
5. [Pinning in a bundle](#pinning-in-a-bundle)
6. [Versioning your own bundle](#versioning-your-own-bundle)

## How Exponential is versioned

| What | Name | Gives you |
|---|---|---|
| Release tags | `v6.0.0` to `v6.0.14` (the 6.0 line) | an exact, permanent release |
| Default branch | `main` | the current development of the 6.0 line, today 6.0.15 |
| Release branches | `6.0.7` to `6.0.14` | the state of each release line |

Source: [3.1 Versions, tags and branches](../../install/03-getting-the-code.md#31-versions-tags-and-branches). `main`
identifies itself as `6.0.15stable` (`lib/version.php`), and `composer.json` gives it the branch alias `6.0.x-dev`, so
packages that require `se7enxweb/exponential` with a constraint such as `^6.0.14` still resolve against it.

Every extension is its own Composer package with its own tags; `ezinfo.php` and `extension.xml` of a release carry
that release's number ([Extension metadata](../../specifications/6.0/extension-metadata.md#release-rule)).

## A published release never changes

"Once published, a tag and the package version Packagist built from it are permanent; a fix always arrives as the next
version" ([3.1](../../install/03-getting-the-code.md#31-versions-tags-and-branches)). `SECURITY.md` says the same for
security fixes: "a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next release, not as a
corrected copy of the old one".

For a bundle that means:

- A pinned tag gives you the same files every time. That is what makes reproducible bundles possible.
- A fix you need is in the **next** version. Plan to re-release your bundle, not to wait for a corrected tag.
- The same rule applies to your own bundle: never move, delete or re-cut a tag you have published. If a published
  bundle is wrong, publish the next version and say in its notes what was wrong with the previous one.

## Find the newest release correctly

```bash
git ls-remote --tags https://github.com/se7enxweb/exponential.git 'v6*'
git tag -l 'v*' --sort=version:refname | tail -5      # inside a clone, after git fetch --tags
```

"Always sort by version (`--sort=version:refname` or `sort -V`), never by name: by name `v6.0.10` sorts before
`v6.0.8`, and a script that takes the last line of a name sort picks the wrong release." Check the release notes on
<https://github.com/se7enxweb/exponential/releases> as well, because a tag can exist before its notes do.

## Tilde constraints

The root `composer.json` pins extensions with `~`, for example `"se7enxweb/ezoracle": "~2.3.8"`. "The version
constraints use `~`, which lets the last position float: `~6.0.8` accepts `6.0.9` and `6.0.10` but not `6.1.0`"
([Default extension distribution](../../features/6.0/default-extension-distribution.md#how-it-works)).

The last position keeps counting: after `.9` comes `.10`, and a four-part version such as `~1.1.6.6`
(`bcgooglesitemaps`) floats its fourth position the same way.

## Pinning in a bundle

| Layer | Pin with | Why |
|---|---|---|
| The kernel | an exact tag (`v6.0.14`) or a recorded commit of `main` | the base must be identical in every build |
| Extensions | the kernel's `~` constraints plus **your `composer.lock`** | the constraint says what may come; the lock says what came |
| Your own extensions | exact tags of your repositories | you control their releases |
| Site packages | the package version in `package.xml` and the file you ship | see [Site packages in a bundle](site-packages-in-a-bundle.md) |
| Runtime | image digests, OS package versions | so a rebuild does not change PHP under you |

## Versioning your own bundle

- Give the bundle its own version, independent of Exponential's, and state the Exponential version inside it in the
  release notes ("Example Bundle 3.2, based on Exponential 6.0.14").
- Increase only the last position for fixes; move a higher position only as a deliberate decision about what the
  release means.
- Write the version into the files of the bundle in the commit you tag, never after the tag.
- Keep a changelog with the categories Added, Updated, Removed, Renamed so recipients see what changed.

## Related pages

- [Lock files and reproducible builds](lock-files-and-reproducible-builds.md)
- [Composer-based bundles](composer-based-bundles.md)
- [Updates and upgrade paths](updates-and-upgrade-paths.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
