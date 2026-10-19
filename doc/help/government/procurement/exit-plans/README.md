---
title: Exit plans for an Exponential site
audience: government, project-manager, admin, partner
category: public-sector
order: 10
summary: The index of the exit plans section - why and how a public body plans to leave a supplier or a system, the export formats Exponential provides, backups it should hold itself, documentation, keys, personal data, redirects and the contract clauses that keep exit possible.
keywords: exit plan, exit strategy, vendor lock-in, data portability, export, handover, transition, change of supplier, leaving Exponential
type: help
---
# Exit plans for an Exponential site

This section is for project managers, procurement officers, data protection officers and administrators who must
make sure the public body can change supplier, host or system without losing data or service. Exponential is free
software with open storage and documented export tools, so the technical path exists; these pages turn it into a
plan, a contract annex and a tested routine.

## Contents
1. [Pages in this section](#pages-in-this-section)
2. [Reading order](#reading-order)
3. [Principles of this section](#principles-of-this-section)

## Pages in this section

| Page | Answers |
|---|---|
| [Why plan the exit](why-plan-the-exit.md) | What goes wrong without a plan, and what the software already makes possible |
| [Exit plan template](exit-plan-template.md) | A plan to fill in and attach to the contract |
| [Data export formats](data-export-formats.md) | Every export tool, its format and which receiver it suits |
| [Backing up everything](backing-up-everything.md) | What a complete copy contains and how to hold your own |
| [Documentation handover](documentation-handover.md) | Which documents the supplier delivers and what the site produces itself |
| [Transition to another provider](transition-to-another-provider.md) | Moving the same site to a new host or support partner |
| [Transition to another system](transition-to-another-system.md) | Moving content from Exponential to a different CMS |
| [Keeping public addresses working](url-redirects-after-exit.md) | Building and testing the redirect table |
| [Personal data at exit](personal-data-at-exit.md) | Exporting only what is needed, passwords, consent, deletion |
| [Lock-in clauses to avoid](lock-in-clauses-to-avoid.md) | Contract terms to refuse and model wording to ask for |
| [Keys and secrets handover](keys-and-secrets-handover.md) | Secrets that cannot be recreated and secrets to rotate |

## Reading order

1. Before the tender: [Why plan the exit](why-plan-the-exit.md), [Lock-in clauses to avoid](lock-in-clauses-to-avoid.md).
2. At contract signature: [Exit plan template](exit-plan-template.md), [Data export formats](data-export-formats.md).
3. During the contract, yearly: [Backing up everything](backing-up-everything.md),
   [Documentation handover](documentation-handover.md), a test of the plan.
4. At the exit: the transition page that fits, then [redirects](url-redirects-after-exit.md),
   [personal data](personal-data-at-exit.md) and [keys and secrets](keys-and-secrets-handover.md).

The cost of exit belongs in the [total cost of ownership](../total-cost-of-ownership/README.md) model; evaluating
whether a bidder makes exit easy is part of [Evaluation](../evaluation/README.md).

## Principles of this section

- **Plan before signing; test every year.** An untested export is a hope, not a plan.
- **Hold your own copy.** Backups, settings, keys and code are delivered to you, not only kept by the supplier.
- **Documented commands only.** Every command and format comes from
  [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md) and the hosting section.
- **Personal data moves only as far as needed**, over encrypted channels, and is deleted when agreed.

## Related pages
- [Procurement: overview](../README.md)
- [Governments: start here](../../government-start-here.md)
- [Migrating away from Exponential](../../../../install/migration/leaving-exponential.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
