---
title: "Tender template: service levels clause"
audience: government, project-manager, hosting, partner
category: public-sector
order: 100
summary: Clause text for service levels of an Exponential site - availability, support response by severity, maintenance windows, recovery, security updates, measurement, reporting and remedies - with every figure left blank for your own needs.
keywords: service levels, SLA, availability, uptime, response time, severity, maintenance window, RPO, RTO, credits, measurement, reporting, tender template
type: help
---
# Tender template: service levels clause

This page is for procurement officers who set service levels for hosting and support of an Exponential site. It
follows the structure of [SLA options](../../../hosting/business/sla-options.md), written for hosts, and turns it
into a customer-side clause. **This help gives no figures**: availability percentages, response times and credits
depend on your needs and budget. The Exponential project itself publishes no SLA; the only response commitment in its
files is a first answer to a private security report within one business day
([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).

**Template** - adapt to your procurement rules; not legal advice

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [The clause](#the-clause)
2. [Defining severities](#defining-severities)
3. [Measuring what is promised](#measuring-what-is-promised)
4. [Notes for the evaluator](#notes-for-the-evaluator)

## The clause

```text
S. SERVICE LEVELS

S.1 Availability. The public website shall be available <percentage> of the time
    per <calendar month>, measured by <external check> requesting <URL that
    touches the database> every <interval> from <location>. Announced maintenance
    windows under S.3 are excluded.

S.2 Support response. The Supplier responds to and resolves incidents as follows:
      Severity 1: first response <time>, workaround <time>, hours <24x7 / business>
      Severity 2: first response <time>, workaround <time>, hours <...>
      Severity 3: first response <time>, resolution <time>, hours <...>
      Severity 4: first response <time>, resolution next planned release
    "First response" means a reply from a qualified person, not an automatic
    acknowledgment.

S.3 Maintenance. Planned work takes place in <day/time, time zone>, announced <n>
    days ahead to <contact>. Visitors see a maintenance page; editors are informed.

S.4 Recovery. RPO <time>; RTO <time>; restore tested every <interval> (see
    <hosting clause>).

S.5 Security updates. As set out in <vulnerability and patching clause>.

S.6 Reporting. The Supplier reports monthly: availability, incidents by severity
    with response and resolution times, maintenance performed, updates applied,
    restore tests.

S.7 Remedies. If a service level is missed in <period>: <service credits /
    remediation plan / escalation / termination right>, as set out in <annex>.

S.8 Exclusions. Content or configuration changes made by the Customer against the
    Supplier's documented advice; third-party services the Customer chose;
    force majeure as defined in <clause>.
```

## Defining severities

| Severity | Example definition (adapt) |
|---|---|
| 1 | Public site down or showing wrong content to all visitors; security incident; editors cannot publish at all |
| 2 | An important function fails (search, forms, a language version) with no workaround |
| 3 | A function fails with a workaround; a single page is wrong |
| 4 | Questions, cosmetic issues, requests for change |

Hosting companies' own tier designs are in [Support tiers](../../../hosting/business/support-tiers.md).

## Measuring what is promised

| Commitment | How it is measured with Exponential |
|---|---|
| Availability | external check of a page that uses the database ([Health checks and uptime](../../../hosting/operations/health-checks-and-uptime.md)) |
| Errors seen by visitors | request logs and the log viewer ([Request logs](../../../../features/6.0/request-logs.md), [Log viewer](../../../../features/6.0/log-viewer.md)) |
| Maintenance windows | maintenance mode with a message and an end time ([Maintenance mode](../../../../guides/maintenance-mode.md)); switching it is recorded in the audit trail |
| RPO and RTO | backup timestamps and the last restore test |
| Response times | the supplier's ticket system; ask for read access |

Velocity can be guarded by a watchdog that restarts the engine after a crash
([Velocity for hosts](../../../hosting/servers/velocity-for-hosts.md)); this helps availability but does not replace
monitoring.

## Notes for the evaluator

- Compare service levels with the staff the bidder names; a promise without on-call staff is not credible.
- A partner of the project can show its own escalation times from the project
  ([Support levels and response times](../../../partners/support-levels-and-response-times.md)).

## Related pages
- [Tender templates](README.md)
- [Support and maintenance clause](support-and-maintenance-clause.md)
- [Hosting and data location clause](hosting-and-data-location-clause.md)
- [Operations and monitoring requirements](../requirements/operations-and-monitoring-requirements.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
