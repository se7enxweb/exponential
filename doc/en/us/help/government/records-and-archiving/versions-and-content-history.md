---
title: Versions and content history
audience: government, editor, admin
category: public-sector
order: 30
summary: For editors and records staff - how Exponential keeps every save of an object as a version (draft, published, archived, pending, rejected), how to see who changed what and when, compare versions, bring back an earlier text, and the limits that matter for records.
keywords: versions, content history, version history, archived versions, compare versions, diff, make a new draft, creator, modified, content/history, content/versionread, content/diff, accountability, public sector
type: help
---
# Versions and content history

This page is for editors, publishers and records staff. Every save of an object in Exponential is a **version**; the
versions page shows them, compares them and makes new drafts from old ones
([Versions of an object](../../../guides/content-history.md)).

## Contents

1. [Version statuses](#version-statuses)
2. [The versions page](#the-versions-page)
3. [Answering "what did the page say on that date?"](#answering-what-did-the-page-say-on-that-date)
4. [Limits](#limits)
5. [Related pages](#related-pages)

## Version statuses

| Status | Means |
|---|---|
| Draft | being edited; only its creator edits it |
| Published | the version visitors see |
| Archived | an earlier published version, kept |
| Pending | waiting in an approval workflow |
| Rejected | refused in an approval workflow |

When a draft is published, the version before it becomes archived.

## The versions page

`/content/history/<object id>`, opened with **Manage versions** in the editor or from the item's context menu:

- figures (all versions, per status, translations), filters by status, translation and creator, order;
- one card per version: number, status, translations, **creator**, **created** and **modified**;
- **View** (needs `content/versionread`), **Compare** (needs `content/diff`), **Make a new draft**;
- **Remove selected versions**: drafts, archived, rejected; never the published version or versions in a running
  workflow.

## Answering "what did the page say on that date?"

1. Open the versions page; filter by status **Archived** and order by oldest first.
2. Find the last version whose publication (modified) date is before the date in question.
3. **View** it; **Compare** it with the next one to see what changed.
4. For evidence, record the version number and dates, and save a copy (print to PDF from the version view).
5. Who published it: the version's creator; for a system-wide record, enable the audit event `content.object.publish`
   (off by default) so publishing is logged in the audit trail.

## Limits

- Versions are kept up to the **version history limit** (`content.ini [VersionManagement]`, 45 by default); then the
  oldest archived version is removed. See "The version history limit".
- Removing versions by hand is final.
- A removed object (after the trash is emptied) takes its versions with it.
- Editors who may edit an object can open its versions without a read policy; limit edit policies accordingly.

## Related pages

- [Versions of an object](../../../guides/content-history.md)
- [Publishing and versions](../../publishing-and-versions.md)
- [Drafts and pending items](../../../guides/drafts-and-pending.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
