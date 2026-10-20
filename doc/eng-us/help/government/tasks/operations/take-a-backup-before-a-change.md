---
title: Take a backup before a change
audience: government, admin, hosting
category: public-sector
order: 40
summary: Step by step - take a consistent backup of database and files right before a change, inside the maintenance window, with the right tool for your database engine, and check that the files are complete.
keywords: backup, before a change, database dump, mysqldump, pg_dump, sqlite backup, var/storage, settings, maintenance mode, consistent backup, git_manager
type: help
---
# Take a backup before a change

This task is for administrators before an upgrade, a migration or any change that touches the database. At the end
you have a database dump and a file archive taken at the same moment, which is your rollback. Background:
[What to back up](../../operations/backups-and-recovery/what-to-back-up.md).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Pitfalls](#pitfalls)
5. [Related pages](#related-pages)

## Before you start

- Shell access, the database user and enough disk space on another volume or system.
- Know your engine: `grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php settings/site.ini`.

## Steps

1. Switch maintenance on, so database and files match ([Take the site into maintenance for an upgrade](take-the-site-into-maintenance-for-an-upgrade.md)).
2. Dump the database (the client asks for the password; never type it on the command line):

   | Engine | Command |
   |---|---|
   | MySQL, MariaDB | `mysqldump --single-transaction -u USER -p DATABASE > backup-before-change.sql` |
   | PostgreSQL | `pg_dump -U USER DATABASE > backup-before-change.sql` |
   | SQLite | `sqlite3 var/storage/sqlite3/exponential.db ".backup 'backup-before-change.db'"` (your file name) |

   Other engines: [Backups per database engine](../../operations/backups-and-recovery/backups-per-database-engine.md).
3. Archive the files:

   ```bash
   tar czf backup-before-change-files.tgz var/storage settings/override settings/siteaccess extension design
   ```

4. Copy both to the backup location, off this server.

On MySQL or MariaDB sites with the git_manager extension, **Setup > Backup**, **Create full site backup** is an
alternative from the browser; its archives stay on the same server until you move them
([Backups from the admin with git_manager](../../operations/backups-and-recovery/backups-from-the-admin-with-git-manager.md)).

## Check it worked

- The dump ends normally (for MySQL the last line is a `-- Dump completed` comment) and has a plausible size.
- `tar tzf backup-before-change-files.tgz | head` lists the files.
- Note the file names and sizes in the change record.

## Pitfalls

- The audit keys are in `settings/override/audit.ini.append.php`: the archive above contains them, so protect it like
  a secret ([Keys and secrets in backups](../../operations/backups-and-recovery/keys-and-secrets-in-backups.md)).
- Never copy a SQLite file while the site runs; use `.backup`.
- A backup you have never restored is a hope: [Restore last night's backup to a test server](restore-last-nights-backup-to-a-test-server.md).

## Related pages
- [Back up a SQLite site safely](back-up-a-sqlite-site-safely.md)
- [Rollback planning](../../operations/upgrades-and-change/rollback-planning.md)
- [Upgrading, step 1](../../../../guides/upgrading.md#step-1-back-up-always)
- [Backups: what to back up (hosting)](../../../hosting/operations/backups-what-to-back-up.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
