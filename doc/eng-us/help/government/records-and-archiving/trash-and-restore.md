---
title: The trash and restoring content
audience: government, editor, admin
category: public-sector
order: 100
summary: For editors and administrators - how Exponential's trash works (who trashed what, where it was, restore to its place), that users never go to the trash, how long items stay (KeepItemsForDays and the trashpurge cronjob), and why the trash is not an archive.
keywords: trash, restore, content/trash, trashed by, original placement, KeepItemsForDays, trashpurge, empty trash, users never in trash, content.object.restore, content.trash.empty, records, public sector
type: help
---
# The trash and restoring content

This page is for editors and administrators. Removing content in Exponential normally moves it to the trash, from
where it can be restored until the trash is emptied ([The trash view](../../../bc/6.0/trash.md)).

## Contents

1. [What the trash shows](#what-the-trash-shows)
2. [Restoring an item](#restoring-an-item)
3. [How long items stay](#how-long-items-stay)
4. [What never goes to the trash](#what-never-goes-to-the-trash)
5. [The trash is not an archive](#the-trash-is-not-an-archive)
6. [Related pages](#related-pages)

## What the trash shows

`content/trash` in the administration: for each item its name, the number of nodes that were below it, its type and
section, the **original placement** (the full path, and whether the parent still exists, is in the trash too, has moved
or no longer exists), and the **date trashed with by whom** and from where (`web admin`, `cli <script>`). Filters by
user, type and date range are kept in the address:

```
content/trash/(trashed_by)/<user id>/(from)/<YYYY-MM-DD>/(to)/<YYYY-MM-DD>
```

## Restoring an item

1. Find the item (filter if needed).
2. **Restore** (`content/restore/<object id>`).
3. If the original parent still exists, it goes back there; if the parent is in the trash too, restore that first; if it
   moved or is gone, the restore page asks for a place.

Restores are recorded in the audit as `content.object.restore`; removals to the trash as `content.node.remove.*`.

## How long items stay

- Until someone empties the trash (**Empty trash** empties the whole trash, whatever the filter), or the `trashpurge`
  cronjob part runs.
- The `trashpurge` part is in no cronjob part by default. With `content.ini [TrashSettings] KeepItemsForDays=<days>`
  it purges only items trashed at least that many days ago; empty or 0 purges everything.
- On the command line: `php bin/php/trashpurge.php --trashed-days=<days>`.

```ini
# settings/override/content.ini.append.php
[TrashSettings]
KeepItemsForDays=90
```

Emptying is recorded as `content.trash.empty`; purged objects are gone with their versions.

## What never goes to the trash

User accounts: a user object is removed for good (`eZContentObjectTreeNode::isNodeTrashAllowed()`). Retire leavers with
Disable or Deactivate and move instead ([Check user accounts](../../../features/6.0/user-hygiene.md)).

## The trash is not an archive

Items in the trash are on their way out, invisible to visitors, and may be purged by a schedule set by someone else.
For content that must be kept, archive it (see "Archiving old notices"). A records schedule should say who may empty the
trash and when.

## Related pages

- [The trash view](../../../bc/6.0/trash.md)
- [Archiving old notices](archiving-old-notices.md)
- [Restore a page and its history](versions-and-content-history.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
