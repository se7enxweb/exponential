---
title: Sizing memory, CPU and disk
audience: hosting, admin
category: hosting
order: 170
summary: The memory, CPU and disk figures the Exponential repository actually states (PHP memory limits, Velocity workers, OPcache, APCu, SQLite page cache, MongoDB, disk health thresholds, log rotation), what grows on disk, and how to measure a real site instead of guessing.
keywords: sizing, capacity planning, memory, RAM, CPU, disk, memory_limit, Velocity workers, Workers, SpareWorkers, PSS, RSS, OPcache, APCu, apc.shm_size, SQLite cache_size, MongoDB, var/storage, var/cache, var/log, LogFileMaxSize, exp:benchmark, System information, hosting plans
type: help
---
# Sizing memory, CPU and disk

This page is for system administrators and product managers at a hosting company who size servers and plans for
Exponential sites. It answers: which figures the project's documentation and settings actually give, what consumes
memory, CPU and disk on a running site, and how to measure a real site. It gives no figures the repository does not
state; [chapter 2.10](../../../install/02-requirements.md#210-memory-cpu-and-disk) says the same: "The documentation
gives few hard numbers, and this book does not invent others."

## Contents

1. [The figures the repository gives](#the-figures-the-repository-gives)
2. [Memory](#memory)
3. [CPU](#cpu)
4. [Disk](#disk)
5. [Measure, do not guess](#measure-do-not-guess)
6. [Turning measurements into plans](#turning-measurements-into-plans)
7. [Checklist](#checklist)
8. [Related pages](#related-pages)

## The figures the repository gives

| Item | Value | Source |
|---|---|---|
| PHP `memory_limit`, installer floor | 64M | `settings/setup.ini`, `[memory_limit] MinMemoryLimit` |
| PHP `memory_limit`, health check | below 128M a failure, below 256M a warning | [System information](../../../guides/system-information.md#3-health-checks) |
| PHP `memory_limit` under FrankenPHP | 512M shipped | `settings/velocity.ini`, `[FrankenPHPSettings] IniOptions[]` |
| One Velocity worker | about 10 MB of private memory (measured) | `settings/velocity.ini`; [Velocity web server](../../../features/6.0/velocity-web-server.md) |
| Velocity pool | `Workers=4` shipped; the engine's own default is what fits in RAM, at most 8 per core and 64, never fewer than 4 | `settings/velocity.ini`, [chapter 2.10](../../../install/02-requirements.md#210-memory-cpu-and-disk) |
| OPcache | `memory_consumption=256`, `max_accelerated_files=20000`, `interned_strings_buffer=32` | chapter 10.12 and `velocity.ini [PHPSettings]` |
| APCu for Velocity | `apc.shm_size=256M` (reserved, not used, until it fills) | `velocity.ini [PHPSettings]` |
| SQLite page cache | up to 64 MB per process that opened the database; 256 MB memory map | [Database support](../../database-support.md#performance-and-scaling) |
| SQLite `VACUUM` | about twice the database's size in free disk | [chapter 9.2](../../../install/09-databases.md#analyze-vacuum-and-the-wal-file) |
| A MongoDB installation | RAM 512 MB minimum, 2 GB recommended; disk 2 GB minimum, 10 GB recommended | [porting reference](../../../bc/6.0/MONGODB_KERNEL_SUPPORT_EXPANSION.md), section 24.1 |
| Free disk, health check | under 1 GB or 5 % a failure; under 5 GB or 10 % a warning | [System information](../../../guides/system-information.md#3-health-checks) |
| Log rotation | each log rotated at `LogFileMaxSize=2097152` (2 MB), `LogFileRotateCount=3` kept | `settings/site.ini`, `[FileSettings]` |
| A kernel clone with history | about 370 MB | [chapter 3.2](../../../install/03-getting-the-code.md#a-git-clone-and-composer-install-recommended) |

## Memory

What uses memory on a server running Exponential sites:

- **PHP processes.** Each PHP-FPM child or Velocity worker up to its `memory_limit` at peak; package installs, imports
  and image work need the most. Plan pool sizes so that the busiest moment fits ([PHP-FPM tuning](../performance/php-fpm-tuning.md)).
- **Velocity workers** hold about 10 MB of private memory each from the moment they are forked, idle or not. With
  `SpareWorkers` the pool keeps only that many when quiet and grows to `Workers` under load, retiring idle workers
  after `IdleWorkerTimeout` seconds.
- **Shared caches.** OPcache (`opcache.memory_consumption`) and APCu (`apc.shm_size`) reserve shared memory. PHP-FPM
  takes them from its `php.ini`; Velocity sets its own in `velocity.ini [PHPSettings] IniOptions[]`, because the
  command-line default for APCu is 32 MB, too small for its response cache and the SQL query cache together.
- **The database.** SQLite's page cache counts per process that used it: `SQLitePragmas[]=cache_size=-16384` lowers
  it to 16 MB, which helps on servers with many small SQLite sites. Database servers need their own sizing.
- **Optional services.** Solr, Valkey or Redis, if a plan includes them.

> **Note**
> Read Velocity's memory as its control panel shows it: the proportional set size (PSS) of the parent and its workers.
> The resident set size (RSS) of each forked worker counts the memory they share again and again, and adds up to far
> more than the machine really uses ([chapter 2.10](../../../install/02-requirements.md#210-memory-cpu-and-disk)).

## CPU

- **Cached pages cost little.** Velocity's response cache answers anonymous visitors in the server process without a
  worker. `settings/velocity.ini` records a measurement: one server answers cached pages at about 0.3 ms of CPU each,
  about 3,500 a second on a 12-core machine; with `Instances` above 1 the kernel spreads connections across servers
  (two measured 6,600 to 6,900, four 12,200 to 13,100). Renders are bounded by the workers, not by this.
- **Rendered pages** (first views after a clear, signed-in editors, search) are where CPU goes. Keep the content view
  cache and template-block cache on (both shipped), and leave the static cache off
  ([chapter 10.12](../../../install/10-after-installing.md#1012-performance-tuning)).
- **OPcache matters.** Under Velocity, `opcache.file_update_protection` above 0 made a render take about four times the
  CPU it takes under PHP-FPM; the shipped `velocity.ini` sets it to 0
  ([Velocity opcode cache](../../../features/6.0/velocity-opcode-cache-and-profile.md)).
- **FrankenPHP** threads default to twice the CPU cores when `[FrankenPHPSettings] Workers` is empty.
- **Cron and image work** add bursts: image variations are generated on first use, and the cronjobs run on schedule.

## Disk

| What grows | Where | How to keep it in check |
|---|---|---|
| Uploaded files and image variations | `var/storage` (per site `var/<site>/storage`) | grows with content; the main factor of a plan's disk |
| Caches | `var/cache` (per site `var/<site>/cache`) | cleared and rebuilt; can live on a memory file system ([multi-site paths](../../../features/6.0/multi-site-log-and-cache-paths.md)) |
| Logs | `var/log`, `var/vc/` for Velocity | rotation (`LogFileMaxSize`, `LogFileRotateCount`, `LogFileCompress`), plus the system's logrotate for cron logs |
| The SQLite database | `var/storage/sqlite3/` | room for the database twice over, for `VACUUM` and backups |
| Backups | off the machine, ideally | never only on the same disk |
| Code | the installation, `vendor/`, extension clones | Git clones with history take more than archives |

The disk check of the administration (**Setup > System information**) fails under 1 GB or 5 % free and warns under
5 GB or 10 %; use the same thresholds in your monitoring. See also the [disk full runbook](../runbooks/disk-full.md).

## Measure, do not guess

```bash
php bin/php/console exp:benchmark --save=var/benchmark/base.json --allow-root-user
php bin/php/console exp:benchmark kernel --repeat=30 --allow-root-user
php bin/php/console exp:system:info --json --allow-root-user
du -sh var/storage var/cache var/log
```

`exp:benchmark` requests the front page, menu pages, the search page and the admin login page and prints median, p90
and p99; it is polite by default against other hosts ([Benchmarking](../../../guides/benchmarking.md)).
`exp:system:info` prints the facts and health checks of **Setup > System information** (memory limit, OPcache and
APCu, disk, machine memory and load). Velocity's [control panel](../../../features/6.0/velocity-control-panel.md) shows
the memory of the pool.

> **Tip**
> Build a reference site from your most common plan, measure it with these commands on your hardware, and size from
> that. A figure measured on your own servers is worth more than any rule of thumb.

## Turning measurements into plans

The repository gives no plan sizes and no prices; these are the dimensions a host can choose to put limits on, each
measurable with the commands above:

| Dimension | Measured with |
|---|---|
| Disk for `var/storage` | `du -sh var/storage` |
| PHP memory per process | `memory_limit` and the pool's peak |
| Number of PHP workers | the pool size or Velocity's `Workers` |
| Database size | **Setup > System information**, Database card |
| Upload size | `upload_max_filesize`, `post_max_size` |

## Checklist

- [ ] `memory_limit` 256M or more for production pools.
- [ ] Velocity's `Workers` set explicitly; `SpareWorkers` considered on busy multi-site servers.
- [ ] OPcache and APCu sizes set for PHP-FPM and for Velocity separately.
- [ ] Velocity memory read as PSS.
- [ ] Disk monitoring at the System information thresholds.
- [ ] A reference site benchmarked on your own hardware.

## Related pages

- [Requirements at a glance](requirements-at-a-glance.md)
- [Caching layers](../performance/caching-layers.md) and [Benchmarking a hosted site](../performance/benchmarking-a-hosted-site.md)
- [Velocity workers and pools](../servers/velocity-workers-and-pools.md)
- [Designing hosting plans](../business/designing-hosting-plans.md)
- [Chapter 2.10: Memory, CPU and disk](../../../install/02-requirements.md#210-memory-cpu-and-disk)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
