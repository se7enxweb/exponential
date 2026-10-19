---
title: Monitoring
audience: government, admin, hosting, project-manager
category: public-sector
order: 10
summary: Index of the pages on watching a public-sector Exponential site - an overview with a routine, the log viewer, error digest and log health, request and CSP logs, audit alerts and verification, cronjobs, system information, file integrity, outside uptime checks and log retention.
keywords: monitoring, logs, log viewer, error digest, log health, security log, 404 log, CSP reports, audit alerts, audit verification, cronjobs, system information, uptime, retention, index
type: help
---
# Monitoring

This folder is for in-house IT teams, security officers and service owners who must know whether a public website is
healthy, available and safe, and be able to show it. Exponential brings much of the instrumentation itself (logs with
digests, request classification, a hash-chained audit trail with alerts, health checks); these pages show how to use
it and what to add from outside.

## Contents
1. [Pages](#pages)
2. [Reading order](#reading-order)
3. [Related pages](#related-pages)

## Pages

| Page | Answers |
|---|---|
| [Monitoring overview](monitoring-overview.md) | Which question is answered where; signals for your monitoring system; a routine. |
| [The log viewer](the-log-viewer.md) | Reading, following and searching logs with personal data masked. |
| [Error digest and log health](error-digest-and-log-health.md) | The daily summary of distinct errors; disk, growth and permissions of logs. |
| [Request logs: 404 and security](request-logs-404-and-security.md) | Not-found answers and suspected attacks, apart from real faults. |
| [CSP reports](csp-reports.md) | Collecting Content Security Policy reports before enforcing. |
| [Audit alerts](audit-alerts.md) | The seven shipped rules, recipients, tuning, routing to a security team. |
| [Audit trail verification](audit-trail-verification.md) | Proving the record is intact; what to do when a chain breaks. |
| [Cronjob monitoring](cronjob-monitoring.md) | What stops when cron stops, and how to notice. |
| [System information and health checks](system-information-and-health-checks.md) | The health checks and the support report. |
| [Upgrade check as a monitor](upgrade-check-as-a-monitor.md) | Scheduled file integrity checks against the release manifest. |
| [External uptime monitoring](external-uptime-monitoring.md) | Checking the site as citizens reach it. |
| [Log retention and rotation](log-retention-and-rotation.md) | Default periods and how to set your own. |

## Reading order

1. [Monitoring overview](monitoring-overview.md).
2. [External uptime monitoring](external-uptime-monitoring.md) and [System information and health checks](system-information-and-health-checks.md): up and healthy.
3. [The log viewer](the-log-viewer.md), [Error digest and log health](error-digest-and-log-health.md), [Cronjob monitoring](cronjob-monitoring.md): daily operation.
4. Security: [Request logs](request-logs-404-and-security.md), [Audit alerts](audit-alerts.md), [Audit trail verification](audit-trail-verification.md), [CSP reports](csp-reports.md), [Upgrade check as a monitor](upgrade-check-as-a-monitor.md).
5. [Log retention and rotation](log-retention-and-rotation.md) with your records and data protection colleagues.

Up: [Running Exponential in the public sector](../README.md). The section landing page is
[Governments: start here](../../government-start-here.md).

## Related pages
- [Incidents](../incidents/README.md)
- [Operations tasks](../../tasks/operations/README.md)
- [Monitoring overview (hosting)](../../../hosting/operations/monitoring-overview.md)
- [Security and audit guide](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
