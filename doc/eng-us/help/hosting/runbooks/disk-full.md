---
title: "Runbook: disk full"
audience: hosting, admin
category: hosting
order: 40
summary: What to do when the disk of an Exponential site fills up: find what grew (storage, caches, logs, database, backups), free space safely with the cache and log commands, never delete storage or settings, and prevent a repeat.
keywords: runbook, disk full, no space left, storage, var, cache, logs, log rotation, prune, archives, database, WAL, backups, quota, du, df
type: help
---
# Runbook: disk full

For on-call staff when a server or a customer account runs out of disk space. Symptoms: uploads fail, publishing
fails, pages show errors, logs stop growing, the database refuses writes.

## Contents

1. [First five minutes](#first-five-minutes)
2. [What may be removed, and what never](#what-may-be-removed-and-what-never)
3. [Freeing space safely](#freeing-space-safely)
4. [After the incident](#after-the-incident)

## First five minutes

1. Confirm and find the file system:

   ```bash
   df -h /var/www/vhosts/<domain>
   ```

2. Find what grew, in the installation:

   ```bash
   du -sh var/* | sort -h | tail -15
   du -sh var/storage var/cache var/log var/vc 2>/dev/null
   ```

3. Check outside the installation: backups written to the same disk, the database server's data directory, system
   logs, PHP session files.

## What may be removed, and what never

| Path | Remove? | Why |
|---|---|---|
| `var/cache`, `var/<site>/cache` | yes, through the cache command | "Caches ... need no backup: they are regenerated" ([10.9](../../../install/10-after-installing.md#109-backups-and-restore)) |
| Rotated log archives (`*.log.1` ... and compressed archives) | yes, through `exp:logs --prune` | they are old copies |
| `var/log/*.log` (the current logs) | empty them through `exp:logs --truncate`, not by deleting | keeps the files and their permissions |
| `var/storage`, `var/<site>/storage` | **never** | uploads and images: customer data |
| `settings/` | **never** | the installation's configuration |
| `var/storage/sqlite3/` | **never** by hand | the SQLite database and its `-wal` and `-shm` files |
| Old backups on the same disk | by your retention policy | and move future backups off the machine |

## Freeing space safely

**Caches.** Clear them with the cache command; it knows the paths of every site:

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
```

Pages are slower until the caches are warm again; warm them with `php bin/php/warm.php --allow-root-user` when there is
space.

**Log archives.** See them, plan, then prune:

```bash
./console exp:logs --archives                 # each log with its archives and their sizes
./console exp:logs --prune --dry-run          # the archives beyond the site.ini [FileSettings] limits
./console exp:logs --apply --prune                    # remove them
./console exp:logs --apply --compress                 # compress the archives from .2 on
```

**A log that grows fast.** Find out why before you empty it; a log that grows by megabytes a minute is a symptom.
`./console exp:logs --list` shows lines a minute per file. Then:

```bash
./console exp:logs --apply --truncate=debug           # rotate it (the content stays in .1)
```

**Velocity's logs** are under `var/vc/qbix/log` and rotate with a date in the name; old ones can be archived or
removed by your log policy ([Log viewer and rotation](../operations/log-viewer-and-rotation.md)).

**SQLite.** A large `-wal` file shrinks at a checkpoint; `VACUUM` reclaims space after large deletions but needs free
space of its own. Read [ANALYZE, VACUUM and the WAL file](../../../install/09-databases.md#analyze-vacuum-and-the-wal-file)
before you run anything.

> **Warning**
> Do not run `rm -rf` on directories under `var/` by hand during an incident. A wrong path removes customer uploads,
> and a cache directory removed while workers run can leave files with the wrong owner. Use the commands above.

## After the incident

- [ ] Check that the site works: upload, publish, sign in.
- [ ] Check the logs that stopped during the incident; something may have failed silently (a cron job, a backup).
- [ ] Set up a disk alert below the level you reached ([Monitoring overview](../operations/monitoring-overview.md)).
- [ ] Review log rotation and backup placement ([Log viewer and rotation](../operations/log-viewer-and-rotation.md),
      [Backup schedules and retention](../operations/backup-schedules-and-retention.md)).
- [ ] If customer data grew past the plan, tell the customer ([Billing and metering options](../business/billing-and-metering-options.md)).

## Related pages

- [Storage layout](../operations/storage-layout.md)
- [Slow site](slow-site.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
