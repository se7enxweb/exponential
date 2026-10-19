---
title: Extranets and partner portals
audience: businesses, project-manager, partner
category: business-solutions
order: 1
summary: The landing page for organizations that give partners, customers, dealers or suppliers a private area on Exponential - what the section covers, who it is for, every page with one line, a suggested order and the neighboring sections.
keywords: extranet, partner portal, customer portal, dealer portal, supplier portal, B2B, registration, approval, siteaccess, separate host, sections, object states, file sharing, permissions review
type: help
---
# Extranets and partner portals

This section is for organizations that share information with people outside the company: resellers who need price
lists, customers who download their documents, dealers who order, suppliers who upload certificates. It is written
for the managers who own those relationships and for whoever builds and runs the portal. Each page explains what
Exponential offers for the task and links to the technical guides.

## Contents

1. [What an extranet on Exponential is](#what-an-extranet-on-exponential-is)
2. [How it differs from an intranet](#how-it-differs-from-an-intranet)
3. [The pages of this section](#the-pages-of-this-section)
4. [A suggested order](#a-suggested-order)
5. [Neighboring sections](#neighboring-sections)
6. [Related pages](#related-pages)

## What an extranet on Exponential is

An extranet is an Exponential site, usually a siteaccess on its own host name, where outside users sign in and see
what their organization is entitled to. Everything it needs is in the kernel:

| Need | Exponential feature | Source |
|---|---|---|
| Partners register, the company approves | registration with e-mail verification, the "New user registered" mail with signed approve and reject links, an Approve workflow | [Signed links in the "New user registered" mail](../../../features/6.0/registration-admin-links.md) |
| A separate address such as `partners.example.com` | siteaccess matching by host | [Siteaccess by host prefix](../../../features/6.0/siteaccess-host-and-language-matching.md) |
| Each partner sees only its own area | sections, subtree limitations, object states | [Sections](../../../guides/sections.md), [Object states](../../../guides/object-states.md) |
| Partners download documents safely | `content/download` checks permissions | [Files and downloads](../../creators/website-creators/files-and-downloads.md) |
| Partner groups get their own prices | shop discount groups per user group | [Products, prices and VAT](../../creators/website-creators/products-prices-and-vat.md#discounts) |
| Regular checks of who has access | role pages, Check user accounts, the audit trail | [Check user accounts](../../../features/6.0/user-hygiene.md), [The audit trail](../../../features/6.0/audit-trail.md) |

> **Note**
> This section describes what the repository contains. It names no customers and quotes no statistics.

## How it differs from an intranet

| | Intranet | Extranet |
|---|---|---|
| Users | your staff | people of other organizations |
| Where accounts come from | your directory or administrators | registration with approval, or created by your staff |
| Main risk | wrong department sees a page | one partner sees another partner's data |
| Typical structure | areas per department | areas per partner, tier or region |
| Leaving | staff leave through HR | partner contracts end; accounts must be closed |

The tools are the same, so the [Intranets](../intranets/README.md) section is useful reading too.

## The pages of this section

| Page | What it answers |
|---|---|
| [Partner portal planning](partner-portal-planning.md) | What to decide first: audiences, tiers, what is shared, who approves |
| [Registration and account approval](registration-and-account-approval.md) | How outside users get accounts, and how the company approves or rejects them |
| [Customer and partner areas](customer-and-partner-areas.md) | Giving each partner or tier its own area with sections, subtrees and states |
| [Sharing files with partners](sharing-files-with-partners.md) | Documents, price lists and uploads, with downloads that check permissions |
| [Separate hosts and siteaccesses](separate-hosts-and-siteaccesses.md) | A portal on its own address, beside the public site, with its own sign-in rules |
| [Dealer and supplier portals](dealer-and-supplier-portals.md) | Ordering with group prices, product information, forms for suppliers |
| [Extranet permissions review](extranet-permissions-review.md) | A routine for checking who can see what, and removing what is no longer needed |
| [Extranet launch checklist](extranet-launch-checklist.md) | Everything to tick before the first partner is invited |
| [Extranet FAQ](extranet-faq.md) | Short answers to common questions |

## A suggested order

1. [Partner portal planning](partner-portal-planning.md) with the people who own partner relationships.
2. [Separate hosts and siteaccesses](separate-hosts-and-siteaccesses.md) to decide the address and shape.
3. [Customer and partner areas](customer-and-partner-areas.md) to decide the structure.
4. [Registration and account approval](registration-and-account-approval.md) to decide how accounts arrive.
5. [Sharing files with partners](sharing-files-with-partners.md) and, for trade portals,
   [Dealer and supplier portals](dealer-and-supplier-portals.md).
6. [Extranet launch checklist](extranet-launch-checklist.md), then the
   [Extranet permissions review](extranet-permissions-review.md) as a recurring routine.

## Neighboring sections

- [Intranets](../intranets/README.md): the same tools for staff.
- [Member sites](../members/README.md): open registration for the public.
- [Stores](../stores/README.md): the built-in shop for selling online.
- [Security](../security/README.md): access reviews, GDPR basics, accounts and passwords.
- [Multisite](../multisite/README.md): several sites in one installation.
- Public bodies: Governments start here.

## Related pages

- [Businesses: start here](../businesses-start-here.md)
- [How businesses sell online](../how-businesses-sell-online.md)
- [Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
