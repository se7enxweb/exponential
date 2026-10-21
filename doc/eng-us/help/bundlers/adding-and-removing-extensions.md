---
title: Adding and removing extensions in a bundle
audience: bundler, developer
category: bundling
order: 100
summary: How a bundle adds an extension (Composer, autoloads, caches, ActiveExtensions or the site package), how it leaves a required one out, the loading order, extension exports and their secret check, and a checklist for every extension you put in.
keywords: extension, add extension, remove extension, ActiveExtensions, composer require, composer remove, autoload, loading order, extension export, secret check, AllowedExtensions, bundle
type: help
---
# Adding and removing extensions in a bundle

This page is for bundlers who change the set of extensions Exponential ships with. The base procedure is on
[The default extension distribution](../../features/6.0/default-extension-distribution.md); this page adds what a
bundle has to think about.

## Contents

1. [Add an extension](#add-an-extension)
2. [Activate it](#activate-it)
3. [Leave one out](#leave-one-out)
4. [Loading order](#loading-order)
5. [Taking an extension out of an installation as a package](#taking-an-extension-out-of-an-installation-as-a-package)
6. [Checklist for every extension you add](#checklist-for-every-extension-you-add)

## Add an extension

```bash
composer require se7enxweb/ezflow
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

Source: [Add an extension](../../features/6.0/default-extension-distribution.md#add-an-extension). Use the package
and version your bundle really needs; the example is the one on that page.

## Activate it

Two places can activate an extension:

| Where | When to use it |
|---|---|
| Your site package's settings (it writes `ActiveExtensions[]` during the install) | the extension belongs to the site you install; "An installation made from a site package activates the extensions of its package for you" |
| `settings/override/site.ini.append.php` | the extension belongs to every site the bundle installs |

```ini
[ExtensionSettings]
ActiveExtensions[]=ezflow
```

`settings/override/` is written by the installer and holds the database credentials afterwards; a bundle that ships
settings must ship them as part of the site package or apply them after the install, not ship a filled
`settings/override/` ([Security defaults of a bundle](security-defaults-of-a-bundle.md)).

## Leave one out

1. Remove it from `ActiveExtensions` and clear the caches.
2. Run `composer remove se7enxweb/<name>`.

"If you installed with Git checkouts of the extensions instead of Composer, do not run `composer update`: it replaces
the checkouts" ([Leave one out](../../features/6.0/default-extension-distribution.md#leave-one-out)).

Before you leave out a **required** extension, check that nothing else needs it: `extension.xml` names dependencies in
`<requires>` ([Extension metadata](../../specifications/6.0/extension-metadata.md)), and site packages may activate
it. Record the reason in your release notes; recipients will ask.

## Loading order

The order of `ActiveExtensions` decides which extension's settings and templates win. **Setup > Extensions** shows the
loading order by default and applies changes after a review
([Extension list](../../features/6.0/extension-list-and-downloads.md), [The Extensions page](../../guides/extensions-page.md)).
Put your theme and override extensions where they are meant to win, and test the order you ship.

## Taking an extension out of an installation as a package

Setup > Extensions can download an extension as `tar.gz`, `.zip`, `tar.bz2` or `.ezpkg`, and `ezpm.php add` can pack
one. Exporting "is now off by default": the extension must be listed in `package.ini [ExtensionExportSettings]
AllowedExtensions[]` (or `*`), and the role needs the policy `package/export_extension`. "An extension that holds a
secret value or key material is not packed" ([Extension list](../../features/6.0/extension-list-and-downloads.md),
[The secret check](../../bc/6.0/extension-package-secret-check.md)). For a bundle, prefer building from your
repositories over exporting from a running site.

## Checklist for every extension you add

- [ ] Its license is known and compatible with distributing it next to GPL code
      ([Third-party components](third-party-components-and-their-licenses.md)).
- [ ] It states `Version`, `License`, `Copyright` and `Info_url` in `ezinfo.php` and `extension.xml`, so the
      **Info > About** page shows it correctly ([About and Copyright pages](../../features/6.0/about-and-package-pages.md)).
- [ ] It runs on every PHP version your bundle supports.
- [ ] It is pinned (tag, lock file).
- [ ] You know who maintains it and where its issues go ([Support boundaries](support-boundaries.md)).
- [ ] It holds no secrets, keys or customer data.

## Related pages

- [Exponential 6 as a base](exponential-6-as-a-base.md)
- [Extension bundles](extension-bundles.md)
- [Chapter 10.13 Extension management](../../install/10-after-installing.md#1013-extension-management)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
