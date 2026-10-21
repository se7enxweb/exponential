---
title: Bundle checklists
audience: bundler, hosting, partner
category: partnership
order: 510
summary: The short checklists of the bundler pages in one place - before you build, the build, the artifact, first boot, the installed site, licenses, the release, after the release - each item linking to the page that explains it.
keywords: checklist, checklists, bundle, build, artifact, first boot, installed site, licenses, release, after release, review
type: help
---
# Bundle checklists

This page is for bundlers who want the checks without the explanations. Each item links to the page that explains
it. For a full release, use the [Bundle release checklist](bundle-release-checklist.md).

## Contents

1. [Before you build](#before-you-build)
2. [The build](#the-build)
3. [The artifact](#the-artifact)
4. [First boot](#first-boot)
5. [The installed site](#the-installed-site)
6. [Licenses](#licenses)
7. [After the release](#after-the-release)

## Before you build

- [ ] Base chosen and recorded ([Choosing the base](choosing-the-base.md)).
- [ ] Exponential version chosen with a version sort from published tags ([Versions, tags and pinning](versions-tags-and-pinning.md)).
- [ ] Database and server chosen ([Database choices](database-choices-for-bundles.md), [Web server and engine choices](web-server-and-engine-choices.md)).
- [ ] Site package chosen and its files secured ([Site packages in a bundle](site-packages-in-a-bundle.md)).
- [ ] Update path and support terms drafted ([Updates and upgrade paths](updates-and-upgrade-paths.md), [Support boundaries](support-boundaries.md)).

## The build

- [ ] `composer install --no-dev --no-interaction` from the committed lock file ([Lock files](lock-files-and-reproducible-builds.md)).
- [ ] The installer plugin allowed in `composer.json` ([Composer-based bundles](composer-based-bundles.md#the-installer-plugin)).
- [ ] `php bin/php/ezpgenerateautoloads.php -e` run; `exp:checkclasses` clean ([Caches and autoloads](caches-and-autoloads-after-building.md)).
- [ ] `exp:velocity ext check` passes on the shipped PHP ([Getting the code](getting-the-code-for-a-bundle.md#check-the-result)).
- [ ] `php bin/php/checkmanifest.php --all` result recorded ([Integrity checks](integrity-checks.md)).
- [ ] Build steps run as the site user ([File permissions](file-permissions-and-var-ownership.md#building-without-root-owned-files)).

## The artifact

- [ ] No `settings/override/` or `settings/siteaccess/` from the build ([First boot and install once](first-boot-and-install-once.md#what-must-not-be-in-the-bundle)).
- [ ] No `kickstart.ini`, `var/log/`, `var/cache/`, `initial-admin-password`, `config.env.php`.
- [ ] No `EXP_DEMO_MODE` unless it is a demo bundle ([Demo mode in bundles](demo-mode-in-bundles.md)).
- [ ] Notices, third-party list and source offer included ([License texts and notices](license-texts-and-notices-to-ship.md)).
- [ ] `SHA256SUMS` of the artifacts.

## First boot

- [ ] Waits for the database; skips if already installed ([First boot and install once](first-boot-and-install-once.md#a-first-boot-sequence)).
- [ ] Secrets from the platform, the database password in `EXP_INSTALL_DB_PASSWORD` ([Unattended install](unattended-install-with-exp-install.md#secrets)).
- [ ] Installs as the site user with the recipient's URL and e-mail.
- [ ] Checks the exit status; keeps `var/log/setup.log` on failure.
- [ ] Hands over the password once; deletes `var/log/initial-admin-password` ([Default credentials](default-credentials-and-first-sign-in.md)).
- [ ] Starts the server and cron.

## The installed site

- [ ] Site and administration answer at the documented addresses.
- [ ] `/.git/config`, `/settings/site.ini`, `/composer.json` not served ([Security defaults](security-defaults-of-a-bundle.md)).
- [ ] Hardened file modes; nothing under `var/` owned by root.
- [ ] No **Demo mode** banner.
- [ ] **Info > About** and **Info > Copyright** correct.

## Licenses

- [ ] `LICENSE`, `LICENSE.md`, `COPYRIGHT.md` unchanged ([GPL obligations](gpl-obligations-when-distributing.md)).
- [ ] `composer licenses --no-dev` reviewed ([Third-party components](third-party-components-and-their-licenses.md)).
- [ ] Own parts' license fields agree ([License fields](license-fields-in-your-own-packages.md)).
- [ ] Documentation's GFDL notices kept ([Documentation to ship](documentation-to-ship.md)).

## After the release

- [ ] The tag is never moved; mistakes are fixed by the next release ([Versions, tags and pinning](versions-tags-and-pinning.md#a-published-release-never-changes)).
- [ ] Exponential releases watched; security releases bundled promptly ([Updates and upgrade paths](updates-and-upgrade-paths.md#security-releases)).
- [ ] Upstream bugs reported, fixes contributed ([Contributing fixes back](contributing-bundle-fixes-back.md)).

## Related pages

- [Bundle release checklist](bundle-release-checklist.md)
- [Bundle worksheet](bundle-worksheet.md)
- [Hosting checklists](../hosting/checklists/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
