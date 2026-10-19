---
title: composer.json for an extension
audience: developer, app-creator
category: extension-development
order: 60
summary: The composer.json that installs an extension into extension/<name>: package type, installer, extension name, PHP and kernel requirements, license, authors, with two real examples.
keywords: composer.json, composer, packagist, ezpublish-legacy-extension, ezpublish-legacy-extension-name, installer, require, php version, license, authors, support, package name
type: help
---
# composer.json for an extension

Part 3, chapter 6. With a `composer.json`, your extension can be installed with `composer require` and published on
Packagist. This chapter explains the fields that matter for an extension, using two real files.

## A modern example

`se7enxweb/expqueue`'s `composer.json` (shortened):

```json
{
    "name": "se7enxweb/expqueue",
    "description": "The Exponential job queue: a generic, database-backed job queue ...",
    "type": "ezpublish-legacy-extension",
    "homepage": "https://github.com/se7enxweb/expqueue",
    "license": "GPL-2.0-or-later",
    "authors": [
        { "name": "7x", "homepage": "https://se7enx.com", "role": "Author" }
    ],
    "support": {
        "issues": "https://github.com/se7enxweb/expqueue/issues",
        "source": "https://github.com/se7enxweb/expqueue"
    },
    "require": {
        "php": ">=8.0",
        "ext-json": "*",
        "ext-mbstring": "*",
        "se7enxweb/exponential": "^6.0.14",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": {
        "ezpublish-legacy-extension-name": "expqueue"
    }
}
```

## The fields

| Field | What to write | Why |
|---|---|---|
| `name` | `<vendor>/<package>`, lower case | the Packagist name; usually the repository name |
| `type` | `ezpublish-legacy-extension` | tells the installer to put the package in `extension/` |
| `extra.ezpublish-legacy-extension-name` | the folder name | the installer names the folder after it; without it the package name is used |
| `require.php` | the PHP versions you tested | `>=8.0`, or a list such as `^8.0 \|\| ^8.1 \|\| ^8.2 \|\| ^8.3 \|\| ^8.4` |
| `require` of a legacy installer | `se7enxweb/ezpublish-legacy-installer` or `se7enxweb/exponential-legacy-installer` | the Composer plugin that knows the extension type (the kernel's `composer.json` requires both) |
| `require.se7enxweb/exponential` | the kernel versions your code needs, when it uses new kernel APIs | Composer refuses an installation with an older kernel |
| `require.ext-*` | PHP extensions you call | a clear error instead of a fatal at run time |
| `license` | an SPDX identifier: `GPL-2.0-or-later` | Packagist and tools read it |
| `authors` | everyone who wrote it, including original authors | credit; see below |
| `support` | issues and source links | where users report problems |
| `suggest` | optional packages with a reason | shown by Composer on install |

Do not put `version` into `composer.json` unless you keep it in step with every tag: Packagist takes versions from
tags. If you keep one, bump it with `ezinfo.php` and `extension.xml`
(Bumping ezinfo.php and extension.xml).

## Crediting the original author

`se7enxweb/expchangeclass` is a fork; its `composer.json` keeps the original author:

```json
"authors": [
    { "name": "7x", "homepage": "https://se7enx.com" },
    { "name": "<original author>", "role": "Original author of eZChangeclass" }
]
```

(The real file names the person; this page leaves names of individuals out.) Do the same when you fork:
Attribution and copyright notices.

## Check it

```bash
composer validate extension/mysite_hello/composer.json
```

`composer validate` reports missing or invalid fields; fix every error, and read the warnings.

## Installing it from a repository before Packagist

While the package is not on Packagist, a project can install it from Git by adding a repository to its own
`composer.json`:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/your-organization/mysite_hello" }
]
```

then `composer require your-organization/mysite_hello:dev-main`. Publishing on Packagist is
Composer and Packagist.

> **Warning**
> Never run `composer update` on a live site to try a new extension. Try it on your development installation, commit
> `composer.lock`, and deploy that.

## Related pages

- Previous: [Extension load order](extension-load-order.md)
- Next: [Settings in an extension](settings-in-an-extension.md)
- [Finding ideas on Packagist](../part-1-from-user-to-developer/finding-ideas-on-packagist.md)
- [getcomposer.org: the composer.json schema](https://getcomposer.org/doc/04-schema.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
