---
title: Server and virtual machine images
audience: bundler, hosting
category: bundling
order: 290
summary: How to bundle Exponential in a cloud server or virtual machine image - the operating systems the project lists, what to install at build time, what to leave for first boot, the services an image must start (web server or Velocity, cron, backups), and an image checklist.
keywords: server image, virtual machine, VM, cloud image, marketplace, operating system, tier, first boot, cloud-init, systemd, Velocity, PHP-FPM, cron, backups, bundle
type: help
---
# Server and virtual machine images

This page is for bundlers who publish a ready server: a cloud marketplace image, a virtual machine template, a
preinstalled dedicated server. The guidance is generic; Exponential does not ship image build files.

## Contents

1. [Choose the operating system](#choose-the-operating-system)
2. [At build time](#at-build-time)
3. [At first boot](#at-first-boot)
4. [Services the image runs](#services-the-image-runs)
5. [Image checklist](#image-checklist)

## Choose the operating system

[Operating system support](../os-support.md) sorts systems into four tiers, from "tested here" to "not possible
today". Build images on a tier 1 or tier 2 system ([The four tiers](../os-support.md#the-four-tiers)). Notes for Red
Hat Enterprise Linux and its rebuilds, for Debian and Ubuntu, and for Plesk are in
[2.9 Operating system notes](../../install/02-requirements.md#29-operating-system-notes).

PHP: Exponential 6.0.15 supports PHP 8.0 to 8.5, Velocity included, and is usable without support on PHP 7.4; "Velocity, the recommended web server, needs PHP 8.1"
([Database support](../database-support.md#versions-checked-against-the-code)). The required PHP extensions are in
[2.3](../../install/02-requirements.md#23-php-extensions).

## At build time

1. Install PHP, its extensions, Composer, and the database server if the image carries one.
2. Create the site's system user.
3. Put the code in place from your lock file, as that user ([Getting the code](getting-the-code-for-a-bundle.md)).
4. Import your site packages ([Offline bundles](offline-bundles.md)).
5. Regenerate autoloads ([Caches and autoloads after building](caches-and-autoloads-after-building.md)).
6. Install the web server configuration or the Velocity unit ([Web server and engine choices](web-server-and-engine-choices.md)).
7. Install the crontab for `runcronjobs.php`, disabled until the site is installed ([Cron jobs](../hosting/operations/cron-jobs.md)).
8. Add the first-boot script; leave every secret out.
9. Clean: package caches, shell history, logs, SSH host keys if your platform regenerates them.

## At first boot

The first-boot script does what [First boot and install once](first-boot-and-install-once.md#a-first-boot-sequence)
describes: wait for the database, check ownership, skip if installed, read secrets from the platform (instance
metadata, a cloud-init user-data field), run `exp:install` with the recipient's URL and e-mail, hand over the password
and delete `var/log/initial-admin-password`, start serving, enable cron.

Where the recipient's URL is not known at first boot, either ask for it in your platform's launch form or install on
the server's address and document how to change `SiteURL` afterwards
([10.2 Siteaccesses and site addresses](../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).

## Services the image runs

| Service | Notes |
|---|---|
| Velocity, or Apache/nginx with PHP-FPM | one user for all of them, or a common group ([File permissions](file-permissions-and-var-ownership.md)) |
| The database server, if inside | its own backups ([Database backups per engine](../hosting/operations/database-backups-per-engine.md)) |
| Cron for `runcronjobs.php` | the site's user's crontab |
| Backups | the database and `var/storage`, `settings/override`, `settings/siteaccess` |
| Log rotation | `var/log` and per-site logs ([Log viewer and rotation](../hosting/operations/log-viewer-and-rotation.md)) |
| Health check | an HTTP check that treats 503 during install as "installing" ([Health checks and uptime](../hosting/operations/health-checks-and-uptime.md)) |

## Image checklist

- [ ] Operating system on tier 1 or 2; PHP and extensions checked with `exp:velocity ext check`.
- [ ] Code from the lock file; ownership set; autoloads fresh.
- [ ] No secrets, no installed site, no `kickstart.ini`, no logs in the image.
- [ ] First boot installs once, as the site's user, and hands over the password.
- [ ] Cron, backups, log rotation and the health check are set up and documented.
- [ ] Security defaults applied ([Security defaults of a bundle](security-defaults-of-a-bundle.md)).
- [ ] License notices and the source offer are in the image and its listing ([GPL obligations](gpl-obligations-when-distributing.md)).
- [ ] The update path is documented ([Updates and upgrade paths](updates-and-upgrade-paths.md)).

## Related pages

- [VPS hosting](../hosting/models/vps-hosting.md), [Dedicated servers](../hosting/models/dedicated-servers.md)
- [New server checklist](../hosting/checklists/new-server.md)
- [Appliances](appliances.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
