---
title: Version history as accountability
audience: government, editor, admin, project-manager
category: public-sector
order: 140
summary: How Exponential's version history shows what a published page said at an earlier time and who saved each version, how to compare two versions, who may see them, and where the history stops being a complete record.
keywords: version history, versions, compare versions, accountability, what changed, earlier version, archived version, content/history, versionread, record, audit
type: help
---
# Version history as accountability

This page is for editors, managers and records officers who must show what a page said at a given time and who
changed it. Every save of a content object in Exponential is a version; the versions page lists them and compares
two of them ([Content history](../../../guides/content-history.md)). The audit trail adds who did what at system level
(see "The audit trail for who changed what").

## Contents

1. [What a version is](#what-a-version-is)
2. [Opening the history](#opening-the-history)
3. [Comparing two versions](#comparing-two-versions)
4. [Who may see the history](#who-may-see-the-history)
5. [Where the history is not complete](#where-the-history-is-not-complete)
6. [Answering "what did the page say on that date?"](#answering-what-did-the-page-say-on-that-date)

## What a version is

- A draft becomes the **published** version when it is published; the version before it becomes **archived**.
- An approval workflow can leave a version **pending** or **rejected**.
- Each version records its creator, when it was created and modified, its language and all its fields, files
  included.
- Translations are versioned too: publishing the German text makes a version in German.

## Opening the history

- In the editor: **Manage versions** in the left column.
- From the item: its context menu, or the address `/content/history/<object ID>`.

The page shows an overview (ID, created, modified, published version), filters, and one card per version with what
you may do with it. Filters and order are kept in the address, so a link shows the same list.

## Comparing two versions

Under **Compare two versions**, choose the **Older version**, the **Newer version** and the **Translation**, and
compare. The comparison shows the fields that differ. Use it to show exactly what changed in a correction.

To look at an old version as a whole, open it (**View**, policy `content/versionread`).

## Who may see the history

The history is internal. Visitors never see older versions. Staff need `content/versionread`; to restore one they
make a **new draft from this version** and publish it, which becomes a new version, so the restore is itself in the
history.

## Where the history is not complete

| Limit | What to do |
|---|---|
| The number of versions is limited: `content.ini [VersionManagement] DefaultVersionHistoryLimit=45`, folders 75 | Raise it for record-relevant classes (see "Keeping versions longer") |
| Staff with `content/versionremove` can remove archived versions | Give that policy only to administrators |
| Removing an object's translation deletes that language in every version | Restrict `content/remove` and translation removal |
| Removing the object removes its history (after the trash is emptied) | Do not remove record-relevant content; hide or withdraw it |
| Settings and structure changes are not versions | The audit trail records them |

The version history is not a certified records system. For long-term records, transfer documents to your records
system or archive according to your records policy.

## Answering "what did the page say on that date?"

1. Open the history of the page.
2. Find the version that was published at that date: the one published before the date, and not replaced until
   after it.
3. Open it and save or print it for the file; note the version number and object ID.
4. If the version is gone because of the limit, look in backups and in the audit trail for when it was published.

## Related pages

- [Content history](../../../guides/content-history.md)
- [Publishing, versions and the trash](../../publishing-and-versions.md)
- [Keeping versions longer](keeping-versions-longer.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
