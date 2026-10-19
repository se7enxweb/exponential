---
title: Restore last night's backup to a test server
audience: government, admin, hosting
category: public-sector
order: 60
summary: Step by step - prove your backup works by restoring last night's database and files to a test server, with mail switched to the file transport so no citizen is contacted, then check the site and record the time it took.
keywords: restore test, backup verification, test server, restore database, mysql restore, psql, sqlite, file transport, recovery time, evidence, disaster recovery
type: help
---
# Restore last night's backup to a test server

This task is for administrators who must show that backups can be restored, as most continuity plans require. At the
end a test server runs the site from last night's backup, and you know how long a restore takes. Background:
[Restore testing](../../operations/backups-and-recovery/restore-testing.md).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check it worked](#check-it-worked)
4. [Record the result](#record-the-result)
5. [Related pages](#related-pages)

## Before you start

- A test server with the same Exponential version, PHP and database engine, and an empty database.
- The backup files: database dump and file archive of the same night.
- Agreement with your data protection officer on restoring production data to that server, and who may access it.

## Steps

1. Note the start time.
2. Put the code of the same version on the test server (checkout of the tag, `composer install`).
3. Unpack the files: `tar xzf files-<date>.tgz` in the installation directory.
4. **Before** anything sends mail, set the file transport for this copy:

   ```ini
   # settings/override/site.ini.append.php on the test server
   [MailSettings]
   Transport=file
   ```

   Also point `[DatabaseSettings]` at the test database.
5. Restore the database:

   | Engine | Command |
   |---|---|
   | MySQL, MariaDB | `mysql -u USER -p DATABASE < backup.sql` |
   | PostgreSQL | `psql -U USER DATABASE < backup.sql` |
   | SQLite | copy `backup.db` to `var/storage/sqlite3/<file>` with the site in maintenance mode |

6. Regenerate and clear:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

7. Note the end time.

## Check it worked

- The front page and a few deep pages render; images load (from `var/storage`).
- You can sign in to the administration with a test account.
- **Setup > System information**: health checks; Mail shows `Transport=file`.
- `./console exp:audit status --allow-root-user` reads the restored audit configuration (keys came with the settings).

## Record the result

Date, backup used, time taken, problems found, who checked. The time taken is a fact for your recovery planning
([RPO and RTO as decisions](../../operations/backups-and-recovery/rpo-and-rto-as-decisions.md)). Remove or lock down
the test copy when done, because it holds production data.

## Related pages
- [Restoring to a test server](../../operations/backups-and-recovery/restoring-to-a-test-server.md)
- [Restore and restore tests (hosting)](../../../hosting/operations/restore-and-restore-tests.md)
- [Restore from backup (hosting runbook)](../../../hosting/runbooks/restore-from-backup.md)
- [Disaster recovery plan](../../operations/backups-and-recovery/disaster-recovery-plan.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
