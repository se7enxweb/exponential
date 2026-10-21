---
title: Managed service reseller
audience: reseller, partner, hosting
category: reselling
order: 80
summary: How to sell Exponential sites as a managed service - you run, update, back up and watch the site for a monthly fee - with the tasks the service covers, the tools Exponential provides for each, a responsibility matrix to put in the contract, and the costs to price in.
keywords: managed service, managed site, care plan, maintenance plan, monthly fee, updates, backups, monitoring, ezupdate, upgrade check, maintenance mode, responsibility matrix
type: help
---
# Managed service reseller

This page is for agencies and hosting companies that sell a **managed site**: the customer edits content, and you
keep the platform running, current and safe for a monthly fee. It is the most common reseller model around
Exponential, because the customer's need is ongoing and so is your revenue.

## Contents

1. [What the service covers](#what-the-service-covers)
2. [The tools you use](#the-tools-you-use)
3. [A responsibility matrix for the contract](#a-responsibility-matrix-for-the-contract)
4. [Costs to price in](#costs-to-price-in)
5. [Selling it](#selling-it)
6. [Checklist](#checklist)

## What the service covers

| Area | Typical managed service | Usually left to the customer |
|---|---|---|
| Server, PHP, engine, certificates | Yes | |
| Exponential and extension updates | Yes, on a schedule, with a test step | Approving the window |
| Backups and restore tests | Yes | |
| Monitoring and log review | Yes | |
| Security hardening and incident response | Yes, for the platform | Content and user accounts |
| Content, users, roles | | Yes |
| Custom code (extensions, designs) | Deploying it | Writing it, unless sold separately |

Source: [Managed hosting](../../hosting/models/managed-hosting.md#what-managed-can-cover).

## The tools you use

| Task | Tool in Exponential | Page |
|---|---|---|
| Updates | The `ezupdate` extension: **Setup > Updates and packages**, with a check that changes nothing, a dry run and a backup confirmation | [ezupdate](../../../features/6.0/extensions/ezupdate.md) |
| Before an upgrade | **Setup > Upgrade check** (read-only file and database consistency) | [The upgrade check](../../../guides/upgrade-check.md) |
| Planned work | **Setup > Maintenance**, or `php bin/php/maintenance.php` | [Maintenance mode](../../../guides/maintenance-mode.md) |
| Health | **Setup > System information** and `exp:system:info` | [System information](../../../guides/system-information.md) |
| Logs | **Setup > Log files** | [Log viewer](../../../features/6.0/log-viewer.md) |
| Scheduled jobs | **Setup > Cronjobs** | [Cronjobs](../../../guides/cronjobs.md) |
| Who did what | The audit trail | [Audit trail](../../../features/6.0/audit-trail.md) |

The full table, with the command-line forms, is on [Managed hosting](../../hosting/models/managed-hosting.md#the-tools-for-each-task).

## A responsibility matrix for the contract

Copy it into the service description and fill in the names.

```text
Task                                  | Your company | Customer | 7x / project
--------------------------------------|--------------|----------|---------------------------
Server, PHP, engine, TLS              |      R       |          |
Exponential and extension updates     |      R       |    A     | publishes releases
Backups, restore tests                |      R       |    I     |
Monitoring, log review                |      R       |    I     |
Security incident on the platform     |      R       |    I     | fixes in the next release
Content, users, roles                 |              |    R     |
Custom extensions and designs         |   R or -     |  R or -  |
Bug in Exponential itself             | reports it   |    I     | fixes in a release
R = responsible, A = approves, I = informed
```

Bugs in Exponential are fixed in the next 6.0.x release; "a published tag is never changed" (`SECURITY.md`). Your
service includes applying that release, not a private fix from 7x.

## Costs to price in

- Hosting (your own or bought in), backups and their storage.
- Staff time per site per month: updates, checks, reports. Measure it on your first sites.
- An on-call rota if you promise anything outside business hours.
- Your partner subscription, if your back-up is partner support ([Choosing a partner level](../basics/choosing-a-partner-level.md)).
- A reserve for upgrades that need code changes, or price them separately.

Work the numbers with the [Margin and cost worksheet](../offers/margin-and-cost-worksheet.md).

## Selling it

- Lead with outcomes: "your site stays current and backed up; you get a short report each month".
- Show what the customer will see: the monthly report, the update notices, the restore test result.
- Explain the exit: the customer can leave at any time, with a complete handover
  ([Handing over to a customer or a new provider](../customers/handing-over.md)).

## Checklist

- [ ] Service description with the responsibility matrix.
- [ ] Update rhythm and window agreed ([Keeping customers current](../customers/keeping-customers-current.md)).
- [ ] Monthly report template ([Monthly checklist](../checklists/monthly-checklist.md)).
- [ ] Service levels you can meet with your own staff ([Service level design](../offers/service-level-design.md)).
- [ ] Contract with data processing terms ([Data protection for resellers](../responsibilities/data-protection.md)).

## Related pages

- [Managed hosting](../../hosting/models/managed-hosting.md)
- [Maintenance agreement template](../../customers/partners-and-hosts/maintenance-agreement-template.md)
- [Support contract reseller](support-contract-reseller.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
