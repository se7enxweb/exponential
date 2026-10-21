---
title: Third-party components and their licenses
audience: bundler, partner, developer
category: partnership
order: 130
summary: How a bundler finds every component inside a bundle and its license - Composer packages, extensions, JavaScript and CSS libraries, site packages, the runtime and the operating system - how to build the third-party list, and how to spot a license that does not fit. Not legal advice.
keywords: third-party, licenses, composer licenses, SPDX, extension license, JavaScript library, site package license, operating system, inventory, THIRD-PARTY, compatibility
type: help
---
# Third-party components and their licenses

This page is for bundlers who must say what is inside their bundle and under which terms. Exponential itself is GPL
version 2 or later; a bundle around it contains much more. Not legal advice: ask counsel about combinations you are
unsure of.

## Contents

1. [The layers of a bundle](#the-layers-of-a-bundle)
2. [Composer packages](#composer-packages)
3. [Extensions](#extensions)
4. [Site packages and content](#site-packages-and-content)
5. [Runtime and operating system](#runtime-and-operating-system)
6. [The third-party list](#the-third-party-list)
7. [Warning signs](#warning-signs)

## The layers of a bundle

| Layer | How to list it | License information from |
|---|---|---|
| Composer packages (`vendor/`, Composer-installed extensions) | `composer licenses` | each package's `composer.json` `license` field |
| Extensions | the `extension/` directory, **Info > About** | `ezinfo.php`, `extension.xml`, `LICENSE` |
| Libraries inside extensions (JavaScript, CSS, fonts) | the extension's files and README | the library's own license header or file |
| Site packages | `package.xml` of each package | the package's license entry ([License fields](license-fields-in-your-own-packages.md)) |
| Runtime: PHP, Velocity, a web server, a database | your image or package list | their projects |
| Operating system | your distribution's package list | the distribution |

## Composer packages

`composer licenses` (a built-in Composer command) prints every installed package with the license its
`composer.json` declares. Run it in the built bundle and keep the output with the release:

```bash
composer licenses --no-dev > THIRD-PARTY-composer.txt
composer licenses --no-dev --format=json > var/log/bundle-licenses.json
```

A package without a license field shows as "none"; look it up by hand before you ship it.

## Extensions

The **Info > About** page of an installed bundle lists every extension "with its license and website", and a
**Third-party software** table with "software, version, license and the extension that includes it"
([About and Copyright pages](../../features/6.0/about-and-package-pages.md)). Use it to cross-check your list; an
extension that shows no license there needs a look at its files.

Extensions made from the RAD wizards and package wizard choose their license from the strict list in
`settings/package.ini [LicenseSettings]` ([Package licenses and Semantic Versioning](../../features/6.0/package-licenses-and-versions.md)).

## Site packages and content

A site package can contain classes, templates, images, styles and demo content. The license of the package applies
to all of them unless the package says otherwise. Demo images in particular may carry a license of their own; read
the package's documents before you ship its demo content to customers
([Documentation requirements: license and documents](../site-packages/requirements/license-and-documents.md)).

## Runtime and operating system

- **Velocity** is a Composer package (`se7enxweb/exponential-velocity`) and also published as deb and rpm packages,
  Docker images and static binaries ([Velocity packages, Docker images and binaries](../../features/6.0/velocity-packages-and-binaries.md)).
  Its license is in its repository.
- **FrankenPHP**, if you use it, is a downloaded binary that bundles Caddy, PHP and PHP extensions
  ([FrankenPHP](../../bc/6.0/frankenphp.md)); list it with its own license.
- **PHP, the database and the operating system** come with their own terms; your distribution's tools list them.

## The third-party list

Ship one list (`THIRD-PARTY.txt` or a section of your notice) in this shape:

```text
Component            Version     License               Source / where to get it
se7enxweb/exponential 6.0.14     GPL-2.0-or-later      https://github.com/se7enxweb/exponential
<extension>          <version>   <license>             <URL>
<library>            <version>   <license>             <URL>
PHP                  <version>   <license>             <URL>
...
```

Generate the Composer part with `composer licenses`; add the rest by hand from the table above.

## Warning signs

| You see | Ask |
|---|---|
| A component with no license at all | May you distribute it? Without a license the answer is usually no. |
| A non-commercial or no-derivatives Creative Commons license on code or images | Does your bundle's use fit those terms? |
| A proprietary extension next to GPL code | Can it be distributed in the same bundle? Ask counsel. |
| `LicenseRef-Proprietary` in a package | It means "all rights reserved" ([Package licenses](../../features/6.0/package-licenses-and-versions.md)): get written permission. |
| A library copied into an extension without its license header | Restore the header or replace the library. |

## Related pages

- [GPL obligations when you distribute](gpl-obligations-when-distributing.md)
- [License texts and notices to ship](license-texts-and-notices-to-ship.md)
- [Licensing for partners](../partners/licensing-for-partners.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
