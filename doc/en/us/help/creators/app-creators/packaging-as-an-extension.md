---
title: Packaging your application as an extension
audience: app-creator, developer
category: app-creation
order: 440
summary: Turn an application into an installable extension - repository layout, composer.json with the extension type, extension.xml and ezinfo.php, license and copyright, install steps, settings defaults, documentation and the checklist before others use it.
keywords: packaging, extension, composer.json, ezpublish-legacy-extension, extension.xml, ezinfo.php, license, GPL, install, README, distribution, app creator
type: help
---
# Packaging your application as an extension

This page is for application creators who want others (or other installations) to install their application. An
extension that installs with one `composer require`, activates with one setting and documents itself is easy to adopt.

![The About page lists every extension with what its ezinfo.php or extension.xml says](../../images/creators/about-extensions.webp)

## Contents

1. [The repository](#the-repository)
2. [composer.json](#composerjson)
3. [extension.xml and ezinfo.php](#extensionxml-and-ezinfophp)
4. [Install steps](#install-steps)
5. [License](#license)
6. [Checklist](#checklist)

## The repository

One Git repository per extension, its root being the extension directory ([Anatomy of an extension](extension-anatomy.md)):

```text
expdemo/
├── composer.json
├── extension.xml
├── ezinfo.php
├── LICENSE
├── README.md          what it does, requirements, install, settings
├── doc/               the longer documentation
├── classes/ modules/ design/ settings/ share/ bin/ cronjobs/ translations/
└── tests/
```

## composer.json

Extensions are Composer packages of type `ezpublish-legacy-extension`; the installer plugin places them in
`extension/<name>` ([3.5 Extensions are Composer packages](../../../install/03-getting-the-code.md#35-extensions-are-composer-packages)):

```json
{
    "name": "yourvendor/expdemo",
    "description": "Demo application for Exponential",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "require": { "php": ">=8.0" },
    "extra": { "ezpublish-legacy-extension-name": "expdemo" }
}
```

Libraries your code needs go in `require`; never commit `vendor/`. Publishing it: [Composer packages and Packagist](composer-and-packagist.md).

## extension.xml and ezinfo.php

Both state the same name, version, copyright, license and website; the about page (`/ezinfo/about`) reads
`extension.xml` first. Dependencies on other extensions go into `extension.xml` `<dependencies>`. See the shipped
`extension/ezstarrating/extension.xml` for a complete example.

## Install steps

Document, and automate where possible:

1. `composer require yourvendor/expdemo`
2. `ActiveExtensions[]=expdemo`
3. `./console ext:expdemo:install-schema` (your idempotent table installer, [Database schema and .dba files](schema-and-dba-files.md))
4. `php bin/php/ezpgenerateautoloads.php -e` and `./console exp:velocity deploy`
5. Policies to give, cron lines to add, settings to set in `settings/override/`.

## License

Exponential is GPL v2 or later; code that runs inside it is usually distributed under a compatible license. Put the
license in `LICENSE`, `composer.json`, `ezinfo.php` and `extension.xml`. Credit code you based your work on
([Licensing for buyers](../../business/licensing-for-buyers.md)); this is not legal advice.

## Checklist

- [ ] Installs with Composer into `extension/<name>`; activates with one line.
- [ ] Works with a clean installation and the shipped design (templates overridable).
- [ ] No secrets, no host names, no personal data in the repository.
- [ ] Translatable texts, at least English.
- [ ] Tests run in CI ([Continuous integration for extensions](ci-for-extensions.md)).
- [ ] Version in all metadata files matches the tag ([Versions and releases](versions-and-releases.md)).
- [ ] README with requirements, install, settings and support.

## Related pages

- [Composer packages and Packagist](composer-and-packagist.md)
- [Versions and releases of your extension](versions-and-releases.md)
- [Source control and releases standard](../../../standards/source-control-and-releases.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
