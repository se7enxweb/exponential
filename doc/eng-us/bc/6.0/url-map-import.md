# URL map import and export

Read this page when you upgrade to Exponential 6.0.15 and want to know what importing and exporting URL maps adds to
an existing installation, and whether anything you have changes. In short: your URL aliases and wildcards work as
before; there is a new table of redirects that is checked first, and it is empty until you import into it.

[The URL map file formats](../../specifications/6.0/url-map-file-formats.md) ·
[Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)

## What is added

| Area | Addition |
|---|---|
| Database | the tables `exp_url_redirect` (redirects checked on the full request), `exp_urlmap_batch` and `exp_urlmap_batch_item` (the record of imports), made by the upgrade step `6.0.15-url-map` on every supported database |
| Classes | `kernel/classes/urlmap/`: `expUrlMapPlanner`, `expUrlMapPlan`, `expUrlMapImporter`, `expUrlMapExporter`, `expUrlRedirect`, `expUrlMapBatch`, `expUrlMapBatchItem`, `expUrlMapEnvironment` and `expUrlMapKernelEnvironment` |
| Console | `exp:url:import`, `exp:url:export`, `exp:url:batches`, `exp:url:undo` |
| Wildcards | the type `eZURLWildcard::TYPE_FORWARD_TEMPORARY` (3): a forward wildcard that redirects with 302 |
| Settings | `error.ini` `[HTTPError-302]`, `[HTTPError-303]`, `[HTTPError-307]`, `[HTTPError-308]` |
| Audit | `content.urlalias.import`, `content.urlalias.import_undo` (on by default) |

## What does not change

- Global URL aliases, the custom aliases of nodes and wildcards are stored and answered as before. A 301 redirect to
  a page of the site is imported as an ordinary global URL alias that redirects, exactly as the URL translator makes
  one; it shows up there and can be removed there.
- Without the tables (before the upgrade step has run) nothing is read: no request is answered differently, and an
  import is refused with a message that names the step.
- A wildcard of type 1 still answers with 301, an alias that redirects still answers with 301.

## Where an imported row goes

| Row | Stored as |
|---|---|
| `redirect` (301) to a page of this site, old address without a query | a global URL alias that redirects (`ezurlalias_ml`) |
| `alias` | a global URL alias that does not redirect |
| `wildcard-forward`, `wildcard-forward-temporary`, `wildcard-direct` | a URL wildcard of type 1, 3 or 2 (`ezurlwildcard`); wildcards apply in every siteaccess, and 303 or 307 are answered as 302, 308 as 301 |
| every other redirect: an old address with a query, a temporary status (302, 303, 307), 308, a target with a query, a target on another site | a row of `exp_url_redirect` |

A row whose old address is a page of this site (the address of a node), a module address (`/content/view/full/12`,
`/user/login`) or the front page `/` without a query is a conflict and is never imported. `/index.php` without a query
(the front controller) is not importable; `/index.php?id=12` is.

## Redirects checked on the full request

The redirects of `exp_url_redirect` are checked once per request, on the address the visitor asked for, before the URL
aliases and wildcards (in `ezpKernelWeb::dispatchLoop()`, so for PHP-FPM, Velocity and every other front end alike):

- The path is compared normalized as in the URL map files (one slash between segments, no trailing slash, dot
  segments resolved, percent-encoded UTF-8 decoded) and without regard to case, as aliases and wildcards are. It is
  tried as the request's whole path (`/index.php`, `/eng/old`) and as the path inside the siteaccess (`/old`).
- The query is compared exactly, but the order of its parameters does not count: the parameters are decoded (`+` is
  a space, `a` counts as `a=`) and sorted. A redirect without a query answers only a request without a query.
- A redirect of a siteaccess answers only there; one of every siteaccess (`*` in the file's `siteaccess` column,
  stored without one) everywhere, after those of the siteaccess.
- The target is `node:<id>` (the node's address at the time of the request, so it follows a moved node), a path of
  this site, or a full URL. The request's query is not carried over to the target.
- The redirect answers with its status (301, 302, 303, 307, 308) when `error.ini` has the `[HTTPError-<status>]`
  group, else with 301.

The table is read from a cache file next to the wildcard cache (`var/<site>/cache/wildcard/urlredirect_*.php`), which
the URL alias cache clears too and which an import or undo replaces at once; a request with no redirects costs one
small cached file.

## Import, history and undo

An import is planned first (`expUrlMapPlanner`): every row is new, unchanged, changed, duplicate (an earlier row of
the file has the same old address; the first one counts), conflict, broken-target, unknown-host or not-importable.
New rows are imported; changed ones only with the conflict policy overwrite (`--on-conflict=overwrite`). The import
(`expUrlMapImporter`) runs in one transaction where the database has them, and records the batch and, for each row
it wrote, the row before (none for a new one) and after. Before each write it compares the entry at the old address
with the plan's and skips a row whose entry changed in between.

`exp:url:undo <batch>` puts back every row as it was, the last first. A row changed after the import is kept (the
batch is then partly-undone) unless `--force` is given. `exp:url:batches` lists the imports.

```bash
./console exp:upgrade --apply                                  # makes the tables (step 6.0.15-url-map)
./console exp:url:import old-site.csv -s site --dry-run --report=var/tmp/plan.csv
./console exp:url:import old-site.csv -s site
./console exp:url:import old-site.htaccess --host=www.old-site.com=site --on-conflict=overwrite
./console exp:url:batches
./console exp:url:undo 3
./console exp:url:export --output=var/tmp/url-map.xlsx
./console exp:url:export --kind=wildcard,redirect --format=nginx > var/tmp/rules.nginx
```

A full old address picks the siteaccess by its host, through the installation's siteaccess matching; a host that is
not one of the installation's is listed as unknown-host until `--host=<old host>=<siteaccess>` maps it (or the host
points at the installation). Bare paths belong to the siteaccess of `-s`, else the default siteaccess; the language
is the row's, else `--language`, else the siteaccess's content language.

`exp:url:import` and `exp:url:export` take the file's format with `--format` and print JSON (`exp-url/1`) with
`--output-format=json`; `exp:url:batches` and `exp:url:undo` print it with `--format=json`. The exit codes are
`exp:upgrade`'s: 0 done, 1 failed, 2 usage, 3 the database unreachable, 4 internal error.

## Export

`exp:url:export` writes the global URL aliases of the URL translator (with `--include-node-aliases` also the custom
aliases of nodes, target `node:<id>`), the wildcards and the redirects, in every format the import reads and as
`.ods`. Importing the export into the same installation finds every row unchanged.

## For developers

- The planner asks the installation through `expUrlMapEnvironment`; `expUrlMapKernelEnvironment` is the real one, a
  test passes its own, so the classification is tested without a database.
- `expUrlMapPlan::setDecision( $index, 'import'|'skip' )` overrides a row's decision before `apply()`;
  `toArray()` and `writeReport()` give the preview (schema `exp-url-map-plan/1`).
- `eZURLWildcard::isForwardType()`, `redirectStatusOfType()`, `typeName()` and `lastRedirectStatus()` are new;
  code that compares a wildcard's type with `TYPE_FORWARD` should use `isForwardType()` to include the temporary
  type.

## Oracle and MongoDB

The upgrade step makes the tables from `share/db_schema.dba` on Oracle (through ezoracle's schema handler) and MongoDB
(collections) too. The import writes with the same SQL statements the kernel's URL alias code uses; it was tested on
SQLite, MySQL 8.0 and PostgreSQL 13.
