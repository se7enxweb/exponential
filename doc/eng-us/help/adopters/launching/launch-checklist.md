---
title: Launch checklist for adopters
audience: adopter, project-manager, admin, customer
category: adoption
order: 51
summary: A launch checklist for adopters of Exponential - content and URLs, accounts and roles, security hardening, backups and restore, caches and speed, monitoring, accessibility, legal pages, and the people side - before go-live and on the day.
keywords: launch checklist, go-live, security hardening, backups, restore, caches, monitoring, accessibility, accounts, roles, demo mode
type: help
---
# Launch checklist for adopters

## Contents

1. [Before go-live](#before-go-live)
2. [On the day](#on-the-day)
3. [The first week](#the-first-week)

## Before go-live

```text
Content and URLs
[ ] Content migrated and accepted (Content migration checklist)
[ ] Old addresses redirect; no important 404s

Accounts and roles
[ ] Default and test accounts removed or disabled; administrator password changed
[ ] Roles and permissions set for every group (Roles and permissions for the rollout)
[ ] Two-step sign-in for administrators, if used

Security
[ ] Hardening applied (Security hardening, chapter 13 of the installation book)
[ ] PHP and Exponential on supported versions
[ ] Not running in demo mode

Operations
[ ] Backups scheduled AND one restore tested
[ ] Monitoring of availability and errors in place
[ ] Caches configured; speed checked under expected load
[ ] Maintenance mode tested

People
[ ] Editors trained close to launch
[ ] Support contacts and escalation known
[ ] Owner has accepted the site
```

Pages: [Security hardening](../../security-hardening.md), [Change the admin password](../../change-the-admin-password.md),
[Default administrator password](../../default-administrator-password.md), [Demo mode](../../demo-mode.md),
[Backups](../../customers/healthy/backups.md), [Restore test](../../customers/healthy/restore-test.md),
[Monitoring](../../customers/healthy/monitoring.md), [Speed and caching](../../customers/healthy/speed-and-caching.md).

## On the day

```text
[ ] Content freeze in place; final migration run
[ ] Smoke test: front page, a page per content type, search, a form, sign-in, checkout
[ ] Error log empty of new errors
[ ] DNS or proxy switched; HTTPS valid
[ ] Announcement to editors and users
```

## The first week

```text
[ ] Error log and 404s read every day
[ ] Editor questions collected and answered
[ ] First backup after launch restored on a test system
[ ] Old site kept read-only until acceptance
```

## Related pages

- [Launching](README.md)
- [Go-live checklist](../../businesses/templates/go-live-checklist.md)
- [Security checklist for owners](../../customers/healthy/security-checklist-for-owners.md)
- [Content migration checklist](../migrating/content-migration-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
