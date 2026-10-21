---
title: Export and import layouts
audience: admin, developer, website-creator, designer, hosting
category: build-two-ways
order: 600
summary: Move layouts between installations, keep them in version control and roll a layout back - exp:layouts list, export and import with dry runs and strategies, the .zip format, and the page cache after a command-line import. Real output.
keywords: exp:layouts, export layouts, import layouts, layouts zip, dry run, strategy skip overwrite copy, publish, rollback, version control, staging to production, references
type: help
---
# Export and import layouts

Layouts live in the database, but they need not stay there: `./console exp:layouts` writes them to a `.zip` and reads
them back, on the same or another installation. The administration has the same under **Layouts > Export** and
**Layouts > Import**.

## Contents

1. [List](#list)
2. [Export](#export)
3. [What is in the file](#what-is-in-the-file)
4. [Import with a dry run](#import-with-a-dry-run)
5. [Strategies](#strategies)
6. [After a command-line import](#after-a-command-line-import)

## List

```text
$ ./console exp:layouts list
ID     Identifier                               Name                                 Status     Shared    Rules  Links to
2      fd0aa7af77a85a03bf1080018f5db1dc         Header / Footer                      published  shared        0
7      d14c82715a0c5f1da1e6df4d0b77e638         Prefooter                            published  shared        0
...
```

`--drafts` adds open drafts, `--json` prints JSON.

## Export

```text
$ ./console exp:layouts export --only=d14c82715a0c5f1da1e6df4d0b77e638 --output=prefooter.zip
Wrote prefooter.zip: 1 layouts (1 shared, 0 drafts), 4 blocks, 0 mappings, 3 content references.
```

| Option | Means |
|---|---|
| `--only=a,b` | these layouts (by identifier) |
| `--all` | every layout, shared layout and mapping |
| `--shared=all\|a,b` | shared layouts to add (the ones the chosen layouts link to are always included) |
| `--rules=none\|selected\|all` | the layout mappings: none, those of the chosen layouts (default) or all |
| `--drafts` | the open drafts too |
| `--output=file.zip` | the file (default `layouts-<date>.zip` in the current directory) |

## What is in the file

`layouts.json` (layouts, zones, blocks, parameters, collections and items), `rules.json` (mappings),
`references.json` and `manifest.json`. Content is referred to by **remote ID and path**, not only by node ID, so an
import into another installation can find the same content:

```json
"node": {
    "93": { "remote_id": "media-n-725", "path": "fit_healthy/fitness/7_keystone_habits_to_maximize_your_mornings" },
    "98": { "remote_id": "media-n-731", "path": "fit_healthy/fitness/why_track_your_workouts" }
}
```

`manifest.json` names the format (`exponential-layouts-transfer`, version 1), the source site and Exponential version,
the database, the selection and counts.

## Import with a dry run

Always start with `--dry-run`. Importing the file into the installation it came from:

```text
$ ./console exp:layouts import prefooter.zip --dry-run
File: format version 1, made 2026-10-19T12:37:15+00:00 on www.example.com.
State        Action      Kind       Key
identical    none        layout     layout:d14c82715a0c5f1da1e6df4d0b77e638:published
Totals: 0 new, 1 identical, 0 changed, 0 conflicting.
Dry run: nothing was written.
```

An older export of the same layout, after the layout was changed:

```text
$ ./console exp:layouts import prefooter-before.zip --dry-run
State        Action      Kind       Key
changed      skip        layout     layout:d14c82715a0c5f1da1e6df4d0b77e638:published
Totals: 0 new, 0 identical, 1 changed, 0 conflicting.
Dry run: nothing was written.
```

## Strategies

| `--strategy=` | An item that exists already |
|---|---|
| `skip` (default) | is left as it is |
| `overwrite` | is replaced; with `--publish` the result is published, otherwise it stays a draft |
| `copy` | is imported as a new copy beside it |

`--item=<key>=<strategy>,...` sets a strategy per item, with the keys the dry run prints. `--unmapped=drop|keep|fail`
decides what happens to references to content that the target installation does not have (default `drop`).

## After a command-line import

Since explayouts_core 1.0.10, an import that publishes a layout (`--publish`) or writes a rule purges every page of
the page cache itself, on the command line too, as publishing in the editor does. Clearing the content cache by hand
after a command-line import is no longer needed. Drafts, dry runs and refused imports purge nothing. The import still
ends with the line `N items written; M resolver cache entries removed.` (for example
`1 items written; 0 resolver cache entries removed.`).

## Related pages

- [Roll a layout back](roll-back-a-layout.md)
- [Shared layouts and zone linking](shared-layouts-and-zone-linking.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
