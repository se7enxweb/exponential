---
title: Restore and restore tests
audience: hosting, admin
category: hosting
order: 80
summary: The restore procedure for an Exponential site step by step, how to run a restore test on another machine without sending mail or running jobs, and the checks that prove the restored site is complete.
keywords: restore, restore test, disaster recovery, backup verification, maintenance mode, caches, autoloads, Velocity, file transport, integrity_check, audit verify, staging copy
type: help
---
# Restore and restore tests

This page is for the system administrators and support staff who bring a customer's site back from a backup, and for
the people who prove, before anything goes wrong, that the backups can be restored. It answers: in which order to
restore, what to clear and restart afterwards, how to run a test restore safely on another machine, and how to check
that the result is complete.

"A backup that was never restored is a hope, not a backup"
([10.9 Backups and restore](../../../install/10-after-installing.md#109-backups-and-restore)). The emergency version
of this page, for an incident, is the runbook [Restore from backup](../runbooks/restore-from-backup.md).

## Contents

1. [Before you restore](#before-you-restore)
2. [The restore, step by step](#the-restore-step-by-step)
3. [Checks after a restore](#checks-after-a-restore)
4. [A restore test on another machine](#a-restore-test-on-another-machine)
5. [What goes wrong](#what-goes-wrong)
6. [Checklist](#checklist)

## Before you restore

- **Agree what is restored.** A restore brings the whole site back to the moment of the backup: content published
  since then is lost. For a single deleted page, look first at the trash and the older versions inside Exponential.
- **Back up the current state** before you overwrite it, so a wrong restore can be undone.
- **Check the release.** Restore onto the same Exponential release (and the same `composer.lock`) the backup was taken
  with. A database from an older release needs the upgrade steps afterwards
  ([Upgrading customer sites](upgrading-customer-sites.md)).
- **Know the engine's restore command**: [Database backups per engine](database-backups-per-engine.md).

## The restore, step by step

From [10.9, Restore](../../../install/10-after-installing.md#restore):

1. **Maintenance on.** Visitors get the 503 maintenance page while the data changes.

   ```bash
   php bin/php/maintenance.php on --message="Restoring" --allow-root-user
   ```

   Under Velocity also stop the server, so no worker holds the old database:

   ```bash
   php bin/php/console exp:velocity stop --allow-root-user
   ```

2. **Restore the database** with the tool of its engine, and **unpack the files** over the installation
   (`var/storage`, the per-site storage, `settings/override`, `settings/siteaccess`, your extensions).
3. **Fix the owner and mode** of what you unpacked: the files must belong to the site's user, or to the shared group
   ([8.7 File permissions and ownership](../../../install/08-serving-the-site.md#87-file-permissions-and-ownership)).
4. **Clear every cache and regenerate the autoloads**:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

5. **Start Velocity, clear its response cache, switch maintenance off**, and open the front page and the
   administration:

   ```bash
   php bin/php/console exp:velocity start --allow-root-user
   php bin/php/console exp:velocity cache clear --allow-root-user
   php bin/php/maintenance.php off --allow-root-user
   ```

Under Apache or nginx with PHP-FPM there is no Velocity step; reload the PHP-FPM that serves the site if the code or
settings changed ([Deploying changes](deploying-changes.md)).

> **Warning**
> Run the commands as the site's user (or as root with `--allow-root-user`, then fix ownership). Files created by root
> in `var/` cannot be replaced by the web server afterwards, and the site fails in odd places
> ([12.3 Permissions and ownership](../../../install/12-troubleshooting.md#123-permissions-and-ownership)).

> **Note**
> The maintenance state is the file `var/maintenance.json`. If a backup of the whole `var/` directory was taken while
> maintenance was on, the restored site comes back in maintenance; `php bin/php/maintenance.php off` ends it.

## Checks after a restore

| Check | Command or action | Good result |
|---|---|---|
| The site answers | `curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/` | `200` |
| The administration answers | open `/admin/user/login`, sign in | the dashboard |
| Version | **Setup > System information**, or `./console exp:systeminfo` | the expected release; no failed check |
| Caches | `php bin/php/cache.php status --allow-root-user` | ends in `PASS` |
| Audit trail | `./console exp:audit verify --allow-root-user` | exit status 0 |
| Content | open recent pages and an image; publish a test object | images load, publishing works |
| SQLite only | `sqlite3 <db file> "PRAGMA integrity_check"` | `ok` |
| New errors | `tail -n 50 var/log/error.log` | nothing you cannot explain |

Sources: [10.11 Monitoring and health checks](../../../install/10-after-installing.md#1011-monitoring-and-health-checks),
[Database support](../../database-support.md#backup-and-restore). When content moved between machines or engines,
also compare the row counts of `ezcontentobject`, `ezcontentobject_tree` and `ezcontentobject_attribute` with the
source ([Moving a site to another database](../../database-support.md#moving-a-site-to-another-database)).

## A restore test on another machine

A test restore proves the backup, the procedure and the time it takes. Do it "on another machine at least once and
after every major upgrade" ([10.9](../../../install/10-after-installing.md#restore)), and on a schedule you define for
your service ([Backup schedules and retention](backup-schedules-and-retention.md)).

A restored copy is a full site with real user accounts and real e-mail addresses. Keep it from acting like the live
site:

1. **Stop mail.** Set the file transport on the test machine, so every mail is written to `var/log/mail/` instead of
   being sent ([Mail delivery](mail-delivery.md)):

   ```ini
   # settings/override/site.ini.append.php on the test machine
   [MailSettings]
   Transport=file
   ```

   Or keep the transport and set `DebugSending=enabled` with `DebugReceiverEmail=<your test address>`, which sends
   every mail to one address. A per-machine variant (`settings/override/site.ini.test.append.php` with
   `EXP_ENV=test`) keeps this out of the live settings ([Settings per environment](../../../features/6.0/environment-settings.md)).
2. **Do not install the crontab** on the test machine, or notifications, newsletters and content jobs run against
   real addresses. Run a part by hand only when you test it.
3. **Restrict access.** Do not expose the copy on the public internet; it holds personal data.
4. **Restore, check, measure.** Follow the steps and checks above and write down how long each took.
5. **Remove the copy** when the test is done, including its database and files.

> **Tip**
> Record each test in one line: date, site, backup used, time to restore, result, who ran it. That line answers the
> customer's (and auditor's) question "do your backups work?" with evidence.

## What goes wrong

| Symptom after a restore | Cause and fix |
|---|---|
| Pages show content from before the restore | caches still hold it: clear all caches and Velocity's response cache, after any FPM reload |
| `Class ... not found` | autoloads or `vendor/` do not match: `php bin/php/ezpgenerateautoloads.php -e`, and `vendor/` must match `composer.json` |
| Images missing, pages fine | the files half was not restored, or from another moment than the database |
| Errors about missing tables or columns | the backup is from another release: apply the upgrade steps |
| The web server cannot write `var/` | owner or mode wrong after unpacking: fix them |
| SQLite `.restore` fails | a writer held the lock, or a page size mismatch; use the file replacement described on [Database backups per engine](database-backups-per-engine.md#1-sqlite-3) |

## Checklist

- [ ] The current state was backed up before the restore.
- [ ] Maintenance was on and Velocity stopped while data changed.
- [ ] Database and files come from the same backup moment.
- [ ] Owner and mode are correct; caches and autoloads were rebuilt.
- [ ] The checks above all pass.
- [ ] Test restores run on another machine with mail off and no crontab, and are recorded.

## Related pages

- [Restore from backup](../runbooks/restore-from-backup.md) (runbook)
- [Backups: what to back up](backups-what-to-back-up.md)
- [Database backups per engine](database-backups-per-engine.md)
- [Maintenance mode](maintenance-mode.md)
- [Moving a site between hosts](../migrations/moving-a-site-between-hosts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
