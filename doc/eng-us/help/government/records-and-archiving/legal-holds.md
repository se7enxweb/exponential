---
title: Legal holds
audience: government, admin, project-manager
category: public-sector
order: 230
summary: For public bodies that must preserve website content and system records during litigation, an investigation or an information request - Exponential has no legal hold feature, so this page gives a process built from object states, roles, suspended cleanup jobs, extended audit retention and exports.
keywords: legal hold, litigation hold, preservation, investigation, freedom of information request, object states, roles, StateGroup, trash purge, unpublish, hide, version cleanup, audit retention, export, records, public sector
type: help
---
# Legal holds

This page is for records managers, administrators and legal staff. A legal hold suspends normal deletion for
information relevant to a case. **Exponential has no legal hold feature.** A hold can be implemented with existing
tools: mark the content, stop the people and the jobs that could change or remove it, extend retention of system
records, and export a copy.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What can destroy information](#what-can-destroy-information)
2. [A hold, step by step](#a-hold-step-by-step)
3. [Lifting the hold](#lifting-the-hold)
4. [Related pages](#related-pages)

## What can destroy information

| Mechanism | Hold measure |
|---|---|
| editors editing or removing | object state `hold/Legal hold` and roles that cannot edit or remove in that state |
| version history limit removing old versions | export the versions now; raise the class's limit |
| `unpublish.php` moving items to the trash on a date | clear the date, or remove the subtree from `[UnpublishSettings] RootNodeList[]` |
| `hide.php` hiding on a date | usually harmless (hiding keeps content); clear the date if visibility matters |
| `trashpurge` emptying the trash | suspend the part, or check the trash for held items first |
| old drafts cleanup | suspend `old_drafts_cleanup.php` if drafts matter |
| audit retention purging records | raise `ArchiveDays` for the channels concerned |
| log pruning | raise `MaxAgeDays` for the logs concerned, or copy them |
| form submission removal routines | pause for the forms concerned |

## A hold, step by step

1. **Scope**: the content (subtrees, classes, users), the period, the system records.
2. **Mark** the content: a state group `hold` (None, Legal hold); set Legal hold on the subtrees with "Also for everything
   below this node" ([Object states](../../../guides/object-states.md)).
3. **Restrict**: limit editors' `content/edit` and `content/remove` to `StateGroup_hold = None`; give `state/assign` for
   the hold group only to records staff.
4. **Suspend jobs** listed above, in `settings/override/` (cronjob parts, content.ini, audit.ini, logviewer.ini); record
   each change (setting writes are in the audit as `system.setting.write`).
5. **Export a copy**: content (REST API, packages or PDF), versions you need, audit records as a signed bundle
   (`./console exp:audit export ... --format=bundle`), and verify output. Store with chain-of-custody notes.
6. **Inform** the people concerned; document the hold (scope, date, measures, owner).

## Lifting the hold

Reverse the measures in the opposite order, set the state back to None, and record the date. Normal retention then
applies again; items whose retention ended during the hold may be due for disposal at once.

## Related pages

- [Object states for records](object-states-for-records.md)
- [Audit records as evidence](audit-records-as-evidence.md)
- [Retention schedules for website content](retention-schedules.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
