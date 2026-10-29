---
title: Pre-sales questions and answers
audience: reseller, partner, sales-marketing
category: reselling
order: 270
summary: The questions a reseller asks a prospect in discovery, and the questions prospects ask a reseller - license and cost, hosting, databases, languages, multi-site, shop, security, privacy, updates, support, exit - with sourced answers and the page that proves each one.
keywords: pre-sales, discovery, questions, answers, prospect, requirements, license, cost, hosting, databases, languages, multisite, shop, security, privacy, updates, support, exit, tender
type: help
---
# Pre-sales questions and answers

This page is for sales staff and pre-sales consultants. The first half lists what to ask a prospect. The second half
answers what prospects ask you, each answer with the page that proves it. When a question is not covered, say "I will
find out and send you the source" rather than guess.

## Contents

1. [Questions to ask the prospect](#questions-to-ask-the-prospect)
2. [Questions prospects ask](#questions-prospects-ask)
3. [Questions to pass on](#questions-to-pass-on)

## Questions to ask the prospect

```text
Organization and goal
  What should the site achieve in one year? Who are its visitors?
Content
  Which kinds of content (pages, news, events, products, documents)? Roughly how many items?
  Who edits, how many editors, do they need approval steps?
Sites and languages
  How many sites, brands or countries? Which languages for visitors? Which for editors?
Commerce
  Do you sell online? Which countries, currencies, payment providers?
Integration
  Which systems must the site talk to (single sign-on, CRM, accounting, search)?
Hosting and operations
  Hosted by us, by you, or by another provider? Any data location requirements?
Compliance
  Accessibility, privacy, records, security requirements? Any audit coming?
Today
  Which system do you use now? What must move? What do you dislike about it?
Budget and timeline
  Budget range for the project and per month? Launch date? Who decides?
```

## Questions prospects ask

| Question | Answer | Proof |
|---|---|---|
| What does the license cost? | Nothing: "Using Exponential under the terms of the GNU GPL is free of charge"; you pay for services | `README.md`; [Licensing explained for buyers](../../business/licensing-for-buyers.md) |
| Can we leave you later? | Yes: the code is GPL, your content is in a standard database, and we hand everything over | [Handing over](../customers/handing-over.md), [Comparing Exponential factually](../../business/comparing-exponential-factually.md#no-lock-in-point-by-point) |
| Which databases? | SQLite 3 (the default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, in that order of preference | [Database support](../../database-support.md) |
| Which PHP versions? | 8.0 to 8.5 | [Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md) |
| Can we have several sites? | Yes, as siteaccesses on one installation, sharing content and users where wanted | [Multi-site offers](../offers/multi-site-offers.md) |
| Which languages? | 359 locale files for content; administration translations at three support levels; right to left supported | [Multilingual offers](../offers/multilingual-offers.md) |
| Can we sell online? | Yes, a shop is built in; some things (stock, coupons, shipping labels) are not included | [E-commerce offers](../offers/e-commerce-offers.md) |
| How is security handled? | Private reports to security@se7enx.com with a first answer within one business day; fixes in the next 6.0.x release; hardening guide | `SECURITY.md`, [Security hardening](../../security-hardening.md) |
| Can editors sign in with two steps? | Yes, two-step sign-in exists | [Two-step sign-in](../../../guides/two-factor-login.md) |
| What about privacy and GDPR? | Features such as consent records, masked logs and erasure on account removal; the operator still decides purposes and answers requests | [Privacy standards](../../../standards/privacy.md) |
| Is it accessible? | The administration is tested against WCAG 2.2 AA with the status "Partly complies"; your public design must be audited | [Accessibility standards](../../../standards/accessibility.md) |
| Who keeps it up to date? | We do, under the care plan; the project releases 6.0.x versions and publishes no release calendar | [Keeping customers current](../customers/keeping-customers-current.md) |
| Is there an SLA from the project? | No; the project publishes no SLA. Our contract gives you ours | [Service level design](../offers/service-level-design.md) |
| Is it still maintained? | Yes: fifteen 6.0.x releases from 1 January 2024 to 7 June 2026, 6.0.15 in development | [Handling objections](../../business/objection-handling.md) |
| Is this the old product under a new name? | Exponential continues the old code base, a product its creator discontinued after almost 18 years; it is maintained today by 7x and the Exponential Foundation | [Who is 7x?](../../who-is-7x.md) |

Never answer with a feature you have not checked in the version you will deliver. The help's feature pages
(`doc/eng-us/features/6.0/`) and the business pages are the sources.

## Questions to pass on

| Question | To whom |
|---|---|
| Partner status, OEM terms, white-label prices | The partner team, <partners@exponential.earth> |
| Deep technical questions in a large bid | 7x pre-sales support, from Professional ([Pre-sales support](../../partners/pre-sales-support.md)) |
| Legal interpretation of the GPL for their product | Their legal adviser |
| Tax treatment of your invoices | Their accountant and yours |

## Related pages

- [Feature highlights for customers](../../business/feature-highlights-for-customers.md)
- [Who Exponential is for](../../business/who-exponential-is-for.md)
- [Handling objections as a reseller](handling-objections.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
