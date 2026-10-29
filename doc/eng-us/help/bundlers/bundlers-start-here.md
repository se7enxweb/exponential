---
title: "Bundlers: start here"
audience: bundler, partner, hosting
category: bundling
order: 1
summary: The landing page for people and companies who bundle Exponential with other software and ship the result - server images, containers, panel installers, distribution packages, agency starter kits, distributions, site packages, extension bundles, appliances and demo bundles. What a bundle is, the rules that matter, a map of every bundler page and reading paths by role.
keywords: bundler, bundle, bundling, distribution, image, container, appliance, one-click installer, starter kit, site package, extension bundle, packaging, GPL, kickstarter, exp:install, release checklist
featured: true
type: help
---
# Bundlers: start here

This section is for **bundlers**: people and companies who take Exponential, combine it with other things (an
operating system, a web server, a database, extensions, a theme, a site package, their own tools) and ship the
result to someone else. A preinstalled server image, a container, a hosting control panel's one-click installer, a
Linux distribution package, an agency's starter kit, a Legacy or Nexus distribution, a site package, an extension
bundle, an appliance and a classroom or demo bundle are all bundles.

It answers four questions: what you may ship and what you must ship with it, which base to build on, how to make the
installation inside your bundle repeatable and safe, and how to keep the people who receive your bundle up to date.

## Contents

1. [Bundling Exponential in one minute](#bundling-exponential-in-one-minute)
2. [The ground rules of this section](#the-ground-rules-of-this-section)
3. [Map of the bundler pages](#map-of-the-bundler-pages)
4. [Reading paths](#reading-paths)
5. [Who to ask](#who-to-ask)

## Bundling Exponential in one minute

- **You may bundle and ship it.** Exponential's code is free software under the GNU General Public License,
  version 2 or any later version (`composer.json`: `"license": "GPL-2.0-or-later"`). You may charge for the bundle,
  for your work and for services ([GPL obligations when you distribute](gpl-obligations-when-distributing.md)).
- **Shipping has duties.** Pass on the license texts and notices, offer the corresponding source code, add no
  restrictions. This is not legal advice; the license texts decide.
- **Pick a base on purpose.** Exponential 6 on its own, or a Platform distribution that carries it (Legacy, Nexus)
  ([Choosing the base](choosing-the-base.md)).
- **Pin versions.** A release tag never changes; build from tags and keep your lock file
  ([Versions, tags and pinning](versions-tags-and-pinning.md)).
- **Install unattended.** `php bin/php/console exp:install --apply` or the kickstarter (`kickstart.ini`) do the whole
  installation without a browser ([The three installers](the-three-installers.md)).
- **Never ship a known password.** The installers generate a random administrator password when none is given and
  write it to `var/log/initial-admin-password`; public demos are the only exception, with `EXP_DEMO_MODE=1`
  ([Default credentials and first sign-in](default-credentials-and-first-sign-in.md)).
- **Build, then refresh.** Autoloads and caches after every build, ownership of `var/` right, the integrity
  manifest checked ([Caches and autoloads after building](caches-and-autoloads-after-building.md),
  [Integrity checks](integrity-checks.md)).
- **Plan the update path before the first release.** Say who updates what, and how
  ([Updates and upgrade paths](updates-and-upgrade-paths.md)).
- **Name it honestly.** "Based on Exponential", never a name that suggests you are the project
  ([Naming and branding a bundle](naming-and-branding-a-bundle.md)).

## The ground rules of this section

1. **Every technical claim names its source**: a file, a setting, a command or another documentation page. The
   commands and options are the ones the code has; none is invented.
2. **Licensing pages are reminders, not legal advice.** Ask counsel about your own bundle.
3. **Partner program facts are quoted from the partner pages.** Where they are silent, these pages say "not
   published; ask the partner team".
4. **Examples use placeholders**: `example.com`, "your company", `<site user>`.

## Map of the bundler pages

| Area | Pages |
|---|---|
| Understand bundling | [What a bundle is](what-a-bundle-is.md) · [Kinds of bundles](kinds-of-bundles.md) · [Reading path](bundle-reading-path.md) · [Glossary](bundler-glossary.md) · [FAQ](bundler-faq.md) |
| Choose the base | [Choosing the base](choosing-the-base.md) · [Exponential 6 as a base](exponential-6-as-a-base.md) · [Platform distributions as a base](platform-distributions-as-a-base.md) |
| Get and pin the code | [Getting the code](getting-the-code-for-a-bundle.md) · [Versions, tags and pinning](versions-tags-and-pinning.md) · [Composer-based bundles](composer-based-bundles.md) · [Lock files and reproducible builds](lock-files-and-reproducible-builds.md) · [Adding and removing extensions](adding-and-removing-extensions.md) |
| Licenses and documentation | [GPL obligations](gpl-obligations-when-distributing.md) · [License texts and notices](license-texts-and-notices-to-ship.md) · [Third-party components](third-party-components-and-their-licenses.md) · [License fields in your packages](license-fields-in-your-own-packages.md) · [Documentation to ship](documentation-to-ship.md) |
| Site packages | [Site packages in a bundle](site-packages-in-a-bundle.md) · [Your own package server](your-own-package-server.md) · [Offline bundles](offline-bundles.md) |
| Installing inside the bundle | [The three installers](the-three-installers.md) · [Unattended install with exp:install](unattended-install-with-exp-install.md) · [The kickstarter for bundles](the-kickstarter-for-bundles.md) · [kickstart.ini templates](kickstart-ini-templates.md) · [First boot and install once](first-boot-and-install-once.md) |
| Database and serving | [Database choices](database-choices-for-bundles.md) · [SQLite bundles](sqlite-bundles.md) · [Web server and engine choices](web-server-and-engine-choices.md) · [Velocity in a bundle](velocity-in-a-bundle.md) |
| Bundle types | [Container images](container-images.md) · [Server and virtual machine images](server-and-virtual-machine-images.md) · [Hosting panel installers](hosting-panel-installers.md) · [Linux distribution packaging](linux-distribution-packaging.md) · [Agency starter kits](agency-starter-kits.md) · [Extension bundles](extension-bundles.md) · [Appliances](appliances.md) · [Education and demo bundles](education-and-demo-bundles.md) |
| Safe by default | [Demo mode in bundles](demo-mode-in-bundles.md) · [Default credentials and first sign-in](default-credentials-and-first-sign-in.md) · [Security defaults of a bundle](security-defaults-of-a-bundle.md) · [File permissions and var/ ownership](file-permissions-and-var-ownership.md) |
| Build and release | [Caches and autoloads after building](caches-and-autoloads-after-building.md) · [Integrity checks](integrity-checks.md) · [Testing a bundle](testing-a-bundle.md) · [Release checklist](bundle-release-checklist.md) · [Checklists](bundle-checklists.md) · [Bundle worksheet](bundle-worksheet.md) |
| Life after release | [Updates and upgrade paths](updates-and-upgrade-paths.md) · [Support boundaries](support-boundaries.md) · [Contributing fixes back](contributing-bundle-fixes-back.md) · [Publishing extensions](publishing-extensions-from-a-bundle.md) |
| Brand and partnership | [Naming and branding a bundle](naming-and-branding-a-bundle.md) · [OEM and the partner program](oem-and-the-partner-program-for-bundlers.md) |

## Reading paths

| You are | Read in this order |
|---|---|
| Building a container or server image | [What a bundle is](what-a-bundle-is.md), [Container images](container-images.md) or [Server and virtual machine images](server-and-virtual-machine-images.md), [First boot and install once](first-boot-and-install-once.md), [File permissions](file-permissions-and-var-ownership.md), [Release checklist](bundle-release-checklist.md) |
| Writing a hosting panel installer | [Hosting panel installers](hosting-panel-installers.md), [Unattended install with exp:install](unattended-install-with-exp-install.md), [Default credentials](default-credentials-and-first-sign-in.md), then the hosting pages it links |
| Packaging for a Linux distribution | [Linux distribution packaging](linux-distribution-packaging.md), [GPL obligations](gpl-obligations-when-distributing.md), [Third-party components](third-party-components-and-their-licenses.md), [Updates and upgrade paths](updates-and-upgrade-paths.md) |
| An agency shipping a starter kit | [Agency starter kits](agency-starter-kits.md), [Composer-based bundles](composer-based-bundles.md), [Site packages in a bundle](site-packages-in-a-bundle.md), [kickstart.ini templates](kickstart-ini-templates.md) |
| Building a distribution | [Choosing the base](choosing-the-base.md), [Platform distributions as a base](platform-distributions-as-a-base.md), [Versions, tags and pinning](versions-tags-and-pinning.md), [Naming and branding](naming-and-branding-a-bundle.md) |
| A site package or extension bundle author | [Site packages in a bundle](site-packages-in-a-bundle.md), [Extension bundles](extension-bundles.md), [License fields in your packages](license-fields-in-your-own-packages.md), [Publishing extensions](publishing-extensions-from-a-bundle.md) |
| An appliance vendor | [Appliances](appliances.md), [Security defaults](security-defaults-of-a-bundle.md), [Support boundaries](support-boundaries.md), [OEM and the partner program](oem-and-the-partner-program-for-bundlers.md) |
| Preparing a classroom or demo bundle | [Education and demo bundles](education-and-demo-bundles.md), [Demo mode in bundles](demo-mode-in-bundles.md), [SQLite bundles](sqlite-bundles.md) |

The complete order, page by page, is the [Bundler reading path](bundle-reading-path.md).

## Who to ask

| Question | Where | Source |
|---|---|---|
| The partner program, OEM resale, your level | The partner team: <partners@exponential.earth>, or the form at <https://exponential.earth/partner/contact-us> | [Partner plans and prices](../partners/partner-plans-and-prices.md#partner-terms) |
| Paid support and services from 7x | <https://se7enx.com/contact> | [Partners: start here](../partners/partners-start-here.md#who-to-ask) |
| Technical questions | Forums <https://share.exponential.earth/forums>, Telegram <https://t.me/exponentialcms> | [Partners: start here](../partners/partners-start-here.md#who-to-ask) |
| A security problem in Exponential | security@se7enx.com, privately | `SECURITY.md` in the repository root |
| A bug in the kernel | the issue tracker of <https://github.com/se7enxweb/exponential> | [Contributing fixes back](contributing-bundle-fixes-back.md) |

## Related pages

- [Partners: start here](../partners/partners-start-here.md)
- [Hosting companies: start here](../hosting/hosting-companies-start-here.md)
- [Site packages: start here](../site-packages/README.md)
- [Resellers: start here](../resellers/resellers-start-here.md)
- [Integrators: start here](../integrators/integrators-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
