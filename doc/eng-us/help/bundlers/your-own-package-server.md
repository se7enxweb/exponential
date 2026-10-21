---
title: Your own package server for a bundle
audience: bundler, hosting, developer
category: bundling
order: 170
summary: When a bundle needs its own package server or a mirror of the public one, the settings that point an installation at it (RemotePackagesIndexURL, RemotePackagesIndexURLBase, Vendor, ezupdate servers), the warning that your index replaces the public one, and how to keep it current.
keywords: package server, mirror, index.xml, RemotePackagesIndexURL, RemotePackagesIndexURLBase, AdditionalRemotePackagesIndexURLs, Vendor, ezupdate, server-add, package.ini, override, bundle
type: help
---
# Your own package server for a bundle

This page is for bundlers who install site packages from a server they control: a mirror of the public package
server, or a server with their own packages. The reference pages are
[Running your own package server](../site-packages/your-own/running-your-own-package-server.md) and
[Pointing a site at your package server](../site-packages/your-own/pointing-a-site-at-your-package-server.md).

## Contents

1. [When you need one](#when-you-need-one)
2. [What a package server is](#what-a-package-server-is)
3. [Pointing a bundle at it](#pointing-a-bundle-at-it)
4. [Adding it to ezupdate](#adding-it-to-ezupdate)
5. [Keeping it current](#keeping-it-current)

## When you need one

| Situation | Package server |
|---|---|
| The bundle installs only the public 7x packages and may follow their fixes | none: use the public server |
| The bundle must install exactly the tested packages | a mirror you control, or ship the files ([Offline bundles](offline-bundles.md)) |
| The bundle installs your own site package | your own server, or ship the file |
| Many installations in a network without internet access | a mirror inside the network |

## What a package server is

"The **package server** is a static HTTPS site ... It has one folder per Exponential release with an `index.xml` and
the `.ezpkg` files" ([Site packages: start here](../site-packages/README.md#the-short-version)). Any web server that
serves static files is enough. The `index.xml` format is on
[The index.xml format](../site-packages/package-server/the-index-xml-format.md).

## Pointing a bundle at it

The setting lives in `settings/package.ini [RepositorySettings]`; a bundle overrides it in
`settings/override/package.ini.append.php`, **before** the installer runs:

```ini
<?php /* #?ini charset="utf-8"?
[RepositorySettings]
RemotePackagesIndexURL=https://packages.example.com/exponential/6.0/6.0.15
*/ ?>
```

or with the release path filled in automatically:

```ini
[RepositorySettings]
RemotePackagesIndexURL=
RemotePackagesIndexURLBase=https://packages.example.com/exponential
```

When `RemotePackagesIndexURL` is empty, the site types step "takes `RemotePackagesIndexURLBase` and appends
`/<major.minor>/<version>/`". `settings/package.ini` also has `AdditionalRemotePackagesIndexURLs[]`, read after
`RemotePackagesIndexURL`; see [Package server settings](../site-packages/package-server/package-server-settings.md)
for every key.

> **Warning**
> "Your index replaces the 7x index; it is not added to it. Only the packages your `index.xml` lists can be
> downloaded, so list every package your site package requires"
> ([Pointing a site at your package server](../site-packages/your-own/pointing-a-site-at-your-package-server.md#override-it)).

If your packages use their own vendor (`<vendor>example</vendor>`) and you want offline installs from them, set
`Vendor=example` in the same override ([The Vendor setting](../site-packages/your-own/pointing-a-site-at-your-package-server.md#the-vendor-setting)).

## Adding it to ezupdate

Installed sites look for updated packages through ezupdate. Add your server on **Setup > Updates and packages >
Package servers**, or:

```bash
php extension/ezupdate/bin/php/ezupdate.php server-add ezpkg example https://packages.example.com/exponential/6.0/6.0.15
php extension/ezupdate/bin/php/ezupdate.php packages example
```

It is written to `settings/override/ezupdate.ini.append.php`. "ezupdate needs an `https://` address"
([Offline installs and mirrors](../site-packages/package-server/offline-installs-and-mirrors.md#pointing-installations-at-the-mirror)).

## Keeping it current

- A mirror of the public server follows its fixes only if you update it: `git pull` in a Git mirror and rewrite the
  addresses in `index.xml` again, or download again and compare checksums
  ([Keeping a mirror current](../site-packages/package-server/offline-installs-and-mirrors.md#keeping-a-mirror-current)).
- Decide per bundle whether the mirror follows every fix or stays at a tested state; "a host that installs many
  customer sites usually tests first".
- Check every address of the index answers `200` before each bundle release.
- Check the installer against the server with `exp:install --package=<name> --dry-run`, which "downloads the site
  package and its requirements into `var/storage/packages/dryrun/`".

## Related pages

- [Site packages in a bundle](site-packages-in-a-bundle.md)
- [Offline bundles](offline-bundles.md)
- [Package servers in ezupdate](../site-packages/admin-gui/package-servers-in-ezupdate.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
