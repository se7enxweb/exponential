---
title: URL map examples
audience: admin, developer
category: examples
order: 60
summary: Small, realistic URL map files in CSV, JSON, Apache and nginx format, with query-string redirects, temporary redirects and wildcards, each checked with exp:url:import --dry-run.
keywords: URL map, example, mapping file, CSV, JSON, htaccess, Apache, nginx, redirect, wildcard, query string, temporary redirect, exp:url:import
type: example
---
# URL map examples

These files are small URL maps of an imagined old site, moving to the Fit & Healthy site of the Exponential
demo. Copy one, change the addresses to your own and import it. Every target is a
page of that site, so each file imports cleanly into it (only the node ID may need changing, see below).

How to use them: [Migrating URLs](../../help/migrating-urls.md). The columns and kinds:
[Importing and exporting URL maps](../../features/6.0/url-map-import-export.md). Every detail of the formats:
[The URL map file formats](../../specifications/6.0/url-map-file-formats.md).

## The files

| File | Format | What it shows | Rows |
|---|---|---|---|
| [`old-site.csv`](old-site.csv) | CSV | Permanent redirects of single pages, a target by node ID (`node:163`), an `alias` that shows the page at the old address | 8 |
| [`old-site.json`](old-site.json) | JSON | The same redirects as JSON, with the `exp-url-map/1` header | 7 |
| [`old-site.htaccess`](old-site.htaccess) | Apache | `RewriteRule`, a `Redirect` of a whole section (read as Apache answers it: the page and everything below it), `RedirectMatch` as a wildcard, and an old address with a query (`RewriteCond %{QUERY_STRING}`) | 7 |
| [`old-site.nginx`](old-site.nginx) | nginx | `location =` with `return`, old addresses with a query (`if ($args = ...)`), `rewrite` as a wildcard, and a `map` of single pages | 7 |
| [`query-and-temporary.csv`](query-and-temporary.csv) | CSV | Query-string redirects (`/index.php?id=12`), a 308, and temporary redirects by kind (`redirect-temporary`) and by status (307) | 5 |
| [`wildcards.csv`](wildcards.csv) | CSV | Wildcards: a section that moved, a `*` in the middle of an address, a temporary wildcard, and a direct one (no redirect) | 4 |

"Rows" is what the import makes of the file: an Apache `Redirect` gives two rows (the page and a wildcard for
everything below it).

## Try one

Plan the file and write nothing:

```bash
./console exp:url:import doc/eng-us/examples/url-maps/old-site.csv -s site --dry-run
```

```text
URL map dry run: doc/eng-us/examples/url-maps/old-site.csv (8 rows)
  Plan:   8 new, 0 changed, 0 unchanged, 0 duplicate, 0 conflict, 0 broken target, 0 unknown host, 0 not importable
  Action: 8 to create, 0 to update, 0 skipped
  Done:   8 created, 0 updated, 0 skipped, 0 failed
```

In a dry run, "Done" counts what the import would do. Leave out `--dry-run` to import, and undo it again with
`./console exp:url:undo <number>` (the number is printed by the import and listed by `./console exp:url:batches`).
In the administration, the same files go into **Setup** > **URL translator** > **Import**.

## How they were checked

Each file was planned with `exp:url:import --dry-run` against an installation with the Fit & Healthy site: every row
of every file came out as **new**, with no broken target, conflict, unknown host or row that cannot be imported.

## On your own site

- Replace the old addresses with your old site's and the new ones with pages of your site. A new address that does
  not exist is listed as **broken target** and not imported.
- `node:163` is the Running page of the installation the files were checked on. Node IDs differ from one
  installation to the next: use the node ID of your own page (the demo's Running page is `node:171`).
- `/user/login`, `/content/view/full/2` and other addresses of the system cannot be old addresses: the preview lists them as a
  **conflict**.
