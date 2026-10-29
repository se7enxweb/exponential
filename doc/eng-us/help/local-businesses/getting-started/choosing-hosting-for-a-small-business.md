---
title: Choosing hosting for a small business
audience: local-business, businesses, customer
category: local-business
order: 15
summary: Hosting options for a small business website on Exponential in plain words - shared hosting, a managed host, a small virtual server or the partner's servers - what each means for you, what Exponential needs from a server, and the questions to ask before signing.
keywords: hosting, web hosting, small business, managed hosting, VPS, shared hosting, SQLite, PHP, HTTPS, backups, requirements, host
type: help
---
# Choosing hosting for a small business

This page is for the owner of a small business who must choose where the website runs. The short answer: choose the
option where someone you trust is responsible for updates, backups and HTTPS, and where you can reach that person.

## Contents

1. [What Exponential needs](#what-exponential-needs)
2. [The options](#the-options)
3. [Questions to ask a host](#questions-to-ask-a-host)
4. [Domain, mail and the website](#domain-mail-and-the-website)
5. [Checklist](#checklist)

## What Exponential needs

From [Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md):

- PHP 8.0 or later (PHP 7.4 usable without support) with a few common extensions;
- a database: SQLite needs no database server and is enough for a site on one machine; MySQL, MariaDB and
  PostgreSQL are supported too ([Database support](../../database-support.md));
- a web server such as Apache or Nginx, or Exponential Velocity, the application server that ships with Exponential
  ([Velocity is part of Exponential 6.0.15](../../velocity-is-part-of-exponential-6-0-15.md));
- command line access to run cron jobs (sitemaps, newsletters, cleanup) and updates;
- writable directories for caches and uploaded files
  ([Writable directories and permissions](../../hosting/basics/writable-directories-and-permissions.md)).

The command line access is the point where cheap shared hosting often falls short. Ask about it first.

## The options

| Option | You get | You must still | Suits |
|---|---|---|---|
| Your partner's servers | build, hosting and updates from one supplier | read the agreement; keep the domain in your name | most local businesses |
| A host that runs Exponential for customers | a managed installation, backups, HTTPS | edit content; agree response times | businesses without a partner |
| A small virtual server | full control, low running cost | update the operating system, PHP and Exponential; check backups | a business with a technical person |
| Shared hosting | low cost | check PHP version, cron and command line first | small sites without shop, with a technical person |

More detail: [Hosting models compared](../../businesses/choosing/hosting-models-compared.md) and
[Choosing hosting](../../businesses/choosing/choosing-hosting.md).

## Questions to ask a host

1. Which PHP version runs, and can I choose a newer one?
2. Can cron jobs run every few minutes, and do I get command line access?
3. How often are backups made, how long are they kept, and how do I get one restored?
4. Is HTTPS included and renewed automatically?
5. Can the server send mail, and is the sender address set up so that mail is not marked as spam? (This matters for
   contact form messages, shop confirmations and newsletters.)
6. Who do I call on a Saturday, and how fast do they answer?
7. Where are the servers? (Ask your adviser whether this matters for your customers' data.)

## Domain, mail and the website

These are three different things, often bought from three companies:

| Thing | What it is | Keep in |
|---|---|---|
| Domain | your name on the internet (`example.com`) | the business's name |
| Mail | your mailboxes (`info@example.com`) | any mail provider |
| Website hosting | the server that runs Exponential | the host or partner |

Moving the website to a new host does not have to touch your mailboxes, as long as the domain settings are changed
with care. Ask the host to do it.

## Checklist

- [ ] The server meets the [requirements](../../hosting/basics/requirements-at-a-glance.md), including cron and command line.
- [ ] Backups: frequency, retention and restore tested ([Backups that restore](../../businesses/reliability/backups-that-restore.md)).
- [ ] HTTPS with automatic renewal.
- [ ] Mail from the site arrives (test a contact form message).
- [ ] Support hours and contact written down.
- [ ] The domain is registered to the business.

## Related pages

- [Choosing a host](../../customers/partners-and-hosts/choosing-a-host.md)
- [What hosting Exponential involves](../../hosting/basics/what-hosting-exponential-involves.md)
- [Hosting cost drivers](../../businesses/costs/hosting-cost-drivers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
