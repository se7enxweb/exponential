---
title: Restoring to a test server
audience: government, hosting, admin
category: public-sector
order: 90
summary: For operations teams in public bodies running a restore test or preparing a copy for acceptance testing. The step-by-step restore of an Exponential site onto a separate machine, with mail stopped, no cronjobs, network restriction and the checks at the end.
keywords: restore to test server, test restore, staging copy, acceptance environment, file transport, DebugSending, no crontab, maintenance mode, Velocity stop, ezcache, autoloads, checks
type: help
---
# Restoring to a test server

This page is for operations teams in public bodies who restore a backup onto a separate machine: as a restore test,
or to give testers a realistic copy. It gives the procedure and the steps that keep the copy from acting like the
live site. It follows [Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md) and
[10.9 Backups and restore](../../../../install/10-after-installing.md#109-backups-and-restore).

## Contents

1. [Before you start](#before-you-start)
2. [Keep the copy quiet](#keep-the-copy-quiet)
3. [The restore step by step](#the-restore-step-by-step)
4. [Checks](#checks)
5. [Clean up](#clean-up)
6. [Checklist](#checklist)

## Before you start

- **Same release.** Check out the same Exponential release and `composer.lock` the backup was taken with. A database
  from an older release needs the upgrade steps afterwards.
- **A host name of its own**, reachable only from your network.
- **Approval** for copying production data, including personal data, to this machine, as your data protection rules
  require ([Environments for development, test and production](../hosting/environments-dev-test-prod.md)).
- **The backup**: database and files from the same moment, plus the key files.

## Keep the copy quiet

Do these before the site starts serving:

1. **Mail.** Write every mail to files instead of sending it:

   ```ini
   # settings/override/site.ini.test.append.php, with EXP_ENV=test on this machine
   [MailSettings]
   Transport=file
   ```

   Mails then go to `var/log/mail/`. Or keep the transport and set `DebugSending=enabled` with
   `DebugReceiverEmail=<test address>`.
2. **Cron.** Do not install the crontab, or notifications, newsletters and content jobs run against real addresses.
3. **Network.** Restrict the host to your network, as you would the administration
   ([Separating the administration from the public site](../hosting/separating-admin-and-public-siteaccesses.md)).
4. **Environment badge.** With `EXP_ENV=test`, admin4 can show the environment so nobody mistakes the copy for
   production.

## The restore step by step

1. Maintenance on, and under Velocity stop the server so no worker holds the database:

   ```bash
   php bin/php/maintenance.php on --message="Restoring" --allow-root-user
   php bin/php/console exp:velocity stop --allow-root-user
   ```

2. Restore the database with the tool of its engine ([Backups per database engine](backups-per-database-engine.md)),
   and unpack the files (`var/storage`, per-site storage, the audit directory, `settings/override`,
   `settings/siteaccess`, your extensions).
3. Adjust settings that name the live machine: database host, `SiteURL` of each siteaccess, host matching, Velocity
   host names ([Moving a site between hosts](../../../hosting/migrations/moving-a-site-between-hosts.md)).
4. Give the files to the site's user, then rebuild caches and autoloads:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

5. Start Velocity, clear its response cache, end maintenance:

   ```bash
   php bin/php/console exp:velocity start --allow-root-user
   php bin/php/console exp:velocity cache clear --apply --allow-root-user
   php bin/php/maintenance.php off --allow-root-user
   ```

   Under PHP-FPM, reload the PHP-FPM that serves the test site instead.

If the backup was taken while maintenance was on, the restored `var/maintenance.json` keeps the copy in maintenance;
`php bin/php/maintenance.php off` ends it.

## Checks

Run the checks of [Restore testing](restore-testing.md): the site and the administration answer, the version is right,
`cache.php status` ends in `PASS`, `exp:audit verify` exits 0, recent content and images open, and `error.log` shows
nothing unexplained. Record the duration.

## Clean up

A restore-test copy holds personal data. When the test is done and reported, remove the copy, or keep it only as long
as your rules allow. Note in the report when it was removed.

## Checklist

- [ ] Same release and `composer.lock` as the backup.
- [ ] Mail goes to files; no crontab; the host is restricted to your network.
- [ ] Settings that name the live machine were adjusted.
- [ ] Owners fixed, caches and autoloads rebuilt.
- [ ] The checks pass, the duration is recorded.
- [ ] The copy is removed or kept under your data rules.

## Related pages

- [Backups and recovery](README.md) index of this folder
- [Restore from backup](../../../hosting/runbooks/restore-from-backup.md) runbook for a real incident
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
