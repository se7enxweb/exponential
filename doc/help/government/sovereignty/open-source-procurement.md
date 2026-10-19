---
title: Open source procurement
audience: government, project-manager
category: public-sector
order: 100
summary: How a public body buys services around free software like Exponential when there is no license to buy: what to procure (hosting, building, support, training), requirements that keep you independent, how to evaluate offers, and what to put in the contract.
keywords: procurement, tender, open source procurement, services, support contract, hosting contract, requirements, evaluation, exit clause, source code delivery, sovereignty
type: help
---
# Open source procurement

This page is for project managers and procurement officers. Exponential has no license fee: "Using Exponential under
the terms of the GNU GPL is free of charge" ([Licensing explained for buyers](../../business/licensing-for-buyers.md)).
What a public body procures are services. Procurement law differs by jurisdiction; the section of this help on
procurement covers the process in more detail.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What there is to buy](#what-there-is-to-buy)
2. [Requirements that keep you independent](#requirements-that-keep-you-independent)
3. [Evaluating offers](#evaluating-offers)
4. [Contract clauses](#contract-clauses)
5. [Avoid these](#avoid-these)

## What there is to buy

| Lot | Typical content | Who offers it |
|---|---|---|
| Hosting and operations | servers, database, backups, updates, monitoring | hosting companies, partners ([Hosting partners](../../partners/hosting-partners.md)) |
| Building | content model, design, templates, migration | implementation partners ([Implementation partners](../../partners/implementation-partners.md)) |
| Support | response times, fixes, upgrade help | partners, 7x ([Support levels and response times](../../partners/support-levels-and-response-times.md)) |
| Training | editors, administrators, developers | training partners ([Course catalogue](../../trainers/course-catalogue.md)) |
| Development of features | new functions, given back to the project | any qualified developer ([Feature requests and sponsored features](../../partners/feature-requests-and-sponsored-features.md)) |

Splitting lots lets you change one provider without changing all.

## Requirements that keep you independent

- All custom code (extensions, designs, settings) is delivered in a Git repository the body owns, under the GPL
  or a license the body chooses within the GPL's limits.
- No custom changes to the kernel; customizations go in extensions and overrides, so upgrades stay possible.
- Documentation of the installation (siteaccesses, extensions, cronjobs, settings overrides) is delivered and kept
  current.
- Content and files can be exported at any time in documented formats (see "Exporting content and packages").
- Hosting in the regions the body requires; backups delivered on request.
- Optional third-party services listed and approved by the body (see "Third-party services to review").

## Evaluating offers

| Criterion | Evidence to ask for |
|---|---|
| Experience with Exponential | references the bidder may name, sample code on GitHub |
| Contributions to the project | published extensions, merged pull requests |
| Operations competence | runbooks, backup and restore tests |
| Security handling | process for updates and incidents ([Security responsibilities](../../partners/security-responsibilities.md)) |
| Exit support | how they hand over to a successor |

## Contract clauses

- Delivery of source and documentation at each milestone and at the end.
- An exit clause: handover assistance, data export, timelines.
- Service levels for support and security updates.
- Who owns the hosting accounts and domain names (the body should).
- Contribution of generic improvements back to the project, if the body wants that (see "Contributing back as a
  public body").

## Avoid these

- Requirements that name one supplier's proprietary add-on as mandatory.
- Contracts where the provider holds the only copy of the code or the only admin account.
- Custom kernel patches that only one supplier understands.

## Related pages

- [Licensing explained for buyers](../../business/licensing-for-buyers.md)
- [Partners start here](../../partners/partners-start-here.md)
- [Planning an Exponential project](../../leadership/planning-an-exponential-project.md)
- [Procurement](../procurement/README.md): the procurement section of the government help
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
