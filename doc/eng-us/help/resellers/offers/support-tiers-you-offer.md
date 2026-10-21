---
title: Support tiers you offer
audience: reseller, partner, hosting
category: reselling
order: 200
summary: How a reseller structures the support it sells - the line between hosting support and application support, tier options from basic to development, what each tier needs from your team, how partner support and paid 7x support sit behind your tiers, and a tier sheet to copy.
keywords: support tiers, support levels, hosting support, application support, managed, development, first line, second line, escalation, partner tickets, tier sheet
type: help
---
# Support tiers you offer

This page is for support leads and product managers at resellers. It turns the support you can give into tiers that
customers understand and that your team can deliver. Response times are covered on
[Service level design](service-level-design.md).

## Contents

1. [Two kinds of support](#two-kinds-of-support)
2. [Tier options](#tier-options)
3. [Lines of support](#lines-of-support)
4. [What customers can do themselves](#what-customers-can-do-themselves)
5. [A tier sheet to copy](#a-tier-sheet-to-copy)

## Two kinds of support

| Hosting support | Application support |
|---|---|
| The server, PHP, the engine, the database server | Content classes, templates, design |
| Backups and restores | The customer's extensions and custom code |
| TLS certificates | Editorial questions ("how do I publish?") |
| Mail delivery from the server | Data fixes inside the content |
| Applying Exponential updates, where agreed | Upgrades that need code changes |

Source: [Support tiers for Exponential hosting](../../hosting/business/support-tiers.md). Write this line into your
support terms; most friction comes from leaving it unclear.

## Tier options

| Tier | Typically includes | Suits | Your team needs |
|---|---|---|---|
| Basic | Hosting support by ticket, business hours | Shared plans | Operations staff |
| Standard | Hosting support, faster first response, phone or chat | VPS plans | More operations staff |
| Managed | Hosting support plus Exponential updates, cache and performance tuning, restore on request | Managed plans | Someone who owns updates |
| Application | Managed plus help with templates, extensions and configuration, by agreement | Customers without developers | Exponential developers |
| Development | Project work: migrations, new features, extensions | Billed as a project | A development team |

## Lines of support

```text
Customer
  -> 1st line (your company): takes the request, collects the facts, answers known questions
  -> 2nd line (your company): developers and administrators who know the site
  -> 3rd line (outside):
       - your partner support tickets at 7x, by level
       - paid support from 7x or another supplier
       - the Exponential issue tracker for reproducible bugs
       - security@se7enx.com, privately, for vulnerabilities
```

The third line is never visible as a promise in your customer's contract. Escalation is described on
[Escalation for resellers](../customers/escalation.md).

## What customers can do themselves

Every ticket a customer can solve alone saves you money. Point them to:

| Need | Self-service |
|---|---|
| How to edit and publish | [Getting started](../../getting-started.md), [Editing content](../../editing-content.md) |
| What is the error | The error reference on the error page, which your staff look up ([Finding an error by its reference](../../../guides/finding-an-error-by-its-reference.md)) |
| Is the site healthy | [Customers: start here](../../customers/customers-start-here.md) and the owner pages it lists |
| When to call whom | [When to call whom](../../customers/partners-and-hosts/when-to-call-whom.md) |

You may also hand customers adapted copies of these pages under your own brand
([Documentation you can give your customers](../customers/documentation-for-your-customers.md)).

## A tier sheet to copy

```text
Tier: ______________   Price per month: $______
Kind: hosting support / application support / both
Channels: ticket / e-mail / phone (S1 only) / chat
Hours: ______________ (time zone ______)
First response: S1 ____ S2 ____ S3 ____ S4 ____
Included: ______________________________________
Not included: __________________________________
Escalation to 3rd line: by us, not by the customer
Report: monthly / quarterly / none
```

## Related pages

- [Support tiers for Exponential hosting](../../hosting/business/support-tiers.md)
- [Support contract reseller](../models/support-contract-reseller.md)
- [Opening a partner support ticket](../../partners/opening-a-partner-support-ticket.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
