# The trash view: who trashed it, where it was, what is below it

Read this page if you use the trash in the admin (`content/trash`), override its template, or run several web
servers. The trash view now shows, for each item, where it was, what was below it, whether it can go back to its
place, and who moved it to the trash. Everything on the page comes from data that exists; the one new piece of data
is who trashed an item, which is recorded from this version on.

## In short

| | |
|---|---|
| What changed | Richer rows, a summary line, filters by user, type and date, sorting (since January 2024), and a record of who trashed each item, in the columns `trashed_by` and `trashed_via` of `ezcontentobject_trash` (until 9 October 2026 in `<VarDir>/trash/trashed.json`). |
| Who is affected | Editors (more information); overrides of `content/trash.tpl`; every installation must run the database update before the code moves content to the trash. |
| How to check | Open `content/trash` in the admin; rows show **Date trashed** with "by &lt;user&gt;" or "by unknown". |
| How to fix | Run the database update (see "Where "trashed by" is kept"), then `php update/common/scripts/6.0/movetrashrecords.php`. An old template override keeps working without the details (see "Template overrides"). |

The admin designs that have the page are `design/admin` and `design/admin4`; `admin2` and `admin3` have no
`trash.tpl` of their own and fall back to `design/admin`.

## Use it

1. Open the trash in the admin interface (`content/trash`).
2. Read each row. **Date trashed** shows the date and **by whom**, with where the removal came from (`web admin`, or
   `cli <script>`). Items trashed before recording started say "by unknown" and show the last modifier.
3. Filter by user, type or date range. The filters live in the address, so paging and sorting keep them:

   ```
   content/trash/(trashed_by)/14/(class)/1/(from)/2026-10-01/(to)/2026-10-02
   ```

   **Show all** clears them. **Empty trash** still empties the whole trash, whatever the filter.
4. **Restore** works as before (`content/restore/<object id>`). The **Original placement** column says whether the
   parent still exists, is in the trash too, or has moved.

## What each row shows

| Column | Content |
| --- | --- |
| Name | The name (links to the version view), object id, number of nodes that were below it, its languages, "Still in the tree at" when the object still has a location, and a **Details** toggle: owner, last modified by, published, modified, languages, other locations. |
| Type, Section | As before. |
| Original placement | The full path with names. An ancestor still in the tree is a link; one in the trash or gone is in italics. Below it, the state of the parent: **exists** (restores to its original place), **in the trash too** (restore it first), **moved** or **no longer exists** (the restore page then asks for a place). |
| Date trashed | The date and **by whom**, with where the removal came from (`web admin`, `cli <script>`). Items trashed before recording started say "by unknown" and show the last modifier, labelled as such. |
| (last) | A **Restore** button: the existing `content/restore/<object id>` flow. |

Above the table, a summary line covers the whole trash: items, how many were removed directly, how many were below
them, the oldest, and how many have a recorded "trashed by". The filters follow it.

## Filters

| Filter | Values |
|---|---|
| `Trashed by` | The users with a record, or *Unknown*. |
| `Type` | The classes present in the trash. |
| `Trashed from` / `to` | A date range; both days inclusive. |

**Filter** posts the form, and the view redirects to the same list with the filters as view parameters, so paging,
sorting and the alphabetical navigator keep them:

```
content/trash/(trashed_by)/14/(class)/1/(from)/2026-10-01/(to)/2026-10-02/(sort_field)/name/(sort_order)/1
```

Invalid values (a non-numeric user, an impossible date) are dropped.

## Sorting (since January 2024)

The trash list has been sortable since January 2024 (upstream pull request #31, merged as `fa42a6e960` and
`75361f4bea`). Click a column heading to sort by it; click again to reverse. Before, the list was ordered by name,
ascending. Now the default is **date trashed, newest first**, so the item you removed a moment ago is at the top.

| View parameter | Values | Default |
|---|---|---|
| `(sort_field)` | `name`, `class_name`, `section`, `trashed` | `trashed` |
| `(sort_order)` | `0` descending, `1` ascending | `0` |

Example, the oldest trashed items first:

```
content/trash/(sort_field)/trashed/(sort_order)/1
```

The accepted values are those the view script checks (`kernel/private/classes/views/content/trash.php`); anything
else falls back to the defaults.

## Upgrade notes

### Template overrides

The template keeps working with a view from before this change (one that sets only `view_parameters`): it then
fetches the list itself, as it always did, and leaves out the details. To get the new columns in your override,
merge it with the shipped `design/admin/templates/content/trash.tpl` or `design/admin4/templates/content/trash.tpl`.

### Where "trashed by" is kept

Since 9 October 2026 in the trash row itself, written in the same transaction as the rest of it:

| Column | Content |
|---|---|
| `trashed_by` | the content object id of the user who moved the object to the trash; 0 when not known (rows from before 9 October 2026). A script run without a login records the anonymous user |
| `trashed_via` | where the removal came from: `web <siteaccess>`, or `cli <script>` (for `ezexec.php`, the script it runs) |

- **Written** by `eZContentObjectTrashNode::createFromNode()`. Every trash move passes through it:
  `eZContentObjectTreeNode::removeNodeFromTree()` (called by `removeSubtrees()`, which the admin's delete, the
  content jobs and scripts all use) creates the trash row there. Each node of a removed subtree gets its own value.
- **Gone with the row**: purging or restoring an object removes its trash row, and with it the record.
- **The same on every web server**, in the database's backups, and readable with SQL.

**The database update adds the columns, and it is required.** Run the update of your engine before the new code
serves requests (`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, see the
[upgrade guide](../../guides/upgrading.md)). The statements for the two columns are guarded on MySQL and PostgreSQL and can run again; on a database that
applied an earlier copy of the file, later statements of the file fail on what exists, which is expected.
**Setup > System upgrade** (`setup/systemupgrade`) lists the two columns as missing until then. Without them:

- moving content to the trash fails: the transaction is rolled back and the request ends with an error page;
- the trash view still opens, and shows every item as trashed by unknown;
- restoring finds no trash row, so the original place is not offered and a new one has to be chosen;
- the trash service of the remote services (`exptrashservices`) does not see objects as trashed.

On SQLite run the two `ALTER TABLE ezcontentobject_trash ADD COLUMN trashed_by|trashed_via` lines of
`update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql` once. Oracle (the `ezoracle` extension, its own package)
needs the same two columns: `ALTER TABLE ezcontentobject_trash ADD ( trashed_by INTEGER DEFAULT 0 NOT NULL,
trashed_via VARCHAR2(100) )`; `trashed_via` stays nullable there, because Oracle stores an empty string as NULL.

### What was recorded before: `trashed.json`

Until 9 October 2026 the record was a JSON file under the var directory, `<VarDir>/trash/trashed.json`, one entry
per object (`node_id`, `trashed`, `user_id`, `user_name`, `via`, `recorded`). It was local to one web server and
outside the database's transactions and backups. The kernel no longer writes it, but:

- the trash view still reads it for rows whose `trashed_by` is 0, when the entry matches the row (same node id and
  trashed time), so nothing recorded so far is lost before the copy;
- purging or restoring an object still removes its entry;
- `update/common/scripts/6.0/movetrashrecords.php` copies the entries into the columns of their rows (rows that
  have a `trashed_by` keep it, so it can run again). `--dry-run` counts only; `--remove-file` deletes the file when
  every entry has a trash row (exit status 2 when it keeps the file, 1 on an error, for instance when the columns are
  missing). Run it once per var directory (`-s <siteaccess>` for a siteaccess with a var directory of its own); on a
  cluster, on every web server.
- The file kept the user's name at the time; the columns keep the id. A user removed since shows as `#<id>`.

```bash
php update/common/scripts/6.0/movetrashrecords.php --dry-run
php update/common/scripts/6.0/movetrashrecords.php --remove-file
php update/common/scripts/6.0/movetrashrecords.php -s othersite --remove-file
```

A site that recorded the user in a table of its own (an extension that replaced `eZContentObjectTrashNode`) copies
it into `trashed_by` with SQL instead.

## For developers

### Filtering in PHP

`eZContentObjectTrashNode::trashList()` and `trashListCount()` take the matching parameters: `ClassIDList`,
`TrashedFrom`, `TrashedTo` (timestamps, inclusive), `ContentObjectIDList` (an empty array matches nothing) and
`ExcludeContentObjectIDList`. The IN lists go through `eZDBInterface::generateSQLINStatement()`, so engines with a
limit on IN lists handle them.

### The old record: `Exponential\Service\TrashRecord`

`kernel/private/classes/services/trashrecord.php` reads `trashed.json` (`all()`, `entryFor()`), forgets entries
(`forget()`), copies them into the columns (`moveToColumns()`, optionally for some objects only) and removes the file
(`removeFile()`; the lock file stays). `columnsExist()` tells whether the database update has run. `record()`, which
wrote the file, is deprecated and no longer called; `via()` gives `eZContentObjectTrashNode::currentVia()`.

The filter by user runs in SQL: `trashList()` takes `TrashedBy` (a user's content object id) and `TrashedByUnknown`,
each with `TrashedByFileObjectIDList` for the rows known only from the old file.

### The presenter: `Exponential\Service\TrashList`

The view (`kernel/private/classes/views/content/trash.php`) asks the database for what one page needs; it never
reads the whole trash. `context()` only tells whether the columns `trashed_by` and `trashed_via` exist and reads
the entries of the old file for rows without a `trashed_by`. For each item of the page, `describe()` works out the
path (the trash rows among its ancestors, read once for the page), the parent state, the nodes below it (a count on
the indexed `path_string`, `TrashList::belowCondition()`), the owner, last modifier, dates, languages and remaining
locations. `summary()` and `userOptions()` count in SQL (`userOptions()` groups by `trashed_by`, which has the
index `ezcobj_trash_trashed_by`). The class (`kernel/private/classes/services/trashlist.php`) also provides
`classOptions()`, and `filters()`, `filterURI()` and `listParams()` for the URL filters.

### A large trash

Measured on SQLite with synthetic trash rows (groups of one item and nine below it, pointing to published objects),
as administrator, one page of 50 items, the PHP steps of the view without the template, for the filters none / by
user / unknown. The scripts and the results are kept with the change; the times vary by about a fifth between runs.

| Rows in the trash | Before | After (with the index) | Memory before | Memory after |
|---|---|---|---|---|
| 30,000 | 0.84–1.04 s | 0.17–0.32 s | 48 MB | 2–3 MB |
| 100,000 | 2.7–3.2 s | 0.54–0.90 s | 160 MB | 2–3 MB |

Before, the view read every trash row (`SELECT *`, the long `path_identification_string` included) and counted the
nodes below each item of the page by going through all of them. What is left is the list query itself
(`trashList()` and `trashListCount()`, about 0.13–0.32 s each at 100,000 rows) and the summary line (0.18–0.24 s).
The index on `trashed_by` cut the user list from about 100 ms to 16 ms at 100,000 rows.

The nodes below an item are counted with a range on `path_string` on SQLite (which answers `LIKE` without the
index) and with `LIKE '<path>%'` on the other engines, as the kernel's subtree queries do. A range is not used
there because a locale collation (PostgreSQL with `en_US.UTF-8`) ignores the slashes when it compares. Like those
subtree queries, PostgreSQL answers the `LIKE` from an index only when the database uses the C collation or the
index has `text_pattern_ops`.

### Tests

`tests/tests/kernel/classes/trash/eZContentObjectTrashRecordTest.php` runs on the installation's own database (no
test database). It creates a folder with a child and a grandchild under the media root, trashes it, and checks the
columns of the trash rows, the description, the filters, and that an entry of the old file is read, copied into its
row and forgotten on purge. Afterwards it purges everything it made. It needs the columns `trashed_by` and
`trashed_via`.

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/trash/eZContentObjectTrashRecordTest.php
```

The trash browser test matrix (list, remove, restore, empty; on Apache and Velocity; both admin designs; 960 px wide
at scale 2) passes with the new page.

## Related fixes of the same day

- **Empty trash purges in batches**: 100 items at a time, each batch in its own transaction, clearing the content
  cache and pausing a second between batches, exactly as `bin/php/trashpurge.php` and the `trashpurge` cronjob part
  do (`Exponential\Service\Trash::purgeInBatches()`).
- "Remove timed out sessions" in Setup also removes the shop baskets of those sessions, like
  `bin/php/ezsessiongc.php` (`SessionGarbageCollector::collect()`).
- The `clusterpurge` cronjob part purges files that expired 30 days ago; it passed a bare 30, which meant 30 seconds.
- Removing a subtree that holds every location of an object removes the object too (the main-location flag of a node
  already in a batch was stale).
- Removing a media file's attribute deletes the stored file only when no other media row names it (a copy shares the
  source's file).
- A copied object with an image owns its image files, and removing the copy leaves the source intact. Removing a
  draft removes every image file of that draft, not only the ones its XML names.

## Related pages

- [Content jobs](../../features/6.0/content-jobs.md) and the [content jobs upgrade](content-jobs.md)
- [Upgrade checklist of 1 and 2 October 2026](behaviour-changes-2026-10.md)
- [Audit event model](../../specifications/6.0/audit-event-model.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- History: [October 2026](../../history/2026/2026-10.md), [January 2024](../../history/2024/2024-01a.md) (sorting)
