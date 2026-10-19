---
title: Contracts and SLA basics
audience: businesses, project-manager, hosting, partner
category: business-solutions
order: 60
summary: What a business should cover in a hosting, development or support contract for an Exponential site - scope, service levels (availability, response, recovery, updates), how each is measured, exclusions, data protection, ownership under the GPL, warranty and exit - with a checklist. Not legal advice.
keywords: contract, SLA, service level agreement, availability, response time, RPO, RTO, recovery, maintenance window, security updates, exclusions, warranty, GPL, ownership, exit, data processing, not legal advice
type: help
---
# Contracts and SLA basics

This page is for owners, managers and procurement staff who negotiate a contract with a host or partner for an
Exponential site. It lists what such a contract usually covers, what each commitment depends on, and the points
specific to open source software under the GPL.

> **Warning**
> This page is not legal advice. Have your legal adviser review every contract. The Exponential project itself
> publishes no SLA; the only response commitment in its own files is a first answer to a private security report
> "within one business day" (`SECURITY.md`; [SLA options](../../hosting/business/sla-options.md)).

## Contents

1. [What the contract should cover](#what-the-contract-should-cover)
2. [Service levels](#service-levels)
3. [Measuring what is promised](#measuring-what-is-promised)
4. [Exclusions](#exclusions)
5. [Open source points: GPL, ownership and warranty](#open-source-points-gpl-ownership-and-warranty)
6. [Data protection](#data-protection)
7. [Exit](#exit)
8. [Checklist before signing](#checklist-before-signing)

## What the contract should cover

| Topic | Questions it answers |
|---|---|
| Scope | Which sites, environments (live, staging) and services are included? |
| Roles | Who does what: updates, backups, restores, monitoring, content, custom code? |
| Service levels | What is promised, how it is measured, what happens if it is missed? |
| Changes | How are changes requested, priced and approved? |
| Security | How fast are security releases applied? Who is told about incidents? |
| Data protection | Which personal data is processed, where, and under which terms? |
| Ownership | Who owns custom templates, extensions, designs and content? |
| Warranty and liability | What does the supplier stand behind? |
| Term and exit | How long, how to end it, and how the handover works |

## Service levels

From [SLA options](../../hosting/business/sla-options.md#what-an-sla-can-cover), the commitments a host or partner
can offer:

| Commitment | The supplier promises | It depends on |
|---|---|---|
| Availability | the site answers, measured from outside | machine, network, engine, database |
| Support response | a first answer within a time, per severity | their staffing |
| Recovery point (RPO) | at most this much data can be lost | backup frequency |
| Recovery time (RTO) | a restore is finished within this time | backup method, data size, tested restores |
| Maintenance windows | planned work only in announced windows | their procedures |
| Security updates | a released Exponential security fix is applied within a time | their update procedure |
| Platform updates | PHP and OS updates are applied within a time | their operations |

> **Tip**
> Ask for "a released fix is applied within a time", not "a vulnerability is fixed within a time": the second
> depends on the project, the first on the supplier
> ([SLA options](../../hosting/business/sla-options.md#security-updates)).

Choose levels that match what downtime costs you ([Cost of downtime worksheet](../costs/cost-of-downtime-worksheet.md)).
Stricter levels cost more; a level you never need is money spent for nothing.

## Measuring what is promised

| Commitment | Measured by | Source |
|---|---|---|
| Availability | an external check of a page that touches the database | [Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md) |
| Response times | the supplier's ticket system; ask for reports | |
| RPO | timestamps of backups | [SLA options](../../hosting/business/sla-options.md#measuring-what-you-promise) |
| RTO | the duration of the last restore test | [Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md) |
| Updates applied | the site's version and the audit trail's deploy records | [The audit trail](../../../features/6.0/audit-trail.md) |

Ask for a monthly or quarterly report with these figures.

## Exclusions

Suppliers usually exclude ([SLA options](../../hosting/business/sla-options.md#exclusions-to-state)):

- your own code, templates and extensions, and configuration you changed;
- extensions they did not supply;
- outages caused by your content (for example an import that fills the disk);
- planned maintenance in announced windows.

Read the exclusions as carefully as the promises; they decide what you really get.

## Open source points: GPL, ownership and warranty

From [Licensing explained for buyers](../../business/licensing-for-buyers.md):

- **No license fee** for Exponential; the contract should not charge one for the software itself.
- **Running a website is not distributing.** When an agency builds your site and hands the code to you, it
  distributes it to you, and you receive the GPL rights, including the source.
- **Warranty**: the GPL comes with no warranty (`LICENSE.md`, sections 11 and 12). A warranty, guaranteed response
  or liability coverage comes only from your contract; the GPL explicitly allows offering "warranty protection in
  exchange for a fee".
- **Your own work**: agree in writing who owns designs, templates, extensions and content made for you. Note that
  code built on Exponential and distributed falls under the GPL's conditions; ask your adviser what that means for
  your plans.
- **Extensions** each carry their own license, shown on **Info > About**
  ([About and Copyright pages](../../../features/6.0/about-and-package-pages.md)).

## Data protection

- If the supplier processes personal data for you, a written agreement on that processing is usually needed; your
  adviser knows which form ([GDPR basics](../security/gdpr-basics.md)).
- Name the locations of servers and backups.
- Agree how the supplier helps with requests to see, export or delete data
  ([Data retention and deletion](../security/data-retention-and-deletion.md)).
- Agree who tells whom, and how fast, after an incident.

## Exit

- [ ] Administrator access and hosting accounts belong to you, or are handed over on request.
- [ ] The code of your site, the database and uploaded files are delivered in a stated format and time.
- [ ] The supplier cooperates with a new provider during a handover.
- [ ] Personal data is deleted by the old supplier after the handover, with confirmation.

The [Customer offboarding checklist](../../hosting/checklists/customer-offboarding.md) shows what a well-run host does
at the end of a contract.

## Checklist before signing

- [ ] Scope and roles written as a table.
- [ ] Service levels match your cost of downtime.
- [ ] Each level has a measure and a report.
- [ ] Exclusions read and accepted.
- [ ] Security update time stated.
- [ ] Data protection terms agreed.
- [ ] Ownership of custom work stated.
- [ ] Warranty and liability terms understood.
- [ ] Exit and handover described.
- [ ] Reviewed by your legal adviser.

An SLA template with placeholders, written for hosts, is in [SLA options](../../hosting/business/sla-options.md#an-sla-template).

## Related pages

- [Support tiers](../../hosting/business/support-tiers.md)
- [Questions to ask a partner](questions-to-ask-a-partner.md)
- [The partnership agreement (for partners)](../../partners/the-partnership-agreement.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
