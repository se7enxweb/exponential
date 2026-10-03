# Velocity scheduler

*Applies to: Exponential Velocity 0.0.4.x. First release: 21 July 2026. History: [July 2026](../../history/velocity/2026-07.md), [25 to 30 September](../../history/velocity/2026-09e.md) (tasks give up root). Reference: [Engine settings](../../specifications/6.0/velocity-engine-settings.md). Overview: [Velocity web server](velocity-web-server.md).*

## What it is

A small cron inside the server. It runs a **handler** at an interval or at fixed times of day, without a cron table, without a second process manager, and without blocking the requests the server answers. Each run is forked from the server, so a slow task never holds up the event loop, and a task that fails is logged and ends there.

It is a feature of the Qbix handler model: a task names a handler (`tasks/cleanup`), which the server dispatches with `Q::event()` exactly as it dispatches an event for a web request. That means it needs the engine's application layer (`--app`, or an application that provides `handlers/`). It does not run an arbitrary command-line PHP script; for that, keep using the system's cron (Exponential's own periodic jobs run through `php runcronjobs.php`).

## Why use it

- One place for periodic work that belongs to the application: expiring sessions, rotating files, sending a report at 09:00.
- No lost or doubled runs around a restart: a restart in the minute a time-based task is due does not fire it twice, and an interval task waits one full interval before its first run.
- A task is marked as run before it is forked: if the server crashes at that moment the run is skipped, never doubled.

## Configure it

Settings live under `Q.scheduler` in a site file or the `--config` JSON. Each key is a task name.

```json
{ "Q": { "scheduler": {
    "cleanup":         { "handler": "tasks/cleanup", "every": 3600 },
    "daily-report":    { "handler": "tasks/report",  "times": ["09:00"] },
    "business-check":  { "handler": "tasks/check",   "times": ["09:00", "12:00", "17:00"], "weekdays": ["mon", "wed", "fri"] },
    "monthly-invoice": { "handler": "tasks/invoice", "times": ["00:00"], "monthdays": [1] }
} } }
```

| Field | Default | What it does |
|---|---|---|
| `handler` | required | Handler path, dispatched by `Q::event()`; a task without one is ignored |
| `every` | none | Run every this many seconds, counted from start |
| `times` | none | Run at these `HH:MM` times (24 hours, the server's PHP time zone) |
| `weekdays` | every day | With `times`: only on these days (`mon` to `sun`, or the full name) |
| `monthdays` | every day | With `times`: only on these days of the month (1 to 31) |

A task needs `every` or `times`; `weekdays` and `monthdays` apply to `times` tasks only. The scheduler looks at the clock once a second.

The handler is a function in `handlers/tasks/cleanup.php`; it receives the task name and a flag:

```php
<?php
function tasks_cleanup(&$params, &$result)
{
    // $params['task'] is "cleanup", $params['scheduled'] is true
    MyApp_Sessions::expireOld();
}
```

## Built-in tasks

The server adds two tasks of its own when it needs them (names beginning with an underscore): `_cacheSweep` removes expired entries of the [response cache](velocity-response-cache.md) (`Q.web.cache.sweep.every`, default 300 seconds) and `_certRenewal` renews certificates of [automatic hosts](velocity-https-certificates.md) in the background. Do not name a task of your own with a leading underscore.

## Safety

- A server started as root gives up root in the forked child before the handler runs ([worker pool](../../specifications/6.0/velocity-worker-pool.md), "The user workers run as"); a child that cannot switch is ended.
- A forked task is tracked like a worker, so `Q.webserver.requestTimeout` (default 30 seconds) applies to it.
- Without `pcntl` (Windows) a task runs inside the server process and blocks the event loop for as long as it takes. Keep such tasks short.

## Check it

1. Start the server in the foreground with a task that has `"every": 5`, handler writing a line to a file.
2. Wait ten seconds; the file has one line, not two (the first run comes after one interval).
3. A handler that throws writes `scheduler: <name> failed: <message>` to the server's error output and the next run still happens.

To check the configuration without starting a server: `php sbin/qbixserver.php --root=web -t --config=<site file>`.

## Limits

- Tasks run only while the server runs; there is no catch-up for missed times.
- Times are matched to the minute and use the server's PHP time zone.
- A task that runs longer than its interval can overlap itself; the scheduler does not wait for the previous run.

## See also

- [Velocity web server](velocity-web-server.md) and [Engine settings](../../specifications/6.0/velocity-engine-settings.md).
- [July 2026 chronicle](../../history/velocity/2026-07.md), where the scheduler arrived (commit `62d9b23`).
- [Changelog](../../changelogs/extensions/exponential-velocity.md).
