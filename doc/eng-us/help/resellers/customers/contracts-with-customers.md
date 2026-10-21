---
title: Contracts with customers
audience: reseller, partner
category: reselling
order: 350
summary: What a reseller's contract with an end customer for an Exponential-based offer should contain - parties and subcontractors, scope, service levels, license statement, ownership, data processing, security, changes, term and renewal, exit and handover - with a clause checklist. Not legal advice.
keywords: contract, customer contract, agreement, terms, scope, service levels, license, ownership, data processing, DPA, subcontractors, warranty, liability, term, renewal, termination, exit, handover
type: help
---
# Contracts with customers

This page is for owners and account managers who write or review the contract between a reseller and an end
customer. It lists what such a contract should cover when the offer is built on Exponential. It is **not legal
advice**: have your contract template reviewed by a lawyer in your jurisdiction.

## Contents

1. [The parts of the contract](#the-parts-of-the-contract)
2. [Clauses particular to Exponential](#clauses-particular-to-exponential)
3. [Subcontractors, including 7x](#subcontractors-including-7x)
4. [Exit and handover](#exit-and-handover)
5. [Clause checklist](#clause-checklist)

## The parts of the contract

| Part | Content | Page |
|---|---|---|
| Parties | Your company and the customer; you are responsible for your subcontractors | below |
| Scope | Sites, siteaccesses, languages, extensions, services in the tier | [Packaging offers in tiers](../offers/packaging-offers-in-tiers.md) |
| Service levels | Support hours, response times, availability, backups, security releases | [Service level design](../offers/service-level-design.md) |
| Responsibilities | Who does what (a responsibility matrix) | [Managed service reseller](../models/managed-service-reseller.md#a-responsibility-matrix-for-the-contract) |
| License statement | Exponential is GPL v2 or later; no license fee | below |
| Ownership | Content, domain, custom code, design assets, accounts | [Agencies reselling to end customers](../models/agency-reselling-to-end-customers.md#who-owns-what) |
| Data processing | Processing terms, subprocessors, data location, breach notice | [Data protection for resellers](../responsibilities/data-protection.md) |
| Security | Updates, incident handling, customer duties (passwords, accounts) | [Security responsibilities of resellers](../responsibilities/security-responsibilities.md) |
| Changes | How change requests are made and priced | [Change requests](../../customers/partners-and-hosts/change-requests.md) |
| Fees and invoicing | Prices, payment terms, price changes with notice | [Invoicing and taxes](../responsibilities/invoicing-and-taxes.md) |
| Term and renewal | Length, automatic renewal, notice periods | [Renewals and churn](renewals-and-churn.md) |
| Exit | Export, handover, deletion of data | [Handing over](handing-over.md) |
| Warranty and liability | What you warrant, limits | Your lawyer |

## Clauses particular to Exponential

```text
Software. The site runs on Exponential, free software under the GNU General Public License, version 2 or
any later version. No license fee is charged for the software. Fees under this contract are for the
services described in Schedule <n>.

Customer's rights. Nothing in this contract restricts the rights the Customer has under the GNU General
Public License for copies of the software it receives.

Releases. <Company> applies Exponential releases as described in the service levels. Fixes to Exponential
itself are made by the Exponential project and published as new releases; <Company> does not warrant
that a particular fix will be published by a particular date.

Warranty. <Company> warrants its own services as described in Schedule <n>. The GNU GPL provides the
software without warranty; any warranty is given by <Company> under this contract.
```

The GPL allows you to "offer warranty protection in exchange for a fee" (section 1); the software itself comes without
warranty from the project ([Licensing explained for buyers](../../business/licensing-for-buyers.md)).

## Subcontractors, including 7x

- Name yourself as responsible to the customer for every service, including those delivered by subcontractors.
- The partner contract says "Partners are fully responsible for their subcontractors"
  (<https://exponential.earth/partner/program>); the same logic applies when 7x is your subcontractor
  ([White-label reseller](../models/white-label-reseller.md)).
- Data protection law may require you to list subprocessors; check yours.

## Exit and handover

A good exit clause makes the sale easier. State:

- [ ] what the customer receives on exit: a database dump, `var/storage`, `settings/override` and
      `settings/siteaccess`, custom code you agreed to hand over, documentation;
- [ ] within how many days, in which format, and how passwords are transferred (never by plain e-mail);
- [ ] that the domain is in the customer's name;
- [ ] when and how you delete the customer's data after the exit;
- [ ] the price of exit work beyond the included export, if any.

Details: [Handing over](handing-over.md) and the customer's own [Handover checklist](../../customers/partners-and-hosts/handover-checklist.md).

## Clause checklist

- [ ] Parties, and responsibility for subcontractors.
- [ ] Scope with sites, languages, extensions.
- [ ] Service levels your own staff can meet.
- [ ] Correct license statement; no "license fee" for the software.
- [ ] Ownership of content, domain, custom code, design assets.
- [ ] Data processing terms and subprocessor list.
- [ ] Security: release rhythm, incident notice, customer duties.
- [ ] Change requests and pricing.
- [ ] Fees, price changes with notice, payment terms.
- [ ] Term, renewal, notice periods.
- [ ] Exit, handover and deletion.
- [ ] Reviewed by a lawyer.

## Related pages

- [Contracts and SLAs (for site owners)](../../customers/partners-and-hosts/contracts-and-slas.md)
- [Maintenance agreement template](../../customers/partners-and-hosts/maintenance-agreement-template.md)
- [The partnership agreement](../../partners/the-partnership-agreement.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
