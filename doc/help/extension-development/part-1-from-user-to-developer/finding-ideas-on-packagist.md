---
title: Finding ideas on Packagist
audience: user, developer, designer, app-creator
category: extension-development
order: 100
summary: Packagist lists every package of the extension type: how to find them, read versions, requirements and licenses, and install one on a development installation.
keywords: packagist, composer, ezpublish-legacy-extension, package type, versions, require, license, composer show, composer require, vendor se7enxweb
type: help
---
# Finding ideas on Packagist

Part 1, chapter 10. [Packagist](https://packagist.org/) is the public index Composer reads. Every extension that can
be installed with `composer require` is listed there, with its versions, its requirements and its license. For
someone looking for ideas it is a second catalogue, organized by package rather than by repository.

## The extension package type

Composer packages say what they are with a `type`. Extensions for this code base use the type
`ezpublish-legacy-extension`; the installer package (`se7enxweb/exponential-legacy-installer`) recognizes that type
and puts the package into `extension/<name>` instead of `vendor/`. The name of the folder comes from
`extra.ezpublish-legacy-extension-name` in the package's `composer.json`, for example:

```json
{
    "name": "se7enxweb/str_replace",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "extra": {
        "ezpublish-legacy-extension-name": "str_replace"
    }
}
```

## List every extension package

Packagist has a listing by type. In a browser:
[packagist.org/packages/list.json?type=ezpublish-legacy-extension](https://packagist.org/packages/list.json?type=ezpublish-legacy-extension).
From a terminal:

```bash
curl -s 'https://packagist.org/packages/list.json?type=ezpublish-legacy-extension' | grep -o '"[^"]*/[^"]*"' | wc -l
curl -s 'https://packagist.org/packages/list.json?vendor=se7enxweb' | grep -o '"se7enxweb/[^"]*"'
```

When this page was written (October 2026), Packagist listed 398 packages of the extension type, 127 of them under
the `se7enxweb` vendor, and 218 packages under `se7enxweb` in total (the rest are libraries, distributions and
bundles). The numbers change; run the commands for today's.

## Read a package

The page `https://packagist.org/packages/<vendor>/<name>` shows, for each version:

| On the page | Why it matters to you |
|---|---|
| Versions and their dates | Is it maintained? Does the newest version match the newest tag on GitHub? |
| `require` | Which PHP and which other packages it needs |
| License | What you may do with the code |
| Source | The repository; read the history there |
| Installs | A rough sign of how many sites use it |

The same as JSON, which is useful in scripts:

```bash
curl -s https://packagist.org/packages/se7enxweb/eztags.json | head -c 600
```

From an installation, Composer shows what it knows about an installed package:

```bash
composer show se7enxweb/eztags
composer show --all se7enxweb/eztags | head -30
```

## Versions you can trust

A version on Packagist comes from a Git tag. A tag, once Packagist has read it, is never read again, so a version
number means the same code forever. This is why the projects behind these packages never move a tag and publish a new
version instead (see [Tags and releases are permanent](../part-7-releasing/tags-are-permanent.md)).

When a newer tag exists on GitHub but Packagist does not show it, the repository's webhook to Packagist is usually
missing; [The Packagist webhook](../part-7-releasing/the-packagist-webhook.md) explains how to check.

## Install one on your development installation

```bash
composer require se7enxweb/<name>
```

Composer puts the package into `extension/<name>`. That does not switch it on: activate it as described in
[Extensions: find, install, configure, build and release](../../../guides/extensions.md), regenerate the autoloads
and clear the caches. Some extensions need more (a database table, a content class); their README says so.

> **Warning**
> Install new packages on your development installation first, never directly on a live site. A package runs with the
> full rights of the kernel.

## Packagist versus GitHub

| Question | Look on |
|---|---|
| Can I install it with Composer? | Packagist |
| Which versions exist? | Packagist (tags it read), GitHub (all tags) |
| Why was it changed? | GitHub (history, issues, pull requests) |
| Is there an older, unpublished extension that does this? | GitHub forks, [Exponential Projects](projects-exponential-earth.md) |

## Related pages

- Previous: [Finding ideas on GitHub](finding-ideas-on-github.md)
- Next: [Share Exponential (share.exponential.earth)](share-exponential-earth.md)
- [composer.json for an extension](../part-3-extension-developer/composer-json-for-extensions.md)
- [Composer and Packagist](../part-7-releasing/composer-and-packagist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
