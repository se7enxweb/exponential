---
title: Offline bundles
audience: bundler, hosting
category: partnership
order: 180
summary: How to build a bundle that installs Exponential without internet access - code and Composer packages in the bundle, site packages imported with ezpm before the install, what the installer does when the package server cannot be reached, the dry-run caveat, and a test that proves the bundle works offline.
keywords: offline, air-gapped, no internet, import, ezpm import, var/storage/packages, local index.xml, dry run, appliance, classroom, bundle
type: help
---
# Offline bundles

This page is for bundlers whose bundle must install where there is no internet: appliances, classrooms, secure
networks, trade-show laptops. It also helps every other bundler, because an offline-capable bundle installs the same
thing every time.

## Contents

1. [What must already be inside](#what-must-already-be-inside)
2. [Importing the site packages](#importing-the-site-packages)
3. [What the installer does without the server](#what-the-installer-does-without-the-server)
4. [The dry-run caveat](#the-dry-run-caveat)
5. [Proving it works offline](#proving-it-works-offline)

## What must already be inside

| Part | How it gets into the bundle |
|---|---|
| The code with `vendor/` and every extension | built with Composer at build time ([Getting the code](getting-the-code-for-a-bundle.md)) |
| The PHP extensions | in the image or the OS packages |
| The site package and every package it requires | `.ezpkg` files imported into `var/storage/packages/` ([below](#importing-the-site-packages)) |
| Velocity, or another web server | Composer (Velocity) or OS packages |
| Search, if used | a Solr installation inside the bundle, or the built-in search |

## Importing the site packages

The documented way: "the reliable way to install offline is to **import every package first**"
([Offline installs and mirrors](../site-packages/package-server/offline-installs-and-mirrors.md#what-the-installer-does-without-the-server)).

```bash
for f in var/tmp/packages/*.ezpkg; do php ezpm.php import "$f"; done
ls var/storage/packages/7x/
php bin/php/console exp:install --package=sevenx_multisite_clean
```

For `sevenx_multisite` the required packages are `sevenx_classes` and `sevenx_multisite_democontent`; the requirements
of other packages are found as described on
[Downloading a package by hand](../site-packages/package-server/downloading-a-package-by-hand.md#find-what-it-requires).
Remove the copies in `var/tmp/packages/` after the import.

In a bundle, do the import at build time, so the image already holds `var/storage/packages/`, or as the first step
of the first-boot script before the installer runs.

## What the installer does without the server

From `kernel/setup/steps/ezstep_site_types.php`, as documented:

| Situation | Kickstarter and `exp:install` |
|---|---|
| index unreachable, `var/storage/packages/7x/index.xml` exists | that file is used instead |
| index unreachable, no local `index.xml` | the imported site package is used when all its required packages are imported; otherwise the step stops |
| a required package is not imported and cannot be downloaded | the step stops with an error naming the package |
| everything imported | nothing is downloaded |

> **Warning**
> A local `index.xml` whose `url`s cannot be reached can hide your imported site package in the setup wizard when its
> versions are higher. "Keep the versions in a local index equal to or lower than those of the imported packages, or
> use no local index."

## The dry-run caveat

"A dry run (`exp:install --dry-run`, `exp:kickstarter run --dry-run`) always downloads the site package and its
requirements into the temporary repository `var/storage/packages/dryrun/` to check them. It needs the package server
(or a mirror) even when the packages are imported." Run dry runs in the build, where the network is available, not on
the offline target.

## Proving it works offline

1. Build the bundle.
2. Start it in an environment with no outgoing network (a container with networking switched off, a virtual machine
   without a network adapter).
3. Run the first-boot install.
4. Check the result: exit status `0`, `var/log/setup.log` ends with a RESULT line without errors, the site answers.
5. Keep the log with the release ([Testing a bundle](testing-a-bundle.md)).

Things that may still try the network after the install, and that an offline bundle should configure: mail delivery
([Mail delivery](../hosting/operations/mail-delivery.md)), ezupdate's package servers, and any extension that calls an
external service.

## Related pages

- [Offline installs and mirrors](../site-packages/package-server/offline-installs-and-mirrors.md)
- [Site packages in a bundle](site-packages-in-a-bundle.md)
- [Appliances](appliances.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
