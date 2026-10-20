---
title: Composer and Packagist
audience: developer, designer, app-creator
category: extension-development
order: 50
summary: Register an extension on Packagist so sites can install it with composer require - the composer.json it needs, submitting the repository, how versions come from tags, and how users install and update it.
keywords: Composer, Packagist, composer require, submit package, composer.json, tags, versions, ezpublish-legacy-extension, installer, update
type: help
---
# Composer and Packagist

Part 7, chapter 5. Packagist is the public index Composer looks in. Once your extension is registered there, a site
installs it with one command, and gets your updates with another. This chapter registers an extension and shows
what users then do.

## Before you submit

- The repository is public on GitHub (or another host Packagist can read).
- `composer.json` is valid and complete ([composer.json for an extension](../part-3-extension-developer/composer-json-for-extensions.md)):
  `name`, `description`, `type` `ezpublish-legacy-extension`, `license`, `require` with `php` and an installer, and
  `extra.ezpublish-legacy-extension-name`.
- `composer validate` passes.
- There is at least one tag ([Choosing the next version number](choosing-the-next-version.md)).

```bash
composer validate
```

## Submit

1. Sign in on [packagist.org](https://packagist.org) (with your GitHub account is easiest).
2. Click **Submit**.
3. Paste the repository's address, for example `https://github.com/your-account/myext`.
4. Packagist reads `composer.json`, shows the package name, and creates the package page
   `https://packagist.org/packages/your-account/myext`.

The package name comes from `composer.json`, not from the repository name. It must be free on Packagist, and it
belongs to whoever registered it first, which is why a fork uses its own vendor name.

## Where versions come from

Packagist creates one version per tag (`v1.2.4` becomes `1.2.4`) and one development version per branch (`main`
becomes `dev-main`). It reads them when it is told the repository changed: through the GitHub integration, which you
should check after the first submit ([The Packagist webhook](the-packagist-webhook.md)).

A `version` field in `composer.json` is not needed and, if present, must match each tag exactly, so most extensions
leave it out.

## What users do

```bash
composer require your-account/myext:^1.2
```

Composer finds the package, checks that its requirements fit the site (PHP version, other packages), downloads it,
and the legacy installer plugin puts it into `extension/myext/` (the name from
`extra.ezpublish-legacy-extension-name`). Then the user activates it and clears the caches, as your README says.

Later:

```bash
composer update your-account/myext
```

takes the newest version their constraint allows ([Versioning rules](versioning-rules.md)).

## Requirements that protect users

What you put in `require` decides where your extension can be installed:

| Requirement | Effect |
|---|---|
| `"php": "^8.0"` | Composer refuses to install on PHP 7 |
| `"se7enxweb/exponential": "^6.0.14"` | refuses to install with an older kernel (use it when you call APIs that came with that release) |
| `"ext-mbstring": "*"` | refuses when the PHP extension is missing, instead of a fatal error later |
| another extension with a version range | installs it too, or refuses on a conflict |

Require what you really need, and no more: every requirement is a reason Composer may refuse.

## Updating the package information

The package page updates itself when the webhook works. If it does not, the package page has an **Update** button
for maintainers; use it once, then fix the webhook.

## Related pages

- Previous: [Bumping ezinfo.php and extension.xml](bumping-ezinfo-and-extension-xml.md)
- Next: [The Packagist webhook](the-packagist-webhook.md)
- [Modernizing composer.json](../part-4-upgrading/modernizing-composer-json.md)
- [Packagist: about](https://packagist.org/about)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
