---
title: SLA options for Exponential hosting
audience: hosting
category: hosting
order: 30
summary: The service level commitments a host can choose to offer for Exponential sites (availability, response, restore, updates, security fixes), what each depends on technically, how to measure it, and an SLA template with placeholders. Not legal advice; no figures are given.
keywords: SLA, service level agreement, availability, uptime, response time, recovery time, recovery point, RTO, RPO, restore, maintenance window, security fixes, updates, credits, exclusions, template
type: help
---
# SLA options for Exponential hosting

This page is for product managers and managers at a hosting company who write the service level agreement (SLA) of
an Exponential hosting offer. It answers: which commitments are possible, what each one depends on, how you measure
it, and what to exclude.

> **Warning**
> The Exponential project publishes no SLA. Commercial terms of 7x are "on the 7x website, not here"
> ([Who is 7x?](../../who-is-7x.md#services-and-hosting)). The only response commitment in the project's own files
> is in `SECURITY.md`: a first answer to a private security report "within one business day". Everything on this
> page is an option for **your** contract. Have your legal adviser review the final text; this page is not legal
> advice.

## Contents

1. [What an SLA can cover](#what-an-sla-can-cover)
2. [Each commitment and what it depends on](#each-commitment-and-what-it-depends-on)
3. [Measuring what you promise](#measuring-what-you-promise)
4. [Exclusions to state](#exclusions-to-state)
5. [An SLA template](#an-sla-template)

## What an SLA can cover

| Commitment | You promise | Hosts usually offer it in |
|---|---|---|
| Availability | the site answers, measured from outside | every plan above the cheapest |
| Support response | a first answer within a time, per severity | every plan, with different times |
| Recovery point (RPO) | at most this much data can be lost | plans with backups |
| Recovery time (RTO) | a restore is finished within this time | managed plans |
| Maintenance windows | planned work happens only in announced windows | every plan |
| Security updates | a released Exponential security fix is applied within a time | managed plans |
| Platform updates | PHP and operating system updates are applied within a time | every plan the host operates |

## Each commitment and what it depends on

### Availability

Depends on the machine, the network, the engine and the database. Things in Exponential that help you keep it:

- Velocity can be guarded by an optional watchdog that starts the engine again after a crash (`exp:velocity watchdog install`, `[WatchdogSettings]` in `settings/velocity.ini`); see
  [Velocity for hosts](../servers/velocity-for-hosts.md).
- Several web nodes behind a load balancer need a database server and shared storage
  ([Several web nodes](../operations/several-web-nodes.md)).
- Caches reduce the load a traffic spike causes ([Caching layers](../performance/caching-layers.md)).

### Support response

Depends on your staffing, not on the software. Define severities first ([Support tiers](support-tiers.md)), then
times per severity.

### Recovery point and recovery time

Depend on the backup frequency, the backup method per database and the size of `var/storage`. The time of a restore
is only known after you have done one: test it ([Restore and restore tests](../operations/restore-and-restore-tests.md)).

### Security updates

Depend on how fast the project releases and how fast you apply. The project's side, from `SECURITY.md`: security
fixes "are made on the `main` branch ... and published with the next 6.0.x release", and "a published tag is never
changed". Your side: watching releases ([Working with the project and 7x](working-with-the-project-and-7x.md)) and an
update procedure ([Upgrading customer sites](../operations/upgrading-customer-sites.md)).

> **Note**
> Promise to apply a released fix within a time, not that a vulnerability will be fixed within a time: the second
> depends on the project, the first on you.

## Measuring what you promise

| Commitment | Measure with |
|---|---|
| Availability | an external check of a page that touches the database, see [Health checks and uptime](../operations/health-checks-and-uptime.md) |
| Response times | your ticket system |
| Errors seen by visitors | the request logs and the log viewer ([Request logs](../operations/request-logs.md), [Log viewer and rotation](../operations/log-viewer-and-rotation.md)) |
| RPO | the timestamps of your backups |
| RTO | the duration of your last restore test |

## Exclusions to state

- the customer's own code, templates and extensions, and changes the customer made to the configuration;
- extensions not supplied by the host;
- outages caused by a customer's content (for example an import that fills the disk);
- planned maintenance in announced windows;
- third-party services the site calls (payment providers, mail, external APIs);
- force majeure, as your legal adviser words it.

## An SLA template

```text
Service level agreement — <plan name>                 version <x>, valid from <date>

1. Availability        <percentage> per <month>, measured by <external check> on <URL>,
                       excluding announced maintenance windows.
2. Maintenance         windows <day/time, time zone>, announced <n> days ahead by <channel>.
3. Support             severities and first-response times as in the support tier sheet <tier name>.
4. Backups             <what> every <interval>; kept <n> days; stored <where>.
   RPO                 <time>.   RTO <time>, restore last tested on <date>.
5. Security updates    released Exponential security fixes applied within <time> of release;
                       PHP and OS security updates within <time>.
6. Exclusions          <list from this page>.
7. Credits             <your credit model, if any>.
8. Reporting           <monthly report: availability, incidents, restore tests>.
```

## Related pages

- [Support tiers](support-tiers.md)
- [Designing hosting plans](designing-hosting-plans.md)
- [Health checks and uptime](../operations/health-checks-and-uptime.md)
- [Backup schedules and retention](../operations/backup-schedules-and-retention.md)
- [Security updates and disclosure](../security/security-updates-and-disclosure.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
