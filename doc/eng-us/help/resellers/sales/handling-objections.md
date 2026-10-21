---
title: Handling objections as a reseller
audience: reseller, partner, sales-marketing
category: reselling
order: 280
summary: The objections resellers hear - about the software (security, maintenance, longevity, lock-in, skills) and about the reseller itself (why pay for free software, why you and not 7x, what if you disappear) - with answers built only from sources, and the honest answer where the sources say no.
keywords: objections, objection handling, security, maintained, longevity, lock-in, skills, free software, price, why pay, reseller risk, exit, honest answer
type: help
---
# Handling objections as a reseller

This page is for sales staff at resellers. Buyers raise two kinds of objection: about Exponential itself, and about
buying it from you. The first kind is covered in depth on [Handling objections](../../business/objection-handling.md);
this page summarizes it and adds the second kind.

## Contents

1. [A method for any objection](#a-method-for-any-objection)
2. [Objections about Exponential](#objections-about-exponential)
3. [Objections about reselling](#objections-about-reselling)
4. [When the honest answer is no](#when-the-honest-answer-is-no)
5. [Record new objections](#record-new-objections)

## A method for any objection

1. Listen to the end and write the objection down in the buyer's words.
2. Acknowledge it: most objections are about risk.
3. Ask what is behind it ("When you say support, do you mean security fixes, someone to call, or PHP versions?").
4. Answer with a source and offer to send the link.
5. Offer a check the buyer can do themselves.
6. Say what you do not know, and follow up.

Source: [Handling objections](../../business/objection-handling.md#a-method-for-any-objection).

## Objections about Exponential

| Objection | Short answer | Proof |
|---|---|---|
| "Open source is less secure." | Private vulnerability reporting with a first answer within one business day; fixes in the next 6.0.x release; a Content Security Policy, an audit trail, two-step sign-in and a hardening guide | `SECURITY.md`, [Security hardening](../../security-hardening.md) |
| "Is it still maintained?" | Fifteen 6.0.x releases from 1 January 2024 to 7 June 2026, 6.0.15 in development | [Who is 7x?](../../who-is-7x.md#the-60x-releases) |
| "Will it last?" | A code base kept since 1999, a company and a nonprofit behind it, and a GPL license that keeps the code available to everyone | [Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md) |
| "We will be locked in." | GPL code, six databases, almost any operating system, documented moves between databases | [Comparing Exponential factually](../../business/comparing-exponential-factually.md#no-lock-in-point-by-point) |
| "Nobody knows this system." | The sources do not count developers; they document the stack and provide free learning material | [Handling objections](../../business/objection-handling.md#skills-can-we-find-people-who-know-it) |
| "Upgrades are risky." | Published versions never change; an upgrade check in the administration | [The upgrade check](../../../guides/upgrade-check.md) |
| "Isn't it the old discontinued product?" | Exponential continues a code base whose original product its creator discontinued after almost 18 years; fifteen 6.0.x releases have followed since 1 January 2024 | [Who is 7x?](../../who-is-7x.md) |

## Objections about reselling

| Objection | Answer |
|---|---|
| "Why pay you if the software is free?" | You pay for work, not for a license: servers, updates applied every month, tested backups, people who know your site, and a clean handover if you leave. Show the tier sheet ([Packaging offers in tiers](../offers/packaging-offers-in-tiers.md)). |
| "Why not buy from 7x directly?" | You can, and we can introduce you. What we add: <your region, language, sector knowledge, response hours, local contract>. If 7x fits better, we refer you ([Referral-only reselling](../basics/referral-only-reselling.md)). |
| "What if your company disappears?" | Your site does not depend on us: the code is GPL, the content is yours, and our contract includes a handover. Another provider can take over ([Handing over](../customers/handing-over.md)). |
| "Are you official?" | Answer with your exact status: "Exponential Partner (<Level>)", or "we are not a partner" ([Naming and brand for resellers](naming-and-brand.md)). |
| "Your response times are slower than the partner plan's." | The partner plan's response times are what 7x promises us, not a promise to end customers; ours are measured on our own team ([Service level design](../offers/service-level-design.md)). |
| "Can you guarantee the bug is fixed by Friday?" | We can guarantee what we control: diagnosis, a workaround, a rollback. Fixes in Exponential come in the next release, which we apply as agreed. |
| "We want the source code." | You have every right to the source of what we distribute to you; for a site we only host, ask us for a copy as part of the handover. |

## When the honest answer is no

Say these yourself; each is documented on [Comparing Exponential factually](../../business/comparing-exponential-factually.md#where-exponential-is-not-the-right-fit):

- Microsoft SQL Server is required: no driver in 6.0.x.
- Several replicas sharing one SQLite file: not possible; use a database server.
- More than 62 content languages in one installation: not possible.
- A published project SLA document: the project publishes none; service terms come from a provider.
- A Symfony-only project without the legacy kernel: a different product of the family, not Exponential 6.

A lost deal is cheaper than a customer who was promised something that does not exist.

## Record new objections

```text
| Date | Objection (buyer's words) | Answer given | Source | Outcome | Page to update |
|------|---------------------------|--------------|--------|---------|----------------|
```

Share new objections with your team, and with the project if the documentation should answer them
([Contributing documentation and translations](../../partners/contributing-docs-and-translations.md)).

## Related pages

- [Handling objections](../../business/objection-handling.md)
- [Factual comparisons for resellers](factual-comparisons.md)
- [Pre-sales questions and answers](pre-sales-questions-and-answers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
