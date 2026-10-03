# The trash shows who trashed an item and where it was

Until 6.0.15 the trash listed a name, a type and a date. Each row of `content/trash` now shows who moved the item
to the trash and from where, its original place as a full path, how many nodes were below it, its languages and
any location still in the tree. Added 2026-10-02. Technical guide:
[doc/bc/6.0/trash.md](../../bc/6.0/trash.md).

## Use it

1. Open the trash in the admin interface (`content/trash`).
2. Read each row: **Date trashed** shows the date and **by whom**, with where the removal came from (`web admin`,
   or `cli <script>`); items trashed before recording started say "by unknown" and show the last modifier.
3. Filter by user, type or date range; the filters live in the address, for example
   `content/trash/(trashed_by)/14/(class)/1/(from)/2026-10-01/(to)/2026-10-02`, so paging and sorting keep them.
   "Show all" clears them. **Empty trash** still empties the whole trash, whatever the filter.
4. **Restore** works as before (`content/restore/<object id>`); the Original placement column says whether the
   parent still exists, is in the trash or has moved.

## Where the record is kept

No database schema change (it would have to reach six engines): `eZContentObjectTrashNode::storeToTrash()`, which
every trash move passes through (the admin, content jobs, `removeSubtrees()` from scripts), calls
`Exponential\Service\TrashRecord` (`kernel/private/classes/services/trashrecord.php`), which writes the user, the
trash row's node id and time and the origin into `<VarDir>/trash/trashed.json` (for example
`var/site/trash/trashed.json`), under a file lock. `purgeForObject()` forgets an entry on purge and on restore. The
file is local to the server: on a cluster each node records its own trash moves. A failure to record is logged and
never stops the trash move.

## Related fixes the same day

- **Empty trash purges in batches**, each in its own transaction, 100 items at a time, clearing the content cache
  and pausing a second between batches, exactly as `bin/php/trashpurge.php` and the `trashpurge` cronjob part do
  (`Exponential\Service\Trash::purgeInBatches()`).
- "Remove timed out sessions" in Setup also removes the shop baskets of those sessions, like
  `bin/php/ezsessiongc.php` (`SessionGarbageCollector::collect()`).
- The `clusterpurge` cronjob part purges files expired for 30 days; it passed a bare 30, meaning 30 seconds.
- Removing a subtree that holds every location of an object removes the object too (the main-location flag of a
  node already in a batch was stale).
- Removing a media file's attribute deletes the stored file only when no other media row names it (a copy shares
  the source's file).
- A copied object with an image owns its image files, and removing the copy leaves the source intact; removing a
  draft removes every image file of that draft, not only the ones its XML names.

Related: [content jobs](content-jobs.md), [October 2026 chronicle](../../history/2026/2026-10.md).

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [audit event model](../../specifications/6.0/audit-event-model.md).
