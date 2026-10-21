---
title: License fields in your own packages
audience: bundler, developer
category: bundling
order: 140
summary: Where the license of the parts you add to a bundle is written - composer.json, ezinfo.php, extension.xml, package.xml of a site package and your LICENSE file - with the SPDX identifiers Exponential uses, the strict license list in package.ini, how to add your own license to it, and versions in Semantic Versioning.
keywords: license field, SPDX, composer.json license, ezinfo.php License, extension.xml license, package.xml, package.ini LicenseSettings, DefaultLicense, LicenseList, GPL-2.0-or-later, Semantic Versioning, version
type: help
---
# License fields in your own packages

This page is for bundlers who add their own extensions, themes and site packages. Every part you add should state
its license in the places tools and people read it, in agreement with each other.

## Contents

1. [The places a license is written](#the-places-a-license-is-written)
2. [SPDX identifiers](#spdx-identifiers)
3. [The license list of package.ini](#the-license-list-of-packageini)
4. [Adding your own license to the list](#adding-your-own-license-to-the-list)
5. [Versions](#versions)
6. [Checklist](#checklist)

## The places a license is written

| File | Field | Read by |
|---|---|---|
| `composer.json` | `"license": "GPL-2.0-or-later"` | Composer, Packagist, `composer licenses` |
| `ezinfo.php` | `'License' => "GNU General Public License v2.0 (or any later version)"` | **Info > About** |
| `extension.xml` | `<license>GNU General Public License v2.0 (or any later version)</license>` | **Info > About** (read first when it has `<metadata>`), Setup > Extensions |
| `package.xml` of a site package | the package's license identifier | Setup > Packages, `package/view/full` |
| `LICENSE` | the license text | people |

The kernel's own `composer.json` says `"license": "GPL-2.0-or-later"`. The release rule for extensions: "Set `license`
to the full text `GNU General Public License v2.0 (or any later version)`, or exactly what the extension's license
file says" ([Extension metadata](../../specifications/6.0/extension-metadata.md#release-rule)).

## SPDX identifiers

Exponential's package tools use SPDX identifiers "where one exists, otherwise an SPDX-style `LicenseRef-<name>`"
([Package licenses and Semantic Versioning](../../features/6.0/package-licenses-and-versions.md)). Use the same
identifier in `composer.json` and in `package.xml`, and the long form in `ezinfo.php` and `extension.xml`.

## The license list of package.ini

`settings/package.ini [LicenseSettings]` holds the list the package wizard and the RAD wizards offer:

| Key | Default | Meaning |
|---|---|---|
| `DefaultLicense` | `GPL-2.0-or-later` | preselected |
| `LicenseList[]` | GPL, LGPL, AGPL (each `-only` and `-or-later`), `MIT`, `GFDL-1.3-or-later`, `GFDL-1.3-only`, Creative Commons 1.0 to 4.0, `CC0-1.0`, `LicenseRef-PDM-1.0`, `LicenseRef-Proprietary` | what may be chosen |
| `AliasList[GPL]` | `GPL-2.0-or-later` | old names mapped to identifiers |

"The server accepts only a configured identifier (or an alias such as `GPL`) and refuses anything else." The generated
`ezinfo.php`, `extension.xml` and `composer.json` of a wizard "carry the chosen SPDX identifier".

## Adding your own license to the list

If your bundle's packages use a license that is not on the list, add it in `settings/override/package.ini.append.php`
of your build installation, as the feature page shows:

```ini
<?php /* #?ini charset="utf-8"?
[LicenseSettings]
LicenseList[]=Apache-2.0

[License_Apache-2.0]
Name=Apache License 2.0
URL=https://www.apache.org/licenses/LICENSE-2.0
Group=software
*/ ?>
```

Clear the INI cache afterwards (`php bin/php/ezcache.php --clear-tag=ini`). Whether the license you add can be
distributed next to GPL code is a question for counsel.

## Versions

- New packages start at `1.0.0`; the wizards accept "a strict Semantic Versioning 2.0.0 version: `MAJOR.MINOR.PATCH`
  with optional `-prerelease` and `+build`, at most 24 characters" ([Package licenses and Semantic Versioning](../../features/6.0/package-licenses-and-versions.md)).
- Extensions write the release number into `ezinfo.php` (`'Version'`, no `v` prefix) and `extension.xml`
  (`<version>`) in the commit that is tagged ([Extension metadata](../../specifications/6.0/extension-metadata.md#release-rule)).
- Site packages: see [Version numbers](../site-packages/requirements/version-numbers.md).

## Checklist

- [ ] `composer.json`, `ezinfo.php`, `extension.xml`, `package.xml` and `LICENSE` name the same license.
- [ ] The identifier is an SPDX identifier or a `LicenseRef-`.
- [ ] Copyright lines name your company and any earlier authors.
- [ ] Versions agree across the files, and the tag points at the commit that carries them.
- [ ] `php -l ezinfo.php` and `xmllint --noout extension.xml` pass ([Publishing an extension](../partners/publishing-an-extension.md)).

## Related pages

- [Third-party components and their licenses](third-party-components-and-their-licenses.md)
- [Documentation requirements for site packages](../site-packages/requirements/README.md)
- [Publishing extensions from a bundle](publishing-extensions-from-a-bundle.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
