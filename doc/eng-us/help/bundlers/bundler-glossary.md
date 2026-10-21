---
title: Bundler glossary
audience: bundler, partner, hosting
category: partnership
order: 530
summary: The words of the bundler pages in one place - bundle, base, distribution, release tag, lock file, site package, package server, installer, kickstarter, exp:install, CreateSites, demo mode, manifest, Velocity, engine, OEM and more - each with the page that explains it.
keywords: glossary, terms, bundle, distribution, tag, lock file, site package, ezpkg, package server, kickstarter, exp:install, CreateSites, demo mode, manifest, Velocity, OEM, GPL, GFDL
type: help
---
# Bundler glossary

This page is for anyone reading the bundler pages who meets an unfamiliar word. Terms are in alphabetical order.

## Contents

1. [A to F](#a-to-f)
2. [G to O](#g-to-o)
3. [P to Z](#p-to-z)

## A to F

| Term | Meaning | Page |
|---|---|---|
| Already-installed test | `grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php`, the same test `exp:install` uses | [First boot and install once](first-boot-and-install-once.md) |
| Appliance | hardware or a sealed virtual machine with Exponential inside | [Appliances](appliances.md) |
| Autoload arrays | generated class maps in `autoload/` and `var/autoload/` | [Caches and autoloads](caches-and-autoloads-after-building.md) |
| Base | what a bundle builds on: Exponential 6, Legacy or Nexus | [Choosing the base](choosing-the-base.md) |
| Bundle | Exponential combined with other things and handed to someone outside your organization | [What a bundle is](what-a-bundle-is.md) |
| CreateSites | the install step that writes the database, the packages and the settings | [The three installers](the-three-installers.md) |
| DatabaseAction | `remove`, `ignore` or `skip`: what the installer does with an existing database | [The kickstarter for bundles](the-kickstarter-for-bundles.md#databaseaction) |
| Demo mode | `EXP_DEMO_MODE=1`: administrator passwords cannot be changed; for public demos only | [Demo mode in bundles](demo-mode-in-bundles.md) |
| Distribution (legal) | handing copies to others, which brings the GPL's conditions | [GPL obligations](gpl-obligations-when-distributing.md) |
| Distribution (product) | Exponential Platform Legacy or Nexus, which carry Exponential 6 | [Platform distributions](platform-distributions-as-a-base.md) |
| `.ezpkg` | a package file: a gzip compressed tar with `package.xml` | [Site packages in a bundle](site-packages-in-a-bundle.md) |
| `exp:install` | the one-command installer (`bin/php/install.php`) | [Unattended install](unattended-install-with-exp-install.md) |
| FrankenPHP | an engine of Velocity: Caddy with PHP built in | [Web server and engine choices](web-server-and-engine-choices.md) |

## G to O

| Term | Meaning | Page |
|---|---|---|
| GFDL | GNU Free Documentation License 1.3, the license of the documentation | [Documentation to ship](documentation-to-ship.md) |
| GPL | GNU General Public License, version 2 or later, the license of the code | [GPL obligations](gpl-obligations-when-distributing.md) |
| `initial-admin-password` | `var/log/initial-admin-password`, the generated password, mode `0600` | [Default credentials](default-credentials-and-first-sign-in.md) |
| Kickstarter | the installer that reads `kickstart.ini` (`exp:kickstarter`, `bin/php/kickstarter.php`) | [The kickstarter for bundles](the-kickstarter-for-bundles.md) |
| Lock file | `composer.lock`: the exact versions of a build; yours, not the project's | [Lock files](lock-files-and-reproducible-builds.md) |
| Maintenance mode | `var/maintenance.json`; the site answers 503 while an install or update runs | [The three installers](the-three-installers.md#maintenance-mode-during-the-install) |
| Manifest | `share/filelist.md5`, checksums of every distributed file | [Integrity checks](integrity-checks.md) |
| OEM | reselling your own products based on Exponential, a partner benefit | [OEM and the partner program](oem-and-the-partner-program-for-bundlers.md) |

## P to Z

| Term | Meaning | Page |
|---|---|---|
| Package server | a static HTTPS site with `index.xml` and `.ezpkg` files per release | [Your own package server](your-own-package-server.md) |
| Release tag | a permanent Git tag such as `v6.0.14`; never moved | [Versions, tags and pinning](versions-tags-and-pinning.md) |
| Site package | a package of type `site` that builds the new site | [Site packages in a bundle](site-packages-in-a-bundle.md) |
| Siteaccess | a configured entry to the site; directories `site`, `admin`, `editor` | [The three installers](the-three-installers.md#what-every-installer-writes) |
| Source offer | how recipients get the corresponding source of GPL parts | [GPL obligations](gpl-obligations-when-distributing.md) |
| Starter kit | an agency's repository from which new projects start | [Agency starter kits](agency-starter-kits.md) |
| Tilde constraint | `~6.0.8`: the last position floats | [Versions, tags and pinning](versions-tags-and-pinning.md#tilde-constraints) |
| Velocity | Exponential's web and application server; engine `qbix` recommended | [Velocity in a bundle](velocity-in-a-bundle.md) |

More terms: [Partner glossary](../partners/partner-glossary.md), [Glossary for hosts](../hosting/glossary-for-hosts.md),
[Site packages glossary](../site-packages/glossary.md).

## Related pages

- [Bundler FAQ](bundler-faq.md)
- [Bundler reading path](bundle-reading-path.md)
- [Bundlers: start here](bundlers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
