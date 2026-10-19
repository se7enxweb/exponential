---
title: Retention schedules for website content
audience: government, project-manager, editor, admin
category: public-sector
order: 110
summary: For records managers and project managers of public bodies - how to write and apply a retention schedule for website content and system data with Exponential, given that the product has no retention schedule feature: classes and states to carry the rules, the scheduled hide and unpublish scripts, and routines.
keywords: retention schedule, records schedule, disposition, content lifecycle, retention rules, hide date, unpublish date, object states, sections, audit retention, trash purge, routines, public sector
type: help
---
# Retention schedules for website content

This page is for records managers, project managers and content owners. A retention schedule says how long each kind
of information is kept and what happens then. **Exponential has no retention schedule feature**: no disposition
authority, no automatic review dates. It has building blocks to carry and apply your rules, and some system data has
its own retention settings.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Write the schedule by class](#write-the-schedule-by-class)
2. [Carry the rule in the content](#carry-the-rule-in-the-content)
3. [Apply it](#apply-it)
4. [System data](#system-data)
5. [Related pages](#related-pages)

## Write the schedule by class

Content in Exponential is typed by **classes** (article, notice, event ...). Write the schedule per class:

| Class | Keep on site | Then | Record copy |
|---|---|---|---|
| `<notice>` | until its end date | hide, or move to the archive folder | export to records system at publication |
| `<news article>` | 2 years | archive folder | none |
| `<event>` | until the event | hide | none |
| `<consultation>` | until closed | archive folder, kept public | export documents |

Fill the values from your law and policy; these rows only show the shape.

## Carry the rule in the content

- **Date fields** in the class (`hide_date`, `unpublish_date`, or a review date of your own) that editors fill when they
  publish.
- **Object states** (for example `lifecycle`: Current, Archived; `hold`: None, Legal hold) to mark where an item is.
- **Sections** for different access rules of archived content.

## Apply it

| Action | Tool |
|---|---|
| Hide on a date | cronjob script `hide.php` with `content.ini [HideSettings]` |
| Move to the trash on a date | cronjob script `unpublish.php` with `content.ini [UnpublishSettings]` and an `unpublish_date` attribute; use only where removal is the decision |
| Move to an archive | by hand or a content job on a selection |
| Mark archived | set the state, also for a whole subtree |
| Purge the trash after a period | `trashpurge` cronjob part with `[TrashSettings] KeepItemsForDays` |
| Review | a list in your template or search filtered by date or state, worked through monthly |

Both cronjob scripts are in the default part of `settings/cronjob.ini` and do nothing until their root nodes are set.
A legal hold must stop all of these for the items concerned (see "Legal holds").

## System data

| Data | Setting |
|---|---|
| Content versions | `content.ini [VersionManagement]` version history limit |
| Audit records | `audit.ini [AuditChannel_<channel>] LiveDays`, `ArchiveDays` |
| Logs | `logviewer.ini [RotationSettings] MaxAgeDays[<key>]` |
| Consent log | `mailpreferences.ini [ConsentSettings] RetentionDays` |
| Form submissions | no setting: a manual routine |

The privacy section of this help has a full retention overview.

## Related pages

- [Archiving old notices](archiving-old-notices.md)
- [Object states for records](object-states-for-records.md)
- [The trash and restoring content](trash-and-restore.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
