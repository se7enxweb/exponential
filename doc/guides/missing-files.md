# Missing files and image aliases

This guide shows how to find content whose files are gone from the storage: an image whose original is not in
`var/<site>/storage` any more, a file or a media object whose upload was lost, and images whose aliases are missing
or were made with other `image.ini` settings. It is for administrators who move storage between servers, restore
backups or clean up disks, and for anyone who sees broken images on the site.

[Guides](README.md) · Related: [The content checks](../features/6.0/content-checks.md#missing-files),
[The upgrade check](upgrade-check.md)

## In short

- The check is a **target** of **Setup > Broken relations**: tick **Missing files in var/<site>/storage** and press
  **Check**. Ticked with the other targets, the page shows the broken relations and the missing files; ticked alone,
  only the missing files.
- Every file-backed field is checked (image originals and their aliases, files, media, and the datatypes an
  extension adds), in the published version and in drafts, through the cluster file handler, so a cluster's DFS
  counts.
- Each row names the object (with its node), the field, the version, the **expected path**, the time the version
  was last changed and the problem: **File missing**, **Alias missing**, **Alias out of date** or **Alias never made**.
- The same list on the command line: `./console exp:content-check --missing-files --node=<node id>`.
- Nothing is changed by the check itself; the resolutions (section 6) always show a preview first.

## 1. Open the check

1. Sign in to the administration and open **Setup > Broken relations** (or **Advanced > Broken relations in
   subtree** in the context menu of a node in the content tree).
2. In **Start location**, keep the top node or give the node id of the part of the tree to check.
3. Under **Targets**, tick **Missing files in var/site/storage** (the name of your storage directory is shown).
   Untick the other targets if you only want the files.
4. Press **Check**.

Two more filters appear once the target is ticked:

- **Missing files: problems**: **File missing**, **Alias missing** and **Alias out of date** are ticked; **Alias
  never made** is not, because an alias is made the first time an image is shown in it, so a missing one is normal.
- **Missing files: versions**: the **Published version** and **Drafts**.

## 2. Read a row

| Column | What it says |
|---|---|
| Object | the object's name, linked to its node, with the object and node ids |
| Field | the field (and its language); the file's original name below it |
| Version | the version number, published or draft |
| Expected path | where the field says the file is, below `var/<site>/storage`; for an alias, the aliases and their state |
| Last modified | when the version was last changed; for an alias, also the time of the alias file |
| State | **File missing** (red), **Alias missing** or **Alias out of date** (amber), **Alias never made** (blue) |

An image whose original is missing shows one row, **File missing**: its aliases cannot be made without the original,
so they are not listed separately.

**Alias out of date** means one of three things: the alias was made with other settings than `image.ini` has now
(its key differs), it was made before the image aliases were last expired, or it is older than the original.

## 3. Page through a large tree

The list has a total and pages, like the other lists: "1 to 10 of 446" in its header, the page links below it, and
**Per page** to choose 10, 25 or 50 rows. To count, every object below the start location is walked once (at most
50,000, `ListLimit` of `[MissingFileSettings]` in `contentcheck.ini`); the findings are kept for a minute
(`ListCacheTTL`), so the next pages come at once. Its pages are `(file_offset)` in the address, so they do not move
the pages of the broken relations above it.

## 4. The same from a shell

```bash
./console exp:content-check --missing-files --node=2 --allow-root-user
```

```
Missing files and image aliases below node 2: 3 in 4 objects checked
  13902-2      object 41518    published alias_missing var/site/storage/images/.../w532-image-one_small.png (small, medium)
  13905-2      object 41519    published missing       var/site/storage/original/text/a9d2344afd0d6ea0134cef55f8384827.txt
  13908-1      object 41520    published missing       var/site/storage/images/.../w532-image-two.png
```

The first column is the field version (`<attribute id>-<version>`). `--problems=missing,alias_missing` and
`--versions=published` filter as the page does; `--json` prints JSON. The exit code is 1 when something is found.

To have the cronjob part `contentcheck` count the problems every night, set `CronjobReport=enabled` in
`[MissingFileSettings]` of `settings/override/contentcheck.ini.append.php`; it writes a warning to the cronjob output
when there are any.

## 5. An extension's file datatype

A datatype that keeps its file in a table with the columns of `ezbinaryfile` needs two lines:

```ini
[MissingFileSettings]
Handlers[mydatatype]=expContentCheckFileTable
Tables[mydatatype]=mytable
```

Any other datatype adds a class implementing `expContentCheckFileHandler` (`files()`, `removeReference()`,
`hasAliases()`) and names it in `Handlers[]`.

## 6. Resolve what was found

With the policy `contentcheck/fixfiles` (and `content/edit` on the object) every row has buttons, and the list has
tick boxes and **With the ticked rows**. Whatever you choose, the next page is a **preview** that says, row by row,
what will happen or why it cannot; nothing happens until you press **Confirm**.

| You see | Do this | What happens |
|---|---|---|
| **Alias missing** or **Alias out of date** | **Regenerate** | the aliases are made again from the original with the settings of `image.ini` now |
| **File missing**, and an older version still has a file | **Restore** | that version's file is copied to the expected path (the preview names the version and the file) |
| **File missing**, and you have the file | **Re-upload** | choose the file on the preview; it must have the same extension, and an image must be an image |
| **File missing**, and the file is gone for good | **Remove reference** | the field is emptied in that version; no file is deleted |
| You are not sure | **Mark for review** | the row shows **Marked for review** until it is resolved |

1. Tick the rows (or the box in the header for the whole page; **Select all on this page** without JavaScript).
2. Choose the action under **With the ticked rows** and press **Preview**. With more pages, **Preview for all
   results below this node** takes the rows of every page.
3. Read the preview, and press **Confirm for N field versions**.
4. Large selections run in batches of 10: the page shows the progress and goes on by itself; **Stop here** goes
   back to the list.
5. The list says how many rows were done and why any were not.

Every row done is recorded in the audit (**Setup > Audit trail**, channel content): `content.file.alias.regenerate`,
`content.file.restore`, `content.file.replace`, `content.file.reference.remove`, `content.file.review.mark`.

From a shell, always with a dry run first:

```bash
./console exp:content-check --missing-files --node=2 --fix=regenerate --dry-run --allow-root-user
./console exp:content-check --fix=restore --items=13905-2 --allow-root-user
```

```
Fix restore: 1 field versions planned, 1 allowed, 1 done, 0 not done
  13905-2      allowed    var/site/storage/original/text/a9d2344afd0d6ea0134cef55f8384827.txt  (from version 1: var/site/storage/original/text/aaa078c1d252cb083b3e94fde402d21d.txt)
```
