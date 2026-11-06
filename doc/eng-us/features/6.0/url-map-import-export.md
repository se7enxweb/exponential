# Importing and exporting URL maps

When a site moves to Exponential, or gets a new structure, its old addresses have to keep working: links on other
sites, bookmarks and search engine results point at them. Exponential 6.0.15 takes those addresses in as a **URL
map**, a file that lists each old address and where it goes now. It imports the whole file in one step, with a
preview first, and can take the whole import back again. It also writes the site's own redirects out as a file, for a
web server or as a backup.

Before 6.0.15 a global URL alias (the URL translator) or a URL wildcard had to be created one at a time in the
administration, and there was no way to export them.

[Features 6.0](README.md) · Tutorial: [Migrating URLs](../../help/migrating-urls.md) · Example files:
[URL map examples](../../examples/url-maps/README.md)

## What it does

- **Imports** a URL map into the URL translator, the URL wildcards and a new list of redirects, as one batch.
- **Previews** every row before anything is written: what is new, what would change, and what cannot be imported
  and why. A **dry run** checks every row against the site and writes nothing.
- **Undoes** an import as a whole: every entry it created is removed and every entry it replaced is put back.
- **Exports** the site's global URL aliases, wildcards and redirects in every format it reads. Importing an export
  into the same site changes nothing.
- **Redirects old addresses with a query**, such as `/index.php?id=12` of an old CMS, to the new page.
- **Keeps temporary redirects temporary** (302, 303, 307), so search engines keep the old address where that is meant.

It works in the administration (the URL translator and URL wildcards pages) and on the command line.

## Formats

| Format | Import | Export |
|---|---|---|
| CSV (also `;` or tab separated, as spreadsheet programs save it) | yes | yes |
| JSON | yes | yes |
| Excel `.xlsx` | yes | yes |
| LibreOffice Calc `.ods` | yes | yes |
| Apache rules (`Redirect`, `RedirectMatch`, `RewriteRule` with `RewriteCond`) | yes | yes |
| nginx rules (`location =`, `return`, `rewrite`, `map`) | yes | yes |

The format is recognized from the file's name or its content. A spreadsheet or CSV file has the columns
`old_url`, `new_url`, `kind`, `siteaccess`, `language`, `note` and `status`; only the first two are needed:

```text
old_url,new_url
/about-us.html,/fitness
/recipes/hummus.html,/recipes/home-made-hummus
/old-recipes/*,/recipes/{1}
```

Every detail of every format, including what a rewrite rule can and cannot be turned into, is in the specification
[The URL map file formats](../../specifications/6.0/url-map-file-formats.md).

## Old and new addresses

- **The old address** is a path (`/about-us.html`) or a full address (`https://www.old-site.com/about-us.html`). A
  full address picks the siteaccess by its host. An old host that this installation does not answer for is mapped to
  a siteaccess in the import screen, or with `--host=www.old-site.com=site` on the command line.
- **The new address** is a path of this site (`/recipes`), a node (`node:163`, which follows the page when it moves),
  or a full address on another site.
- A `*` in the old address makes a **wildcard**: `{1}` in the new address stands for what the first `*` matched.

## Kinds and statuses

The `kind` column decides what a row becomes. Left empty, a row with `*` is a redirecting wildcard and every other
row a permanent redirect.

| Kind | What visitors get | Stored as |
|---|---|---|
| `redirect` | A permanent redirect (301, or 308 in the `status` column) | A global URL alias that redirects; a redirect when it has a query or a target on another site |
| `redirect-temporary` | A temporary redirect (302, or 303 or 307 in `status`) | A redirect |
| `alias` | The new page, shown at the old address, without a redirect | A global URL alias |
| `wildcard-forward` | A permanent redirect for every matching address | A URL wildcard (redirecting) |
| `wildcard-forward-temporary` | A temporary redirect (302) for every matching address | A URL wildcard (temporary redirect) |
| `wildcard-direct` | The new page at the old address, for every matching address | A URL wildcard (direct) |

Global URL aliases and wildcards are the same entries you would create by hand; they show up on the URL translator
and URL wildcards pages and can be changed there. Redirects that an alias cannot hold (an old address with a query, a
temporary status, a target on another site) go into a list of their own, **Redirects**.

## Query-string redirects

Old content management systems often had addresses such as `/index.php?id=12` or `/shop.php?category=3`. A row with
such an old address is a redirect of the full request:

```text
old_url,new_url
/index.php?id=12,/recipes
/shop.php?category=3,/recipes/kale-salad
```

These redirects are checked first on every request, before the URL aliases and wildcards. The order of the query's
parameters does not count (`?b=2&a=1` is `?a=1&b=2`); another query, or none, does not match. The old query is not
carried over to the new address.

## Preview, conflicts and dry run

Each row of a file is planned before it is imported, and gets one of these states:

| State | Meaning | Imported |
|---|---|---|
| new | Nothing is at the old address yet | yes |
| changed | Another entry is at the old address | only when you choose to replace existing entries |
| unchanged | The same entry is there already | no |
| duplicate | An earlier row of the file has the same old address; the first one counts | no |
| conflict | The old address is a page of this site, an address of the system (such as `/user/login`) or the front page | never |
| broken target | The new address does not exist on this site | no |
| unknown host | The old host is not one of this installation's | no, until the host is mapped to a siteaccess |
| not importable | The row itself is wrong; its notes say why | no |

A **dry run** goes through every row against the site as the import would, counts what it would do and writes nothing.

## Undo

Every import is recorded as one batch, with every entry it wrote as it was before and after. **Undo** removes what
the import created and puts back what it replaced, the last first. An entry that someone changed after the import is
kept, unless you choose to undo it too. The import history lists who imported which file when, with the counts.

## Export

An export writes the site's global URL aliases (optionally also the custom aliases of pages, as `node:<id>`), the
wildcards and the redirects, in any of the formats above. Use it as a backup before a big import, to edit the map
in a spreadsheet and import it again, or to hand the redirects to the web server: the Apache and nginx exports are
rules the server answers itself, without PHP.

## In the administration

On the **URL translator** and **URL wildcards** pages (**Setup** tab), the buttons **Import**, **Export**, **Import
history** and **Redirects** lead to the screens:

| Screen | Address | What it does |
|---|---|---|
| Import | `/content/urlimport` | Upload a file or paste rows, choose the siteaccess and language of bare paths and what happens to existing entries, map old hosts; then the preview with a filter per state, a tick per row, **Dry run**, the plan as CSV or JSON, and **Import** |
| Import history | `/content/urlimporthistory` | Every import with who, when, the file and the counts; one import's entries; **Undo import** |
| Export | `/content/urlexport` | The format, what to include, the siteaccess and language; downloads the file |
| Redirects | `/content/urlredirects` | The redirects with a query or a status, with removing one or the ticked ones |

Importing and undoing ask you to confirm who you are first ("Verify it's you"). The screens are explained one by
one in the guide [URL maps: import, export and undo](../../guides/url-maps.md).

## Policies

| What | Policy |
|---|---|
| Import, the import history, undo | `content/urltranslator_import` |
| Export, the list of redirects | `content/urltranslator` |

The Administrator role has both. Give `content/urltranslator_import` only to the people who may change the site's
addresses in bulk: an import can redirect hundreds of addresses at once. Every import and undo is recorded in the
audit trail (`content.urlalias.import`, `content.urlalias.import_undo`).

## On the command line

| Command | What it does |
|---|---|
| [`exp:url:import`](../../examples/commands/expurlimport.md) | Plan and import a file; `--dry-run` writes nothing, `--report=<file>` writes the plan row by row |
| [`exp:url:export`](../../examples/commands/expurlexport.md) | Write the URL map to a file or the standard output |
| [`exp:url:batches`](../../examples/commands/expurlbatches.md) | List the imports |
| [`exp:url:undo`](../../examples/commands/expurlundo.md) | Undo an import |

```bash
./console exp:url:import old-site.csv -s site --dry-run --report=var/tmp/plan.csv
./console exp:url:import old-site.csv -s site
./console exp:url:import old-site.htaccess -s site --host=www.old-site.com=site
./console exp:url:batches
./console exp:url:undo 3
./console exp:url:export --output=var/tmp/url-map.xlsx
./console exp:url:export --kind=wildcard,redirect --format=nginx
```

Bare paths belong to the siteaccess given with `-s` (else the default siteaccess). With `--on-conflict=overwrite`
an import replaces the entries at addresses that have one; by default it skips them. `--output-format=json` (import,
export) and `--format=json` (batches, undo) print machine-readable output.

## Installing it

Upgrading to 6.0.15 adds three tables, made by the upgrade step `6.0.15-url-map` (`./console exp:upgrade --apply`).
Until the step has run, nothing changes and an import is refused with a message that names the step. What is added
and what stays the same: [URL map import and export](../../bc/6.0/url-map-import.md).

## Related pages

- [Migrating URLs](../../help/migrating-urls.md): the step-by-step tutorial
- [URL map examples](../../examples/url-maps/README.md): example files that import cleanly
- [The URL map file formats](../../specifications/6.0/url-map-file-formats.md)
- [URL map import and export](../../bc/6.0/url-map-import.md)
- [Links, URL aliases, wildcards and search statistics](../../guides/urls-and-aliases.md)
- [URL wildcards and custom aliases](../../help/creators/website-creators/url-wildcards.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
