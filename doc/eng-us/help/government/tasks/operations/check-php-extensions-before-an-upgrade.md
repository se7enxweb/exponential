---
title: Check PHP extensions before an upgrade
audience: government, admin, hosting
category: public-sector
order: 350
summary: Step by step - before a PHP or Exponential upgrade, list the PHP extensions the installation wants and which are missing on the target machine with exp:velocity ext check, compare with System information, and clear the deprecations log.
keywords: PHP extensions, exp:velocity ext check, PHP upgrade, missing extensions, fileinfo, intl, opcache, system information, deprecated.log, preparation
type: help
---
# Check PHP extensions before an upgrade

This task is for administrators preparing a PHP upgrade, a move to a new server or an Exponential upgrade. At the end
you know which PHP extensions the target machine lacks and have the command to install them
([Deploying, section 2](../../../../guides/deploying.md#2-check-the-machine-before-you-start)).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Related pages](#related-pages)

## Before you start

- Shell access on the target machine (staging first).

## Steps

1. PHP version and extensions:

   ```bash
   php -v | head -1
   ./console exp:velocity ext check --allow-root-user
   ```

   The second lists every PHP extension the installation wants, marks what is missing and prints the install command
   for the system (for example `sudo dnf install -y php-<name>`). Missing optional extensions do not stop the site.
2. The web server's PHP can differ from the command line's: open **Setup > System information** through the web
   server; the health checks *PHP extensions* (failure when a required one is missing) and *Recommended extensions*
   (curl, fileinfo, intl, xsl, OPcache, APCu, gd or imagick) cover it.
3. Code that a newer PHP will refuse:

   ```bash
   sort var/log/deprecated.log | uniq -c | sort -rn | head
   ```

   Give the list to your developers; your own extensions are the usual source.
4. Install what is missing, then restart PHP-FPM or Velocity so the extensions load.

## Check it worked

- `./console exp:velocity ext check --allow-root-user` shows nothing missing for the features you use.
- **Setup > System information** has no red PHP checks.
- `php -m | grep fileinfo` (fileinfo matters for upload checks).

## Related pages
- [PHP and database upgrades](../../operations/upgrades-and-change/php-and-database-upgrades.md)
- [PHP versions and extensions (hosting)](../../../hosting/basics/php-versions-and-extensions.md)
- [PHP upgrade (hosting runbook)](../../../hosting/runbooks/php-upgrade.md)
- [Requirements](../../../../install/02-requirements.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
