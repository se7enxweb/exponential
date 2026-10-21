---
title: "Observability: logs, debug bar and audit trail"
audience: software-engineer, admin, developer
category: software-engineering
order: 400
summary: What an Exponential installation tells you about itself and where - the kernel logs and their rotation, the request logs 404.log and security.log, the log viewer and exp:logs, error references, the debug bar, the tamper-evident audit trail, system information, health checks and Velocity's status - and how to combine them when you investigate.
keywords: observability, logs, error.log, 404.log, security.log, log viewer, exp:logs, error reference, debug bar, audit trail, exp:audit, system information, monitoring, health check, metrics, software engineer
type: help
---
# Observability: logs, debug bar and audit trail

This page is for engineers who need to see what a running installation is doing: to debug a page, to investigate an
incident, or to wire the system into monitoring. Exponential has no metrics endpoint of its own; what it has is
structured logs, a log viewer with search, a debug bar, a tamper-evident audit trail and status commands.

## Contents

1. [The signals at a glance](#the-signals-at-a-glance)
2. [Logs](#logs)
3. [The log viewer and exp:logs](#the-log-viewer-and-explogs)
4. [The debug bar](#the-debug-bar)
5. [The audit trail](#the-audit-trail)
6. [Health and status](#health-and-status)
7. [An investigation, end to end](#an-investigation-end-to-end)
8. [Privacy in the signals](#privacy-in-the-signals)

## The signals at a glance

| Signal | Where | Question it answers |
|---|---|---|
| Kernel logs | `var/log/error.log`, `warning.log`, `notice.log`, `debug.log`, `strict.log`, `deprecated.log`, `auth.log` | what went wrong in the code |
| Request logs | `var/log/404.log`, `var/log/security.log` | what was not found, what looked like a probe or an attack |
| Per-site logs | `var/<site>/log/*.log` | the same, per site, when sites have their own log directory |
| Error reference | the error page and `error.log` (`ERR-...`) | which log lines belong to a visitor's report |
| Debug bar | the page itself, for listed addresses | why this page is slow or wrong |
| Audit trail | Audit tab, `./console exp:audit` | who did what, and proof the record is intact |
| System information | Setup > System information | versions, engine, database, caches |
| Velocity status | `./console exp:velocity status` | is the server up, which engine, which workers |

## Logs

- The debug logs and `eZLog` rotate when a file outgrows `site.ini [FileSettings] LogFileMaxSize` (2 MB), keeping
  `LogFileRotateCount` (3) archives ([Log rotation and pruning](../../features/6.0/log-rotation.md)).
- Not-found answers go to `404.log` with the reason the kernel found; what the request classifier takes for a probe,
  a traversal, an injection, an attack tool or a burst of not-found answers goes to `security.log`. Each request is
  written to one log only, and nothing is blocked by the classification ([Request logs](../../features/6.0/request-logs.md)).
- With several sites, logs can go to each site's directory or another file system
  ([Logs, INI cache and expiry timestamps per site](../../features/6.0/multi-site-log-and-cache-paths.md)).
- Code writes through `eZDebug::writeError()`, `writeWarning()`, `writeNotice()` and `eZLog`, never with `echo` or
  `var_dump` ([eZDebug and logging](../extension-development/part-8-reference/ezdebug-and-logging.md)).

## The log viewer and exp:logs

**Setup > Log files** (`setup/logs`) lists every log of the installation, follows one live, searches one file, every
kernel log or every file with their rotations, parses lines into time, client, siteaccess, request and message, groups
lines by request, and links them to the audit records of the same minute. It is read-only by default. The same from a
shell:

```bash
./console exp:logs --help
./console exp:logs --reference=ERR-0123456789
```

See [The log viewer](../../features/6.0/log-viewer.md) and [Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md).

## The debug bar

The Exp Debug bar replaces the classic debug report with a pinned bar: page time, SQL count and time, peak memory,
templates used, warnings and errors, and tabs for messages, settings, cache, timing, SQL, templates, included files,
memory and Velocity. Show it only to your own address:

```ini
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]=203.0.113.10
```

Behind a proxy, the address is only right when the proxy is listed in `TrustedProxies[]`
([The Exp Debug bar](../../features/6.0/exp-debug-bar.md), [Debug bar guide](../../bc/6.0/debug-bar.md)).

## The audit trail

Security-relevant events (sign-ins and refusals, content changes, settings writes, cache clears, deploys, orders,
VAT changes) are recorded in a hash-chained log with signed manifests, on by default. Alert rules watch for brute-force
sign-ins, an administrator role granted, settings written out of hours, mass deletes and a broken chain.

```bash
./console exp:audit status --allow-root-user
./console exp:audit tail --channel=access --name='access.session.*' --lines=3 --allow-root-user
./console exp:audit verify --allow-root-user        # exit 1 when a chain is broken
```

The `frequent` cronjob group must run for indexing, alerts and rotation ([The audit trail](../../features/6.0/audit-trail.md),
[Audit guide](../../bc/6.0/audit.md)).

## Health and status

Exponential has no single health URL. Combine a check from outside (the front page and the administration login,
because the front page may be answered from a cache while the database is down) with status commands on the server
([Health checks and uptime](../hosting/operations/health-checks-and-uptime.md), [Monitoring overview](../hosting/operations/monitoring-overview.md)).
Maintenance mode answers `503` with `Retry-After`, so a planned window is distinguishable from an outage.

## An investigation, end to end

1. A visitor reports an error page with a reference: search it in the log viewer or with `exp:logs --reference`.
2. Read every line of the same request, then the audit records of the same minute: what changed just before?
3. Reproduce with the debug bar on for your address.
4. Check `security.log` if the request looks unusual.
5. Record what you found ([Incident handling](incident-handling.md)).

## Privacy in the signals

Log viewer, request logs and audit trail mask e-mail and IP addresses, tokens, keys, session ids and cookies by
default; passwords and secrets are never recorded. Unmasking in the log viewer needs the policy `setup/unmasklogs`.
Keep it that way when you export logs into another system.

## Related pages

- [Debugging and logs](../creators/app-creators/debugging-and-logs.md)
- [Log viewer and rotation, for hosts](../hosting/operations/log-viewer-and-rotation.md)
- [Security and audit](../../guides/security-and-audit.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
