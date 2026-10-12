# expqueue: the job queue

Read this page if an extension you use queues work (Exponential Intelligence does) or if you want to run your own
background jobs. The extension is the Composer package `se7enxweb/expqueue`, required by `composer.json` from 6.0.15
on (`~0.1.0`), so it is in `extension/expqueue` on every installation and stays inactive until it is activated. Its
full documentation lives with it, in the [expqueue repository](https://github.com/se7enxweb/expqueue).

## In short

| | |
|---|---|
| What it does | A generic, database-backed job queue. Producers queue a job with one call; workers run it on the command line, from cron or as a systemd service, never inside a web request. Extensions register job types and their handlers in `expqueue.ini`. |
| Scheduling | Seven priority classes with weights, fair scheduling with aging and a minimum share per class, quotas per user and site, concurrency limits per resource that adapt to rate-limit hints |
| Reliability | Leases with a reaper, retries with backoff, a dead letter, idempotency keys, dependencies, not-before times, job groups with progress, cancel, pause and resume, a result checkpoint for handlers |
| Admin | Setup > Job queue (dashboard, jobs, groups, controls); policies `expqueue/view` and `expqueue/manage` |
| Engines | MySQL/MariaDB, PostgreSQL, SQLite, Oracle and MongoDB |
| Release | [0.1.0](https://github.com/se7enxweb/expqueue/releases/tag/v0.1.0) |

expqueue is separate from the kernel's [content jobs](../../../bc/6.0/content-jobs.md), which run large content
operations (removes, copies, moves, package installs, audit exports) as jobs of their own.

## Switch it on

1. Activate it globally, so producers on every siteaccess and the workers see it, in
   `settings/override/site.ini.append.php`: `[ExtensionSettings] ActiveExtensions[]=expqueue`.
2. Create the tables (idempotent; also the upgrade):

   ```bash
   ./console ext:expqueue:install-schema
   ```

3. `php bin/php/ezpgenerateautoloads.php -e` and `./console exp:velocity deploy` (or clear the INI, template and
   translation caches and reload PHP-FPM by hand).
4. A worker, as the site user, every minute:

   ```cron
   * * * * * cd /path/to/installation && php runcronjobs.php -q expqueue >> var/log/cronjob-expqueue.log 2>&1
   ```

   When the job types of an extension are active for some siteaccesses only (expai activated per admin siteaccess),
   add `-s <that siteaccess>`, or the worker does not know them. A systemd service
   (`./console ext:expqueue:service install`) can run next to cron for shorter waits.
5. Check: `./console ext:expqueue:enqueue expqueue.noop`, `./console ext:expqueue:work --once`,
   `./console ext:expqueue:status`, or Setup > Job queue > "Send a test job".

The complete steps: the [installation guide](https://github.com/se7enxweb/expqueue/blob/main/INSTALL.md); the API for
producers, scheduling, workers and settings: [the documentation](https://github.com/se7enxweb/expqueue/blob/main/doc/index.md).

## Related pages

- [expai](expai.md), Exponential Intelligence, which runs its queued work here
- [Content jobs](../../../bc/6.0/content-jobs.md)
- [Installing Exponential, section 10](../../../INSTALL.md#10-after-installing)
- [Extensions index](README.md)
