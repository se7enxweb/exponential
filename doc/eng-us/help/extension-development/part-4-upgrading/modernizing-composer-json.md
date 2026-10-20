---
title: Modernizing composer.json
audience: developer, app-creator
category: extension-development
order: 120
summary: Give an old extension a composer.json that installs it into Exponential 6, or bring an old one up to date - PHP constraint, installer, license, authors and support - with the change expchangeclass made for PHP 8.0.
keywords: composer.json, Composer, Packagist, PHP constraint, ezpublish-legacy-extension, legacy installer, license, SPDX, authors, upgrade
type: help
---
# Modernizing composer.json

Part 4, chapter 12. Most extensions from before 2014 have no `composer.json` at all: they were downloaded as an
archive and unpacked into `extension/`. Those from 2014 to 2020 usually have one, written for an older installer
and an older PHP. This chapter brings either case up to date. The meaning of each field is explained in
[composer.json for an extension](../part-3-extension-developer/composer-json-for-extensions.md); here we go through
what typically needs to change.

## Case 1: no composer.json

Create one in the extension's root. The smallest useful file:

```json
{
    "name": "your-account/myoldext",
    "description": "My Old Extension: what it does, in one sentence.",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "require": {
        "php": "^8.0",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": {
        "ezpublish-legacy-extension-name": "myoldext"
    }
}
```

- `name`: your GitHub account or organization and the repository name, lower case. If you upgrade someone else's
  extension in a fork, use your own account: a Packagist name belongs to whoever registered it.
- `ezpublish-legacy-extension-name`: the folder name the extension had. Keep it exactly, because settings,
  templates and other extensions refer to it.
- `license`: the SPDX identifier of the license the files already carry. The license is not yours to choose when
  you upgrade someone else's work.

Then validate it:

```bash
cd extension/myoldext
composer validate --no-check-publish
```

## Case 2: an old composer.json

These are the fields that are usually out of date, with what to change.

| Old | New | Why |
|---|---|---|
| `"php": ">=5.3.3"` | `"php": "^8.0"` (or the oldest PHP you tested) | the old value claims support you never tested |
| `"ezsystems/ezpublish-legacy-installer": "*"` | `"se7enxweb/ezpublish-legacy-installer": "*"` or `"se7enxweb/exponential-legacy-installer": "*"` | the installers the Exponential kernel's own `composer.json` requires |
| `"ezsystems/ezpublish-legacy": "..."` | `"se7enxweb/exponential": "^6.0"`, or nothing | the kernel package of Exponential 6; require it only if your code needs a particular release |
| `"license": "GPL-2.0"` | `"license": "GPL-2.0-or-later"` or `"GPL-2.0-only"` | the plain `GPL-2.0` identifier is deprecated by SPDX; use what the file headers say ("or any later version" means `-or-later`) |
| `"minimum-stability": "dev"` | remove it | it only applies to the root package and misleads readers |
| `"version": "1.2"` | remove it, or keep it in step with every tag | Packagist takes versions from tags |
| no `authors` | every author, including the original ones | credit |
| no `support` | `issues` and `source` URLs | where users report problems |

## A real change: widening the PHP constraint

From se7enxweb/expchangeclass, commit 32196f0, "installs and runs on PHP 8.0, the oldest PHP Exponential 6 supports":

```text
before:
"php": "^8.1 || ^8.2 || ^8.3 || ^8.4"

after:
"php": "^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4"
```

The commit message explains how that was justified: the code was checked with PHPCompatibility for PHP 8.0, linted
and loaded with PHP 8.0.30, and served by PHP 8.0 on a full installation. Only then was the constraint widened.
Never widen a constraint you have not tested.

> **Note**
> In Composer, `^8.0` already means "8.0 or later, below 9". The list `^8.0 || ^8.1 || ...` is equivalent; some
> extensions list every minor version to show which ones were tested. Both forms install on PHP 8.5.

## Credit in composer.json

When you upgrade or fork someone else's extension, they stay in `authors`. se7enxweb/expchangeclass lists the
maintainer first and the original author of eZChangeclass second, with the role "Original author of eZChangeclass".
The `description` says the same thing in words: "A hardened fork of eZChangeclass". More in
[Attribution and copyright notices](../part-5-extend-fork-contribute/attribution-and-copyright-notices.md).

## Libraries the extension ships

Old extensions often ship third-party libraries in a `lib/` folder. When you add `composer.json`, you can move such
a library into `require` instead, if it is on Packagist and its current version still does what the extension needs.
Two cautions:

- An extension installed into `extension/` by the legacy installer uses the site's `vendor/` autoloader for its
  Composer dependencies. Test the extension in a site installed with Composer, not only in a copy.
- A library that went through several major versions since the extension was written may have changed its API.
  Read its changelog first.

If you keep the library in the extension, keep its license file next to it, and name it in `ezinfo.php` with an
`Includes` entry ([Modernizing ezinfo.php and extension.xml](modernizing-ezinfo-and-extension-xml.md)).

## Check it

```bash
composer validate --no-check-publish
```

Then install the extension from your repository into a test site, before you publish it on Packagist. The steps are
in [composer.json for an extension](../part-3-extension-developer/composer-json-for-extensions.md), section
"Installing it from a repository before Packagist".

## Related pages

- Previous: [Modernizing ezinfo.php and extension.xml](modernizing-ezinfo-and-extension-xml.md)
- Next: [CSP: moving inline scripts into files](csp-moving-inline-scripts-out.md)
- [Composer and Packagist](../part-7-releasing/composer-and-packagist.md)
- [The composer.json schema](https://getcomposer.org/doc/04-schema.md) on getcomposer.org
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
