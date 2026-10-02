# The trash view: who trashed it, where it was, what is below it

`content/trash` in the admin designs (`design/admin` and `design/admin4`; `admin2` and `admin3`
have no `trash.tpl` of their own and fall back to `design/admin`) shows more than a name, a type and
a date for each item. Everything on the page comes from data that really exists. The one new piece
of data is who moved an item to the trash, and that is recorded from this version on.

## What each row shows

| Column | Content |
| --- | --- |
| Name | the name (links to the version view), object id, number of nodes that were below it, its languages, "Still in the tree at" when the object still has a location, and a **Details** toggle: owner, last modified by, published, modified, languages, other locations |
| Type, Section | as before |
| Original placement | the full path with names. An ancestor still in the tree is a link, one in the trash or gone is in italics. Below it, the state of the parent: **exists** (restores to its original place), **in the trash too** (restore it first), **moved** or **no longer exists** (choose a place when restoring, which the restore page then asks for) |
| Date trashed | the date and **by whom**, with where the removal came from (`web admin`, `cli <script>`). Items trashed before recording started say "by unknown" and show the last modifier, labelled as such |
| (last) | a **Restore** button: the existing `content/restore/<object id>` flow |

Above the table there is a summary line covering the whole trash (items, how many were removed
directly, how many were below them, the oldest, and how many have a recorded "trashed by"), plus the
filters.

## Filters

`Trashed by` (the users with a record, or *Unknown*), `Type` (the classes present in the trash) and a
`Trashed from`/`to` date range (both days inclusive). **Filter** posts the form and the view redirects
to the same list with the filters as view parameters, so paging, sorting and the alphabetical
navigator keep them:

    content/trash/(trashed_by)/14/(class)/1/(from)/2026-10-01/(to)/2026-10-02/(sort_field)/name/(sort_order)/1

Invalid values (a non-numeric user, an impossible date) are dropped. **Show all** clears the filters.
**Empty trash** still empties the whole trash, whatever the filter.

`eZContentObjectTrashNode::trashList()` and `trashListCount()` take the matching parameters:
`ClassIDList`, `TrashedFrom`, `TrashedTo` (timestamps, inclusive), `ContentObjectIDList` (an empty
array matches nothing) and `ExcludeContentObjectIDList`. The IN lists go through
`eZDBInterface::generateSQLINStatement()`, so engines with a limit on IN lists handle them.

## Who trashed it: `Exponential\Service\TrashRecord`

The kernel has no column for it, and a schema change would have to reach all six database engines,
so it is kept in one JSON file under the var directory:

    <VarDir>/trash/trashed.json      e.g. var/site/trash/trashed.json
    { "<object id>": { "node_id": 2245, "trashed": 1790949411, "user_id": 14, "user_name": "...",
                       "via": "web admin", "recorded": 1790949411 } }

- **Written** by `eZContentObjectTrashNode::storeToTrash()`. Every trash move passes through it:
  `eZContentObjectTreeNode::removeNodeFromTree()` (called by `removeSubtrees()`, which the admin's
  delete, the content jobs and scripts all use) creates the trash row there. Each node of a removed
  subtree gets its own entry.
- **Forgotten** by `eZContentObjectTrashNode::purgeForObject()`, which runs when an object is purged
  and when it is restored. The file therefore holds about as many entries as the trash holds objects.
- **Valid only for the same trash move**: an entry counts while its `node_id` and `trashed` match the
  trash row. Anything else is shown as unknown, never guessed.
- **Atomic and safe for several writers**: an exclusive `flock()` on `trashed.json.lock`, then a
  temporary file renamed over the old one. When root writes it (a CLI script, Velocity), the files are
  given to the owner and group of the var directory, so the web server's user can write next.
- **Never in the way**: a failure to record goes to the debug log and the trash move goes on. The hook
  loads the class by path when the autoload array of a long-running worker predates it.
- `via` is `web <siteaccess>`, or `cli <script>` (for `ezexec.php`, the script it runs).

The file is local to the server. On a cluster of web servers each node records its own trash moves.

## The presenter: `Exponential\Service\TrashList`

The view (`kernel/private/classes/views/content/trash.php`) reads the trash rows once without joins
(`context()`), then builds the page from them. For each row, `describe()` works out the path, the
parent state, the nodes below it (trash rows under its `path_string`), the owner, last modifier,
dates, languages and remaining locations. It also provides `summary()`, `userOptions()`,
`classOptions()`, and `filters()`/`filterURI()`/`listParams()` for the URL filters.

The template keeps working with a view from before this change (one that only sets
`view_parameters`). It then fetches the list itself, as it always did, and leaves out the details.

## Tests

- `tests/tests/kernel/classes/trash/eZContentObjectTrashRecordTest.php` runs on the installation's own
  database (no test database). It creates a folder with a child and a grandchild under the media root,
  trashes it, and checks the records, the description, the filters and that purging forgets the record.
  Afterwards it purges everything it made.

      php vendor/bin/phpunit tests/tests/kernel/classes/trash/eZContentObjectTrashRecordTest.php

- The trash Playwright matrix (list, remove, restore, empty, on Apache and Velocity, both admin
  designs, 960 px wide at scale 2) passes with the new page.
