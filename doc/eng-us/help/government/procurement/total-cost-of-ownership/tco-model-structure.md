---
title: The structure of a CMS total cost of ownership model
audience: government, project-manager
category: public-sector
order: 20
summary: How to build a total cost of ownership model for a website or intranet on Exponential - the time horizon, the phases, one-off and recurring costs, internal and external costs, risks and exit - so that bids with and without license fees are compared on the same basis.
keywords: total cost of ownership, TCO, cost model, lifecycle cost, budget, business case, one-off costs, recurring costs, contract period, comparison, open source cost
type: help
---
# The structure of a CMS total cost of ownership model

This page is for project managers and finance staff who build the cost comparison for a content management system.
A total cost of ownership (TCO) model adds up every cost of owning the system over a fixed period, not only the
purchase price. It matters with free software like Exponential: there is no license fee, but implementation,
hosting, staff, support, training, upgrades and exit still cost money. This page gives the structure; the other pages
of this folder fill in each part. It gives no prices: all figures come from your own quotes and records.

## Contents
1. [Choose the horizon](#choose-the-horizon)
2. [The phases of the model](#the-phases-of-the-model)
3. [One-off and recurring costs](#one-off-and-recurring-costs)
4. [Internal and external costs](#internal-and-external-costs)
5. [Risks and contingencies](#risks-and-contingencies)
6. [Putting it together](#putting-it-together)
7. [Pitfalls](#pitfalls)

## Choose the horizon

- Use the period the system is expected to live, usually longer than the first contract. Websites of public bodies
  often outlive several suppliers.
- Use the same horizon for every bid.
- Include the cost of leaving at the end of the horizon, so a bid with a high exit cost does not look cheaper than it
  is (see the exit-plans folder).

## The phases of the model

| Phase | What happens | Pages that explain the work |
|---|---|---|
| Acquisition | tender, evaluation, proof of concept | [Proof-of-concept plan](../evaluation/proof-of-concept-plan.md) |
| Implementation | content model, design, templates, integrations, migration of old content | [Implementation playbook](../../../partners/implementation-project-playbook.md), [Migration hub](../../../../install/migration/README.md) |
| Go-live | hardening, accessibility testing, training, launch | [Security hardening](../../../security-hardening.md), [Launch checklist](../../../creators/website-creators/launch-checklist.md) |
| Operation | hosting, support, backups, monitoring, content work | [Operating a site](../../../../guides/operating-a-site.md) |
| Evolution | upgrades, new features, redesigns | [Upgrading](../../../../guides/upgrading.md) |
| Exit | export, handover, transition | [Leaving Exponential](../../../../install/migration/leaving-exponential.md) |

## One-off and recurring costs

| One-off | Recurring |
|---|---|
| implementation project | hosting and infrastructure |
| content migration | support and maintenance contract |
| initial accessibility audit and statement | applying security releases and upgrades |
| penetration test before launch | periodic accessibility and security re-tests |
| initial training | training of new staff |
| exit and transition | internal staff time (web team, editors, administrators) |

## Internal and external costs

External costs appear in bids; internal costs do not, but they are real. Count:

- staff time of the web team, editors in departments, IT operations, security and data protection staff;
- time of the evaluation and acceptance testing;
- management and contract management time.

Estimate internal time in hours per role and multiply by your own internal rate. See
[Staff and skills](staff-and-skills.md).

## Risks and contingencies

Add a line for risks you can name, with a probability and an impact you estimate yourself:

- a supplier stops trading (cost of a new supplier and transition);
- a major upgrade needs more custom work than planned (custom templates and extensions are the usual cause,
  [Upgrading](../../../../guides/upgrading.md));
- a security incident (response, forensic work, communication).

## Putting it together

```text
TCO over <n> years = Acquisition + Implementation + Go-live
                   + sum over years (Operation + Evolution)
                   + Exit
                   + Risk allowance
```

Put each line in the [TCO worksheet template](tco-worksheet-template.md). If your rules require it, discount
future costs to present value with your organization's own rate.

## Pitfalls

- Comparing a license fee in one bid with "no license fee" in another, and stopping there.
- Leaving internal staff time out because it is "already paid".
- Forgetting the exit cost.
- Using different horizons for different bids.

## Related pages
- [Total cost of ownership: overview](README.md)
- [Cost categories](cost-categories.md)
- [Procurement: overview](../README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
