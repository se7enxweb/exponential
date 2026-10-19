---
title: Version history and accountability
audience: government, editor, admin
category: public-sector
order: 140
summary: What the version history of a page and the audit trail tell a public body about who changed what and when, how many versions are kept (45 by default), and how to keep earlier versions longer when records rules require it.
keywords: version history, versions, accountability, who changed what, compare versions, restore, archived versions, DefaultVersionHistoryLimit, records, audit trail, evidence
type: help
---
# Version history and accountability

This page is for editors, web managers and records officers who need to show what a public page said at a given time
and who changed it. Two sources answer this: the **versions page** of each object and the **audit trail**. Sources:
[Versions of an object](../../../../guides/content-history.md), `settings/content.ini [VersionManagement]` and
[Audit trail](../../../../features/6.0/audit-trail.md).

## Contents
1. [The versions page](#the-versions-page)
2. [How many versions are kept](#how-many-versions-are-kept)
3. [The audit trail](#the-audit-trail)
4. [Answering typical questions](#answering-typical-questions)
5. [Limits](#limits)
6. [Related pages](#related-pages)

## The versions page

`/content/history/<object id>`, opened with **Manage versions** in the editor or from the context menu:

- Each save is a version: draft, published, archived (the one before the current), pending or rejected.
- **Compare** shows the differences between two versions (with `content/diff`).
- **Make a new draft** from an old version restores old text: the draft is yours and is published as usual (and
  passes the approval workflow again).
- **Remove selected versions** removes drafts and archived or rejected versions for good (`content/versionremove`);
  the published version and versions in a running workflow are never removed.
- Whoever may edit the object opens its versions; someone else's draft or pending version needs
  `content/versionread` ([Who sees what](../../../../guides/content-history.md#6-who-sees-what)).

## How many versions are kept

`settings/content.ini`:

```ini
[VersionManagement]
DefaultVersionHistoryLimit=45
VersionHistoryClass[1]=75
```

When an object reaches its limit, the oldest archived version makes room for the next. `VersionHistoryClass[]` sets
a limit per class (by id or, since 4.1.1, by identifier). If your records rules require more history for some
content (regulations, decisions), raise the limit for those classes in `settings/override/content.ini.append.php`,
mindful that the database grows with it. After lowering a limit, `bin/php/cleanupversions.php` removes the excess
archived versions (`php bin/php/cleanupversions.php --help --allow-root-user` lists its options).

## The audit trail

The `content` channel records publish, move, copy, remove, hide, sections, states, trash and versions, with the user
and time; the `access` channel records sign-ins. Records are hash-chained, kept live 90 days and archived for 730
days by default (`settings/audit.ini`, channels `content`, `access`, `system`, `commerce`), which you can change per
channel ([Audit trail](../../../../features/6.0/audit-trail.md#what-is-recorded)).

## Answering typical questions

| Question | Where |
|---|---|
| What did this page say on 1 March? | versions page: the version published before and after that date; open it with **View** |
| Who changed it? | the version's creator on its card; the audit trail's `content.*` events for the object |
| Who removed this page? | audit trail: a subtree removal is one parent event with one child per node |
| Who approved it? | while running: the approval request; afterwards: your workflow's records and the audit trail |
| When did it go live? | the published date; the audit event of the publish |

## Limits

- The version history is not an archive: versions beyond the limit are removed, and removing an object removes its
  versions (after the trash is emptied). Long-term preservation belongs to your records process.
- Who approved a version is not stored on the version; keep approval records where your rules require.
- Only content is versioned: settings, templates and roles are not; their changes are in the `system` and `access`
  channels of the audit trail, and in version control when settings are managed by deployment.

## Related pages
- [Drafts and pending items](drafts-and-pending-items.md)
- [Two-person rule for publishing](two-person-rule-for-publishing.md)
- [Role design: auditors](../people-and-permissions/role-design-auditors.md)
- [Versions of an object guide](../../../../guides/content-history.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
