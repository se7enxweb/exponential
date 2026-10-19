---
title: Roll a layout back
audience: admin, website-creator, editor, developer
category: build-two-ways
order: 610
summary: Take a safety copy of a layout before you change it and put it back afterwards with exp:layouts - export, change, import with overwrite and publish, clear the content cache. Worked through on the shared layout Prefooter.
keywords: rollback, undo layout, restore layout, backup layout, exp:layouts export, import overwrite publish, safety copy, layout history
type: help
---
# Roll a layout back

In the editor, **Discard** throws away a draft; it does not bring back a version that was published before. An export
taken before the change is a way back that works from the command line, on any installation.

## Contents

1. [Before the change: export](#1-before-the-change-export)
2. [Make the change](#2-make-the-change)
3. [Roll back: preview](#3-roll-back-preview)
4. [Roll back: write](#4-roll-back-write)
5. [Clear the content cache](#5-clear-the-content-cache)

## 1. Before the change: export

```text
$ ./console exp:layouts export --only=d14c82715a0c5f1da1e6df4d0b77e638 --output=prefooter-before.zip
Wrote prefooter-before.zip: 1 layouts (1 shared, 0 drafts), 4 blocks, 0 mappings, 3 content references.
```

The administration does the same under **Layouts > Export**. Keep the file with a date in its name, or in your
project's repository.

## 2. Make the change

On the test copy the Prefooter got a fourth article, then the view type `list_compact`, then a Notice
block ([Change the prefooter in the Layouts editor](prefooter-in-the-layouts-editor.md)).

## 3. Roll back: preview

```text
$ ./console exp:layouts import prefooter-before.zip --dry-run
State        Action      Kind       Key
changed      skip        layout     layout:d14c82715a0c5f1da1e6df4d0b77e638:published
Totals: 0 new, 0 identical, 1 changed, 0 conflicting.
Dry run: nothing was written.
```

`changed` with the default strategy `skip` would leave the layout alone. Preview the overwrite:

```text
$ ./console exp:layouts import prefooter-before.zip --dry-run --strategy=overwrite --publish
State        Action      Kind       Key
changed      overwrite   layout     layout:d14c82715a0c5f1da1e6df4d0b77e638:published
Totals: 0 new, 0 identical, 1 changed, 0 conflicting.
Dry run: nothing was written.
```

## 4. Roll back: write

```text
$ ./console exp:layouts import prefooter-before.zip --strategy=overwrite --publish
State        Action      Kind       Key
changed      overwrite   layout     layout:d14c82715a0c5f1da1e6df4d0b77e638:published
Totals: 0 new, 0 identical, 1 changed, 0 conflicting.
Written: layout:d14c82715a0c5f1da1e6df4d0b77e638:published overwritten and published (ID 7)
1 items written; 0 resolver cache entries removed.
```

The layout keeps its ID (7) and identifier, so every layout that links to it keeps working. After the import it had its
three original articles again and none of the added blocks.

Without `--publish` the imported version becomes a draft, to check in the editor before publishing.

## 5. Clear the content cache

The import does not purge the page cache. On the test copy the article pages still showed the changed prefooter
(`X-Exp-Cache: HIT`) until:

```text
./console exp:cache clear --tag=content
```

## Related pages

- [Export and import layouts](export-and-import-layouts.md)
- [Change the prefooter in the Layouts editor](prefooter-in-the-layouts-editor.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
