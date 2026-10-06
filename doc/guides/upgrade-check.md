# The upgrade check: files and database before an upgrade

This guide teaches **Setup > Upgrade check** (`/setup/systemupgrade`) of the administration interface: what it
compares, what every part of the page says, how to read each kind of finding and how to deal with it, and how to
get the same answer from a shell.

It is for administrators who are about to upgrade, and for anyone who wants to know whether the files or the
database of an installation have drifted from the release. Every figure below was checked against the code
(`kernel/private/classes/views/setup/systemupgrade.php`, `kernel/private/classes/expfileconsistencyreport.php`,
`kernel/private/classes/expschemaconsistencyreport.php`) on the demonstration server (alpha.se7enx.com) on
6 October 2026, under Apache with PHP-FPM and under Velocity.

[Guides](README.md) · Related: [Upgrading](upgrading.md), [The file manifest](../features/6.0/file-consistency-check.md),
[Quality checks](../features/6.0/quality-checks.md)

## In short

- The page has two checks. **Check file consistency** compares every file listed in `share/filelist.md5` (and in the
  `share/filelist.md5` an active extension may carry of its own) with its checksum. **Check database consistency**
  compares the tables, fields and indexes of the database with the schema files of Exponential and the active
  extensions.
- **Both checks only read.** No file is written and no SQL is run. The SQL the database check shows is text for you
  to read.
- File findings are grouped: **Modified**, **Missing** and **Unreadable** are problems; **Not listed**, **Malformed
  lines** and **Out of order** are notes that do not stop an upgrade. Each group says what it means and what to do.
- Database findings are grouped by table, each with the SQL the database engine would need. Differences the engine
  writes no SQL for are **engine notes** (on SQLite, for example) and do not fail the check.
- Each check shows how long it took. Each can be downloaded: the files as CSV or text, the database as an `.sql`
  file.
- From a shell, `php bin/php/checkmanifest.php --all --extensions` gives the same file findings as the page, because
  both use the same class.

## 1. Open the page

In the administration interface: **Setup** > **Upgrade check**, or `/setup/systemupgrade` under the admin
siteaccess. The page needs the `setup/setup` policy, like every page of the Setup tab.

Make a backup of the installation directory and of the database before you upgrade. The page reminds you of that at
the top.

## 2. What the page shows before a check

| Figure | What it says |
|---|---|
| Version | The Exponential version that runs (`eZPublishSDK::version()`) |
| Files in share/filelist.md5 | How many files the manifest of the release lists. "share/filelist.md5 is missing" in red means the file check cannot run |
| Manifest last committed / written | When the manifest last changed: the date of its last git commit in a git checkout, else the date of the file |
| Active extensions with a manifest of their own | How many active extensions carry their own `share/filelist.md5` |
| Database engine | `mysql`, `postgresql`, `sqlite` or `mongo`: the schema differences depend on it |

**What the file check reads** (folded, at the bottom) lists the manifest of Exponential with its number of files, its
malformed lines if any, its dates, and every active extension that is checked: by **its own manifest** (with the
version in its header, the version of the extension and a warning when they differ or when the header's
`files_count` is not the number of lines) or by **the manifest of Exponential** (the extensions shipped inside the
release: ezoe, ezjscore, ezformtoken, expservices). Active extensions with neither are named as **not checked**.

## 3. Run the file consistency check

Press **Check file consistency**. While it runs, the page says so and the buttons cannot be pressed twice. On the
demonstration server it reads about 8,600 files in about one second with a warm file cache (up to several seconds
on a cold cache or a busy server); the time is shown on the result.

The result starts with one line:

- **File consistency check OK.** (green): every listed file matches. Notes, if any, are counted and listed below.
- **Warning: it is not safe to upgrade without checking the modifications done to the following files** (amber):
  some files differ. The line says how many are modified, missing and unreadable.
- A red line says the check could not run: see [When the check cannot run](#when-the-check-cannot-run).

Then:

- **Figures**: matching, modified, missing, unreadable (only when there are any), not listed, and manifest lines to
  tidy.
- **Manifests read**: one line per manifest with its files, how many need a look, when it was written and whether
  files missing from it could be looked for (only in a git checkout).
- **Find a file**, **Where** and **Show** (with javascript): search the paths, keep the findings of the kernel or of
  one extension, or of one state. The count under the chips says how many are shown.
- One folding group per state, each with **What it means and what to do** and a table: the file, the manifest and
  its line, and the details (the first ten characters of the listed and the current checksum; hover for all of
  them).

The page lists at most 2,000 findings. When there are more (a manifest of another release, for example), the group
says so and the download has them all.

## 4. Each finding and what to do

| Finding | What it means | What to do |
|---|---|---|
| **Modified** | The file differs from the one the release shipped. | If you changed it on purpose, move the change into an override, a design or an extension of your own: an upgrade replaces the file. Then merge it into the new version. If nobody changed it on purpose, restore it from the release. A maintainer who changed it in the source refreshes the line with `php bin/php/checkmanifest.php --fix` and commits the manifest with the change. |
| **Missing** | The file is listed but not there. | Copy it back from the release. A maintainer who removed it on purpose drops the line with `php bin/php/checkmanifest.php --fix`. |
| **Unreadable** | The file is there but could not be read: a directory where a file belongs, or permissions that keep the web server out. | Check owner and mode (`ls -l <file>`); the web server user must be able to read it. |
| **Not listed** (note) | Git tracks the file, but the manifest does not list it, so the check cannot tell whether it changed. Only looked for in a git checkout. | Nothing on an installed site. A maintainer adds it with `php bin/php/checkmanifest.php --fix` in the release that adds the file. |
| **Malformed lines** (note) | A manifest line is not "32 hex digits, two spaces, a path", names a path outside the installation (`../`, `/`), or lists a file a second time. The line is skipped. | Take the manifest from the release, or write it again with `bash bin/shell/generatefilelist.sh`. |
| **Out of order** (note) | The manifest of Exponential is kept sorted so its changes review well; this line sorts before the one above it. | `bash bin/shell/generatefilelist.sh` writes it sorted. Nothing to do on an installed site. |

On a development checkout with work in progress, modified files are expected: they are the files being worked on.
A manifest with Windows line ends (CRLF) is read like one with LF: the check no longer reports every file as missing.

### Extension manifests

An extension released on its own may carry `extension/<name>/share/filelist.md5`, refreshed in each of its
releases. It may start with a header (`name:`, `version:`, `files_count:`), which the page shows. Its findings are
listed with the extension's name in the Manifest column and under **Where**. A modified file there is fixed in the
extension's own release, not in the manifest of Exponential.

## 5. Download the report

Beside **Check file consistency**:

- **Download CSV**: one line per finding, columns `manifest`, `area`, `state`, `path`, `expected_md5`, `actual_md5`,
  `line`, `detail`, UTF-8 with a byte order mark. A cell that a spreadsheet would run as a formula starts with an
  apostrophe.
- **Download text**: a summary and the findings in words, grouped by manifest and state, in the wording of the
  command line check.

Both run the check again and send the result as a file; the page stays as it is. Beside **Check database
consistency**, **Download SQL** sends the SQL of every table under a comment saying what it changes.

## 6. Run the database consistency check

Press **Check database consistency**. It reads `share/db_schema.dba` and the `share/db_schema.dba` of every active
extension that has one, reads the schema of the database, and compares them with the schema handler of the database
engine (`dbschema.ini [SchemaSettings]`). On the demonstration server (SQLite, 11 schema files) it takes about
0.1 seconds.

The figures count **tables with SQL to review**, missing tables, tables not in the schema, changed tables and engine
notes. Then one folding card per table, with what differs (missing, extra and changed fields and indexes, with the
shipped and the current definition) and the SQL for that table:

| Kind | What it means | What to do |
|---|---|---|
| **Missing table** | Shipped with Exponential or an active extension, not in the database. The feature that uses it fails until it is there. | Run its CREATE statement after a backup. |
| **Table not in the schema** | In the database, in no shipped schema: usually a table of an extension that is not active, or of an extension of your own without a schema file. | Leave it unless you know its data is no longer needed. The DROP statement is shown only to be complete, and marked **Removes something**. |
| **Changed table** | Fields or indexes differ from the shipped definition. | Read each statement and run the ones you agree with, after a backup. Statements that remove or change a field or an index are marked **Removes something**: they can remove data of your own. |
| **Engine notes** | The engine's schema handler writes no SQL for the difference. On SQLite the check first sets aside what SQLite cannot tell apart (`text` and `longtext`, the display width of an `auto_increment` key, a default left out or written as false), so a table as the SQLite schema files create it matches; what is left and still has no SQL is an engine note. | Nothing. They are listed in one folded group and do not fail the check. |

**All statements** (folded) has the whole SQL as one block, as the page showed it before. The page never runs any of
it.

On MongoDB the check compares collections instead: missing collections grouped by feature, collections not in the
schema, and the `mongosh` command that creates the missing ones.

## 7. The same from a shell

```bash
php bin/php/checkmanifest.php --all                 # the manifest of Exponential; exit 0 when nothing is modified or missing
php bin/php/checkmanifest.php --all --extensions    # also every extension directory with its own manifest
php bin/php/checkmanifest.php --all --extensions --csv > report.csv   # the CSV of the download, without the byte order mark
```

The command needs no kernel, settings or database. It reads the manifests with the same class as the page
(`expFileConsistencyReport`), so the findings are the same when both look at the same tree. Two differences of
scope: `--extensions` reads every extension directory that has a manifest, active or not, while the page reads the
active ones; and the command prints notes as `warning:` lines and problems as `error:` lines. `--fix` rewrites the
manifest of Exponential (maintainers only; the page never writes it). `--staged` is what the git pre-commit hook
runs.

## When the check cannot run

- **"File share/filelist.md5 does not exist."** The installation has no manifest. Copy `share/filelist.md5` from the
  release this installation runs (the same version as on the page), into the directory the web server serves.
- **"... cannot be read."** The web server user may not read the manifest: check its owner and mode.
- **"... lists no files."** The manifest is empty or has no valid line: take it from the release.
- **"The database schema could not be read."** There is no schema handler for the engine in
  `dbschema.ini [SchemaSettings]`, or the database did not answer.

## Safety

- Both checks and all downloads only read. The page has no button that writes a file or changes the database.
- Every button posts the form with the form token of ezformtoken; the page does not redirect.
- Each run, and each download, is written to the audit trail as `system.upgrade.run` with the check, the outcome,
  the number of differences and, for a download, its format (see [the audit](../bc/6.0/audit.md)).
- Paths, checksums, table names and SQL are escaped on the page.

## Related

- [The file manifest and how a release writes it](../features/6.0/file-consistency-check.md)
- [Upgrading](upgrading.md)
- [Quality checks](../features/6.0/quality-checks.md): `checkmanifest --staged` in the pre-commit hook
- Changelog: [6.0.15](../changelogs/6.0/6.0.15.md)
