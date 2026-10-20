---
title: Contracts and SLAs
audience: customer, businesses, partner
category: customer-success
order: 5
summary: The owner's guide to contracts and service level agreements with a host or partner for an Exponential site - what to cover, which promises depend on whom, how each is measured, the open source points, and how the templates in this folder fit. Not legal advice.
keywords: contract, SLA, service level agreement, availability, response time, RPO, RTO, security updates, exit, GPL, not legal advice
type: help
---
# Contracts and SLAs

This page helps an owner prepare and read a contract with a host or partner. It does not replace legal advice: have
your legal adviser review every contract. The detailed reference for businesses is
[Contracts and SLA basics](../../businesses/choosing/contracts-and-slas-basics.md); this page is the owner's working
summary and points to the templates in this folder.

> **Warning**
> Not legal advice. The Exponential project publishes no SLA. The only response commitment in its own files is a
> first answer to a private security report "within one business day" (`SECURITY.md`). Every other promise comes from
> your contract.

## What to cover

From [Contracts and SLA basics](../../businesses/choosing/contracts-and-slas-basics.md):

| Topic | The question it answers |
|---|---|
| Scope | Which sites, environments and services? |
| Roles | Who does what ([Who does what](who-does-what.md))? |
| Service levels | What is promised, how measured, what if missed? |
| Changes | How are changes requested, priced and approved ([Change requests](change-requests.md))? |
| Security | How fast are security releases applied; who is told about incidents? |
| Data protection | Which personal data, where, under which terms? |
| Ownership | Who owns custom templates, extensions, designs and content? |
| Warranty and liability | What does the supplier stand behind? |
| Term and exit | How long, how to end it, how the handover works |

## Promises and what they depend on

| Commitment | Depends on | Measured by |
|---|---|---|
| Availability | Machine, network, engine, database | An external check of a page that touches the database |
| Support response | Their staffing | Their ticket system; ask for reports |
| Recovery point (RPO) | Backup frequency | Timestamps of backups |
| Recovery time (RTO) | Backup method, data size, tested restores | The duration of the last restore test |
| Security updates | Their update procedure | The site's version and the audit trail's deploy records |

Source: [Contracts and SLA basics](../../businesses/choosing/contracts-and-slas-basics.md), from
[SLA options](../../hosting/business/sla-options.md).

> **Tip**
> Ask for "a released fix is applied within a time", not "a vulnerability is fixed within a time": the second
> depends on the project, the first on the supplier (same pages).

## Choosing the levels

Stricter levels cost more. Match them to what downtime costs your organization; the
[cost of downtime worksheet](../../businesses/costs/cost-of-downtime-worksheet.md) helps. A level you never need is
money spent for nothing, and a level your provider cannot staff is a promise on paper only. Partners are advised
never to promise a customer "a shorter response than you can deliver with your own staff"
([Support levels and response times](../../partners/support-levels-and-response-times.md)); it is fair to ask how
they staff what they promise.

## Open source points

- **No license fee** for Exponential itself; a contract should not charge one for the software.
- **The GPL comes with no warranty**; a warranty, guaranteed response or liability coverage comes only from your
  contract (`LICENSE.md`; [Contracts and SLA basics](../../businesses/choosing/contracts-and-slas-basics.md)).
- **Agree ownership of custom work in writing**, and ask your adviser what the GPL means for your plans.
- **Extensions** each carry their own license, shown on **Info > About**.

## The templates

| Template | Use it for |
|---|---|
| [SLA template](sla-template.md) | Availability, maintenance windows, backups, recovery, security updates, reporting |
| [Support agreement template](support-agreement-template.md) | Channels, hours, severities, first responses, what is included |
| [Maintenance agreement template](maintenance-agreement-template.md) | Updates, PHP upgrades, checks, reports, the yearly review |

All three are templates with fields to fill in, not legal advice, and contain no figures: the values are yours to
agree.

## Before signing

- [ ] Scope and roles written as a table.
- [ ] Each service level has a measure and a report.
- [ ] Exclusions read and accepted: usually your own code, extensions they did not supply, outages caused by your
      content, and announced maintenance ([SLA options](../../hosting/business/sla-options.md)).
- [ ] Security update time stated.
- [ ] Data protection terms agreed ([GDPR basics](../../businesses/security/gdpr-basics.md), not legal advice).
- [ ] Exit and handover described ([Handover checklist](handover-checklist.md)).
- [ ] Reviewed by your legal adviser.

## Related pages

- [Reviewing your provider each year](reviewing-your-provider-each-year.md)
- [Support tiers (for hosts)](../../hosting/business/support-tiers.md)
- [The partnership agreement (for partners)](../../partners/the-partnership-agreement.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
