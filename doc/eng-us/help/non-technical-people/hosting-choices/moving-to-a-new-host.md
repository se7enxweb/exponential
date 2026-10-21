---
title: Moving to a new host
audience: non-technical, customer
category: getting-started
order: 46
summary: The owner's part of moving an Exponential site to a new host, as a plain checklist - before, on the day and after - with what the old and new host do, how to keep e-mail and the domain safe, and when to let go of the old site.
keywords: move host, migration, new host, change hosting, export, DNS, e-mail, maintenance window, checklist, old host, non-technical
type: help
---
# Moving to a new host

Moving an Exponential site without changing its version is routine for a technical person
([Moving a site between hosts](../../hosting/migrations/moving-a-site-between-hosts.md)). Your part is to organize it,
decide the date and check the result. This page is that part.

## Contents

1. [Who does what](#who-does-what)
2. [Before the move](#before-the-move)
3. [On the day](#on-the-day)
4. [After the move](#after-the-move)
5. [Letting go of the old host](#letting-go-of-the-old-host)

## Who does what

| Job | Who |
|---|---|
| Choose the new host and the date | You |
| Export the database, files and settings | Old host |
| Set up the server and import | New host (or your developer) |
| Change the DNS at the registrar | Whoever has the registrar login |
| Test the site | You, with the new host |
| Delete the old copy | Old host, at your request |

## Before the move

- [ ] Choose the new host with [What to ask before you sign](what-to-ask-before-you-sign.md).
- [ ] Agree a date outside your busy period; visitors may see a maintenance page for a while
      ([Maintenance mode and planned work](../../customers/healthy/maintenance-mode-and-planned-work.md)).
- [ ] Ask the old host for a complete export (database, files, settings, versions), and keep a copy yourself
      ([Choosing a host](../../customers/partners-and-hosts/choosing-a-host.md#the-move)).
- [ ] Decide what happens to **e-mail**: does it move too, and who sets it up?
      ([Domain name and e-mail](domain-name-and-email.md))
- [ ] Tell your editors: no publishing during the move window.
- [ ] Write your **test list**: the five pages and forms that matter most (front page, contact form, a news item,
      the shop checkout if you have one, signing in).

## On the day

- [ ] Confirm the old host made the final export after the editing stop.
- [ ] Wait for the new host's "ready".
- [ ] Have the DNS changed. The change can take from minutes to a day to reach everybody.
- [ ] Have the database password changed at the new host
      ([Customer offboarding](../../hosting/checklists/customer-offboarding.md)).

## After the move

- [ ] Run your test list on the new host: pages, a form, signing in, publishing a test item, HTTPS padlock.
- [ ] Send a password reset mail to yourself to check outgoing mail.
- [ ] Check **Setup > System information** (or ask) for the new server's details and the same Exponential version.
- [ ] Confirm backups run at the new host.
- [ ] Update [Who to call](../when-stuck/who-to-call.md) with the new host's contacts.

## Letting go of the old host

Keep the old site until the new one has run well for a few days. Then ask the old host to delete your data, and to
confirm in writing. Cancel the old contract only after that.

## Related pages

- [Hosting choices](README.md)
- [Taking over a site](../../customers/partners-and-hosts/taking-over-a-site.md)
- [Handover checklist](../../customers/partners-and-hosts/handover-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
