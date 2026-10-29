---
title: "Runbook: restore from backup"
audience: hosting, admin
category: hosting
order: 50
summary: Restoring an Exponential site from a backup during an incident: decide what to restore, maintenance mode, stop Velocity, restore the database with the engine's tool and the files, fix owners, clear caches, regenerate autoloads, verify, reopen.
keywords: runbook, restore, backup, recovery, data loss, maintenance mode, Velocity stop, database restore, sqlite restore, pg_restore, mongorestore, mariadb, mysql, Oracle impdp, var/storage, settings, caches, autoloads
type: help
---
# Runbook: restore from backup

For system administrators who must bring a customer site back from a backup: after data loss, a broken update or a
compromise. The procedure is that of [10.9 Backups and restore](../../../install/10-after-installing.md#109-backups-and-restore);
this page arranges it for an incident.

## Contents

1. [Decide first](#decide-first)
2. [Prepare](#prepare)
3. [Restore](#restore)
4. [Verify and reopen](#verify-and-reopen)
5. [After the incident](#after-the-incident)

## Decide first

| Question | Why |
|---|---|
| Which backup? | The newest one from before the problem started; for a compromise, one from before the attacker's first change ([Compromised site](compromised-site.md)). |
| Everything, or only the database, or only some files? | A restore of everything loses all content published since the backup. |
| What is lost? | Publishing between the backup and now. Tell the customer before you start. |
| Where? | In place, or first on a second machine to check the backup (preferred when time allows). |

> **Tip**
> Keep the damaged state. Before restoring over it, take a copy of the current database and files; it may be needed
> to recover content published after the backup or to investigate.

## Prepare

1. Copy the current state aside (database dump and files), as above.
2. Maintenance mode on, so visitors get a 503 page and nobody publishes:

   ```bash
   php bin/php/maintenance.php on --message="Restoring the site" --until=1h --allow-root-user
   ```

3. Under Velocity, stop the server so no worker holds the old database:

   ```bash
   php bin/php/console exp:velocity stop --allow-root-user
   ```

## Restore

1. **The database**, with the tool of its engine (official order):

   | Engine | Restore |
   |---|---|
   | 1. SQLite 3 | `sqlite3 var/storage/sqlite3/exponential.db ".restore '/backup/db.sqlite'"`; never copy over a file in use |
   | 2. PostgreSQL | `pg_restore -U USER -d DATABASE --clean /backup/db.dump` |
   | 3. MongoDB | `mongorestore --uri="..." --drop /backup/mongo` |
   | 4. MariaDB | `mariadb -u USER -p DATABASE < /backup/db.sql` |
   | 5. Oracle | `php bin/php/console ext:ezoracle:datapump --import=db.dmp`, or Oracle's `impdp` |
   | 6. MySQL | `mysql -u USER -p DATABASE < /backup/db.sql` |

   Sources: [10.9](../../../install/10-after-installing.md#109-backups-and-restore) and
   [Database support](../../database-support.md#backup-and-restore). `-p` makes the client ask for the password; never
   write it on the command line.

2. **The files**: unpack `var/storage` (and `var/<site>/storage`), and if needed `settings/override` and
   `settings/siteaccess`, over the installation.
3. **Owners and modes**: the files must belong to the site's user as the engine or PHP-FPM pool expects
   ([Writable directories and permissions](../basics/writable-directories-and-permissions.md)).
4. **Caches and autoloads**:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   ```

5. **Start Velocity** and clear its response cache:

   ```bash
   php bin/php/console exp:velocity start --allow-root-user
   php bin/php/console exp:velocity cache clear --apply --allow-root-user
   ```

   Under PHP-FPM, reload the site's PHP-FPM service.

## Verify and reopen

1. Open the front page and the administration through a hosts-file entry or with maintenance mode letting your
   address through (`--allow-ip`, see [Maintenance mode](../operations/maintenance-mode.md)).
2. Sign in, open recently changed content, upload a test image, run a search.
3. Check `var/log/error.log` for new errors: `./console exp:logs --tail=var/log/error.log --lines=50`.
4. Maintenance off:

   ```bash
   php bin/php/maintenance.php off --allow-root-user
   ```

## After the incident

- [ ] Tell the customer what was restored, from which time, and what must be published again.
- [ ] If the search index looks incomplete, rebuild it (`php bin/php/updatesearchindex.php`; see [Search backends](../basics/search-backends.md)).
- [ ] Record how long the restore took: that is your real recovery time for the SLA ([SLA options](../business/sla-options.md)).
- [ ] Keep the copy of the damaged state as long as your policy says, then delete it.

## Related pages

- [Restore and restore tests](../operations/restore-and-restore-tests.md)
- [Database backups per engine](../operations/database-backups-per-engine.md)
- [Compromised site](compromised-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
