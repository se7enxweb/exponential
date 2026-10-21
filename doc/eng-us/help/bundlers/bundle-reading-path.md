---
title: Bundler reading path
audience: bundler, partner
category: bundling
order: 540
summary: Every bundler page in reading order, in seven stages from understanding bundles to life after release, with what each stage answers and a short path for readers in a hurry.
keywords: reading path, order, bundler pages, stages, learning path, overview, where to start
type: help
---
# Bundler reading path

This page is for anyone who wants to read the bundler section from start to finish. The stages follow the life of a
bundle; read a stage when you reach it in your project.

## Contents

1. [In a hurry](#in-a-hurry)
2. [Stage 1: understand](#stage-1-understand)
3. [Stage 2: choose and pin](#stage-2-choose-and-pin)
4. [Stage 3: licenses](#stage-3-licenses)
5. [Stage 4: install inside the bundle](#stage-4-install-inside-the-bundle)
6. [Stage 5: your kind of bundle](#stage-5-your-kind-of-bundle)
7. [Stage 6: safe, built and tested](#stage-6-safe-built-and-tested)
8. [Stage 7: after release](#stage-7-after-release)

## In a hurry

1. [Bundlers: start here](bundlers-start-here.md)
2. [GPL obligations when you distribute](gpl-obligations-when-distributing.md)
3. [First boot and install once](first-boot-and-install-once.md)
4. [Default credentials and first sign-in](default-credentials-and-first-sign-in.md)
5. [Bundle release checklist](bundle-release-checklist.md)

## Stage 1: understand

| Page | Answers |
|---|---|
| [What a bundle is](what-a-bundle-is.md) | what counts as a bundle; when it becomes a distribution |
| [Kinds of bundles](kinds-of-bundles.md) | the ten kinds compared |
| [Bundler glossary](bundler-glossary.md) | the words |

## Stage 2: choose and pin

| Page | Answers |
|---|---|
| [Choosing the base](choosing-the-base.md) | Exponential 6, Legacy or Nexus |
| [Exponential 6 as a base](exponential-6-as-a-base.md) | what the root package already brings |
| [Platform distributions as a base](platform-distributions-as-a-base.md) | Legacy and Nexus facts and traps |
| [Getting the code](getting-the-code-for-a-bundle.md) | the three ways and build commands |
| [Versions, tags and pinning](versions-tags-and-pinning.md) | permanent tags, version sort, tilde |
| [Composer-based bundles](composer-based-bundles.md) | your own `composer.json` |
| [Lock files and reproducible builds](lock-files-and-reproducible-builds.md) | building twice the same way |
| [Adding and removing extensions](adding-and-removing-extensions.md) | changing the extension set |

## Stage 3: licenses

| Page | Answers |
|---|---|
| [GPL obligations](gpl-obligations-when-distributing.md) | what distributing asks |
| [License texts and notices](license-texts-and-notices-to-ship.md) | the files to ship |
| [Third-party components](third-party-components-and-their-licenses.md) | the inventory |
| [License fields in your packages](license-fields-in-your-own-packages.md) | your own metadata |
| [Documentation to ship](documentation-to-ship.md) | the GFDL and your guides |

## Stage 4: install inside the bundle

| Page | Answers |
|---|---|
| [Site packages in a bundle](site-packages-in-a-bundle.md) | which site, which files |
| [Your own package server](your-own-package-server.md) | mirrors and your packages |
| [Offline bundles](offline-bundles.md) | installing without internet |
| [The three installers](the-three-installers.md) | wizard, kickstarter, `exp:install` |
| [Unattended install with exp:install](unattended-install-with-exp-install.md) | one command |
| [The kickstarter for bundles](the-kickstarter-for-bundles.md) | the file-driven installer |
| [kickstart.ini templates](kickstart-ini-templates.md) | reviewed templates |
| [First boot and install once](first-boot-and-install-once.md) | when to install |
| [Database choices](database-choices-for-bundles.md), [SQLite bundles](sqlite-bundles.md) | databases |
| [Web server and engine choices](web-server-and-engine-choices.md), [Velocity in a bundle](velocity-in-a-bundle.md) | serving |

## Stage 5: your kind of bundle

[Container images](container-images.md) · [Server and virtual machine images](server-and-virtual-machine-images.md) ·
[Hosting panel installers](hosting-panel-installers.md) · [Linux distribution packaging](linux-distribution-packaging.md) ·
[Agency starter kits](agency-starter-kits.md) · [Extension bundles](extension-bundles.md) · [Appliances](appliances.md) ·
[Education and demo bundles](education-and-demo-bundles.md) · [Demo mode in bundles](demo-mode-in-bundles.md)

## Stage 6: safe, built and tested

| Page | Answers |
|---|---|
| [Default credentials and first sign-in](default-credentials-and-first-sign-in.md) | passwords |
| [Security defaults of a bundle](security-defaults-of-a-bundle.md) | the safe state to hand over |
| [File permissions and var/ ownership](file-permissions-and-var-ownership.md) | users and modes |
| [Caches and autoloads after building](caches-and-autoloads-after-building.md) | refreshing |
| [Integrity checks](integrity-checks.md) | the manifest |
| [Testing a bundle](testing-a-bundle.md) | the test matrix |
| [Bundle release checklist](bundle-release-checklist.md), [Bundle checklists](bundle-checklists.md), [Bundle worksheet](bundle-worksheet.md) | the checks and the record |

## Stage 7: after release

| Page | Answers |
|---|---|
| [Updates and upgrade paths](updates-and-upgrade-paths.md) | keeping installations current |
| [Support boundaries](support-boundaries.md) | who supports what |
| [Contributing fixes back](contributing-bundle-fixes-back.md) | sending fixes upstream |
| [Publishing extensions](publishing-extensions-from-a-bundle.md) | sharing generic extensions |
| [Naming and branding](naming-and-branding-a-bundle.md) | names and credits |
| [OEM and the partner program](oem-and-the-partner-program-for-bundlers.md) | partnership |
| [Bundler FAQ](bundler-faq.md) | quick answers |

## Related pages

- [Bundlers: start here](bundlers-start-here.md)
- [Partner reading path](../partners/partner-reading-path.md)
- [Hosting companies: start here](../hosting/hosting-companies-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
