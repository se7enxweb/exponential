---
title: Extension bundles
audience: bundler, developer
category: bundling
order: 330
summary: How to ship several Exponential extensions as one bundle - a Composer meta package or a documented set, dependencies in extension.xml, loading order, version and license fields that agree, the secret check for exported packages, testing against each Exponential release, and documentation.
keywords: extension bundle, meta package, Composer, extension.xml, requires, extends, loading order, ActiveExtensions, ezinfo.php, version, license, secret check, test, bundle
type: help
---
# Extension bundles

This page is for developers and companies who ship a set of extensions that belong together: a shop suite, an
intranet kit, an SEO pack. The single-extension checklist is [Publishing an extension](../partners/publishing-an-extension.md);
this page covers the set.

## Contents

1. [Forms of an extension bundle](#forms-of-an-extension-bundle)
2. [Dependencies](#dependencies)
3. [Loading order](#loading-order)
4. [Metadata that agrees](#metadata-that-agrees)
5. [Packaging an extension as .ezpkg](#packaging-an-extension-as-ezpkg)
6. [Testing and maintaining](#testing-and-maintaining)

## Forms of an extension bundle

| Form | How | Good for |
|---|---|---|
| A Composer meta package | a package whose `composer.json` only `require`s your extensions at matching versions | sites installed with Composer, which is every current installation |
| A documented set | a README listing each package and version | small sets |
| A site package that activates them | a site package whose settings list the extensions | new sites built around the set ([Site packages in a bundle](site-packages-in-a-bundle.md)) |

Each extension remains its own Composer package with its own repository and tags.

## Dependencies

`extension.xml` declares dependencies between extensions:

```xml
<dependencies>
    <requires>
        <extension name="ezjscore" />
    </requires>
    <extends>
        <extension name="ezwt" />
    </extends>
</dependencies>
```

"`<requires>` names extensions that must be active; `<extends>` names extensions this one builds on"
([Extension metadata](../../specifications/6.0/extension-metadata.md)). Mirror the same dependencies in each
`composer.json` so Composer installs them.

## Loading order

The order of `ActiveExtensions` decides whose settings and templates win. Document the order your bundle needs and
test it; **Setup > Extensions** shows it ([Extension list](../../features/6.0/extension-list-and-downloads.md),
[Loading order and safe saving](../../features/6.0/extension-loading-order.md)).

## Metadata that agrees

For every extension in the set:

- `ezinfo.php` and `extension.xml` state the same `Version`, license, copyright and info URL; "`//autogentag//` and
  `dev-master` are not versions" ([Extension metadata](../../specifications/6.0/extension-metadata.md#release-rule));
- `composer.json` names the same license as an SPDX identifier;
- an extension with its own `share/filelist.md5` refreshes it in the same release, because the admin's file
  consistency check lists every stale line as a modified file ([Integrity checks](integrity-checks.md)).

## Packaging an extension as .ezpkg

Setup > Extensions and `ezpm.php add` can pack an extension as a package, but exporting is off by default and needs
`package.ini [ExtensionExportSettings] AllowedExtensions[]` and the policy `package/export_extension`. "An extension
that holds a secret value or key material is not packed" ([The secret check](../../bc/6.0/extension-package-secret-check.md)).
For a bundle, Composer packages from your repositories are the primary form; use `.ezpkg` where recipients install
through the package system.

## Testing and maintaining

- [ ] Install the whole set on a clean installation of each Exponential version you support, on each database you
      claim.
- [ ] Activate in the documented order; run `php bin/php/console exp:checkclasses`, which "loads every declared class
      and reports those PHP refuses to load" ([10.7](../../install/10-after-installing.md#107-class-autoloads)).
- [ ] Test against each new Exponential release ([Testing releases](../partners/testing-releases.md)).
- [ ] Release the set together, with one changelog naming each extension's version.

## Related pages

- [Publishing an extension as a partner](../partners/publishing-an-extension.md)
- [Extension and theme partners](../partners/extension-and-theme-partners.md)
- [Publishing extensions from a bundle](publishing-extensions-from-a-bundle.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
