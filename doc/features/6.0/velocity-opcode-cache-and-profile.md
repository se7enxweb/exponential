# Velocity and the opcode cache: why a page costs what it does, and how to measure it

This page is for administrators who run Exponential Velocity and want to know where the CPU time of a page goes. Two
changes belong together (added 2026-10-02):

- Velocity's workers now keep the files the kernel rewrites (INI caches, override caches, compiled templates) in the
  opcode cache.
- A diagnostic you switch on writes one line per request that says how the request used the opcode cache, so two
  engines can be compared with numbers.

## What was wrong

PHP's opcode cache does not keep a script that was changed less than `opcache.file_update_protection` seconds before
the **request's start**. Under PHP-FPM every request starts afresh, so only files written in the last two seconds are
skipped. A Velocity worker lives for hours, and its "request start" is the time the worker started. Every file written
afterwards (the caches regenerated after a deploy or a cache clear) counted as too new for the whole life of the
worker, and was compiled again on every include.

Measured on the alpha installation: the cache served 2.7% of the includes under Velocity against 97% under PHP-FPM, and
a rendered page cost 644 ms of CPU against 166 ms.

## What changed

`settings/velocity.ini` passes `opcache.file_update_protection=0` (next to `opcache.revalidate_freq=0`) to the server
process. A file that is still being written is not kept by the cache: the server itself reads an include whole and
drops the cache's copy of a file changed in the last two seconds, so nothing half written is served.

No setting of yours needs to change. A restart of Velocity applies it (workers keep what they loaded at warm-up).

| File | Block | Key | Shipped value | Scope |
|---|---|---|---|---|
| `settings/velocity.ini` | server options | `IniOptions[]=opcache.file_update_protection=0` | `0` | the Velocity server process |
| `settings/velocity.ini` | server options | `IniOptions[]=opcache.revalidate_freq=0` | `0` (check every include) | the Velocity server process |
| `settings/velocity.ini` | server options | `IniOptions[]=opcache.memory_consumption=256` | 256 (MB) | the Velocity server process |

Check:

```bash
grep -n "opcache\." settings/velocity.ini
```

## Measure it with the opcode cache profile

The profile is **off** unless the file `var/tmp/opcache_profile.on` exists (the kernel does one `file_exists()` per
request to find out). With the file present, every request appends one JSON line to `var/tmp/opcache_profile.log`.

1. Switch it on and send a few requests to each engine:

```bash
touch var/tmp/opcache_profile.on
curl -s -o /dev/null https://<site>/
tail -n 3 var/tmp/opcache_profile.log
```

2. Read the line of the second and later requests of a process. A worker that keeps its files in the cache shows
   `included_not_cached` close to 0; one that compiles them again shows many. Compare it with a PHP-FPM line of the
   same address.
3. Switch it off again by moving the `.on` file out of `var/tmp/`. The log grows with every request while it is on.

| Field | Meaning |
|---|---|
| `sapi`, `pid`, `uri`, `time` | Which engine process answered which address |
| `hits_this_request`, `misses_this_request` | The change of OPcache's hit and miss counters during this request |
| `cached_scripts`, `included_files`, `included_not_cached` | How many scripts OPcache holds, how many files the request included, how many of those OPcache did not hold (each is compiled again on every include) |
| `served_most_this_request` | The ten scripts OPcache served most during this request |
| `not_cached_sample`, `not_cached_probe` | The first files not held, with real path, age and what OPcache says about them |
| `opcache_ini` | The OPcache settings the process runs with (`file_update_protection`, `revalidate_freq`, `validate_timestamps`, `use_cwd`) |

## Limits

- Figures belong to the process that answered; compare several lines, not one.
- The profile needs the `opcache` extension (`function_exists('opcache_get_status')`).

## Related pages

- [Velocity persistent worker server](velocity-persistent-worker-server.md)
- [Velocity response cache](velocity-response-cache.md)
- [Static cache generator](static-cache-generator.md)
- [Velocity engines upgrade notes](../../bc/6.0/velocity-engines.md)
- [Velocity engine settings and programs](../../specifications/6.0/velocity-engine-settings.md)
- [Velocity worker pool and compatibility layer](../../specifications/6.0/velocity-worker-pool.md)
- [Changelog: Exponential Velocity engine](../../changelogs/extensions/exponential-velocity.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- [October 2026 chronicle](../../history/2026/2026-10.md)
