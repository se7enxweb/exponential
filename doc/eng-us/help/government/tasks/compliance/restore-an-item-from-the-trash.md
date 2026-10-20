---
title: Restore an item from the trash
audience: government, editor, admin
category: public-sector
order: 480
summary: Step by step - find a removed page in Exponential's trash by who trashed it and when, restore it to its original place (or choose another when the parent is gone), and check what the audit recorded.
keywords: trash, restore, content/trash, trashed by, original placement, content/restore, removed page, undo delete, content.object.restore, public sector
type: help
---
# Restore an item from the trash

For editors and administrators, when a page was removed by mistake. Removed content stays in the trash until the trash
is emptied or purged ([The trash and restoring content](../../records-and-archiving/trash-and-restore.md)). User
accounts are never in the trash.

## Contents

1. [Steps](#steps)
2. [Check that it worked](#check-that-it-worked)
3. [If it is not there](#if-it-is-not-there)
4. [Related pages](#related-pages)

## Steps

1. Open the trash in the administration (`content/trash`).
2. Filter by who trashed it, its class and the date range; the filters are kept in the address:
   `content/trash/(trashed_by)/<user id>/(from)/<YYYY-MM-DD>/(to)/<YYYY-MM-DD>`.
3. Read **Original placement**: whether the parent still **exists**, is **in the trash too** (restore that first), has
   **moved** or **no longer exists**.
4. **Restore** (`content/restore/<object id>`). If the parent is gone, the restore page asks for a new place.

## Check that it worked

- The item is back in the tree with its content and versions.
- The audit records `content.object.restore`; the removal is visible as `content.node.remove.*` with who did it.
- If it had a URL alias, check the address works again.

## If it is not there

- The trash was emptied or purged (`content.trash.empty` in the audit; or the `trashpurge` cronjob with
  `KeepItemsForDays`): only a restore from backup brings it back.
- It was a user account: removed for good.
- It may have been moved or hidden, not removed: search the content tree, including hidden items.

## Related pages

- [The trash view](../../../../bc/6.0/trash.md)
- [Keep trash items for 90 days](keep-trash-items-for-90-days.md)
- [Backups and recovery](../../security/backups-and-recovery.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
