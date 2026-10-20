---
title: Choosing hosting
audience: businesses, project-manager, website-creator, hosting
category: business-solutions
order: 10
summary: How a business chooses hosting for an Exponential site - what the site needs (PHP, database, web server or Velocity, cron, mail, backups), the five questions that decide, what to ask a host, and a decision record to keep.
keywords: choosing hosting, hosting provider, requirements, PHP, SQLite, PostgreSQL, Velocity, Apache, nginx, shared hosting, VPS, managed hosting, cron, mail, backups, decision record
type: help
---
# Choosing hosting

This page is for owners and managers who must pick a host for an Exponential site, and for the project manager who
prepares the decision. It summarizes what the site needs, the few questions that decide most of the choice, and what
to ask a host before you sign. The detailed version for project managers is
[Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md).

## Contents

1. [What the site needs](#what-the-site-needs)
2. [The five questions that decide](#the-five-questions-that-decide)
3. [What to look for beyond the technology](#what-to-look-for-beyond-the-technology)
4. [Questions to send a host](#questions-to-send-a-host)
5. [A decision record](#a-decision-record)

## What the site needs

From [Hosting requirements for a website](../../creators/website-creators/hosting-for-a-website.md#the-checklist)
and `README.md`, "Requirements":

| Need | Minimum | Notes |
|---|---|---|
| PHP | 8.0 | the newest 8.x the system offers is recommended; Velocity needs 8.1 |
| Database | SQLite 3, no server | in the official order: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL |
| Web server | none if Velocity serves the site | or Apache, nginx, LiteSpeed, Caddy, IIS and others |
| Composer | 2.x | installs the software and its extensions |
| Cron | a job every few minutes | runs notifications, cleanup, the audit trail's work |
| Mail | outgoing mail | forms, receipts, password resets, newsletters |
| HTTPS | every public site | Velocity can obtain certificates itself |
| Backups | database, uploaded files, settings, keys, audit trail | [Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md) |

## The five questions that decide

From [Choosing hosting, database and operating system](../../leadership/choosing-hosting-database-and-os.md#the-decision-in-five-questions):

| Question | If the answer is ... | Then consider |
|---|---|---|
| Does more than one machine have to write the database? | yes | a database server, PostgreSQL first; not SQLite |
| Does the hosting allow long-running processes? | no (typical shared hosting) | Apache or nginx with PHP-FPM; Velocity usually not |
| Is the machine's PHP older than 8.1? | yes | a traditional web server; Velocity needs 8.1 |
| Is the organization standardized on one database? | yes | that database, if it is among the six |
| Is there shell access on the server? | no | the setup wizard in the browser |

For most small and mid-sized business sites the simplest answer is one machine with SQLite and Velocity
([Velocity for businesses](../reliability/velocity-for-businesses.md)). Add a database server and more machines when
measurements show the need ([Hosting cost drivers](../costs/hosting-cost-drivers.md)).

> **Tip**
> The right question is not only "which technology is best" but "who can run it, back it up and restore it". Write
> that person's name into the decision record.

## What to look for beyond the technology

| Topic | What to check |
|---|---|
| Who does what | Does the host update PHP, Exponential, extensions? Who restores? |
| Backups | How often, how long kept, where, and has a restore been tested? |
| Monitoring | Does the host watch the site from outside, and who is told? |
| Security | Isolation from other customers, TLS, access to the administration ([Security basics](../security/security-basics-for-businesses.md)) |
| Data protection | Where the servers are, contracts for processing personal data ([GDPR basics](../security/gdpr-basics.md)) |
| Support | Response times in writing ([Contracts and SLA basics](contracts-and-slas-basics.md)) |
| Exit | How you get your data and files if you leave, and in which format |
| Experience | Has the host run Exponential sites before? Hosting partners are listed by the project ([Hosting partners](../../partners/hosting-partners.md)) |

## Questions to send a host

Copy and send; more in [Questions to ask a partner](questions-to-ask-a-partner.md):

- [ ] Which PHP versions do you offer, and can I choose 8.1 or newer per site?
- [ ] May I run a long-running process for Exponential Velocity, or only PHP-FPM behind your web server?
- [ ] Do you support SQLite for production on one machine, and which database servers do you offer?
- [ ] Can I schedule a cron job every few minutes?
- [ ] How is outgoing mail sent, and is DKIM signing available?
- [ ] What do backups include, how long are they kept, and when did you last test a restore?
- [ ] Who applies security updates for PHP, the operating system and Exponential, and how fast?
- [ ] Which response times do you commit to in writing?
- [ ] How do I get all my data and files when I leave?

The full technical list for hosts is
[Hosting requirements for a website: questions](../../creators/website-creators/hosting-for-a-website.md#questions-for-a-hosting-provider).

## A decision record

```text
HOSTING DECISION RECORD
Site: ____________________   Date: __________   Decided by: ______________
Host chosen: ______________   Hosting model: shared / VPS / dedicated / managed / containers
PHP version: _____   Engine: Velocity / FrankenPHP / Apache + PHP-FPM / nginx + PHP-FPM
Database: SQLite 3 / PostgreSQL / MongoDB / MariaDB / Oracle / MySQL
Reason for the choice: ______________________________________________
Backups: frequency ______  retention ______  last restore test ______
Person who restores: ______________
Updates applied by: ______________   within: ______________
Exit: data format and time agreed: ______________
Review date: __________
```

The leadership page has a fuller record:
[A decision record to copy](../../leadership/choosing-hosting-database-and-os.md#a-decision-record-to-copy).

## Related pages

- [Hosting models compared](hosting-models-compared.md)
- [Hosting cost drivers](../costs/hosting-cost-drivers.md)
- [Choosing a hosting model (for hosts)](../../hosting/models/choosing-a-hosting-model.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
