---
title: Content migration checklist
audience: adopter, project-manager, developer, editor
category: adoption
order: 44
summary: A checklist adopters use for migrating content into Exponential - before (inventory, mapping, backups), during each rehearsal (measure, sample checks), at cut-over (freeze, maintenance mode, final run) and after (verification, rollback window).
keywords: checklist, content migration, rehearsal, inventory, mapping, backup, content freeze, maintenance mode, cut-over, verification, rollback
type: help
---
# Content migration checklist

## Contents

1. [Before](#before)
2. [Each rehearsal](#each-rehearsal)
3. [Cut-over](#cut-over)
4. [After](#after)

## Before

```text
[ ] Inventory: content types, counts, languages, files, users, orders
[ ] Decided what is NOT migrated
[ ] Mapping old -> new content types and fields written down
[ ] URL rules decided (see URLs and redirects when migrating)
[ ] Backup of the old site made and restore tested
[ ] Staging copy of the new site ready
```

## Each rehearsal

```text
[ ] Full run on staging, every step timed
[ ] Counts compared: old vs new, per content type and language
[ ] Samples checked by the content lead (text, images, relations, dates)
[ ] Search reindexed and tried
[ ] URL aliases rebuilt for every language; old addresses redirect
[ ] Forms, store, sign-ins tested
[ ] Problems logged and fixed before the next rehearsal
```

## Cut-over

```text
[ ] Date outside the busiest season; everyone informed
[ ] Content freeze announced and started
[ ] Maintenance mode on (see Maintenance mode and planned work)
[ ] Final run with the measured steps
[ ] Checks from the rehearsal repeated
[ ] Maintenance mode off; DNS or proxy switched
```

## After

```text
[ ] Old site kept read-only with backups until acceptance
[ ] Rollback plan still valid during the agreed window
[ ] Error log and 404s watched daily for two weeks
[ ] Editors told what changed; first questions answered
[ ] Acceptance signed by the owner
```

Background: [Switching to Exponential](../../businesses/choosing/switching-to-exponential.md#a-phased-plan),
[Maintenance mode and planned work](../../customers/healthy/maintenance-mode-and-planned-work.md),
[Logs for owners](../../customers/healthy/logs-for-owners.md).

## Related pages

- [Migrating](README.md)
- [Launch checklist](../launching/launch-checklist.md)
- [Go-live checklist](../../businesses/templates/go-live-checklist.md)
- [Quality gates and acceptance](../../leadership/quality-gates-and-acceptance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
