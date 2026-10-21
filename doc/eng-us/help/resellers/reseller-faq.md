---
title: Reseller FAQ
audience: reseller, partner, hosting, sales-marketing
category: partnership
order: 510
summary: Short answers to the questions resellers of Exponential-based offers ask most - licenses and what you may charge, partner levels and prices, commissions, white label and OEM, service levels, naming, demos, documentation, updates and data protection - each with the page that explains it.
keywords: FAQ, questions, answers, reseller, license, charge, partner level, price, commission, white label, OEM, SLA, naming, demo mode, documentation, updates, GDPR
type: help
---
# Reseller FAQ

This page is for anyone at a reseller who needs a quick answer. Each answer links to the page with the details. Where
the sources are silent, the answer says so: ask the partner team, <partners@exponential.earth>.

## Contents

1. [Licenses and money](#licenses-and-money)
2. [The partner program](#the-partner-program)
3. [Offers and promises](#offers-and-promises)
4. [Selling](#selling)
5. [Running customer sites](#running-customer-sites)

## Licenses and money

**May we charge for Exponential at all?**
Yes, for services, products, your own work, warranty and distributing copies. Not for a license to use it: there is
none ([What reselling means for free software](basics/what-reselling-means-for-free-software.md)).

**Must we publish our customers' sites or code?**
No. Running a site is not distributing. When you hand over copies, the recipients get the GPL rights and the source
([Distribution obligations checklist](responsibilities/distribution-obligations-checklist.md)). Not legal advice.

**Can we keep our own extensions closed?**
Code you do not distribute is not affected. If you distribute it with Exponential, ask counsel
([Extension and theme bundles](models/extension-and-theme-bundles.md)).

**What do we write on invoices?**
The service you deliver, never "Exponential license" ([Invoicing and taxes](responsibilities/invoicing-and-taxes.md)).

## The partner program

**Do we have to be a partner to resell?**
No license or permission is needed to sell services around GPL software. The partner program gives benefits such as
support tickets, discounts, white label and leads ([Choosing a partner level](basics/choosing-a-partner-level.md)).

**What do the levels cost?**
Student $25, Now $45, Starter $175, Basic $300, Professional $500, Silver $800, Gold $1,000, Platinum $2,600 per month
([Partner plans and prices](../partners/partner-plans-and-prices.md)).

**How do we join?**
Apply, then pay first (buy the subscription in the 7x store), then sign the agreement and send it to
<partners@exponential.earth> ([How to apply](../partners/how-to-apply.md)).

**What do referrals pay?**
10% of the total contract value for a cold lead, 20% for a hot lead (a customer ready to purchase from 7x)
([Referral-only reselling](basics/referral-only-reselling.md)).

**From which level can we resell 7x services under our brand?**
Professional ([White-label reseller](models/white-label-reseller.md)). Which services and at what price is not
published: ask the partner team.

**Can we sell our own product based on Exponential?**
Yes; "OEM Resale Opportunities" is a benefit at every level. Details are not published: ask the partner team
([OEM product reseller](models/oem-product-reseller.md)).

**May we call ourselves an "Exponential reseller"?**
With the Distribution profile, yes. Otherwise use "Exponential Partner (<Level>)" or describe your services
([Naming and brand for resellers](sales/naming-and-brand.md)).

## Offers and promises

**Can we promise our customers the response times of our partner plan?**
No. Those are what 7x promises you. Promise what your own staff deliver
([Service level design](offers/service-level-design.md)).

**Does the project have an SLA?**
No. The only response commitment in the project's files is a first answer to a private security report within one
business day (`SECURITY.md`).

**Can one installation serve several customers?**
It can technically, but don't: siteaccesses share users, content and updates. Use separate installations for
different customers ([Multi-site offers](offers/multi-site-offers.md)).

**Does the shop do coupons and stock?**
No: discounts are tied to customer groups, and stock management is not included
([E-commerce offers](offers/e-commerce-offers.md)).

## Selling

**How do we run a public demo safely?**
On its own user, pool and database, with `EXP_DEMO_MODE=1`, rebuilt on a schedule, with no real data
([Demo sites for resellers](offers/demo-sites.md)).

**Can we give customers the help as our own manual?**
Yes, under the GNU FDL: keep the notices, include the license, give your version its own title and say what you
changed ([Documentation you can give your customers](customers/documentation-for-your-customers.md)).

**How do we answer "why pay you for free software"?**
See [Handling objections as a reseller](sales/handling-objections.md#objections-about-reselling).

## Running customer sites

**Which version must customer sites run?**
The newest 6.0.x release; older tags are fixed only by upgrading (`SECURITY.md`)
([Keeping customers current](customers/keeping-customers-current.md)).

**Where do we report a security problem?**
Privately to security@se7enx.com ([Escalation for resellers](customers/escalation.md)).

**Are we a processor of our customers' data?**
Usually, when you host or run their site. Ask your lawyer and put processing terms in the contract
([Data protection for resellers](responsibilities/data-protection.md)).

**What does a customer get when leaving?**
A database dump, `var/storage`, `settings/override` and `settings/siteaccess`, agreed code and documentation
([Handing over](customers/handing-over.md)).

## Related pages

- [Partner FAQ](../partners/partner-faq.md)
- [Reseller glossary](reseller-glossary.md)
- [Resellers: start here](resellers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
