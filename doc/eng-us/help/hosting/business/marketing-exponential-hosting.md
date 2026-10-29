---
title: Marketing Exponential hosting
audience: hosting, sales-marketing
category: hosting
order: 60
summary: How a host markets Exponential hosting truthfully: the facts you can use with their sources, claims you must not make, naming rules, the content of a landing page, and a launch checklist.
keywords: marketing, hosting offer, landing page, claims, facts, naming, brand, Exponential, launch, SEO, messaging, proof points, open source, GPL, no license fee, Velocity
type: help
---
# Marketing Exponential hosting

This page is for marketing staff at a hosting company that offers Exponential hosting. It answers: which facts can
you use and where do they come from, what must you not claim, how do you name things, and what belongs on the
landing page of your offer.

The general rules for anyone who talks about Exponential in public are in the Sales and marketing section of this
help, starting at [Sales and marketing: start here](../../business/sales-and-marketing-start-here.md). This page adds
what is specific to hosting.

## Contents

1. [Facts you can use](#facts-you-can-use)
2. [Claims you must not make](#claims-you-must-not-make)
3. [Naming](#naming)
4. [A landing page for your offer](#a-landing-page-for-your-offer)
5. [Messages for different buyers](#messages-for-different-buyers)
6. [A launch checklist](#a-launch-checklist)

## Facts you can use

Each fact has a source a customer can check.

| Fact | Source |
|---|---|
| Exponential is "a professional PHP application framework with advanced CMS (content management system) functionality" | `README.md` |
| Free software under the GNU GPL v2 or later; no license fee | `COPYRIGHT.md`, `README.md` |
| Runs on PHP 8.0 to 8.5 (PHP 7.4 usable without support) | [Chapter 2](../../../install/02-requirements.md) |
| Six supported databases: SQLite 3 (default), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Database support](../../database-support.md) |
| Runs on almost any operating system that runs PHP 8 | [Operating system support](../../os-support.md) |
| Ships its own web server and engine, Exponential Velocity, with HTTPS certificates of its own | [Chapter 8](../../../install/08-serving-the-site.md), [HTTPS certificates](../../../features/6.0/velocity-https-certificates.md) |
| An enforced Content Security Policy in the administration | [Content Security Policy](../../../features/6.0/content-security-policy.md) |
| Security reports get "a first answer within one business day"; published versions never change | `SECURITY.md` |
| A long history: continues the code base of eZ Publish, a product its creator discontinued after almost 18 years | [Who is 7x?](../../who-is-7x.md) |
| Maintained by 7x and the Exponential Foundation | [Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md) |

What your own offer adds (your data center, your support hours, your backups) you state as your facts, with your
own evidence.

## Claims you must not make

| Do not say | Why | Say instead |
|---|---|---|
| "Certified" or "official" Exponential hosting | no certification program is documented | "Exponential hosting by <your company>" |
| "Partner of 7x" or "of the Foundation", unless you have an agreement | not documented for you | describe your actual relationship |
| A number of installations, users or customers of Exponential | no source states one | name the public places: GitHub, Packagist, the forums |
| Performance figures you have not measured on your own platform | no published benchmark covers your hardware | your own measurement, with method and date |
| Compliance certifications for the software ("GDPR certified") | software is not certified by such statements | describe the features, see [Compliance and GDPR](../security/compliance-and-gdpr.md) |
| That the project is associated with eZ Systems or Ibexa | the README says the opposite | "This project is not associated with the original eZ Publish software or its original developer, eZ Systems or Ibexa" |
| An SLA of the project | none exists | your own SLA ([SLA options](sla-options.md)) |

## Naming

- The product is **Exponential**; this release line is **Exponential 6**. The old name is used only as history:
  "continues the code base of eZ Publish, a product its creator discontinued after almost 18 years".
- The engine is **Exponential Velocity** (short: Velocity).
- The company is **7x**; the nonprofit is the **Exponential Foundation**.
- Name your offer as yours: "<Your company> Exponential Hosting" is clear; avoid names that suggest it comes from
  the project.

Full rules: [Brand and naming rules](../../business/brand-and-naming-rules.md).

## A landing page for your offer

| Section | Content |
|---|---|
| Headline | what you offer, for whom, in one sentence |
| Why Exponential | three or four facts from the table above, with links |
| What is included | the building blocks of your plans ([Designing hosting plans](designing-hosting-plans.md)) |
| Plans | your plan table, with the limits you measured |
| Security | your hardening, backups and update policy; link the project's `SECURITY.md` |
| Migration | how you move a customer's site in ([Migrating customers](../migrations/migrating-customers-to-your-hosting.md)) |
| Support | your tiers and hours ([Support tiers](support-tiers.md)) |
| About the software | a link to the project and to this free documentation |
| Contact | how to reach your sales team |

> **Tip**
> Link to the help pages instead of copying them. If you copy, you may: the documentation is under the GNU FDL 1.3,
> so keep the license notice ([Free documentation](../../free-documentation.md)).

## Messages for different buyers

| Buyer | What matters to them | Message |
|---|---|---|
| Agency | many sites, quick installs, staging | one-command installs, Git-based deploys, a staging add-on |
| Organization with in-house editors | reliability, backups, support | tested restores, an SLA you can show, editorial help in the product |
| Developer | shell, Git, Composer, PHP versions | shell access, PHP 8.x choice, Velocity or a traditional stack |
| Public sector or regulated | security and data handling | hardening, the CSP, audit trail, masked request logs; your data location |

## A launch checklist

- [ ] Every fact on the landing page links to its source.
- [ ] No claim from the "must not" table.
- [ ] The product is called Exponential everywhere.
- [ ] Plan limits come from your measurements, with the date.
- [ ] The SLA and support terms are reviewed by your legal adviser.
- [ ] A test customer has gone through ordering, installation and the first backup restore.
- [ ] Support staff have read the [runbooks](../runbooks/README.md).

## Related pages

- [Positioning and value proposition](../../business/positioning-and-value-proposition.md)
- [The community and ecosystem as a selling point](../../business/community-and-ecosystem.md)
- [Partner programs](partner-programs.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
