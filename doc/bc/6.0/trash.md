# The trash view: who trashed it, where it was, what is below it

Read this page if you use the trash in the admin (`content/trash`), override its template, or run several web
servers. The trash view now shows, for each item, where it was, what was below it, whether it can go back to its
place, and who moved it to the trash. Everything on the page comes from data that exists; the one new piece of data
is who trashed an item, which is recorded from this version on.

## In short

| | |
|---|---|
| What changed | Richer rows, a summary line, filters by user, type and date, sorting (since January 2024), and a record of who trashed each item, in the columns `trashed_by` and `trashed_via` of `ezcontentobject_trash` (until 9 October 2026 in `<VarDir>/trash/trashed.json`). |
| Who is affected | Editors (more information); overrides of `content/trash.tpl`; every installation runs the database update that adds the two columns (until then the kernel keeps the old file). |
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

## Keeping items in the trash for a while

The `trashpurge` cronjob part (`cronjobs/trashpurge.php`) empties the whole trash. To keep each item for a number of
days instead, set:

```ini
# settings/override/content.ini.append.php
[TrashSettings]
KeepItemsForDays=90
```

The cronjob then purges only what has been in the trash for at least that many days, counted from the time it was
trashed (the `trashed` column) back from the start of the run. Days are calendar days in the server's time zone: an
item trashed exactly 90 days ago, to the second, is purged; across a change of daylight saving time the limit moves
by an hour. Empty or `0`, the default, purges everything as before. A value that is not a whole number of days up to
36500 (`90 days`, `-1`) purges nothing and reports an error on the command line and in the error log, so a typo
cannot empty the trash. `bin/php/trashpurge.php` is not affected; give it `--trashed-days=<days>`. Both refuse an age
so large that the date arithmetic overflows (it used to match every item, or a date that was not that many days
back).

The setting is read from `content.ini` as the siteaccess the cronjob runs for sees it: `runcronjobs.php -s
<siteaccess>`, the siteaccess chosen under Setup > Cronjobs, or the default siteaccess without `-s`. A value in
`settings/override/` applies to every siteaccess and wins over one in `settings/siteaccess/<name>/`; the trash is one
for the whole database, so set it there. The cronjob reads the setting from the files, not from the INI cache: a
value just added, in a new override file or on a server whose `config.php` turns off the INI modification checks,
counts on the very next run without clearing the cache first.

Items trashed before the `trashed` column existed (an upgrade from 5.x; the database update adds it with 0) count
as trashed in 1970 and go on the first run. To keep them, give them a time first, for instance the upgrade:
`UPDATE ezcontentobject_trash SET trashed = <timestamp> WHERE trashed = 0`. A site that kept the trash time in a
table of its own (an extension that replaced `eZContentObjectTrashNode`) copies it into `trashed` the same way
before switching.

The cronjob part is not in any part of `cronjob.ini` by default; add it to one, for instance:

```ini
# settings/override/cronjob.ini.append.php
[CronjobPart-trashpurge]
Scripts[]
Scripts[]=trashpurge.php
```

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

**The database update adds the columns.** Run the update of your engine
(`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, see the [upgrade guide](../../guides/upgrading.md)).
The statements for the two columns are guarded on MySQL and PostgreSQL and can run again; on a database that
applied an earlier copy of the file, later statements of the file fail on what exists, which is expected.
**Setup > System upgrade** (`setup/systemupgrade`) lists the two columns as missing until then.

Code and update can come in either order. Until the columns exist, `eZContentObjectTrashNode::hasTrashedByColumns()`
says so and the kernel works with the old table: moving content to the trash stores the row without them and
records who did it in `trashed.json` as before, and the trash view, its filter by user and restoring read the row
without them. The next section's script copies what the file holds once the update has run. The check asks the
database's catalogue (`information_schema`, `PRAGMA table_info`, `user_tab_columns`), never a query that could fail
inside a transaction; once the columns are found the answer is kept for the process, while they are missing it is
asked again on the next trash move, so a running Velocity worker picks the update up without a restart.

On SQLite run the two `ALTER TABLE ezcontentobject_trash ADD COLUMN trashed_by|trashed_via` lines of
`update/database/sqlite/6.0/dbupdate-6.0.0-6.0.15.sql` once (a second run fails with "duplicate column name" and
changes nothing). Oracle (the `ezoracle` extension, its own package) needs the same two columns:
`ALTER TABLE ezcontentobject_trash ADD ( trashed_by INTEGER DEFAULT 0 NOT NULL, trashed_via VARCHAR2(100) )`;
`trashed_via` stays nullable there, because Oracle stores an empty string as NULL.

`trashed_via` holds printable ASCII only (anything else becomes `?`) and at most 100 characters, so it fits a
column whose length counts bytes and never carries a byte sequence PostgreSQL refuses.

### What was recorded before: `trashed.json`

Until 9 October 2026 the record was a JSON file under the var directory, `<VarDir>/trash/trashed.json`, one entry
per object (`node_id`, `trashed`, `user_id`, `user_name`, `via`, `recorded`). It was local to one web server and
outside the database's transactions and backups. Once the columns exist the kernel no longer writes it (before, it still does), but:

- the trash view still reads it for rows whose `trashed_by` is 0, when the entry matches the row (same node id and
  trashed time), so nothing recorded so far is lost before the copy;
- purging or restoring an object still removes its entry;
- `update/common/scripts/6.0/movetrashrecords.php` copies the entries into the columns of their rows (rows that
  have a `trashed_by` keep it, so it can run again). `--dry-run` counts only; `--remove-file` deletes the file when
  every entry has a trash row (exit status 2 when it keeps the file, 1 on an error: the columns are missing, or the
  file holds no JSON object, which is then neither copied nor removed). Run it once per var directory
  (`-s <siteaccess>` for a siteaccess with a var directory of its own); on a cluster, on every web server.
- It prints the entries in the file, the rows given a `trashed_by`, the rows that kept theirs, the entries without a
  trash row (their objects were purged or restored, or trashed again since: they are left in the file, and with
  them `--remove-file` keeps it) and the rows whose entry names no user (left at 0).
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
wrote the file, is deprecated; the kernel calls it only while the columns are missing; `via()` gives `eZContentObjectTrashNode::currentVia()`.

The filter by user runs in SQL: `trashList()` takes `TrashedBy` (a user's content object id) and `TrashedByUnknown`,
each with `TrashedByFileObjectIDList` for the rows known only from the old file.

### The presenter: `Exponential\Service\TrashList`

The view (`kernel/private/classes/views/content/trash.php`) reads the trash rows once without joins (`context()`),
then builds the page from them. For each row, `describe()` works out the path, the parent state, the nodes below it
(trash rows under its `path_string`), the owner, last modifier, dates, languages and remaining locations. The class
(`kernel/private/classes/services/trashlist.php`) also provides `summary()`, `userOptions()`, `classOptions()`, and
`filters()`, `filterURI()` and `listParams()` for the URL filters.

### Tests

`tests/tests/kernel/classes/trash/eZContentObjectTrashRecordTest.php` runs on the installation's own database (no
test database). It creates a folder with a child and a grandchild under the media root, trashes it, and checks the
columns of the trash rows, the description, the filters, and that an entry of the old file is read, copied into its
row and forgotten on purge. Afterwards it purges everything it made. It needs the columns `trashed_by` and
`trashed_via`.

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/trash/eZContentObjectTrashRecordTest.php
```

`eZContentObjectTrashOldSchemaTest.php` is its counterpart for a database without the columns (a copy of a site's
database from before the update; skipped on an updated one): moving to the trash, the file entry, fetching the row,
the view's filters, restoring and purging. `TrashedByColumnsTest.php` needs no database: the schema and update
files, `createFromNode()`, `currentVia()` and `definition()` before and after the update.

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
