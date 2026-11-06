# URL maps: import, export and undo in the administration

This page explains the URL map screens of the administration interface: importing a file of old addresses into the
URL translator, the wildcards and the redirects (`/content/urlimport`), the import history with its undo
(`/content/urlimporthistory`), the export (`/content/urlexport`) and the list of redirects
(`/content/urlredirects`). The buttons **Import**, **Export**, **Import history** and **Redirects** above the URL
translator and the wildcards lead there.

It is for administrators who move a site to this installation, or who keep the addresses of an old site working.
The file formats are described in [The URL map file formats](../specifications/6.0/url-map-file-formats.md); what
the import adds to an installation, and the console commands, in [URL map import and export](../bc/6.0/url-map-import.md).

[Guides](README.md) · Related: [Links, URL aliases, wildcards and search statistics](urls-and-aliases.md),
[Verify it's you](verify-its-you.md), [Importing and exporting URL maps](../features/6.0/url-map-import-export.md),
[Migrating URLs](../help/migrating-urls.md) (a tutorial), [URL map examples](../examples/url-maps/README.md)

## 1. Import a URL map

**Import** (`/content/urlimport`) takes a file, or rows pasted into the text box: CSV, JSON, Excel (`.xlsx`),
OpenDocument (`.ods`), Apache or nginx rewrite rules, at most 10 MB. The format is found from the file's name or
content; choose it only when neither says.

| Field | What it decides |
|---|---|
| Siteaccess of bare paths | The siteaccess of rows that give a path (`/about-us`). A row with a full address (`https://www.old-site.com/about-us`) takes the siteaccess of its host. |
| Language | The language of rows without one; by default the siteaccess's language. |
| When an old address has an entry already | Keep the entry and skip the row, or replace it. |
| Old hosts | `www.old-site.com = site`, one per line: the siteaccess an old host stands for. Rows of a host this installation does not answer for are not imported otherwise. |

**Preview** keeps the file on the server and shows what each row would do. Nothing is written yet.

## 2. The preview

Each row of the file is listed with its state, as a filter above the table:

| State | Meaning | Imported |
|---|---|---|
| New | Nothing is at the old address yet | yes (ticked) |
| Changed | Another entry is at the old address | only when it may be replaced |
| Unchanged | The same entry is there already | no |
| Duplicate | An earlier row of the file has the same old address; the first one counts | no |
| Conflict | The old address is a page of this site, a module address or the front page | never |
| Broken target | The new address does not exist on this site | no |
| Unknown host | The old host is not this installation's: map it under **Plan again** | no |
| Not importable | The row itself is wrong (see its notes) | no |

Untick a row to leave it out; **Keep the ticks** saves them for the rows of the page. **Plan again** plans the
file again with old hosts mapped to a siteaccess, or with another choice for addresses that have an entry.
**Dry run** checks every row against the site again and counts what the import would do, without writing. **Plan
as CSV** and **Plan as JSON** download the preview. **Throw away** removes the file; otherwise it is removed after a
day.

**Import** asks you to confirm who you are ("Verify it's you"), then writes the ticked rows as one import and shows
its number. A row whose old address changed since the preview is skipped.

## 3. Import history and undo

**Import history** (`/content/urlimporthistory`) lists every import, newest first: when, by whom, from which file,
how many entries were created, replaced, skipped or failed, and the state (applied, undone, partly undone). An
import's page lists every entry it wrote.

**Undo import** asks "Verify it's you" again, then removes every entry the import created and puts back every entry
it replaced, the last first. An entry changed after the import is listed above the button and kept, unless **Also
undo the entries changed after the import** is ticked (the console's `--force`); the import is then partly undone,
and can be undone again later.

## 4. Export

**Export** (`/content/urlexport`) downloads the global URL aliases of the URL translator (optionally also the custom
URL aliases of content, written as `node:42`), the wildcards and the redirects, as CSV, JSON, `.xlsx`, `.ods`,
Apache or nginx rules. The siteaccess and language filters apply to the redirects (and the language to the aliases);
URL aliases and wildcards belong to every siteaccess. Importing an export into the same installation finds every row
unchanged.

## 5. Redirects

**Redirects** (`/content/urlredirects`) lists the redirects imports made that a global URL alias cannot hold: old
addresses with a query (`/index.php?id=12`), temporary redirects (302, 303, 307), 308 and redirects to other sites.
They are checked on the full address before the URL aliases and wildcards. A redirect can be removed here (one, or
the ticked ones); it stops working at once.

Wildcards that redirect temporarily show **Temporary redirect (302)** on the wildcards page.

## 6. Who may do what

| Screen | Policy |
|---|---|
| Import, Import history, Undo | `content/urltranslator_import` |
| Export, Redirects | `content/urltranslator` |

Importing and undoing ask "Verify it's you" (`site.ini [ReauthGroup_system]`). A role that has `content/*` (or
`*/*`, as Administrator has) includes both functions; give `content/urltranslator_import` on its own to a role that
may import URL maps but has `content/urltranslator` only.
