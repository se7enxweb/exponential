---
title: Low-budget operations
audience: businesses, project-manager, admin, hosting
category: business-solutions
order: 60
summary: How a non-profit keeps an Exponential site cheap to run without making it fragile - no license fee, SQLite as the default database, hosting choices, one-command installation, the routine that prevents expensive failures, what volunteers can do, and where saving money is a false economy.
keywords: low budget, nonprofit hosting, no license fee, SQLite, shared hosting, VPS, maintenance, backups, updates, volunteer administrator, total cost, cheap website
type: help
---
# Low-budget operations

This page is for treasurers, directors and the volunteer or partner who looks after a non-profit's site. It explains
where an Exponential site costs money, which choices keep that low, and the small routine that prevents the expensive
kind of problem. It gives no prices: use your own quotes, and the cost model in [Costs](../costs/README.md).

## Contents

1. [Where the money goes](#where-the-money-goes)
2. [No license fee](#no-license-fee)
3. [SQLite: one file instead of a database server](#sqlite-one-file-instead-of-a-database-server)
4. [Hosting choices](#hosting-choices)
5. [Getting started cheaply](#getting-started-cheaply)
6. [The routine that prevents expensive problems](#the-routine-that-prevents-expensive-problems)
7. [What volunteers can do, and what needs a professional](#what-volunteers-can-do-and-what-needs-a-professional)
8. [False economies](#false-economies)
9. [A worked example](#a-worked-example)

## Where the money goes

| Cost | Can it be zero? |
|---|---|
| Software license | Yes, there is none |
| Hosting | No, but it can be small |
| Domain name and certificate | Domain no; certificates are often free from the host |
| Building the site (design, setup) | Only if a skilled volunteer does it |
| Keeping it running (updates, backups, checks) | Only if someone is reliably responsible |
| Mail delivery for newsletters | Depends on your provider |

## No license fee

"Using Exponential under the terms of the GNU GPL is free of charge" (README, "License"). The extensions this section
mentions (`cjw_newsletter`, `eztags`, Exponential Layouts, `ezpaypal`) are installed with the product through
`composer.json`, with no separate purchase. Buyers pay for "work and services, if they want them: design, development,
migration, support, hosting, training"
([Licensing for buyers](../../business/licensing-for-buyers.md#no-license-fees-and-what-buyers-do-pay-for)).
There is no per-editor, per-site or per-language fee to budget for.

## SQLite: one file instead of a database server

SQLite 3 is the first database in the official order and the default. "With SQLite, a complete Exponential site runs
from one file on disk, with no database server to install, configure or secure"
([SQLite database support](../../../features/6.0/sqlite-database.md)). For a small non-profit that means one less
service to pay for, patch and back up.

Know its limit: "SQLite allows one writer at a time. Many editors publishing at the same moment queue behind each
other ... for a busy editorial team choose MySQL, PostgreSQL or MongoDB" (same page, Limits and good practice). A
non-profit with a handful of editors is usually well within it. Moving later is possible; see
[Database support](../../database-support.md).

## Hosting choices

| Model | Fits | Note |
|---|---|---|
| Shared hosting | Very small sites | Apache or LiteSpeed with `.htaccess`, the setup wizard, SQLite or the panel's database work; Velocity usually does not ([Shared hosting](../../hosting/models/shared-hosting.md)) |
| A small virtual server | Most small non-profits | More control; someone must patch it ([VPS hosting](../../hosting/models/vps-hosting.md)) |
| Managed hosting | Organizations without technical volunteers | The host patches and backs up ([Managed hosting](../../hosting/models/managed-hosting.md)) |

Exponential Velocity, the bundled server, "needs nothing installed beyond PHP" (README, Requirements), which suits a
small virtual server. What a site needs from any host is listed in
[Hosting for a website](../../creators/website-creators/hosting-for-a-website.md); see also
[Choosing hosting](../choosing/choosing-hosting.md).

## Getting started cheaply

`./console exp:install` "installs the multisite package on **SQLite**, in `eng-US`", with a site and an
administration, and prints the login addresses
([Installing Exponential in one command](../../../features/6.0/install-in-one-command.md)). A volunteer can try the
whole product on a laptop this way before anything is bought.

The shipped `ezwebin` classes (article, event, event calendar, feedback form, file, folder, frontpage, blog) cover
most non-profit pages without custom development (`extension/ezwebin/packages/`).

## The routine that prevents expensive problems

Most expensive incidents on small sites come from missing backups, unapplied updates and nobody watching. The website
creator's routine is short ([Maintaining your website](../../creators/website-creators/maintaining-your-website.md)):

- [ ] **Backups** of database and storage, with a restore actually tested. With SQLite, back up with
      `sqlite3 <file> ".backup <target>"`, "not by copying the file while the site is busy"
      ([SQLite database support](../../../features/6.0/sqlite-database.md#limits-and-good-practice),
      [Backups and restores](../../creators/website-creators/backups-and-restores.md)).
- [ ] **Updates** of Exponential, extensions and PHP, tested on a copy first
      ([Updates and upgrades](../../creators/website-creators/updates-and-upgrades.md)).
- [ ] **Cron** running, so newsletters, indexing and cleanups happen ([Cronjobs](../../../guides/cronjobs.md)).
- [ ] **Logs** looked at monthly ([Log viewer](../../../features/6.0/log-viewer.md)).
- [ ] **Accounts** of departed volunteers deactivated ([Check user accounts](../../../features/6.0/user-hygiene.md)).

Put a name and a date next to each line; see the [Monthly operations checklist](../templates/monthly-operations-checklist.md).

## What volunteers can do, and what needs a professional

| Task | A trained volunteer | A professional or partner |
|---|---|---|
| Writing and publishing content | Yes | |
| Campaign pages in Layouts | Yes | |
| Reading forms and newsletters | Yes | |
| Monthly checks from a checklist | Yes | |
| Updates, PHP versions, server security | With experience | Recommended |
| Restoring from backup under pressure | Only if rehearsed | Recommended |
| Custom templates and classes | With experience | Recommended |

Whoever holds the administrator role, make sure a second person can take over
([Handover when volunteers change](../associations/handover-when-volunteers-change.md)).

## False economies

- **No tested restore.** A backup nobody has restored is a hope, not a backup.
- **Skipping updates.** Security fixes are published as updates; see [Security updates and disclosure](../security/security-updates-and-disclosure.md).
- **One person knows everything.** When they leave, the site is stranded.
- **Sending newsletters from an unauthenticated domain.** Mail lands in spam and supporters never see it
  ([Newsletter mail delivery](../newsletters/newsletter-mail-delivery.md#sender-domain-and-reputation)).
- **Unlimited anonymous uploads.** Disk fills and backups grow
  ([Volunteers and sign-up forms](volunteers-and-sign-up-forms.md#letting-people-attach-a-file)).

## A worked example

The cost section has a worked example for a non-profit, with every input labeled as an example assumption for you to
replace with your own figures: [Worked example: non-profit](../costs/worked-example-nonprofit.md).

## Related pages

- [Non-profits](README.md)
- [Total cost of ownership](../costs/total-cost-of-ownership.md)
- [What you do not pay for](../costs/what-you-do-not-pay-for.md)
- [SQLite for hosts](../../hosting/basics/sqlite-for-hosts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
