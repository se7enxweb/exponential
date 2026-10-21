---
title: What you never have to do
audience: non-technical, customer, editor
category: plain-language
order: 6
summary: The technical jobs around an Exponential site - installing, updating, backups, the domain, HTTPS, templates, extensions and the command line - who does each of them for you, and what you ask for so you know it was done.
keywords: no command line, no code, who does what, installation, updates, backups, domain, HTTPS, templates, extensions, Composer, host, developer, partner, division of work
type: help
---
# What you never have to do

Using Exponential well does not require you to become technical. It does require that somebody technical does a
handful of jobs. This page lists those jobs, says plainly that they need a command line or code, and tells you what to
ask for so you can check they were done without understanding how.

## Contents

1. [The rule of thumb](#the-rule-of-thumb)
2. [The jobs, one by one](#the-jobs-one-by-one)
3. [What you ask for in writing](#what-you-ask-for-in-writing)
4. [Things that look technical but are yours](#things-that-look-technical-but-are-yours)
5. [If you want to learn more one day](#if-you-want-to-learn-more-one-day)

## The rule of thumb

If it happens **in the browser, inside the administration**, it is something you can learn. If it happens **on the
server**, through a command line, a file transfer or a code editor, it is somebody else's job. This help marks those
jobs clearly so you never have to guess.

## The jobs, one by one

| Job | Why it needs a technical person | Who usually does it | Learn what it is |
|---|---|---|---|
| Installing Exponential | Needs a server with PHP and a database; the files are normally installed with the tool Composer on the command line, and a setup wizard in the browser finishes the job | Host, developer or partner | [Requirements at a glance](../hosting/basics/requirements-at-a-glance.md) |
| Installing updates and security fixes | Same tools; the database may need changes; caches are cleared afterwards | Same person | [How updates work](../customers/healthy/how-updates-work.md) |
| Backups and restore tests | Copies of files and the database, stored elsewhere | Host or developer | [Backups](../customers/healthy/backups.md), [Restore test](../customers/healthy/restore-test.md) |
| Domain name and DNS | Set at the domain registrar | You or your host | [Domain name and e-mail](hosting-choices/domain-name-and-email.md) |
| HTTPS certificate | Set up on the server | Host | [Hosting, domains and HTTPS explained](words/hosting-domains-and-https-explained.md) |
| Changing the design (templates, colors, fonts) | Template files and stylesheets | Designer or developer | [Extensions explained](words/extensions-explained.md) |
| Adding an extension (maps, newsletter, 2FA ...) | Installed with Composer and switched on in settings | Developer or partner | same |
| New content types or fields | Changed under **Setup** by an administrator who knows the content model | Site builder | [Designing your content model](../creators/website-creators/designing-your-content-model.md) |
| Moving to another host | Files, database and settings are moved | Old and new host | [Moving to a new host](hosting-choices/moving-to-a-new-host.md) |
| Reading the server logs | Files on the server | Administrator or developer | [Logs for owners](../customers/healthy/logs-for-owners.md) |

Some of these jobs can be done from the administration once the site has the right extensions: for example a site
made of Git clones, with the `git_manager` and `ezupdate` extensions active, can be updated from the administration
([How updates work](../customers/healthy/how-updates-work.md#the-four-parts-of-an-update)). Even then, the person
pressing the button should be someone who can fix things if the update goes wrong. That is rarely you, and that is
fine.

## What you ask for in writing

You do not have to understand the work to check that it was done. Ask for these results, in writing:

| After | Ask for |
|---|---|
| An installation | The addresses of the site and the administration, your own account, and confirmation that HTTPS works |
| Every update | The new version number (shown under **Setup > System information**), confirmation that the front page loads, the administration opens and a test item could be published ([How updates work](../customers/healthy/how-updates-work.md#what-tells-you-an-update-worked)) |
| Every quarter | The date of the last restore test and what was restored |
| Any change of design or extension | What changes for editors and what changes for visitors |

[Questions to ask before you sign](hosting-choices/what-to-ask-before-you-sign.md) puts these into the agreement
from the start.

## Things that look technical but are yours

A few things sound technical but are decisions only you can make:

- **Who gets an account, and which role.** Your administrator sets it up, you decide
  ([Roles and permissions explained](words/roles-and-permissions-explained.md)).
- **What the site says.** Content, tone, pictures, the order of the menu.
- **When an update may happen.** Visitors may see a maintenance page for a short time
  ([Maintenance mode and planned work](../customers/healthy/maintenance-mode-and-planned-work.md)).
- **What to do if something fails.** Go back to the backup or fix forward: decide before, not during.
- **Who you call.** Write it down now ([Who to call](when-stuck/who-to-call.md)).

## If you want to learn more one day

Nothing on this page is secret. Every job above is documented in this help, and Exponential is free software you
may study. If curiosity wins, [Develop with Git](../develop-with-git.md) is the recommended first step from user to
developer, and the course catalog in the [Trainers](../trainers/trainers-start-here.md) section shows how the
subjects are taught, step by step. But you never *have* to.

## Related pages

- [Non-technical people: start here](non-technical-people-start-here.md)
- [Three ways to get a site](hosting-choices/three-ways-to-get-a-site.md)
- [Who does what](../customers/partners-and-hosts/who-does-what.md)
- [What you own](../customers/what-you-own.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
