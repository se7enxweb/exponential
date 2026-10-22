---
title: Schedules and cron in 7x SQLI Import
audience: admin, integrator, developer
category: integration
order: 30
summary: How 7x SQLI Import starts imports by itself - the schedule kinds from once to cron expressions, time zones and daylight saving time, missed runs, failure e-mails, and the cron parts sqliimport and expqueue that must run every minute.
keywords: sqliimport, schedule, cron, cron expression, interval, hourly, daily, weekly, monthly, time zone, daylight saving time, catch up, notify, runcronjobs, sqliimport_run, sqliimport_cleanup, tick
type: help
---
# Schedules and cron in 7x SQLI Import

## Contents

1. [Schedule kinds](#schedule-kinds)
2. [Time zones](#time-zones)
3. [Missed runs and e-mails](#missed-runs-and-e-mails)
4. [The cron parts](#the-cron-parts)

## Schedule kinds

An import can have several schedules:

| Kind | Runs |
|---|---|
| Once | At a date and time |
| Interval | Every N minutes, at least 5 |
| Hourly | Every hour at a minute |
| Daily | Every day at a time |
| Weekly | Every week on a weekday at a time |
| Monthly | Every month on a day at a time (the last day in a shorter month) |
| Cron | A five-field cron expression, such as `30 2 * * *` or `0 6 * * mon-fri` |

Cron expressions support lists, ranges, steps, the names of months and days, and the shortcuts `@hourly`, `@daily`,
`@weekly`, `@monthly` and `@yearly`.

In 2.0.0-alpha.1 schedules come from the migration of 1.x scheduled imports, or are created in PHP. The schedule
editor comes with the import editor in 2.0.0-beta.1.

## Time zones

Every schedule has a time zone (`UTC` by default). "Daily at 02:30" in `Europe/Berlin` stays at 02:30 local time all
year. On the day clocks jump forward, a time that does not exist is skipped; on the day they go back, a time that
exists twice runs once.

## Missed runs and e-mails

- When the cron did not run for a while, a schedule runs once to catch up (the default), or waits for its next time
  if it is set to skip missed runs.
- If the import is still running when its schedule is due, that run is not started; the schedule waits for its next
  time.
- A schedule can name e-mail addresses that are told when one of its runs fails or ends with failed records.

## The cron parts

Two cron parts must run every minute:

```bash
* * * * * cd /path/to/exponential && php runcronjobs.php -q expqueue
* * * * * cd /path/to/exponential && php runcronjobs.php -q sqliimport
```

- `sqliimport` queues the runs of due schedules and of pending 1.x import requests, ends runs whose jobs ended, and
  removes old runs. It only queues; it does no import work itself.
- `expqueue` runs the workers that process the runs. expqueue can also run as a systemd service instead.

`./console ext:sqliimport:tick` runs one round of the `sqliimport` part by hand and prints what it did.

The 1.x cron parts `sqliimport_run` and `sqliimport_cleanup` still work but are deprecated. With the job queue
installed, `sqliimport_run` hands the 1.x imports to the 2.x engine. Replace them with `sqliimport`.

Next: [Upgrading from 1.x](upgrading-from-1x.md).
