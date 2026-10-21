---
title: Monitoring integrations
audience: integrator, admin, hosting
category: integration
order: 390
summary: Knowing that an integration works before the customer notices that it does not - what to watch (runs, freshness, backlog, errors, credentials), the signals Exponential provides (summary lines, cronjob pages, the audit trail, the log viewer, system information), alert rules, and a monitoring plan template.
keywords: monitoring, alerting, health check, freshness, backlog, outbox, dead letter, cronjob, summary line, log viewer, exp:logs, audit, exp:audit, system information, systeminfo, API key expiry, uptime, SLA
type: help
---
# Monitoring integrations

This page is for operators and integrators who keep integrations running. An integration usually fails silently: the
cronjob stops, a key expires, the other system changes a field, and the site simply shows old data. Monitoring turns
those silent failures into alerts. The server side of monitoring is in [Monitoring overview](../hosting/operations/monitoring-overview.md)
and [Health checks and uptime](../hosting/operations/health-checks-and-uptime.md).

## Contents

1. [What to watch](#what-to-watch)
2. [Signals Exponential provides](#signals-exponential-provides)
3. [Checks to set up](#checks-to-set-up)
4. [Alerts that help](#alerts-that-help)
5. [A monitoring plan](#a-monitoring-plan)

## What to watch

| Signal | Question | Example threshold |
|---|---|---|
| **Run happened** | Did the nightly import run? | Alert when no summary line for 26 hours |
| **Run succeeded** | Did it finish without failures? | Alert on `failed > 0` or a non-zero exit code |
| **Freshness** | Is the newest imported change recent? | Alert when the newest `modified` of imported objects is older than 2 days |
| **Backlog** | Is the outbox growing? | Alert when more than 500 unsent rows or the oldest unsent is older than 1 hour |
| **Dead letters** | Did anything give up? | Alert on any |
| **Errors** | New entries in `error.log` naming your extension | Alert on a rate, not each line |
| **Credentials** | Will a key expire soon? | Warn 14 days before; Setup > oAuth admin > API keys filters "expiring within seven days" |
| **Volume** | Is today's number of records plausible? | Warn when it differs from the average by more than a set share |

## Signals Exponential provides

| Signal | Where |
|---|---|
| Your summary lines | `var/log/<your log>.log` (`eZLog::write()`), searchable with `php bin/php/console exp:logs --tail=... --search=...` |
| Cronjob runs and output | **Setup > Cronjobs**: last runs, issues, the output of a running part ([Cronjobs](../../guides/cronjobs.md)) |
| Errors with a reference | `error.log`, the log viewer at **Setup > Log files**, `exp:logs --reference=ERR-...` |
| Workflow processes waiting | **Setup > Workflow processes**; a growing list means the workflow cronjob is not running |
| API key use and refusals | Audit events `access.apikey.use.failed` with reasons; `php bin/php/console exp:audit tail --channel=access` |
| Audit sinks | `php bin/php/console exp:audit sinks list`: ready or not, how much is spooled |
| Job queue | **Setup > Job queue** and `php bin/php/console ext:expqueue:status` when `expqueue` is used |
| Installation health | **Setup > System information** and `php bin/php/systeminfo.php --json` ([System information](../../guides/system-information.md)) |
| Maintenance mode | `php bin/php/maintenance.php status` |

## Checks to set up

A small check script of your extension, run by the monitoring system (or by cron with its output mailed to the
operators), turns these signals into a status:

```php
<?php
// extension/myintegration/bin/php/check.php: exit 0 OK, 1 WARNING, 2 CRITICAL; one line of output
// (bootstrap with eZScript as in the CLI chapter, then:)
$db = eZDB::instance();
$pending = $db->arrayQuery( 'SELECT COUNT(*) AS n, MIN(created) AS oldest FROM myintegration_outbox WHERE sent = 0' );
$n = (int)$pending[0]['n'];
$age = $pending[0]['oldest'] ? time() - (int)$pending[0]['oldest'] : 0;
$status = ( $n > 500 || $age > 3600 ) ? 2 : ( $n > 100 ? 1 : 0 );
$cli->output( array( 'OK', 'WARNING', 'CRITICAL' )[$status] . " outbox pending=$n oldest_age={$age}s" );
$script->shutdown( $status );
```

Expose the same check through a protected endpoint only if the monitoring system cannot run commands on the server.

## Alerts that help

- Say **what** stopped and **since when**, and link the runbook section ("Product import: no run since 02:15; see
  runbook 3.2").
- Alert the people who can act: the operator for a stopped cronjob, the customer's ERP team for a broken source file.
- Avoid alert storms: one alert per condition, repeated at a slow rhythm, cleared automatically when the condition ends.
- Keep warnings out of the night; page only for what cannot wait until morning.

## A monitoring plan

```text
Integration: ERP product import                        Owner: ops@example.com
Check                     How                                  Warning        Critical       Who
Run happened              summary line in myintegration.log    > 25 h         > 26 h         operator
Run succeeded             exit code / failed=               skipped > 50   failed > 0     operator
Freshness                 newest modified of erp-product-*     > 1 day        > 2 days       customer ERP team
Outbox backlog            check.php                            > 100          > 500 / 1 h    operator
Key expiry                API keys page                        < 14 days      < 3 days       operator
Error rate                error.log lines naming myintegration > 10 / h       > 100 / h      developer
```

## Related pages

- [Error handling and logging](error-handling-and-logging.md)
- [Monitoring overview for hosts](../hosting/operations/monitoring-overview.md)
- [KPIs from logs and the audit](../leadership/kpis-from-logs-and-audit.md)
- [Handover of an integration](handover-of-an-integration.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
