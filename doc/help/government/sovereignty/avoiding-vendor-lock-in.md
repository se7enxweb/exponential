---
title: Avoiding vendor lock-in
audience: government, project-manager, admin
category: public-sector
order: 110
summary: The kinds of lock-in a public body can run into with a web platform (license, code, data, hosting, skills, contracts) and what Exponential and good practice offer against each, with a short self-assessment.
keywords: vendor lock-in, lock-in, independence, exit, open formats, source code, Composer, GitHub, database choice, contracts, skills, sovereignty
type: help
---
# Avoiding vendor lock-in

This page is for managers and architects who want to be able to change providers, hosts or even platforms without
losing their content or their site. Lock-in has several layers; the license removes only the first. The others are
avoided by how you build, host and contract.

## Contents

1. [Layers of lock-in](#layers-of-lock-in)
2. [What Exponential offers per layer](#what-exponential-offers-per-layer)
3. [Practices that keep you free](#practices-that-keep-you-free)
4. [A self-assessment](#a-self-assessment)

## Layers of lock-in

| Layer | The risk |
|---|---|
| License | you may only use the software with one vendor's permission |
| Code | only one supplier has or understands your custom code |
| Data | content cannot be taken out in a usable form |
| Hosting | the platform runs only at one provider |
| Database | the platform needs one commercial database |
| Skills | only one supplier's staff can operate it |
| Contract | the contract makes leaving slow or expensive |

## What Exponential offers per layer

| Layer | What is there | Evidence |
|---|---|---|
| License | GPL v2 or later: anyone may run, change and pass it on | [Licensing explained for buyers](../../business/licensing-for-buyers.md) |
| Code | public source on GitHub, Composer packages on Packagist, full Git history | [Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md), [Develop with Git](../../develop-with-git.md) |
| Data | exports as CSV, JSON, XML; packages as `.ezpkg`; REST API; database dumps per engine | see "Exporting content and packages" |
| Hosting | many operating systems and hosting models, documented with tiers | [Operating system support](../../os-support.md) |
| Database | six supported engines, three of them free software servers and SQLite | [Database support](../../database-support.md) |
| Skills | free documentation, training material, a partner program open to any company | [Course catalogue](../../trainers/course-catalogue.md), [Partners start here](../../partners/partners-start-here.md) |
| Contract | no license contract at all | the license |

## Practices that keep you free

1. **Own the repository.** Your extensions, designs and settings live in a Git repository of your organization.
2. **No kernel patches.** Customize with extensions and template overrides, never by editing kernel files, so any
   provider can upgrade you.
3. **Own the accounts.** Domain names, certificates, hosting accounts and the administrator account belong to the
   body.
4. **Document the installation.** Active extensions, siteaccesses, cronjobs, overrides, third-party services.
5. **Prefer free and documented formats** for content and data (see "Open formats and data portability").
6. **Test an exit** once: restore a backup elsewhere, or export content into a test installation (see "Exit planning").
7. **Keep skills in-house** (see "Keeping the skills in house").

## A self-assessment

Answer yes or no; each "no" is a lock-in to fix.

- [ ] We have the complete code of our site in our own repository.
- [ ] Another provider could install it from that repository and the documentation.
- [ ] We have a current database dump and the `var/` storage files, and we tested a restore.
- [ ] We could export our main content types as CSV or JSON today.
- [ ] Our hosting contract states the data export and handover at the end.
- [ ] At least one person in our organization can explain how the site is built.

## Related pages

- [Exit planning](exit-planning.md)
- [Exporting content and packages](exporting-content-and-packages.md)
- [Develop with Git](../../develop-with-git.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
