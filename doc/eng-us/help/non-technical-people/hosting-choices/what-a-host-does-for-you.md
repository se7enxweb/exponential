---
title: What a host does for you
audience: non-technical, customer
category: getting-started
order: 42
summary: The jobs a hosting company can take off a non-technical person's hands for an Exponential site - server, HTTPS, updates, backups, monitoring, mail - which of them are often not included, and how to find out what your host really does.
keywords: host, hosting company, what is included, server, HTTPS, updates, backups, monitoring, mail, cron, support, not included, non-technical
type: help
---
# What a host does for you

"Hosting" can mean very different things. One host only rents you space on a server; another runs everything for you.
This page lists the jobs, so you can tick off what your host does and see the gaps.

## Contents

1. [The jobs](#the-jobs)
2. [Often not included](#often-not-included)
3. [Finding out what your host does](#finding-out-what-your-host-does)
4. [Filling the gaps](#filling-the-gaps)

## The jobs

An Exponential site needs these things around it ([Choosing a host](../../customers/partners-and-hosts/choosing-a-host.md#what-the-site-needs)):

| Job | In plain words | Does your host do it? |
|---|---|---|
| The server | A computer that is always on, with PHP and a database | |
| HTTPS | The padlock: a certificate, renewed in time | |
| Cron | A small job that runs every few minutes, for example to update the search and send notifications | |
| Outgoing mail | So password resets and form notifications arrive | |
| Exponential updates | Installing new releases and security fixes | |
| PHP and system updates | Keeping the server's own software current | |
| Backups | Database, uploaded files, settings, stored elsewhere | |
| Restore tests | Proving the backups work | |
| Monitoring | Noticing when the site is down, also at night | |
| Support | Someone to call, with known hours and response times | |

Print this table, or copy it, and fill in the last column with your host.

## Often not included

Even good hosting offers often leave these out, or charge extra:

- **Exponential updates.** A general host updates the server, but not the application on it. That is then your
  developer's job ([How updates work](../../customers/healthy/how-updates-work.md)).
- **Restore tests.** Backups are made, but nobody checks that they restore
  ([Restore test](../../customers/healthy/restore-test.md)).
- **Changes to the site**: new layouts, design changes, extensions.
- **Help for editors.** "How do I add a picture?" is not a hosting question; this help or your developer answers it.
- **The domain name and e-mail mailboxes** may be separate products
  ([Domain name and e-mail](domain-name-and-email.md)).

## Finding out what your host does

Ask in writing, and keep the answer:

1. "Who installs Exponential releases and security fixes, and how soon after they are published?"
2. "What is backed up, how often, where to, and when was a backup last restored as a test?"
3. "How do you notice that our site is down, and what happens then, also on a weekend?"
4. "What are your support hours and how quickly do you answer?"

[What to ask before you sign](what-to-ask-before-you-sign.md) has the full list, and
[What to ask your host](../../customers/healthy/what-to-ask-your-host.md) the version for existing sites.

## Filling the gaps

For every job that nobody does yet, decide who will: the host for an extra fee, a developer, a partner, or, for
content-related jobs, you. Write the answer into [Who to call](../when-stuck/who-to-call.md). A gap you know about is a
plan; a gap you do not know about is an outage waiting to happen.

## Related pages

- [Hosting choices](README.md)
- [Three ways to get a site](three-ways-to-get-a-site.md)
- [Who does what](../../customers/partners-and-hosts/who-does-what.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
