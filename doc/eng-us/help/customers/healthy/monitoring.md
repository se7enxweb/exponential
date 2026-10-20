---
title: Monitoring
audience: customer, businesses, hosting
category: customer-success
order: 11
summary: What an owner should expect from the monitoring of an Exponential site - checks from outside and on the server, the pages a person reads each week, and who receives an alert - with the questions to ask your host.
keywords: monitoring, uptime, health check, alerts, system information, log health, error digest, audit alerts, Velocity status, on call
type: help
---
# Monitoring

Monitoring means someone notices a problem before your customers tell you. This page describes what good monitoring
of an Exponential site looks like from the owner's chair, and what to ask the host or operator who sets it up. The
technical detail is in [Health checks and monitoring](../../businesses/reliability/health-checks-and-monitoring.md)
and, for hosts, [Monitoring overview](../../hosting/operations/monitoring-overview.md).

## No single health address

The installation book is direct: "There is no single health URL; combine a check from outside with the status
commands inside" ([Health checks and monitoring](../../businesses/reliability/health-checks-and-monitoring.md)). Ask
for both halves.

## From outside: what customers see

An external uptime service checks two addresses
([Health checks and monitoring](../../businesses/reliability/health-checks-and-monitoring.md)):

| Check | Healthy answer | Why |
|---|---|---|
| The front page over HTTPS | `200`, in a time you know | What visitors reach first |
| The administration sign-in page | `200` | It goes through the application, while the front page may come from a cache |

During planned maintenance the site answers `503` with a `Retry-After` header; the monitor should be paused for the
window or accept that answer ([Maintenance mode and planned work](maintenance-mode-and-planned-work.md)).

## On the server: why it is failing

The host runs status commands on a schedule and alerts when one fails: the platform health check, the cache status,
the Velocity status where Velocity serves the site, the audit trail verification, cron and free disk space. Each
gives a clear healthy or unhealthy answer
([Health checks and monitoring](../../businesses/reliability/health-checks-and-monitoring.md)). You do not need to
run them; ask that they are scheduled and that a failure reaches a person.

## Pages a person reads each week

| Page | What it shows |
|---|---|
| **Setup > System information** | Which server answered, health checks with what to do, versions, database, cron, mail |
| **Setup > Log files**, log health and error digest | Disk used by logs, the distinct errors of the last 24 hours |
| The **Audit** tab | The state of each audit channel, recent events, alerts |
| **Setup > Cronjobs** | Scheduled jobs, last runs, jobs that need attention |

Source: [Health checks and monitoring](../../businesses/reliability/health-checks-and-monitoring.md). If nobody on your
team reads them, ask your host or partner for a short weekly or monthly summary instead
([Monthly operations](../../hosting/checklists/monthly-operations.md) includes a report to the customer).

## Who is woken up

"A check nobody reads is not monitoring" (same page). Decide and write down:

- [ ] Who receives an alert in office hours, and who outside them; who is the deputy.
- [ ] How fast they are expected to look ([Contracts and SLAs](../partners-and-hosts/contracts-and-slas.md)).
- [ ] Where alerts go: mail, a chat channel, a phone.
- [ ] Who receives the audit trail's own alert mail (brute-force sign-ins, an administrator role granted, the audit
      switched off); the host can list the recipients ([The audit trail](../../../features/6.0/audit-trail.md)).
- [ ] When the owner is told, and by whom.

## Questions for your host

1. Which addresses do you check from outside, how often, and from where?
2. Which status commands run on the server, and what happens when one fails?
3. Who receives alerts outside office hours?
4. Where Velocity serves the site: is its watchdog installed, which starts it again when it is down
   ([Health checks and monitoring](../../businesses/reliability/health-checks-and-monitoring.md))?
5. Can we see the uptime figures each month?
6. Is a failed backup job alerted like a failed health check ([Backups](backups.md))?

## Related pages

- [Logs for owners](logs-for-owners.md)
- [When the site is down](when-the-site-is-down.md)
- [Health checks and uptime (for hosts)](../../hosting/operations/health-checks-and-uptime.md)
- [Uptime practice](../../businesses/reliability/uptime-practice.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
