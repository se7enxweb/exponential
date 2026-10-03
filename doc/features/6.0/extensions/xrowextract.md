# xrowextract: export, import and move content as files

`xrowextract` is the Export tab of the admin. It turns content into files you
can open in a spreadsheet, hand to another system or keep as a backup, and it
reads such files back into content. From 2.5.0 (September 2026) it grew from a
single "one class to CSV" page into a complete data-exchange tool: previews,
filters, saved presets, background jobs, imports of any size, content packages
(`.ezpkg`), scheduled runs and delivery to SFTP, S3 and other destinations.

Open it at **Export** in the top admin menu (`/xrowextract/csv`). Everything on
this page is also available on the command line, see
[Command line](#command-line). The full reference (settings, policies, tables,
classes) is in the [xrowextract specification](../../../specifications/6.0/xrowextract.md);
the history of the work is in the [xrowextract chronicle](../../../history/extensions/xrowextract.md)
and the [release notes](../../../changelogs/extensions/xrowextract.md).

## What you can do now that you could not before 2.5

| Task | Where | Since |
|---|---|---|
| Export one class as CSV, JSON or XML, below a node or across the whole site | **One class of content export** | CSV since 1.0; JSON, XML and whole-site scope 2.5.0 |
| See the export as a spreadsheet before downloading it | **Preview** on the same page | 2.5.0 |
| Export every class below chosen nodes in one ZIP or TAR archive, one file per class | **Multi class of content export** (`/xrowextract/archive`) | 2.5.0 |
| Export content in several languages, one row per translation | Languages card | 2.5.0 |
| Filter by date, section, state, visibility, name, attribute, owner, depth and more, joined with and/or | Filters card | 2.5.0 |
| Save an export as a named, reusable preset, with `{placeholders}` | Presets card (41 shipped, grouped by audience) | 2.5.3 |
| Run a large export in the background and download it from a Jobs page | **Run in the background**, **Jobs** tab | 2.5.0 |
| Import a CSV, JSON or XML file into content objects: map columns, dry run, apply | **Import content file** | 2.5.0 |
| Upload files of any size (chunked, resumable) and stream them row by row | Import page | 2.5.3 |
| Inspect, compare, install and export content packages (`.ezpkg`) | **Import content package** (`/xrowextract/package`) | 2.5.3 |
| Run exports and imports on a schedule and deliver the file to SFTP, FTP/FTPS, a NAS folder, S3, WebDAV or an HTTP endpoint | **Schedules**, destinations and history | 2.5.3 |
| Check what this server can and cannot do for the tool | Notice on each page, `ext:xrowextract:requirements` | 2.5.4 |

## The tabs

After 2.5.3 the tabs read: **One class of content export**, **Multi class of
content export**, **Import content file**, **Import content package**,
**Schedules** and **Jobs**. (Before that, the import and package tabs were
called *Import* and *Package*; the views and policies kept their names, only
the labels changed.)

### One class of content export

Pick a class, a start node and a scope, then download. The page is built from
cards:

* **Scope**: below one node (the node is remembered), or every object of the class
  in the whole site. A numeric depth takes exactly, at most or at least that many
  levels below the node.
* **Columns**: the attributes of the class and a catalogue of special columns
  (object id, remote id, main node, parent node, URL alias, dates, and, for users,
  login and e-mail). Column sets, the format of each attribute and a
  `xrowmetadata` column are included. The page names the datatype and the meta
  information of every column.
* **Languages**: counts per language; all or some; one row per translation.
* **Filters**: date range, section, state, visibility and name quick filters, and
  several conditions, each on a class attribute or on a kernel field (`name`,
  `published`, `modified`, `modified_subnode`, `section`, `owner` by id or login,
  `priority`, `depth`, `class_identifier`, `class_name`, `node_id`,
  `contentobject_id`, `path`, `state`). Operators: contains, starts with, is, is
  not, greater/less (also `>=`, `<=`), is empty / not empty, in list / not in
  list, between / not between, matches / does not match a `*` pattern. Each row
  can be inverted with "not". Rows join with **all (and)** or **any (or)**.

  The kernel's attribute filter has a single join for the whole filter, so "any"
  widens every active filter, not only the condition rows, when more than one is
  set.
* **Extended filters**: any filter registered in `extendedattributefilter.ini`
  (eztags, `XrowExtractHasChildren`, `XrowExtractRelation`,
  `XrowExtractUserStatus`) can be chained with the language filter; its
  parameters are one JSON object, for example `{"has_children": true}` or
  `{"object_id": 123, "reverse": true}`.
* **Named fetch**: apply a `fetchalias.ini` alias (module `content`, function
  `tree`, `list`, `tree_count` or `list_count`). Its node, class, sort, depth,
  limit/offset and main-locations settings are read into the form; a
  `Parameter[]` entry it declares is filled in through **Parameters**, written as
  `key=value,key=value`. The **Fetch parameters** box at the end of the card shows
  the resolved filters as the literal `fetch('content','tree', hash(...))` call a
  template would make.
* **Output**: CSV (separator, line endings, quoting), JSON or XML. Cells that
  start with `=`, `+`, `-` or `@` get a leading `'` so a spreadsheet shows them as
  text and does not run them as formulas (`csv.ini [General] NeutralizeFormulas`).

**Preview** shows the export as a spreadsheet will show it, using the same code
that builds the file, and reads it back with the chosen separator and quoting. It
shows the first 10, 25, 50 or 100 rows (remembered per user), how many rows and
columns the file will have, how full each column is, rows that would shift their
columns and cells that were neutralised as formulas. Rows can be filtered and
sorted, cells expanded or wrapped, and the shown rows copied tab separated. None
of this changes the download.

### Multi class of content export (site archive)

The content below one or more nodes as one archive: a file for every class
(object id, remote id, main node, parent node, URL alias, dates, then every
attribute of the class), plus `manifest.json` and `README.txt`. Ready-made node
sets (content and media, content, media, users, everything), single nodes added
from a list with counts or from the browse page, and classes ticked or unticked
(all classes with content are ticked).

* Formats: ZIP, TAR.GZ, TAR.BZ2, TAR.XZ; 7-Zip and RAR when the `7z` or `rar`
  programs are installed.
* Objects are read with the user's read access, at their main location, and
  written once.
* Password hashes are never exported in an archive unless a user holding the
  policy `xrowextract/password_hash` asks for them (see
  [Password hashes](#password-hashes)).
* The archive is written to a private folder in the cache directory and removed
  after the download.
* Since 2.5.0 the archive can use JSON or XML instead of CSV, a **Sites** set (the
  site roots, optionally set in `export.ini [SiteArchive]`), languages and a
  column choice per class.

### Presets

A preset is a complete, named export definition: scope, node (stored by id and
remote id, so it survives a reinstall's renumbering), class, columns, languages,
every filter, sort and output setting. **Save as preset** captures the form as
it stands. The picker loads one back in place, or **Run in the background**
resolves and starts it as a job without loading it first.

* A preset may contain `{placeholder}` tokens (a node, a date, a class) which you
  fill in through **Parameters**, the same way a named fetch's `Parameter[]` is.
* A preset may *extend* another preset (`user:<id>`) or a named fetch
  (`alias:<name>` or `alias:<name>:<siteaccess>`) and override single keys.
* Two layers: **your own presets** (private, or shared with every export user;
  editable by their owner or by a user holding `xrowextract/all_jobs`) and **site
  presets** shipped as `[Preset_<id>]` blocks in `xrowextract.ini`. 41 site presets
  ship with the extension, grouped by audience (Site, Editors, Developers,
  Partners, Users, Maintenance). The **INI** disclosure on each row shows the
  block to copy into your own settings.
* A background job started from a preset records which one; the Jobs page shows
  it next to the job.

Example of a site preset, in `settings/override/xrowextract.ini.append.php` or an
extension of your own:

```ini
[Preset_hidden_news]
Name=Hidden news
Description=Everything hidden below a node, newest changes first.
Audience=Maintenance
View=csv
Definition={"scope":"tree","subtree":"{node}","sort_field":"modified","sort_ascending":false,"filters":{"visibility":"hidden"}}
Placeholders={"node":{"default":"2"}}
```

### Typed column manifest

Every export carries a manifest: per column its key in the file, id, name,
datatype, chosen format, language and the import target it maps back to; the
class meta (required, translatable, selection options, relation targets,
identifier, remote id, a version signature); row count, file size and SHA-256;
export time, filters, preset, schedule, site and siteaccess.

It is written as `<file>.manifest.json` next to a background job's or the
command line's file, next to every class file inside a site archive, and on the
**One class** page as **Manifest only** or **Download with manifest (.zip)**. XML
files carry it in a `<manifest>` element after `<columns>`, JSON files as an
envelope `{"manifest": ..., "rows": [...], "summary": ...}`. Set
`csv.ini [Manifest] EmbedInJSON=disabled` or `EmbedInXML=disabled` to switch the
embedded copy off; the sidecar still exists. The importer maps every column the
manifest describes exactly; a file without a manifest imports as before.

### Import content file

The other direction. Upload a CSV, JSON or XML file (or take **Try a sample**,
which works for any class), choose the class, the parent node and the language,
map the columns, and press **Preview**. The preview says which class the rows go
into, where, in which language, and names each change; it is a dry run and
writes nothing. **Apply** performs it.

* Uploads of any size use **chunked upload** with resume; the file is streamed a
  row at a time, so memory use does not grow with the file.
* A large import is **queued as a background job** that reports its size and the
  resume point; a **Resume** control continues an interrupted one.
* The file format reference on the page lists every column and element the
  importer understands for CSV, JSON and XML.
* An imported file URL must be of this site's own scheme, host and port.

### Import content package

A content package (`.ezpkg`) is the kernel's own package format: classes, objects
and files in one archive. The **Import content package** tab:

* uploads a package (chunked) or picks one from the package repository;
* **inspects** it without writing anything: its classes, objects, a datatype
  check, and a paginated **contents browser** of every file in it;
* **compares** it with this site (what an install would create or change, down to
  the fields) or with another package;
* **installs** it as a background job with live progress and a live log on the
  Jobs page, which can be cancelled. The job says how many items were created,
  how many already existed and what was done;
* keeps an **install history**, with **Export these again** and **Open in Import**;
* builds **sample and template packages** read-only from existing content;
* is also a **format** of the Import file page and an **export format** of the
  One class and Multi class pages (**Export as package**).

A package archive is accepted only when every entry is a plain file or folder, and
a file opened from the contents browser cannot run script under the admin's
origin.

### Schedules, destinations and history

**Schedules** runs a saved preset, a site archive, an **Export as package** or an
import from a local folder or a destination, on an hourly, daily, weekly or
monthly choice or a five-field cron expression, in full or as a delta (only
changes since the last successful run). Imports always do a dry run first and
are applied only when it found no errors. A schedule that refers to something
that no longer exists is skipped with a warning.

Start the due schedules with the cronjob part or with system cron:

```bash
php runcronjobs.php xrowextract
```

The page shows ready-made crontab lines (every `CronjobEveryMinutes`, default 5).

**Destinations** are where the file is delivered:

| Type | Notes |
|---|---|
| SFTP | system OpenSSH client; key or password; the host key is trusted explicitly |
| FTP / FTPS | |
| Local or NAS folder | only below `xrowextract.ini [Destinations] LocalPathRoots[]` |
| S3-compatible storage | signature version 4 |
| WebDAV | |
| HTTP POST | optional signed webhook (see below) |

Credentials are encrypted with libsodium; the key file is generated with mode
0600 on first use at `settings/override/xrowextract-secrets.key`. Losing it makes
stored secrets unreadable; enter them again. Never commit it.

**History** lists every run with who, what, rows, size, checksum, delivery and
warnings; with filters and pages. Failed scheduled runs show as a red badge on the
Jobs tab until marked as seen. Notifications: e-mail on failure (always the
owner), on success (optional), and a webhook. When
`xrowextract.ini [Notifications] WebhookSecret` is set, the webhook carries
`X-Xrowextract-Signature: sha256=<HMAC-SHA256 of "<timestamp>.<sha256 of the body>">`.

### Jobs

Everything that runs in the background (exports, imports, package installs,
schedules) is listed on **Jobs** with tiles for total, completed, running, queued
and failed jobs, the user who started each as a linked bubble, a progress bar and a
log with a short progress timeline. A queued or running job can be cancelled. A job
whose runner dies is marked failed instead of staying "running". Without the policy
`xrowextract/all_jobs` a user sees only their own jobs. Finished jobs are kept
`csv.ini [Jobs] RetentionDays` days (default 7).

## Password hashes

For a migration to another system the password hash and hash type of user
accounts can be exported as special columns. Only users holding the policy
`xrowextract/password_hash` see the option (administrators have it), and
`csv.ini [General] AllowPasswordHashExport=disabled` switches it off for everyone.
The default in the shipped settings is `enabled`.

## Command line

Every function has a command. Run them from the installation root, either as
`php extension/xrowextract/bin/php/<name>.php` or through the console.

| Command | What it does |
|---|---|
| `ext:xrowextract:csv` | One class as a file (all options of the page, `--preview=10`, `--list-classes`, `--list-columns`, `--preset`, `--list-presets`, `--show-preset`, `--where`, `--fetch-alias`, `--alias-param`) |
| `ext:xrowextract:archive` | A site archive |
| `ext:xrowextract:import` | An import of any size, streamed; can run as a background job; `--manifest`, `--no-manifest` |
| `ext:xrowextract:package` | Content packages: inspect, `--install`, `--export`, `--compare`, `--template`, `--clean`, `--keep` |
| `ext:xrowextract:schedule` | `--list`, `--run`, `--enable`, `--disable`, `--cron`, `--crontab`, `--create` |
| `ext:xrowextract:destination` | `--list`, `--test`, `--send`, `--trust-host-key` |
| `ext:xrowextract:history` | The export history |
| `ext:xrowextract:job` | Runs, lists or cleans up background export jobs (`--clean` removes finished job folders after `RetentionDays`) |
| `ext:xrowextract:requirements` | What this server provides |

Examples that work on any installation with articles under node 2:

```bash
# 20 newest articles as CSV to a file
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 --limit=20 --output=articles.csv

# a spreadsheet preview of the same export in the terminal
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 --preview=10

# which classes exist below node 2, and which columns a class offers
php extension/xrowextract/bin/php/csv.php --list-classes --node=2
php extension/xrowextract/bin/php/csv.php --list-columns --class=ng_article

# articles changed in the last 30 days that are not hidden, as JSON
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 --format=json \
  --visibility=visible --date-field=modified --date=30 --output=recent.json

# the same selection with a condition: articles whose name contains "news"
php extension/xrowextract/bin/php/csv.php --class=ng_article --node=2 \
  --where "name contains news" --output=news.csv
```

`--where` conditions read `"<field> <op> <value>"`, several joined with ` && `
(and) or ` || ` (or), not both in one value. Operators: `contains`, `starts`,
`eq` (`=`), `ne` (`!=`), `gt`, `lt`, `gte`, `lte`, `empty`, `filled`, `in`,
`not_in`, `between` and `not_between` (written `A..B`), `like` and `not_like`
(`*` wildcard). `--user=<login>` exports with that login's read access (default
`admin`), `--changed-since=<date>` makes a delta run, and `--lenient` skips what
no longer resolves with a warning instead of failing.

Use `--help` on any command for the exact option list. Pass `--allow-root-user`
when running as root.

## Policies

Set them on a role in **User accounts > Roles and policies**, module
`xrowextract`.

| Function | Allows |
|---|---|
| `csv` | The One class and Multi class export pages |
| `import` | The Import content file and Import content package tabs, the package contents browser and compare, chunked upload |
| `jobs` | The Jobs page, its status poll and downloads |
| `all_jobs` | See, cancel and download everyone's jobs; edit shared presets |
| `schedule` | Create, change, run, enable and disable schedules |
| `destinations` | Manage delivery destinations and their credentials |
| `history` | The export history |
| `password_hash` | Export the password hash of user accounts |

A role can allow running schedules without seeing or changing any destination's
credentials, or reading the history alone.

## Requirements and the server check

PHP 8.1 or later with `mbstring`, `ctype` and `json`, and writable `var` and cache
folders are required. Single features need more: `dom` and `xmlreader` for
importing XML, `zip` for ZIP downloads and archives, `dom`, `zlib`, `proc_open()`,
`tar` and `gzip` and a writable storage folder for content packages,
`proc_open()`, `exec()` and a PHP command line binary for background jobs and
schedules, `sodium` for destination secrets, `curl` for HTTP, S3, WebDAV and FTP
destinations, and the OpenSSH client for SFTP. Each page names the features that
page cannot offer on this server and what they miss. To check from the command
line:

```bash
php extension/xrowextract/bin/php/requirements.php
php extension/xrowextract/bin/php/requirements.php --feature=package,jobs --strict --json
```

It prints PASS or FAIL per requirement, WARN for a missing optional one with the
features it takes away, and exits 1 when a required one is missing. Run it as the
web server's user: disabled functions and writable folders depend on the user and
the PHP configuration.

## Limits and things to know

* Datatypes that have an export handler are listed in `csv.ini [General]
  ExportableDatatypes[]`; each has a handler block (`HandlerFile`, `HandlerClass`)
  you can replace from your own extension. Date and time, time, keywords, tags
  and object relation handlers were added in 2.5.0.
* The multi class export reads objects with the **user's** read access.
* The tables `xrowextract_schedule`, `xrowextract_destination` and
  `xrowextract_history` are created on first use from `share/db_schema.dba`.
* The tool does not publish the sample objects of the **Package template**
  builder on the public site: they are created below the media root (or
  `export.ini [PackageTemplate] ScratchNodeID`) and removed again. Never point that
  setting at the public front page or a node a layout reaches.
* Under Velocity (the persistent PHP workers), per-request caches no longer outlive
  a request since 2.5.3, and package upload and download work without touching the
  server's file layer.

## Related

* [xrowextract specification](../../../specifications/6.0/xrowextract.md)
* [Chronicle of the extension](../../../history/extensions/xrowextract.md)
* [Release notes](../../../changelogs/extensions/xrowextract.md)
* [Command line and cronjob abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)
* [xrowmetadata](xrowmetadata.md): the datatype the export has a column for
* [eztags](eztags.md): tags export through the keyword and tags handlers
