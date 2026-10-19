---
title: What you do not pay for
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 20
summary: The cost lines you can leave out of a budget for Exponential, each with its source - no license fee, no per-user or per-server fee, no required database server or separate web server, and many features that ship in the box - and the costs that remain.
keywords: no license fee, free of charge, GPL, per user, per seat, per server, SQLite, Velocity, built-in features, shop, newsletter, two-factor, audit trail, vendor lock-in, exit cost
type: help
---
# What you do not pay for

This page is for anyone who prepares a budget and wants to know which cost lines simply do not exist with
Exponential. Each line below names its source in the repository, so procurement or finance can check it. The page
also lists what you still pay for, so the budget stays honest.

> **Note**
> This page describes the software's license and contents. It is not legal advice; for questions about your own
> plans with the license, see [Licensing explained for buyers](../../business/licensing-for-buyers.md) and ask your
> legal adviser.

## Contents

1. [No license fee](#no-license-fee)
2. [No fee per user, server or site](#no-fee-per-user-server-or-site)
3. [No required database server or web server](#no-required-database-server-or-web-server)
4. [Features that ship in the box](#features-that-ship-in-the-box)
5. [No exit fee](#no-exit-fee)
6. [What you still pay for](#what-you-still-pay-for)
7. [Checklist for the budget](#checklist-for-the-budget)

## No license fee

- "Using Exponential under the terms of the GNU GPL is free of charge." (`README.md`, "License")
- "The Professional License is no longer available." The old paid license is gone; Exponential is
  "GNU GPLv2-or-later ONLY" (`README.md`, "License").
- `composer.json` declares `"license": "GPL-2.0-or-later"`.

There is therefore no yearly subscription to renew for the software itself, and no renewal that can lapse and stop
a site.

## No fee per user, server or site

Section 0 of the GPL (`LICENSE.md`) says: "The act of running the Program is not restricted". In practice:

| You may, without a fee | Why |
|---|---|
| Add editors, administrators and members without limit | No clause counts users |
| Run several sites from one installation | Multi-site support is a built-in feature (`README.md`, "Main Exponential features"); see [Multisite](../multisite/README.md) |
| Add servers for load or availability | Running is not restricted; clusters are supported (`README.md`, "Support for highly available and scalable configurations") |
| Keep a staging and a test copy | Copies for your own use are running the program |

## No required database server or web server

- **SQLite 3 is the default database** and "SQLite needs none" (a database server), `README.md`, "Requirements".
  [Database support](../../database-support.md) calls it "a full production database for a site served from one
  machine". One server less to buy, run, patch and back up.
- **Exponential Velocity** is a web server "bundled with Exponential" that "needs nothing installed beyond PHP"
  (`README.md`, "Requirements" and "How Exponential is served"). It serves HTTPS itself and can obtain and renew
  certificates from Let's Encrypt (`README.md`, "When a traditional server is still the right answer").
- If you need a database server later (more than one machine writing, many editors), PostgreSQL is the recommended
  one ([Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md)). If
  you pick a commercial database server, its own license terms apply; check them with its vendor.

## Features that ship in the box

From `README.md`, "Main Exponential features", and the extensions `composer.json` requires:

| Need | Included | Read more |
|---|---|---|
| Online store | "Advanced e-Commerce Stores (Built in)" with VAT, currencies, orders; PayPal through `ezpaypal` | [Stores](../stores/README.md) |
| Newsletters | `cjw_newsletter` is required by `composer.json` | [Newsletters](../newsletters/README.md) |
| Two-step sign-in | `sevenx_authentication_2fa` is required and the installers activate it | [Two-step sign-in](../../../guides/two-factor-login.md) |
| Audit trail | "A tamper-evident audit trail with a console, alerts, exports and extension channels" | [The audit trail](../../../features/6.0/audit-trail.md) |
| Search | "Built in search engine"; Solr through `ezfind` is optional | [Site search](../../creators/website-creators/site-search.md) |
| Languages | "Advanced multi-lingual support" | [Multilingual](../multilingual/README.md) |
| Headless and apps | "A headless CRUD REST API" | [Mobile and headless](../mobile-and-headless/README.md) |
| Page builder | Exponential Layouts | [Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md) |
| Forms | "Contact forms" with the information collector; reCAPTCHA and hCaptcha extensions | [Forms with the information collector](../../creators/website-creators/forms-with-the-information-collector.md) |
| Updates in the admin | `ezupdate` is required | [Automated updates with ezupdate](../../hosting/operations/automated-updates-with-ezupdate.md) |

Before you strike a line from the budget, check that the included feature does what you need: a demonstration on a
test installation is the proof.

## No exit fee

The license gives you the right to take the code to any supplier ([Licensing explained for buyers](../../business/licensing-for-buyers.md),
"What a buyer may do with the software"). The code and its history are public on GitHub
([Develop with Git](../../develop-with-git.md)). Moving a site to another host is a documented procedure
([Moving a site between hosts](../../hosting/migrations/moving-a-site-between-hosts.md)). You still pay for the work
of moving, but nobody charges you for permission.

## What you still pay for

Be clear with management about these; they are real costs on any platform:

- **Work**: design, development, migration, training and support, by your staff or by a company you choose.
  7x offers these; "Prices and terms are on the 7x website, not here" ([Who is 7x?](../../who-is-7x.md#services-and-hosting)).
- **Hosting**: servers or a plan, backups, monitoring ([Hosting cost drivers](hosting-cost-drivers.md)).
- **Third-party services**: payment providers, a hosted AI provider if you use one for Exponential Intelligence,
  mail delivery services, CAPTCHA services. Each sets its own terms.
- **A warranty or guaranteed response time**: the GPL comes with no warranty (`LICENSE.md`, sections 11 and 12);
  a supplier contract can add one ([Contracts and SLA basics](../choosing/contracts-and-slas-basics.md)).
- **Voluntary support of the project**: sponsorship and donations are optional (`README.md`, "Donate and make a
  support subscription").

## Checklist for the budget

- [ ] License line set to 0, with `README.md` "License" as the source.
- [ ] No per-user or per-server line.
- [ ] Database server line only if your plan needs one.
- [ ] Separate web server license line removed if you use Velocity or an open source web server.
- [ ] Add-on lines removed only for features you have seen working.
- [ ] Work, hosting, third-party services and support lines kept and quoted in writing.

## Related pages

- [Total cost of ownership](total-cost-of-ownership.md)
- [Comparing Exponential factually](../../business/comparing-exponential-factually.md)
- [Feature highlights for customers](../../business/feature-highlights-for-customers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
