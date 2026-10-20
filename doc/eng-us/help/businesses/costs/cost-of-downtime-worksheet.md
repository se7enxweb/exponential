---
title: Cost of downtime worksheet
audience: businesses, project-manager, admin, hosting
category: business-solutions
order: 70
summary: A worksheet to work out what one hour offline costs your organization - lost sales, idle staff, recovery work, contract penalties and wasted marketing - with a worked example built from example assumptions, a blank form to copy, and the Exponential features that shorten or prevent an outage.
keywords: downtime, outage, cost of downtime, availability, uptime, worksheet, lost revenue, recovery, maintenance mode, health check, restore, incident, business case
type: help
---
# Cost of downtime worksheet

This page is for owners and managers who want to know what an outage really costs, so they can decide how much to
spend on preventing one. It gives the cost lines, a worked example, a blank form to copy, and the features of
Exponential that help you keep the figure small.

> **Note**
> The worked example uses only **Example assumptions**: round numbers in a neutral currency unit. They are not
> industry averages. Fill in the blank form with your own figures.

## Contents

1. [Planned and unplanned downtime](#planned-and-unplanned-downtime)
2. [The cost lines](#the-cost-lines)
3. [A worked example](#a-worked-example)
4. [The blank worksheet](#the-blank-worksheet)
5. [Using the result](#using-the-result)
6. [What shortens or prevents an outage](#what-shortens-or-prevents-an-outage)

## Planned and unplanned downtime

- **Planned downtime** is announced work: an upgrade, a data import. Exponential's maintenance mode takes the
  public site offline with a friendly page and an HTTP 503 answer, and brings it back with one command or one button
  ([Maintenance mode](../../../features/6.0/maintenance-mode.md)). Planned work in a quiet hour costs little.
- **Unplanned downtime** is an outage: a full disk, an expired certificate, a failed update, an attack. It costs
  the most because it happens at any hour and nobody is ready.

Work out the cost of an unplanned hour at a busy time. That is the figure that justifies prevention.

## The cost lines

| Line | Question to answer |
|---|---|
| A. Lost gross profit | How much gross profit (sales minus cost of goods and fees) does the site bring in an hour at a busy time? |
| B. Lost staff time | How many staff cannot work, or answer complaints instead, and what does their hour cost? |
| C. Recovery work | How many hours of administrators or a partner does a typical recovery take, at what rate (often an emergency rate)? |
| D. Contract penalties | Do you owe credits or penalties to customers or partners for an outage? |
| E. Wasted marketing | Are you paying for ads or a newsletter that sends people to a dead site? |
| F. Lost customers | Not a figure: write it in words, for example "first-time visitors who never come back" |

Cost of one hour = A + B + C/duration + D/duration + E. Recovery work and penalties often come once per incident, so
divide them by the length of the outage, or keep them as a cost per incident.

## A worked example

A business with an online store and a customer service team. Every input is an **Example assumption**.

| Input | Value |
|---|---|
| Example assumption: gross profit from the site at a busy hour | 200 currency units |
| Example assumption: staff affected | 3 people at 40 currency units an hour |
| Example assumption: recovery work | 4 hours at 100 currency units an hour |
| Example assumption: contract credits | none |
| Example assumption: advertising running during the outage | 20 currency units an hour |
| Example assumption: length of the outage | 2 hours |

Step by step:

1. A. Lost gross profit: 200 x 2 = **400**.
2. B. Lost staff time: 3 x 40 x 2 = **240**.
3. C. Recovery work: 4 x 100 = **400**.
4. D. Contract credits: **0**.
5. E. Wasted marketing: 20 x 2 = **40**.
6. Cost of the incident: 400 + 240 + 400 + 0 + 40 = **1,080**.
7. Cost per hour of this outage: 1,080 / 2 = **540**.

If the business expects three such incidents a year without prevention work, the expected yearly cost is
3 x 1,080 = **3,240**, plus the lost customers described in words. Spending less than that on prevention is likely
to pay off; [Return on investment of stability](roi-of-stability.md) shows how to present it.

## The blank worksheet

Copy this into your own document and fill it in.

```text
COST OF DOWNTIME WORKSHEET
Organization: ______________________   Date: ____________   Prepared by: ______________

Site or application: ______________________
Busiest hours and days: ______________________

A. Gross profit from the site per busy hour:        ______  (source: ______________)
B. Staff affected: ___ people x ___ cost/hour     = ______  per hour
C. Recovery work: ___ hours x ___ rate             = ______  per incident
D. Contract credits or penalties                   = ______  per incident
E. Marketing running during an outage              = ______  per hour
F. Other effects (in words): ________________________________________________

Typical outage length (hours): ______
Cost of one incident = (A + B + E) x length + C + D = ______
Cost per hour        = cost of one incident / length = ______
Expected incidents per year without prevention: ______
Expected yearly cost of downtime                    = ______

Reviewed by: ______________   Next review: ____________
```

## Using the result

- Set an availability target in your hosting or support contract that matches the cost
  ([Contracts and SLA basics](../choosing/contracts-and-slas-basics.md)).
- Decide how much to spend on backups, monitoring and restore tests per year.
- Choose a hosting model whose risk you can afford ([Hosting models compared](../choosing/hosting-models-compared.md)).
- Plan upgrades and other planned work in the hours where A is lowest.

## What shortens or prevents an outage

| Measure | What it does | Read |
|---|---|---|
| External monitoring | Finds an outage before customers do | [Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md) |
| Velocity watchdog | Starts Velocity again after a crash (`exp:velocity watchdog install`) | [SLA options](../../hosting/business/sla-options.md) |
| Tested restores | Turns a disaster into a known procedure | [Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md) |
| Caching | Keeps the site answering during traffic peaks | [Caching layers](../../hosting/performance/caching-layers.md) |
| Error references | Visitors' error pages carry a reference that finds the log entry | [Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md) |
| Runbooks | Step-by-step help for a site that is down, a full disk or an expired certificate | [Site down](../../hosting/runbooks/site-down.md), [Disk full](../../hosting/runbooks/disk-full.md), [Certificate expiry](../../hosting/runbooks/certificate-expiry.md) |
| Maintenance mode | Turns risky work into planned downtime | [Maintenance mode](../../../features/6.0/maintenance-mode.md) |

The [Reliability](../reliability/README.md) section explains each practice for businesses, including
[Incident response for small teams](../reliability/incident-response-for-small-teams.md).

## Related pages

- [Uptime practice](../reliability/uptime-practice.md)
- [Return on investment of stability](roi-of-stability.md)
- [Monthly operations checklist](../templates/monthly-operations-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
