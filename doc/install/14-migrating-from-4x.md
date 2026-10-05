# 14. Migrating from the 4.x line

This chapter moves a site that still runs eZ Publish 3.10, 4.0 to 4.7 or an early 5.x legacy kernel on PHP 5 to
Exponential 6.0 on PHP 8. [Chapter 11](11-upgrading.md) lists the update chain; this chapter is the practical side of
walking it from that far back. It covers what stays the same and what does not, how to take stock of the old site, a
staging plan, the database runbook in the order that works with today's code (which update file, which repair script,
which preflight query, per database engine), the files, the settings, the designs and templates, porting your own
extensions from PHP 5 to PHP 8 with before and after code, passwords, search, checking the content afterwards, the
problems people actually meet, a rollback plan and a checklist. Every command, path and setting in it was checked
against the code of this repository; where the vendor's own upgrade pages for 4.0 to 4.7 say something else, the
difference is pointed out.

[Contents](README.md) · Previous: [13. Security hardening for production](13-security-hardening.md) · Next: [15. Migrating from the 5.x legacy stack](15-migrating-from-5x-legacy.md)

## 14.1 What changes, and what does not

Exponential 6.0 is the same legacy kernel, carried forward. That decides the shape of the whole migration.

**What stays the same, so you do not have to convert it:**

| Area | In Exponential 6.0 |
|---|---|
| Database schema family | The same tables (`ezcontentobject`, `ezcontentobject_attribute`, `ezcontentobject_tree`, `ezurlalias_ml` and the rest). The old update files bring an old database up to the current schema; nothing is exported and re-imported. |
| Content datatypes | All 3.x/4.x kernel datatypes are still in `kernel/classes/datatypes/` and in `content.ini [DataTypeSettings] AvailableDataTypes[]`: `ezxmltext`, `ezimage`, `ezbinaryfile`, `ezmedia`, `ezobjectrelationlist`, `ezmatrix`, `ezenum`, `ezoption`, `ezmultioption`, `ezmultioption2`, `ezprice`, `ezmultiprice`, `ezisbn`, `ezkeyword`, `ezuser` and the others. |
| Rich text | `ezxmltext` stays `ezxmltext`, edited with the online editor (ezoe). There is **no** XmlText-to-RichText conversion. |
| Pages | The `ezflow` page datatype ships as a package (`composer.json` requires `se7enxweb/ezflow`). There is **no** Page-to-LandingPage conversion. |
| Templates | The template language, its functions (including the old `section`, `let`, `set` and `default` functions) and its operators keep their names; INI block and key names keep theirs ([The product is called Exponential](../features/6.0/rebranding-to-exponential.md)). |
| Extension API | `eZPersistentObject`, `eZDataType`, `eZWorkflowEventType`, modules with `module.php`, fetch functions in `function_definition.php`, template operators registered in `autoloads/eztemplateautoload.php`, settings in `settings/*.ini.append.php`. |
| Class names | `eZ`-prefixed kernel classes keep their names. The version class is `ExponentialSDK`; `eZPublishSDK` still exists as a subclass of it (`lib/ezpublishsdk.php`). |

**What does change:** PHP 5 to PHP 8 (your own PHP code is the largest piece of work), eZ Components replaced by
Zeta Components installed by Composer, the cluster handlers that stored files in the database, YUI (gone), jQuery
(4.0.0), the online editor (TinyMCE 3 by default, TinyMCE 8 optional), several rewrite rules and a stricter security
baseline. All of it is below.

### Compared with the vendor's migration to its newer platform

The vendor documented a migration from eZ Publish to its Symfony-based successor
([Migrating from eZ Publish](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/)).
That path is a different product and most of its work does not apply here:

| Point made by the vendor's page | For Exponential 6.0 |
|---|---|
| XmlText fields are replaced by a RichText field | Not applicable: `ezxmltext` is kept as it is. |
| The Page field (ezflow) is replaced by LandingPage | Not applicable: ezflow is kept, as a Composer package. |
| The database schema changed incompatibly with legacy | Not applicable: the schema is the legacy one, brought forward by the files in `update/database/`. |
| Custom field types must be rewritten for the new stack, or replaced by a placeholder | Not needed: custom `eZDataType` classes keep working after the PHP 8 fixes in [14.9](#149-porting-your-own-extensions-from-php-5-to-php-8). |
| The web front end and admin modules must be rewritten on Symfony | Not needed: templates, designs and modules carry over ([14.7](#147-settings-and-siteaccesses), [14.8](#148-designs-templates-and-javascript)). |
| Remove internal drafts before migrating (`InternalDraftsCleanUpLimit`, `InternalDraftsDuration`, the internal drafts clean-up cron job) | Worth doing for a smaller database, not required: see [14.4](#144-prepare-the-old-installation). |

## 14.2 Plan the migration on a staging copy

Never migrate the live installation in place. Build the new site next to the old one, rehearse on a copy of the
production database until the run is boring, then repeat it once against a fresh copy taken while the old site is
in maintenance.

1. **Freeze the old code.** Note the exact old version (14.3), the list of active extensions and every file you
   changed in the kernel. Kernel changes are not carried over; they have to become extensions or overrides.
2. **Staging server.** PHP 8.0 to 8.5 (8.1 or later if the site will run on Exponential Velocity), the database engine
   you will use in production, and the requirements of [chapter 2](02-requirements.md).
3. **New code.** Get Exponential as in [chapter 3](03-getting-the-code.md). Do **not** run an installer against the
   old database: the setup wizard, the kickstarter and `exp:install` create a new database
   ([11.1](11-upgrading.md#111-before-you-start)).
4. **Rehearse.** Restore the production dump on staging, run the database runbook
   ([14.5](#145-the-database-step-by-step)), move the files (14.6), the settings (14.7), the designs (14.8) and the
   extensions (14.9), then verify (14.12). Write down every command and how long it took.
5. **Fix and repeat** until the rehearsal runs from top to bottom with no manual repair.
6. **Cut over.** Old site in maintenance, fresh dump, the same runbook, verification, DNS or proxy switch. Keep the old
   site and its database untouched for the rollback (14.14).

Three tracks can be worked in parallel by different people, because they only meet at the end: the **database**
(14.5), the **files and settings** (14.6, 14.7), and the **code** (14.8, 14.9).

## 14.3 Take stock of the old site

Run these on a copy of the old database; they only read.

**Which version.** The rows `ezpublish-version` and `ezpublish-release` of `ezsite_data` tell where the chain starts
(the query is in [11.2](11-upgrading.md#112-which-version-do-you-run)). The update files 5.1 to 5.4 write
`5.1.0alpha1` to `5.4.0alpha1` into that row; that is how they were shipped, not a sign of a failed step.

**Which datatypes are in use.** Anything that is not a kernel datatype comes from an extension you must port or
install:

```sql
SELECT data_type_string, COUNT(*) FROM ezcontentclass_attribute
 WHERE version = 0 GROUP BY data_type_string ORDER BY 2 DESC;
```

**Which password hash types are stored** (see [14.10](#1410-users-passwords-and-sign-in)):

```sql
SELECT password_hash_type, COUNT(*) FROM ezuser GROUP BY password_hash_type;
```

**Charset and table type (MySQL).** The new code connects with `[DatabaseSettings] Charset=utf-8`, which the MySQL
driver maps to MySQL's `utf8` (`lib/ezdb/classes/ezmysqlcharset.php`). A database that is still `latin1` or mixed must
be converted (14.5.2).

```sql
SHOW CREATE DATABASE your_database;
SELECT table_name, engine, table_collation FROM information_schema.tables
 WHERE table_schema = 'your_database' ORDER BY engine, table_collation;
```

or, with the new code already pointed at the database, `php bin/php/ezconvertmysqltabletype.php --list`.

**Cluster mode.** Look in the old `settings/override/file.ini.append.php` for `[ClusteringSettings] FileHandler`.
`eZFSFileHandler` (plain files) and `eZDFSFileHandler` (files on NFS, metadata in a database) exist in Exponential 6.0.
`eZDBFileHandler` (files stored inside the database, table `ezdbfile`) and `eZFS2FileHandler` do **not**: they were
removed in January 2013 (EZP-20288, "eZDB & eZFS2 Cluster removed in favor of eZDFS"). See 14.4.

**Search engine.** `[SearchSettings] SearchEngine` in the old `site.ini` overrides: `eZSearchEngine` is the built-in
one; anything else is an extension such as eZ Find (14.11).

**Size.** These numbers drive how long each step takes (14.15):

```sql
SELECT COUNT(*) FROM ezcontentobject;
SELECT COUNT(*) FROM ezcontentobject_attribute;
SELECT COUNT(*) FROM ezcontentobject_tree;
SELECT COUNT(*) FROM ezurlalias_ml;
SELECT COUNT(*) FROM ezimagefile;
```

and the size of `var/` (or of the DFS mount) on disk.

## 14.4 Prepare the old installation

Do these on the old site while it still runs its old code, before you take the dump you migrate.

- **Check consistency with the old admin.** The vendor's
  [Backup and consistency checks](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Backup-and-consistency-checks.html)
  describe **Setup > Upgrade check** with **Check file consistency** and **Check database consistency**. The same two
  buttons exist in Exponential 6.0 (`design/admin/templates/setup/systemupgrade.tpl`), so run them again after the
  migration and compare.
- **Leave the database cluster.** If the old site uses `eZDBFileHandler`, move the files out of the database with the
  old installation's own tools while it still runs, and switch it to `eZFSFileHandler` (or to DFS). Exponential 6.0
  has no handler that can read `ezdbfile`, so it cannot do this step for you.
- **Optional clean-ups that make the dump smaller:** remove internal drafts (set `content.ini [ContentSettings]
  InternalDraftsCleanUpLimit` and `InternalDraftsDuration` low and run the `internal_drafts_cleanup.php` cron job), old
  archived versions, expired sessions. The same tools exist in Exponential afterwards:
  `php runcronjobs.php --script=internal_drafts_cleanup.php`, `php bin/php/cleanupversions.php`,
  `php update/common/scripts/cleanup.php session`.
- **Back up** the database and the whole old directory, including `var/`. The commands are in
  [11.1](11-upgrading.md#111-before-you-start). Restore the backup once on a scratch machine.
- **Record what works**: a list of URLs per siteaccess and their HTTP status, a few searches and their result counts,
  the number of users who signed in last month. That list is your verification script in 14.12.

## 14.5 The database, step by step

### 14.5.1 Which starting points work

| You run | Start |
|---|---|
| 3.0 to 3.9 | Not with Exponential 6.0. The update scripts for those versions (`updatemultioption.php`, `correctxmltext.php`, `updateclasstranslations.php` and the 3.x `updatexmltext.php` and friends listed in the vendor's [system upgrade scripts](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/The-system-upgrade-scripts.html)) are not in this repository. Bring the site to 3.10 with the old 3.10 release first, as the vendor required ("direct upgrading from version 3.9 (and earlier) to 4.0 is impossible"). |
| 3.10.x | The chain from `4.0/dbupdate-3.10.0-to-4.0.0.sql`. |
| 4.0 to 4.7 | The chain from the file after your version. |
| 5.0 to 5.4 | The chain from the file after your version; then [11.5](11-upgrading.md#115-from-54-or-590-to-600). |

### 14.5.2 Before the first file: charset and table type

On **MySQL**, convert MyISAM tables to InnoDB and a non-UTF-8 database to UTF-8 first, as the vendor did in its 3.10
to 4.0 step. Both scripts are in Exponential 6.0 and read the connection from the site's settings:

```bash
php bin/php/ezconvertmysqltabletype.php --list
php bin/php/ezconvertmysqltabletype.php --newtype=InnoDB
php bin/php/ezconvertdbcharset.php --help
php bin/php/ezconvertdbcharset.php
```

- `ezconvertmysqltabletype.php` takes `--host`, `--user`, `--password`, `--database`, `--list`, `--newtype` and
  `--usecopy`.
- `ezconvertdbcharset.php` runs on MySQL, PostgreSQL and Oracle (it refuses other drivers). Its options:
  `--collation` (default `utf8_general_ci`), `--iconv-character-set` (for a charset that iconv and the database name
  differently, the script's own example is `windows-1252` and `iso-8859-1`), `--log-filename`, and
  `--extra-xml-attributes`, `--extra-xml-data`, `--extra-serialized-data` for your own tables or datatypes that store
  XML or serialized PHP. Serialized PHP stores byte lengths, so a datatype of yours that keeps serialized data must be
  named there or it will not unserialize after the conversion.
- Keep the converted tables on MySQL's `utf8` (3-byte), which is what the connection uses. MySQL 8 creates new tables
  with `utf8mb4` unless the database default says otherwise, and the update files create tables without naming a
  charset; set the database default to the same charset as the converted tables before you run them, so that old and
  new tables can be joined: `ALTER DATABASE your_database CHARACTER SET utf8 COLLATE utf8_general_ci;`

On **PostgreSQL** the database encoding is fixed at creation; a database that is not UTF-8 is converted with
`ezconvertdbcharset.php` as well, or dumped and restored into a new UTF-8 database.

### 14.5.3 Preflight queries

Three statements of the old chain fail on data that older releases allowed. Run these before the chain; each must
return no rows.

```sql
-- 4.1 adds UNIQUE INDEX ezcontentobject_remote_id (from 4.0.1)
SELECT remote_id, COUNT(*) FROM ezcontentobject
 WHERE remote_id IS NOT NULL GROUP BY remote_id HAVING COUNT(*) > 1;

-- 4.1 adds UNIQUE INDEX ezgeneral_digest_user_settings_address before it removes stale rows
SELECT address, COUNT(*) FROM ezgeneral_digest_user_settings
 GROUP BY address HAVING COUNT(*) > 1;

-- 6.0.15 and later refuse to sign in an account without a settings row
SELECT u.contentobject_id, u.login FROM ezuser u
 LEFT JOIN ezuser_setting s ON s.user_id = u.contentobject_id
 WHERE s.user_id IS NULL;
```

What to do when they return rows:

- **Duplicate object remote ids.** Run the 4.1 file without the line
  `ALTER TABLE ezcontentobject ADD UNIQUE INDEX ezcontentobject_remote_id(remote_id);`, finish the chain, run
  `php update/common/scripts/4.1/fixobjectremoteid.php --mode=a` (`--mode=d` asks per duplicate), then add the index
  with exactly that statement. The same family has `fixclassremoteid.php` and `fixnoderemoteid.php`.
- **Duplicate digest addresses.** Keep the oldest row of each address before the 4.1 file:

  ```sql
  -- MySQL
  DELETE d1 FROM ezgeneral_digest_user_settings d1
    JOIN ezgeneral_digest_user_settings d2 ON d1.address = d2.address AND d1.id > d2.id;
  -- PostgreSQL
  DELETE FROM ezgeneral_digest_user_settings a
   USING ezgeneral_digest_user_settings b WHERE a.address = b.address AND a.id > b.id;
  ```

  The 5.0 file later replaces the address by a `user_id` column with a unique index.
- **Users without `ezuser_setting`.** See [14.10](#1410-users-passwords-and-sign-in).

### 14.5.4 Two phases: schema first, data repair second

The PHP repair scripts in `update/common/scripts/` run on the Exponential 6.0 kernel, and that kernel's persistent
object definitions describe the **current** schema. For example `eZNodeAssignment` reads the `priority` and
`is_hidden` columns that only the 5.2 file adds, so `4.3/updatenodeassignment.php` cannot run against a 4.3 database
with today's code. Therefore:

1. **Phase A** applies every SQL file from your version up to 6.0.15, in order, with nothing in between, except the
   one script that must see the old schema (`updateimagesystem.php`, below).
2. **Phase B** runs the repair scripts of every version you passed, in version order, against the finished schema.

This differs from the vendor's per-version pages, which ran each version's scripts right after its SQL file with the
matching old code. With today's single code base the two-phase order is the one that works.

`updateimagesystem.php` converts `ezimage` attributes of sites created before 3.3 and needs the `ezimage` table, which
the 4.1 file drops. Check whether you need it:

```sql
SELECT COUNT(*) FROM ezimage;
```

If it returns 0, skip it. If not, run `php update/common/scripts/4.1/updateimagesystem.php` before the 4.1 file, on
the rehearsal copy first: it runs the 6.0 kernel against a 4.0 schema.

### 14.5.5 Phase A: the SQL files

Apply each file once, whole, in this order, starting after your version. The file names are the same under
`update/database/mysql/` and `update/database/postgresql/` up to 5.4.

| # | From | File | What it changes |
|---|---|---|---|
| 1 | 3.10 | `4.0/dbupdate-3.10.0-to-4.0.0.sql` | removes orphaned `ezuser_setting` and class-group rows; table `ezurlwildcard` (from 3.10.1) |
| 2 | 4.0 | `4.1/dbupdate-4.0.0-to-4.1.0.sql` | object states (`ezcobj_state*` tables), `ezurlalias_ml.alias_redirects` (from 3.10.1), unique object remote id (from 4.0.1), `ezurlalias_ml_incr` (from 4.0.2), `ezsession.user_hash`, `ezpending_actions.created`; **drops `ezimage` and `ezimagevariation`** |
| 3 | 4.1 | `4.2/dbupdate-4.1.0-to-4.2.0.sql` | `ezworkflow_event` text columns (from 4.1.1), `ezsession.user_hash` default (from 4.1.2), keyword and info-collection indexes (from 4.1.4), policy and workflow indexes |
| 4 | 4.2 | `4.3/dbupdate-4.2.0-to-4.3.0.sql` | RSS enclosure, class description and category columns, table `ezscheduled_script` |
| 5 | 4.3 | `4.4/dbupdate-4.3.0-to-4.4.0.sql` | drops `ezcontentobject.is_published`; `ezsection.identifier`; `ezpolicy.original_id`; `ezuser` attributes made untranslatable |
| 6 | 4.4 | `4.5/dbupdate-4.4.0-to-4.5.0.sql` | asynchronous publishing table `ezpublishingqueueprocesses`; REST API tables `ezprest_*` |
| 7 | 4.5 | `4.6/dbupdate-4.5.0-to-4.6.0.sql` | `ezorder_nr_incr`; multiplexer workflow class ids moved to `data_text5` |
| 8 | 4.6 | `4.7/dbupdate-4.6.0-to-4.7.0.sql` | `ezpending_actions.id`; stale account keys removed; float columns to `double`; trigger renamed |
| 9 | 4.7 | `5.0/dbupdate-4.7.0-to-5.0.0.sql` | `ezcobj_state_group_language.real_language_id`; digest settings keyed by `user_id`; index on `ezuser.login` |
| 10 | 5.0 | `5.1/dbupdate-5.0.0-to-5.1.0.sql` | indexes reorganised; **deletes `ezcontentobject_link` rows with `op_code <> 0` or `relation_type = 0`** and drops `op_code` |
| 11 | 5.1 | `5.2/dbupdate-5.1.0-to-5.2.0.sql` | language masks to `BIGINT`; duplicate `ezurl_object_link` rows removed (MySQL); `ezcontentobject.language_mask` corrected; `eznode_assignment.priority`, `is_hidden` |
| 12 | 5.2 | `5.3/dbupdate-5.2.0-to-5.3.0.sql` | index on `contentclassattribute_id` |
| 13 | 5.3 | `5.4/dbupdate-5.3.0-to-5.4.0.sql` | drops `ezsearch_return_count`; empty dates stored as `NULL`; orphaned `ezuser_setting` rows removed |
| 14 | 5.4 | `6.0/dbupdate-5.4.0-6.0.0.sql` (MySQL), `6.0/dbupdate-5.4-to-6.0.sql` (PostgreSQL) | records 6.0.0; widens `ezuser.password_hash` from 50 to 255 for the bcrypt hashes of 6.0, adds `ezcontentobject_trash.trashed` and, on PostgreSQL, renames the sequences to `<table>_<column>_seq` ([11.3](11-upgrading.md#the-612-72-and-73-directories)) |
| 15 | 6.0.0 | `6.0/dbupdate-6.0.0-6.0.15.sql` | the tables and columns of the 6.0 line ([11.6](11-upgrading.md#116-from-any-60x-to-today), step 2) |

The 5.4 file says of its date update: "Skip this if updating from 5.3.3 or higher as this should ideally not be
applied twice." Coming from 4.x you are below 5.3.3, so apply it.

**MySQL.** Every old file from 4.0 to 5.4, and `6.0/dbupdate-5.4.0-6.0.0.sql`, starts with
`SET default_storage_engine=InnoDB;`, which every MySQL from 5.5.3 and MariaDB accept, so apply the files whole. Copies
from before October 2026 started with `SET storage_engine=...;`, which MySQL 5.7.5 and later and MariaDB 12.0 and later
reject; take the current files:

```bash
mysql -u USER -p DATABASE < update/database/mysql/4.1/dbupdate-4.0.0-to-4.1.0.sql
```

The `mysql` client stops at the first failing statement in this mode. If that happens, note the statement, restore
the backup, fix the cause (usually a preflight case) and start again from the backup.

**PostgreSQL.** Stop at the first error, too:

```bash
psql -v ON_ERROR_STOP=1 -U USER -d DATABASE -f update/database/postgresql/4.1/dbupdate-4.0.0-to-4.1.0.sql
```

The `pgcrypto` extension (function `digest`) must exist in the database for 6.0 ([11.3](11-upgrading.md#113-the-update-files)).

**SQLite and MongoDB.** There is no old chain for them: SQLite support begins with 6.0.1, MongoDB with 6.0.14. Migrate
on MySQL or PostgreSQL; moving a finished site to another engine afterwards is a separate project
([chapter 9](09-databases.md)).

**Oracle.** The `ezoracle` extension carries the kernel chain for Oracle up to 5.3 in
`extension/ezoracle/update/database/` ([11.3](11-upgrading.md#113-the-update-files)).

**Patch-release markers.** Some files contain blocks marked `-- START: from 4.0.1` ... `-- END: from 4.0.1` (also
`from 3.10.1`, `from 4.0.2`, `from 4.1.0`, `from 4.1.1`, `from 4.1.2`, `from 4.1.4`). They hold the statements that a
later patch release of the older line already shipped. If your database came through that patch release's own update
(for example you ran 4.0.2 and applied its update), those statements were applied before and fail with "duplicate
column" or "duplicate key". Make a copy of the file without the blocks your site already has and apply the copy. This
is the "select the appropriate SQL for 4.1.1, 4.1.2, 4.1.3 or 4.1.4" note of the vendor's
[direct upgrade from 4.1 to 4.7](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Direct-upgrading/Direct-upgrading-to-4.7-from-4.1-4.2-4.3-4.4-and-4.5/Direct-upgrading-from-4.1-to-4.7.html).
The vendor's 4.0 page also names a file `dbupdate-4.0.0-to-4.0.1.sql`; in this repository its statements are the
`from 4.0.1` block of the 4.1 file.

**`unstable/` directories** hold the files of alphas, betas and release candidates. A site that ran a final release
never applies them. A site that ran a pre-release must apply the remaining `unstable/` files of that line up to the
final release instead of the stable file.

**Cluster files (MySQL only).** The DFS metadata database is a separate database. Apply to it, at the same point in the
chain:

| Step | File | For DFS |
|---|---|---|
| 4.3 | `4.3/dbupdate-cluster-4.2.0-to-4.3.0.sql` | no: it updates `ezdbfile`, the removed database cluster |
| 4.7 | `4.7/dbupdate-cluster-4.6.0-to-4.7.0.sql` | only the `ezdfsfile` statement; the `ezdbfile` one is for the removed handler |
| 5.2 | `5.2/dbupdate-cluster-5.1.0-to-5.2.0.sql` | yes: creates `ezdfsfile_cache` |
| 5.4 | `5.4/dbupdate-cluster-5.3.0-to-5.4.0.sql` | yes: renames the scope `images` to `image` in `ezdfsfile` |

### 14.5.6 Phase B: the repair scripts

Run from the installation root, after phase A, the scripts of every version you passed, in this order. Start each
with `--help`. Scripts that offer a dry run (`--dry-run`, or `-n` where it means "do not change") get one first.

| Version | Command | When |
|---|---|---|
| 4.0 | `php update/common/scripts/4.0/updatebinaryfile.php` | adds the missing file extension to files of `ezbinaryfile` |
| 4.0 | `php update/common/scripts/4.0/updatevatcountries.php` | only if the site skipped the 3.9.3 step ("Fixes bug with aplying VAT rules") |
| 4.0 | `php update/common/scripts/4.0/updatetipafriendpolicy.php -s <admin siteaccess> -l <admin login> -p <password>` | optional; adds a tip-a-friend role for all groups except anonymous |
| 4.1 | `php update/common/scripts/4.1/addlockstategroup.php` | **once**; creates the `ez_lock` state group |
| 4.1 | `php update/common/scripts/4.1/fixclassremoteid.php`, `fixnoderemoteid.php`, `fixobjectremoteid.php` (`--mode=a` or `--mode=d`) | duplicate remote ids |
| 4.1 | `php update/common/scripts/4.1/fixezurlobjectlinks.php --fix` | links in XML blocks not linked for all versions or translations |
| 4.1 | `php update/common/scripts/4.1/initurlaliasmlid.php` | initialises `ezurlalias_ml_incr` |
| 4.1 | `php update/common/scripts/4.1/correctxmlalign.php` | `custom:align` to `align`, missing `align` on embeds (`--skip-embed-align`, `--skip-custom-align`) |
| 4.2 | `php update/common/scripts/4.2/fixorphanimages.php` | image alias handler bug 15155 (`-n` skips the 10 second safety wait) |
| 4.3 | `php update/common/scripts/4.3/updatenodeassignment.php` | unused node assignments (bug 15478) |
| 4.4 / 4.5 | `php update/common/scripts/4.5/updatesectionidentifier.php` | sections without an identifier; the 4.4 and 4.5 copies are identical here, run one |
| 4.6 | `php update/common/scripts/4.6/removetrashedimages.php` | optional; image files of trashed content |
| 4.6 | `php update/common/scripts/4.6/updateordernumber.php` | sites with a web shop: aligns `ezorder_nr_incr` with existing order numbers |
| 5.0 | `php update/common/scripts/5.0/deduplicatecontentstategrouplanguage.php` | **required**: removes duplicates and moves the primary key to `real_language_id`, which the 5.0 file leaves to it |
| 5.0 | `php update/common/scripts/5.0/restorexmlrelations.php` | only sites with more than one language |
| 5.0 | `php update/common/scripts/5.0/disablesuspicioususers.php`, then with `--disable` | accounts whose login contains `<` or `>` |
| 5.1 | `php update/common/scripts/5.1/fiximagesoutsidevardir.php --dry-run` | image references outside `VarDir` |
| 5.2 | `php update/common/scripts/5.2/cleanupdfscache.php` | DFS only: cache rows in `ezdfsfile` |
| 5.3 | `php update/common/scripts/5.3/recreateimagesreferences.php --dry-run` | missing `ezimagefile` rows |
| 5.3 | `php update/common/scripts/5.3/updatenodeassignmentparentremoteids.php` | `eznode_assignment.parent_remote_id` |
| 5.4 | `cleanuntranslatablerelations.php --dry-run`, `cleanupfieldvaluerelations.php`, `fixremovedezurlobjectlinks.php` (`--fix`), `fixtrashedimagereferences.php --dry-run` | stale relations, links and image references |
| 6.0 | `php update/common/scripts/6.0/createaudittables.php --dry-run`, then without | the audit index tables |
| any | `php update/common/scripts/updatenbxmlcontents.php --dry-run` | non-breaking space encoding in XML text |

The vendor's direct-upgrade page says to skip the 4.4 section identifier script "(bug in update script)" and use the
4.5 one; in this repository both files are the same.

## 14.6 Files: storage, images, binary files and the cluster

- **Copy `var/`** (or at least `var/<VarDir>/storage/`) from the old site. Keep `[FileSettings] VarDir` in
  `settings/override/site.ini.append.php` exactly as it was: the paths of every stored image and file in
  `ezimagefile` and in the `ezimage` XML start with it. A site whose `VarDir` changed during its life has images
  outside it; `5.1/fiximagesoutsidevardir.php` fixes the references.
- **Do not copy caches.** Leave out `var/cache/`, `var/<VarDir>/cache/` and `var/autoload/`; the new code rebuilds
  them. The vendor's pages end every step with `php bin/php/ezcache.php --clear-all --purge` and a manual check of
  `var/cache/` and `var/<siteaccess>/cache/`; the same command works here (`--purge` removes the files physically).
- **Image aliases** are made on demand from the original image and the aliases of `image.ini [AliasSettings]
  AliasList[]` (`reference`, `small`, `tiny`, `medium`, `large`, `rss` by default). Carry over your own alias blocks
  from the old `image.ini.append.php` and clear the alias cache (`php bin/php/ezcache.php --clear-id=imagealias`);
  every alias is rebuilt the first time it is asked for. `image.ini [ImageConverterSettings] ImageConverters[]` lists
  `GD` and `ImageMagick`; `[ImageMagick] Executable=convert`, `ExecutablePath` empty.
- **File permissions.** The vendor's pages used `chmod -R a+rwx design extension settings var`. Do not: the user that
  runs PHP needs to write `var/` (and `settings/override/` only if the admin edits settings); see
  [chapter 8](08-serving-the-site.md) and [chapter 13](13-security-hardening.md).

### The cluster

| Old handler | In Exponential 6.0 |
|---|---|
| `eZFSFileHandler` | the same, the default (`file.ini [ClusteringSettings] FileHandler=eZFSFileHandler`) |
| `eZDFSFileHandler` with the `mysql` backend | `eZDFSFileHandler` with `eZDFSFileHandlerMySQLiBackend` (`file.ini [eZDFSClusteringSettings] DBBackend`, the default); the plain `mysql` backend was removed |
| `eZDFSFileHandler` on PostgreSQL | the PostgreSQL DFS backend moved to the extension `ezpostgresqlcluster` in 2012 |
| `eZDBFileHandler` (`ezdbfile`), `eZFS2FileHandler` | removed (EZP-20288): leave them on the old site, see 14.4 |

`index_cluster.php` serves cluster files. It reads `config.php` and `config.cluster.php` and expects the constants
`CLUSTER_STORAGE_BACKEND` (for example `dfsmysqli`), `CLUSTER_STORAGE_HOST`, `CLUSTER_STORAGE_PORT`,
`CLUSTER_STORAGE_USER`, `CLUSTER_STORAGE_PASS` and `CLUSTER_STORAGE_DB` (`config.php-RECOMMENDED` shows them). The
4.5 and 4.6 pages of the vendor set `define( 'STORAGE_BACKEND', 'dfsmysqli' )` inside `index_cluster.php`; that name
is not read any more, and the old `index_cluster_*` and `index_image_*` files are to be removed from the document root
(4.7 notes). The tools for a DFS site: `php bin/php/clusterize.php` (`-u` moves files back to the plain file system),
`php bin/php/dfscleanup.php` (consistency between `ezdfsfile` and the mount), `php bin/php/clusterpurge.php`.

## 14.7 Settings and siteaccesses

### What to carry over

Copy, from the old installation into the new one, **only**:

- `settings/override/` (global overrides),
- `settings/siteaccess/<name>/` for every siteaccess,
- `config.php` or `config.cluster.php` if the old site had them (compare with `config.php-RECOMMENDED`),

then go through them with the list below. Never copy the old `settings/*.ini` files over the new ones: the new
defaults are part of the code. The vendor's pages said the same in their step 1 (copy `settings/siteaccess`,
`settings/override` and the site's own `design/<site>` directories into the unpacked new release).

### Settings to change

| Old setting | What to do |
|---|---|
| `[DatabaseSettings] DatabaseImplementation=ezmysql` | Works: `site.ini [DatabaseSettings] ImplementationAlias[ezmysql]=eZMySQLiDB`. Write `ezmysqli` for clarity. The vendor's 4.5 and 4.6 pages asked for the same change. |
| `[DatabaseSettings] Charset`, `i18n.ini [CharacterSettings] Charset` | `utf-8` (the new defaults); remove other values once the database is converted. |
| `[ExtensionSettings] ActiveExtensions[]` | Remove `ezdhtml` (replaced by `ezoe` since 4.1) and any extension you do not install. Keep the order: it decides which extension's templates and settings win ([Extension loading order](../features/6.0/extension-loading-order.md)). `ezjscore` is required by the admin and by ezwebin. |
| `[UserSettings] HashType`, `SiteName`, `UpdateHash` | See [14.10](#1410-users-passwords-and-sign-in). |
| `[DesignSettings]` of the admin siteaccess | The old `admin2` design became the shipped `admin` design in 2012; today's admin designs are `admin`, `admin3`, `admin4` and `admin4l`, plus the YUI-free classic grey interface as the siteaccess `classic` ([YUI removed](../bc/6.0/yui-removal.md)). Take the `[DesignSettings]` of a freshly installed admin siteaccess instead of the 4.x list. |
| `override.ini.append.php` of the admin siteaccess | Remove the user-tab rules the vendor's 4.6 page names (`[window_controls]`, `[windows]` matching `ezusernavigationpart`); their templates are gone. |
| `override.ini` blocks | Since 12 July 2026 blocks are sorted by `Priority`, lowest first, and a block without one comes last. Give blocks that must win a `Priority` ([11.6](11-upgrading.md#116-from-any-60x-to-today), step 3). |
| `ezjscore.ini` YUI keys (`ExternalScripts[yui*]`, `LocalScripts[yui*]`, `[YUI3]`, `PreferredLibrary=yui3`) | Delete; they mean nothing now. |
| `site.ini [SiteAccessSettings] DetectMobileDevice`, `MobileSiteAccessURL`, `MobileSiteAccessList[]` (vendor's 4.7 page) | Only if you used the mobile siteaccess; the keys are unchanged. |
| `template.ini [PHP] PHPOperatorList[]` | Every PHP function mapped there must exist in PHP 8 (14.9). |
| Files named `*.ini.append` or `*.ini.php` | Still read, but deprecated since 4.4. Rename to `*.ini.append.php` with the `<?php /* #?ini charset="utf-8"?` first line, so a web server that is misconfigured cannot hand them out as text. |
| Extension directories | New and optional: `[ExtensionSettings] AdditionalExtensionDirectories[]` lets your own extensions live in a second root ([Additional extension directories](../bc/6.0/AdditionalExtensionDirectories.md)). |
| `MinPasswordLength`, `GeneratePasswordLength` | New defaults 10 and 16 ([password length](../bc/5.90/password_length.md)). |

Then rebuild the INI cache and check each siteaccess:

```bash
php bin/php/ezcache.php --clear-tag=ini
php bin/php/ezpgenerateautoloads.php -e
```

### Rewrite rules

The vendor's 4.x pages added and removed rewrite rules release by release (`index_ajax.php` for ezjscore calls,
`var/([^/]+/)?cache/(texttoimage|public)`, the extension design directories). `index_ajax.php` no longer exists. Do not
carry the old rules over: take the rules of [chapter 8](08-serving-the-site.md) for your web server, which list exactly
what may be served, and add only rules your own extensions need.

### Siteaccesses

Siteaccess matching (`[SiteAccessSettings] MatchOrder`, `HostMatchMapItems[]`, `URIMatch...`) is unchanged; copy it.
Check each siteaccess after the move:

```bash
php bin/php/eztemplatecheck.php -s<siteaccess>
```

and open its front page. The editor siteaccess and the newer admin designs are additions you can make afterwards
([10. After installing](10-after-installing.md)).

## 14.8 Designs, templates and JavaScript

**Your designs.** Copy your own `design/<name>/` directories, or better, move them into a design extension
(`extension/<yours>/design/<name>/` with `design.ini.append.php` `[ExtensionSettings] DesignExtensions[]`), so that
updates of Exponential never touch them. Overriding templates live in `design/<name>/override/templates/`, as the
vendor's 4.0 page required.

**Do not edit packaged extensions.** ezwebin, ezflow, ezoe, ezjscore and the other extensions of `composer.json` are
replaced on every update. Changes you made inside them on the old site must move into your own design extension as
overrides.

**Check the syntax** of every template of a siteaccess, or of a directory:

```bash
php bin/php/eztemplatecheck.php -s<siteaccess>
php bin/php/eztemplatecheck.php extension/<yours>/design/
```

**What behaves differently in templates:**

| Area | Change | What to do |
|---|---|---|
| `wash` and the HTML escaping operators | Escape single quotes on every PHP version (`ENT_QUOTES \| ENT_SUBSTITUTE \| ENT_HTML401`) | Nothing, unless a test compares exact output; clear the template cache once ([PHP 8.0 support](../bc/6.0/php-8.0-support.md)). |
| `template.ini [PHP] PHPOperatorList[]` | The mapped PHP function is called directly | Remove mappings to functions PHP 8 removed (14.9). |
| YUI | `ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io` load nothing; `YUI(...)`, `YAHOO.*` are undefined | Move the code to jQuery or Exponential UI (`exp::*`); the table of replacements is in [YUI removed](../bc/6.0/yui-removal.md). |
| jQuery | `ezjsc::jquery` is jQuery 4.0.0 with jQuery Migrate 4.0.2 (quiet build), `ezjsc::jqueryUI` is jQuery UI 1.14.2 (`extension/ezjscore/settings/ezjscore.ini`) | Replace `.bind()`, `.unbind()`, event shorthands, `jQuery.trim()`, `.size()` ([jQuery 4](../features/6.0/jquery4-and-yui-removal.md)). |
| Online editor | ezoe runs TinyMCE 3.5.12 by default, TinyMCE 8 is opt-in; the stored XML is the same | Custom ezoe plugins written for TinyMCE 3 keep working on the default engine ([TinyMCE 3 and 8](../bc/6.0/ezoe-tinymce8.md)). |
| ezdhtml | Removed since 4.1 | Use ezoe; the XML of `ezxmltext` needs no conversion. |
| Admin user tabs | `window_controls_user.tpl` and `windows_user.tpl` gone since 4.6 | Remove the override rules (14.7). |

**eZ Webin and eZ Flow.** In 4.x they were upgraded with `bin/php/ezwebinupgrade.php` and `bin/php/ezflowupgrade.php`
against the vendor's package server. Here the extension code arrives with Composer (`se7enxweb/ezwebin`,
`se7enxweb/ezflow`); the two scripts still exist for the installed site packages ([11.6](11-upgrading.md#116-from-any-60x-to-today),
step 4). The vendor's direct-upgrade page adds an index for eZ Flow sites coming from 4.1 or 4.2:
`ALTER TABLE ezm_pool ADD INDEX ezm_pool__block_id__ts_hidden (block_id, ts_hidden);` the current ezflow schema
(`extension/ezflow/sql/mysql/mysql.sql`) has that index, so add it if your database lacks it.

## 14.9 Porting your own extensions from PHP 5 to PHP 8

This is the largest part of most migrations. Work through it on staging with debug output on.

### The checklist

1. **Inventory** your extensions: everything under `extension/` that is not in `composer.json`.
2. **Lint** every PHP file with the PHP you will run: `find extension/<yours> -name '*.php' -print0 | xargs -0 -n1 php -l`.
   A parse error here stops the whole site, not only the extension.
3. **Regenerate the autoload array** (`php bin/php/ezpgenerateautoloads.php -e`) and **load every class**:
   `php bin/php/console exp:checkclasses` reports each class PHP refuses to load (signature conflicts, missing
   parents, removed functions at class level). `--kernel` includes the kernel.
4. **Check characters around the PHP tags**: `php bin/php/ezcheckphptag.php extension/<yours>`.
5. **Fix the PHP 8 breaks** below, then the deprecations shown in the debug output.
6. **Templates and settings** as in 14.7 and 14.8.
7. **Give the extension a version**: `ezinfo.php` and `extension.xml`, shown on **Setup > Extensions**.

### Autoloading and constants

4.0 replaced the include lists with one class per file and generated autoload arrays. In Exponential 6.0 the kernel
array is `autoload/ezp_kernel.php`; the extension, kernel-override and test arrays are generated into
`var/autoload/ezp_extension.php`, `ezp_override.php` and `ezp_tests.php` by `ezpgenerateautoloads.php` (`-e`
extensions, `-k` kernel, `-o` kernel overrides, `-s` tests, `--exclude=<dir>`, `-n` dry run). A kernel override
array is used only when `config.php` defines `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` as true. Registration uses
`spl_autoload_register()`; a `function __autoload()` in your code is a fatal error since PHP 8.0.

The global constants of 3.x were replaced by class constants in 4.0 (the vendor's
[4.0 notes](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.0/Important-notes.html) use
`EZ_CONTENT_OBJECT_STATUS_DRAFT` as the example) and the old names are not defined:

```php
// before (3.x)
if ( $object->attribute( 'status' ) == EZ_CONTENT_OBJECT_STATUS_DRAFT )
// after
if ( $object->attribute( 'status' ) == eZContentObject::STATUS_DRAFT )
```

### Constructors named after the class

Since PHP 8.0 a method with the class's name is **not** a constructor. For a datatype this is fatal: `new` falls
through to `eZDataType::__construct( $dataTypeString, $name, ... )`, whose first two parameters are required.

```php
// before (PHP 5)
class myRatingType extends eZDataType
{
    function myRatingType()
    {
        $this->eZDataType( 'myrating', 'Rating' );
    }
}

// after
class myRatingType extends eZDataType
{
    const DATA_TYPE_STRING = 'myrating';

    public function __construct()
    {
        parent::__construct( self::DATA_TYPE_STRING, 'Rating' );
    }
}
eZDataType::register( myRatingType::DATA_TYPE_STRING, 'myRatingType' );
```

The kernel keeps courtesy methods with the old names (`eZDataType::eZDataType()`, `eZPersistentObject::eZPersistentObject()`,
`eZWorkflowType::eZWorkflowType()`, `eZWorkflowEventType::eZWorkflowEventType()`), so `$this->eZDataType( ... )`
inside your own `__construct()` still works; your class's own old-style constructor is what no longer runs. See also
[5.90 and PHP 7](../bc/5.90/php7.md).

### Signatures that must match the parent

PHP 8 checks every overriding method against its parent when the class loads. By-reference parameters, a different
number of parameters or a different default break the class. The kernel's `eZDataType` methods take objects by value:

```php
// before: fatal on PHP 8 ("Declaration ... must be compatible with ...")
function onPublish( &$contentObjectAttribute, &$contentObject, &$publishedNodes ) { ... }

// after: as in kernel/classes/ezdatatype.php
function onPublish( $contentObjectAttribute, $contentObject, $publishedNodes ) { ... }
```

Exponential's own extensions had exactly this (syndication's `onPublish()`, ezstarrating's `makeObjectsArray()`), see
[PHP 8.0 support](../bc/6.0/php-8.0-support.md).

### Functions and syntax removed from PHP

| PHP 5 code | Removed | Replacement |
|---|---|---|
| `while ( list( $k, $v ) = each( $array ) )` | 8.0 | `foreach ( $array as $k => $v )` |
| `$f = create_function( '$a', 'return $a * 2;' );` | 8.0 | `$f = function ( $a ) { return $a * 2; };` |
| `$first = $string{0};` | 8.0 | `$first = $string[0];` |
| `split( ',', $s )`, `ereg()`, `eregi()` | 7.0 | `explode( ',', $s )`, `preg_match()` |
| `mysql_query()` and the `mysql_*` family | 7.0 | `eZDB::instance()->arrayQuery()` / `query()` |
| `$obj =& new Foo();` | 7.0 | `$obj = new Foo();` |
| `myFunction( &$value );` (call-time reference) | 5.4 | declare `function myFunction( &$value )`, call `myFunction( $value )` |
| `implode( $pieces, ',' )` | 8.0 | `implode( ',', $pieces )` |
| `get_magic_quotes_gpc()`, `money_format()` | 8.0 | remove; `NumberFormatter` |
| `$a ? $b : $c ? $d : $e` | 8.0 | add parentheses |
| `(real) $x` | 8.0 | `(float) $x` |
| `crypt( $password )` without a salt | 8.0 (`ArgumentCountError`) | `password_hash()` |

Exponential fixed the same in its own extensions: birthday used `each()`, enhancedezbinaryfile used `split()`, the
OAuth module and swark called `implode()` with swapped arguments.

### Deprecations to clear (not fatal, but noisy and next in line)

```php
// Implicitly nullable parameter (deprecated in 8.4)
function load( eZContentObject $object = null )      // before
function load( ?eZContentObject $object = null )     // after

// Dynamic properties (deprecated in 8.2): declare them
class myHelper
{
    public $cache = array();                          // declared, no warning
}

// null passed to string functions (deprecated in 8.1)
$length = strlen( $value );                           // before
$length = strlen( $value ?? '' );                     // after

// count() on something that may not be countable (warning since 7.2, TypeError since 8.0)
$n = count( $list );                                  // before
$n = is_countable( $list ) ? count( $list ) : 0;      // after

// "${var}" interpolation (deprecated in 8.2)
$s = "Node ${nodeID}";                                // before
$s = "Node {$nodeID}";                                // after

// Methods implementing Iterator, ArrayAccess, Countable (deprecated return types in 8.1)
#[\ReturnTypeWillChange]
public function current() { ... }
```

`utf8_encode()` and `utf8_decode()` are deprecated since 8.2 (`mb_convert_encoding()`); calling a non-static method
statically is an `Error` since 8.0; comparisons between numbers and non-numeric strings changed in 8.0 (`0 == 'a'` is
false). `eZPersistentObject` already carries `#[AllowDynamicProperties]` and stores undeclared fields itself, so
subclasses do not warn; your other classes must declare their properties. The kernel's own sequence of PHP 8 changes,
release by release, is in [PHP 8 support](../bc/6.0/php8.md).

### php.ini

| PHP 5 setting | Status |
|---|---|
| `register_globals`, `magic_quotes_gpc`, `safe_mode`, `allow_call_time_pass_reference` | removed in PHP 5.4; code that relied on them must read `eZHTTPTool::instance()->postVariable()` and friends |
| `mbstring.func_overload` | removed in 8.0 |
| `date.timezone` | still set it (the vendor's 4.0 page asked for it too); the setup checks it |
| `memory_limit`, `max_execution_time`, `file_uploads`, `upload_tmp_dir`, `open_basedir`, `allow_url_fopen`, `variables_order` | checked by the setup wizard (`kernel/setup/ezsetuptests.php`); values in [chapter 2](02-requirements.md) |

### eZ Components

4.x needed eZ Components installed beside the code, at a minimum version per release (2008.2 for 4.1, 2009.1 for 4.2,
2009.2.1 for 4.3 and 4.4, `ezcomponents-ezp45` to `ezcomponents-ezp47` later). Exponential uses their continuation,
Zeta Components, installed by Composer (`zetacomponents/*` in `composer.json`) and loaded by `ezcBase::autoload`. The
class names (`ezcMail`, `ezcConsoleInput` ...) are the same; remove any include path or `ezc` directory you set up
for 4.x.

## 14.10 Users, passwords and sign-in

**Old hashes keep working and are upgraded at sign-in.** `ezuser.password_hash_type` names the method of each stored
hash (`kernel/classes/datatypes/ezuser/ezuser.php`):

| Type | Name | Notes |
|---|---|---|
| 1 | `md5_password` | md5 of the password |
| 2 | `md5_user` | md5 of login and password |
| 3 | `md5_site` | md5 of login, password and `[UserSettings] SiteName` |
| 4 | `mysql` | MySQL's `PASSWORD()` |
| 5 | `plaintext` | |
| 6 | `bcrypt` | |
| 7 | `php_default` | `password_hash()`; the new default (`[UserSettings] HashType=php_default`) |

With `[UserSettings] UpdateHash=true` (the default) a successful sign-in with an old hash stores a new one of
`HashType`. Things to carry over and check:

- **`md5_site` hashes** contain `[UserSettings] SiteName`. That is a separate key from `[SiteSettings] SiteName`, and
  its default is `ez.no`. If the old site changed it, set the same value in `settings/override/site.ini.append.php`,
  or those users cannot sign in.
- **`mysql` hashes** are checked with `PASSWORD()`, which MySQL 8.0 removed. On MySQL 8, those accounts cannot sign in;
  reset their passwords (below) or have them use "forgot password".
- **Accounts without an `ezuser_setting` row** cannot sign in since August 2026: a missing row now counts as disabled.
  The old chain itself deletes orphaned rows (4.0 and 5.4 files) but never creates missing ones. Find them with the
  preflight query of 14.5.3 or the queries of the
  [August 2026 security specification](../specifications/6.0/security-hardening-2026-08.md#f-06-find-accounts-that-cannot-sign-in).
- **Passwords shorter than 10 characters** keep working; the new minimum applies when a password is set.
- **Reset a password** from the command line: `php bin/php/resetuserpassword.php -a admin -ap '<admin password>' -u <login> -p '<new password>'`.

## 14.11 Search

The built-in engine (`site.ini [SearchSettings] SearchEngine=eZSearchEngine`) is the default. Its tables are part of
the update chain (the 5.4 file drops `ezsearch_return_count`), but rebuild the index after the migration rather than
trust the old one:

```bash
php bin/php/updatesearchindex.php --clean
```

eZ Find (Solr) is suggested in `composer.json` as `se7enxweb/ezfind`; install it as a package and reindex with its own
tools. `settings/solr.ini` and `php bin/php/console exp:solr` control a Solr server for this installation; it is off
until `[SolrSettings] Enabled=true`. Relations are indexed one level deep since 2018 ([relation indexing](../bc/5.90/relation_indexing.md)):
search results that relied on relations of relations change.

## 14.12 Verify the content after the migration

Run the checks of [11.7](11-upgrading.md#117-verify-the-upgrade), and in addition:

1. **Schema against the reference.** `share/db_schema.dba` is the schema of the current release. Compare the migrated
   database with it; the exit code says whether they differ:

   ```bash
   php bin/php/ezsqldiff.php --type=mysql --user=USER --password=PASS DATABASE share/db_schema.dba
   ```

   (`--type=postgresql` on PostgreSQL). Tables of your own extensions show up as differences; anything else is a
   step that was missed.
2. **Counts against the old database.** Run the size queries of 14.3 on the old and the migrated database. Objects,
   attributes and nodes must match exactly; `ezurlalias_ml` grows when aliases are rebuilt.
3. **URL aliases.** `php bin/php/updateniceurls.php` (with `--update-nodes`), then
   `php bin/php/verify_aliases.php` (`--fix` repairs what it can safely, `--verbose` explains).
4. **Images and files.** Open a page of each class with images; the alias is generated on the first request. The
   5.1, 5.3 and 5.4 image scripts of phase B report broken references.
5. **The admin's Upgrade check.** **Setup > Upgrade check**, both buttons.
6. **Every siteaccess**: `php bin/php/eztemplatecheck.php -s<siteaccess>`, then the URL list you recorded in 14.4,
   comparing HTTP status codes.
7. **Editing.** Create, edit and publish an object of each class with `ezxmltext`, an image and a relation, in every
   language. Check that the object appears in search.
8. **Users.** Sign in as an editor whose hash is still an old type (14.10) and check that `password_hash_type` changed
   to 7 afterwards.
9. **Cron jobs** run without errors: `php runcronjobs.php` and `php runcronjobs.php frequent` (or the parts set in
   `cronjob.ini`), see [chapter 10](10-after-installing.md).
10. **Logs.** `var/log/error.log` and `var/log/warning.log` (and `var/<VarDir>/log/`) stay quiet while you click
    through the site.

## 14.13 Common issues

The vendor lists the issues people met in its own migration
([Common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/)).
Their Exponential counterparts first, then the ones specific to the legacy path:

| Issue | Cause | Fix in Exponential 6.0 |
|---|---|---|
| Enabling a migration bundle | The vendor's tools came as a Symfony bundle | Nothing to enable: the tools are the files in `update/` and the scripts in `bin/php/`. |
| URL aliases must be regenerated | Alias tables changed between 4.x and 5.x | `php bin/php/updateniceurls.php --update-nodes`, then `php bin/php/verify_aliases.php --fix`. |
| Images with unusual characters or outside the var directory | File names and `VarDir` changes from old releases | `5.1/fiximagesoutsidevardir.php`, `5.3/recreateimagesreferences.php`, `5.4/fixtrashedimagereferences.php` (each with `--dry-run`), `4.2/fixorphanimages.php`. There is no path-normalising command; keep `VarDir` as it was. |
| "Unknown relation type 0" | Rows with `relation_type = 0` in `ezcontentobject_link` | The 5.1 file deletes them; `5.4/cleanuntranslatablerelations.php` and `cleanupfieldvaluerelations.php` remove other stale relations. |
| Always-available flag on all fields | A behaviour of the vendor's newer public API | Not applicable: Exponential writes and reads `language_id` the way 4.x did. |
| Sub-items not listed (empty `sort_key_string`) | Written by the vendor's newer public API | Not applicable for data written by 4.x. Sort keys are set when an attribute is stored; republishing an object rewrites them. |
| `SET storage_engine` fails | An update file from before October 2026; the spelling was removed in MySQL 5.7.5 and MariaDB 12.0 | Take the current file, which says `SET default_storage_engine` (14.5.5). |
| "Duplicate entry" adding `ezcontentobject_remote_id` or the digest address index | Duplicates older releases allowed | The preflight of 14.5.3. |
| "Duplicate column" or "Duplicate key name" in the 4.0 to 4.2 files | The statements of a patch release already applied | Remove that `START: from` block from a copy (14.5.5). |
| `Unknown column 'priority'` or similar from a repair script | Script run by today's kernel against an intermediate schema | Phase A first, then phase B (14.5.4). |
| `Fatal error: Uncaught ArgumentCountError ... eZDataType::__construct()` | Old-style constructor in a datatype | 14.9, "Constructors named after the class". |
| "Declaration of ... must be compatible with ..." | By-reference or extra parameters in an overriding method | 14.9, "Signatures that must match the parent". |
| `Call to undefined function each()` / `create_function()` / `split()` / `mysql_query()` | Removed from PHP | 14.9, the table of removed functions. |
| `Class '...' not found` | Autoload array not regenerated, or the class in a file the generator excludes | `php bin/php/ezpgenerateautoloads.php -e`, clear the caches. |
| Page shows `?` instead of accented characters | The connection is UTF-8, the data still another charset | `ezconvertdbcharset.php` (14.5.2). |
| "Illegal mix of collations" on MySQL 8 | Old tables in `utf8`, tables made by the update files in `utf8mb4` | Set the database default before the chain (14.5.2); convert the new tables to the same charset. |
| Users cannot sign in | Missing `ezuser_setting`, `md5_site` with another `SiteName`, `mysql` hashes on MySQL 8 | 14.10. |
| Admin has no menus, `YUI is not defined` in the console | Templates or settings of your own still load YUI | [YUI removed](../bc/6.0/yui-removal.md). |
| Files of a cluster site are missing | `eZDBFileHandler` site, or `STORAGE_BACKEND` constants | 14.4 and 14.6. |
| A template override no longer wins | `override.ini` blocks sorted by `Priority`, or extension order | 14.7. |
| The 5.0 state-group translations look doubled | `deduplicatecontentstategrouplanguage.php` not run | Run it (phase B, 5.0). |
| Stale pages after the switch | Old caches, or a cache filled by old code before the reload | `php bin/php/ezcache.php --clear-all --purge` after the PHP reload ([11.7](11-upgrading.md#117-verify-the-upgrade)). |

More symptoms and fixes for running sites: [chapter 12](12-troubleshooting.md).

## 14.14 Rollback plan

A migration is rolled back by **not** switching, never by undoing the migrated database.

- **Until the switch:** the old site keeps serving (in maintenance mode during the final run). Rollback is: take the
  old site out of maintenance (`php bin/php/maintenance.php off` on Exponential; on the old site, whatever it used).
- **After the switch:** keep the old server, its code and an untouched copy of its database for at least as long as
  it takes to be sure. Point DNS or the proxy back to it. Content created on the new site after the switch is not in
  the old database; for that reason keep editing frozen (or at least logged) for the first days, or plan to re-enter
  those changes.
- **Never** run an update file a second time to "repair" a half-done run; restore the dump taken before the run and
  start the runbook again.
- **Keep the dumps:** the old production dump, the migrated dump right after 14.12, and the `var/` archive. With
  those three you can rebuild either side.

## 14.15 How long it takes

The time depends far more on the size of a few tables than on the version you come from. Measure it in the rehearsal
and plan the production window from the measurement, with a margin. What scales with what:

| Step | Grows with |
|---|---|
| Dump and restore | database size on disk |
| Charset and table-type conversion | total rows; every table is rewritten |
| 4.1, 5.1 and 5.2 files | `ezcontentobject`, `ezcontentobject_attribute`, `ezcontentobject_link`, `ezurlalias_ml` (index rebuilds and `BIGINT` changes rewrite those tables) |
| Phase B scripts | objects and attributes with XML text, images, relations; the ones with `--iteration-limit` and `--iteration-sleep` can be throttled |
| `updateniceurls.php` | nodes times languages |
| `updatesearchindex.php --clean` | objects; usually the longest single step |
| Copying `var/` | number and size of stored files |
| Porting extensions | lines of your own PHP and templates, not database size |

The database steps can run while the old site is still serving, on the rehearsal copy; only the final run needs the
maintenance window. Reindexing can be finished after the switch if search may be incomplete for a while.

## 14.16 The vendor's upgrade pages, point by point

The vendor documented each step of the 4.x line separately. Every point those pages make is covered above; this table
says where, and how Exponential differs.

| Vendor's point | Where | Exponential 6.0 |
|---|---|---|
| Back up the database and directory; take the site offline ([backup and checks](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Backup-and-consistency-checks.html)) | 14.4, [11.1](11-upgrading.md#111-before-you-start) | `bin/php/maintenance.php` takes the site offline with HTTP 503 |
| File and database consistency check in Setup > Upgrade check | 14.4, 14.12 | same buttons; file check compares with `share/filelist.md5` |
| Five steps: files, database, system scripts, configuration, caches ([how to proceed](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/How-to-proceed.html)) | 14.5 to 14.8 | database scripts in two phases (14.5.4) |
| Direct upgrade only between consecutive stable releases; otherwise stage it | 14.5.5 | one chain, applied in one session, file by file |
| 4.0: PHP 5, `date.timezone`, autoload arrays, class constants instead of global constants ([important notes](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.0/Important-notes.html)) | 14.9 | PHP 8.0 to 8.5; arrays in `autoload/` and `var/autoload/` |
| 4.0: charset conversion with `ezconvertdbcharset.php`, InnoDB with `ezconvertmysqltabletype.php` ([3.10 to 4.0](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.0/from-3.10.x-to-4.0.y.html)) | 14.5.2 | same scripts; MySQL `utf8`, mind MySQL 8 defaults |
| 4.0: overriding templates in `override/templates/` | 14.8 | unchanged; `Priority` now orders override blocks |
| 4.1: `updateimagesystem.php` **before** the SQL; ezdhtml replaced by ezoe; `$Result['path']` must be an array ([4.0 to 4.1](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.1/4.0.x-to-4.1.y.html)) | 14.5.4, 14.7, 14.9 | only if `ezimage` has rows; ezoe on TinyMCE 3 or 8 |
| 4.1: `addlockstategroup.php` (once), remote id fixes, `fixezurlobjectlinks.php`, `initurlaliasmlid.php`, `correctxmlalign.php` | 14.5.6 | after the schema (phase B) |
| 4.1, 4.2: eZ Flow and eZ Webin upgraded with `ezflowupgrade.php` / `ezwebinupgrade.php` | 14.8 | code by Composer; scripts for the site packages |
| 4.2: PHP 5.2 (not 5.2.9) or 5.3; `correctxmlalign.php`, `fixorphanimages.php` ([4.1 to 4.2](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.2/Upgrading-from-4.1.x-to-4.2.y.html)) | 14.5.6 | |
| 4.3: `updatenodeassignment.php`; optional admin2 design with ezjscore and the `admin_preferences` tool ([4.2 to 4.3](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.3/Upgrading-from-4.2.x-to-4.3.x.html)) | 14.5.6, 14.7 | admin2 is today's `admin`; newer admin designs |
| 4.4: `updatesectionidentifier.php` ([4.3 to 4.4](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.4/Upgrading-from-4.3.x-to-4.4.x.html)) | 14.5.6 | 4.4 and 4.5 copies identical |
| 4.5: `DatabaseImplementation=ezmysqli`, mysqli cluster backends, `STORAGE_BACKEND` in `index_cluster.php`, new rewrite rule ([4.4 to 4.5](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.5/Upgrading-from-4.4-to-4.5.html)) | 14.6, 14.7 | `ezmysql` aliased to mysqli; `CLUSTER_*` constants; rules from chapter 8 |
| 4.6: `removetrashedimages.php`, `updateordernumber.php`, remove user-tab overrides, remove the `index_ajax.php` rule ([4.5 to 4.6](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.6/Upgrading-from-4.5-to-4.6.html)) | 14.5.6, 14.7 | `index_ajax.php` no longer exists |
| 4.7: `index_cluster.php` settings into `config.php` / `config.cluster.php`; cluster `datatype` column; mobile device detection; eZ Flow `add_to_block_frontpage` override ([4.6 to 4.7](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.7/Upgrading-from-4.6-to-4.7.html)) | 14.5.5, 14.6, 14.7 | DFS only; the `ezdbfile` half is for the removed handler |
| Direct 4.1 to 4.7: SQL files in sequence, per-patch-release SQL, `ezm_pool` index, skip the 4.4 section script, enable ezjscore and admin2 ([direct upgrade](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Direct-upgrading/Direct-upgrading-to-4.7-from-4.1-4.2-4.3-4.4-and-4.5/Direct-upgrading-from-4.1-to-4.7.html)) | 14.5.5, 14.8 | `START: from` markers; the `ezm_pool` index is in the current ezflow schema |
| Minimum eZ Components per release | 14.9 | Zeta Components by Composer |
| `chmod -R a+rwx` on design, extension, settings, var | 14.6 | do not; see chapters 8 and 13 |
| `ezcache.php --clear-all --purge` and a manual check of the cache directories | 14.6 | same command |

## 14.17 Migration checklist

Before:

- [ ] Old version, extensions, datatypes, hash types, charset, table types, cluster mode, search engine recorded (14.3)
- [ ] Old site consistency checks run; database cluster left; optional clean-ups done (14.4)
- [ ] Full backup of the old database and directory, restored once on a scratch machine
- [ ] Staging server with PHP 8 and Exponential 6.0 code; no installer run against the old database (14.2)
- [ ] URL list, search counts and sign-in figures recorded for comparison

Database (on staging, then for real):

- [ ] Tables InnoDB, database UTF-8, database default charset set (14.5.2)
- [ ] Preflight queries return no rows, or their fix is planned (14.5.3)
- [ ] `ezimage` checked; `updateimagesystem.php` run before the 4.1 file if needed (14.5.4)
- [ ] Phase A: every SQL file from your version to 6.0.15, in order, patch-release blocks handled, cluster files on the DFS database (14.5.5)
- [ ] Phase B: repair scripts of every version passed, dry runs first (14.5.6)
- [ ] `createaudittables.php` run; version row reads `6.0.15stable`

Files and settings:

- [ ] `var/` storage copied, `VarDir` unchanged, caches not copied (14.6)
- [ ] Cluster: DFS with the mysqli backend, `CLUSTER_*` constants (14.6)
- [ ] `settings/override/` and `settings/siteaccess/` carried over and reviewed against 14.7
- [ ] Rewrite rules taken from chapter 8

Code:

- [ ] Designs moved to a design extension; nothing edited inside packaged extensions (14.8)
- [ ] Templates checked with `eztemplatecheck.php`; YUI and old jQuery calls replaced
- [ ] Own extensions linted, autoloads regenerated, `exp:checkclasses` clean, PHP 8 breaks and deprecations fixed (14.9)

After:

- [ ] Users: `ezuser_setting` rows, `[UserSettings] SiteName`, `mysql` hashes handled (14.10)
- [ ] Search index rebuilt (14.11)
- [ ] Schema compared with `share/db_schema.dba`; counts compared; aliases verified; Upgrade check run (14.12)
- [ ] Editing, publishing, sign-in, cron jobs and logs checked
- [ ] Caches cleared after the PHP reload; old site and dumps kept for rollback (14.14)

## References

In this repository:

- [11. Upgrading](11-upgrading.md): the update chain this chapter walks, the 6.0 steps and the verification commands;
  [12. Troubleshooting](12-troubleshooting.md); [13. Security hardening](13-security-hardening.md);
  [2. Requirements](02-requirements.md); [8. Serving the site](08-serving-the-site.md); [9. Databases](09-databases.md).
- [Upgrading guide](../guides/upgrading.md).
- Release notes of the 4.x line: [4.1](../bc/4.1/changes-4.1.0.txt), [4.2](../bc/4.2/changes-4.2.0.txt),
  [4.3](../bc/4.3/changes-4.3.0.txt), [4.4](../bc/4.4/changes-4.4.0.txt), [4.5](../bc/4.5/changes-4.5.0.txt),
  [4.6](../bc/4.6/changes-4.6.0.txt), [4.7](../bc/4.7/changes-4.7.0.txt).
- Changelogs of the old line: [3.9 to 3.10](../changelogs/3.10/CHANGELOG-3.9.0-to-3.10.0),
  [3.10 to 4.0](../changelogs/4.0/CHANGELOG-3.10.0-to-4.0.0), [4.0 to 4.1](../changelogs/4.1/CHANGELOG-4.0.0-to-4.1.0),
  [4.1 to 4.2](../changelogs/4.2/CHANGELOG-4.1.0-to-4.2.0), [4.2 to 4.3](../changelogs/4.3/CHANGELOG-4.2.0-to-4.3.0),
  [4.3 to 4.4](../changelogs/4.4/CHANGELOG-4.3.0-to-4.4.0), [4.5 to 4.6](../changelogs/4.6/CHANGELOG-4.5.0-to-4.6.0.txt),
  [4.6 to 4.7](../changelogs/4.7/CHANGELOG-4.6.0-to-4.7.0.txt).
- 5.90 line: [overview](../bc/5.90/README.md), [PHP 7](../bc/5.90/php7.md),
  [password length](../bc/5.90/password_length.md), [relation indexing](../bc/5.90/relation_indexing.md).
- PHP 8: [PHP 8 support](../bc/6.0/php8.md), [PHP 8.0 support](../bc/6.0/php-8.0-support.md).
- JavaScript and editor: [YUI removed](../bc/6.0/yui-removal.md),
  [jQuery 4 and YUI removal](../features/6.0/jquery4-and-yui-removal.md),
  [TinyMCE 3 and TinyMCE 8 in ezoe](../bc/6.0/ezoe-tinymce8.md).
- Extensions: [extension loading order](../features/6.0/extension-loading-order.md),
  [additional extension directories](../bc/6.0/AdditionalExtensionDirectories.md),
  [behaviour changes of the legacy extensions](../bc/6.0/extensions-behaviour-changes.md).
- [The product is called Exponential](../features/6.0/rebranding-to-exponential.md),
  [maintenance mode](../features/6.0/maintenance-mode.md),
  [file consistency check](../features/6.0/file-consistency-check.md), [audit](../bc/6.0/audit.md),
  [legacy bridge](../features/6.0/legacy-bridge.md),
  [security hardening August 2026](../specifications/6.0/security-hardening-2026-08.md).
- Code: `update/database/`, `update/common/scripts/`, `bin/php/ezconvertdbcharset.php`,
  `bin/php/ezconvertmysqltabletype.php`, `bin/php/ezsqldiff.php`, `bin/php/updateniceurls.php`,
  `bin/php/verify_aliases.php`, `bin/php/ezpgenerateautoloads.php`, `bin/php/eztemplatecheck.php`,
  `bin/php/resetuserpassword.php`, `kernel/classes/datatypes/ezuser/ezuser.php`, `kernel/classes/ezdatatype.php`,
  `kernel/classes/ezpersistentobject.php`, `lib/ezdb/classes/ezmysqlcharset.php`, `index_cluster.php`,
  `config.php-RECOMMENDED`, `share/db_schema.dba`.

The vendor's upgrade documentation for the 4.x line (archived copy of the former doc.ez.no pages):

- [How to proceed](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/How-to-proceed.html),
  [Backup and consistency checks](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Backup-and-consistency-checks.html),
  [Direct upgrading](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Direct-upgrading.html),
  [The system upgrade scripts](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/The-system-upgrade-scripts.html).
- [4.0: important notes](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.0/Important-notes.html),
  [3.10 to 4.0](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.0/from-3.10.x-to-4.0.y.html),
  [4.0 to 4.1](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.1/4.0.x-to-4.1.y.html),
  [4.1 to 4.2](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.2/Upgrading-from-4.1.x-to-4.2.y.html),
  [4.2 to 4.3](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.3/Upgrading-from-4.2.x-to-4.3.x.html),
  [4.3 to 4.4](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.4/Upgrading-from-4.3.x-to-4.4.x.html),
  [4.4 to 4.5](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.5/Upgrading-from-4.4-to-4.5.html),
  [4.5 to 4.6](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.6/Upgrading-from-4.5-to-4.6.html),
  [4.6 to 4.7](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Upgrading-to-4.7/Upgrading-from-4.6-to-4.7.html),
  [direct 4.1 to 4.7](https://ezpublishdoc.mugo.ca/eZ-Publish/Upgrading/Direct-upgrading/Direct-upgrading-to-4.7-from-4.1-4.2-4.3-4.4-and-4.5/Direct-upgrading-from-4.1-to-4.7.html).

The vendor's migration to its newer platform:

- [Migrating from eZ Publish](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/)
- [Common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/)

PHP migration guides:
[7.0](https://www.php.net/manual/en/migration70.php), [7.1](https://www.php.net/manual/en/migration71.php),
[7.2](https://www.php.net/manual/en/migration72.php), [7.3](https://www.php.net/manual/en/migration73.php),
[7.4](https://www.php.net/manual/en/migration74.php), [8.0](https://www.php.net/manual/en/migration80.php),
[8.1](https://www.php.net/manual/en/migration81.php), [8.2](https://www.php.net/manual/en/migration82.php),
[8.3](https://www.php.net/manual/en/migration83.php), [8.4](https://www.php.net/manual/en/migration84.php),
[8.5](https://www.php.net/manual/en/migration85.php).

Code and packages:

- Exponential on GitHub: [github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential)
  ([releases](https://github.com/se7enxweb/exponential/releases)).
- The original legacy kernel, as the historical source of the update files:
  [github.com/ezsystems/ezpublish-legacy](https://github.com/ezsystems/ezpublish-legacy).
- Packagist: [se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential).
- MySQL: [character sets](https://dev.mysql.com/doc/refman/8.4/en/charset.html); PostgreSQL:
  [psql](https://www.postgresql.org/docs/current/app-psql.html), [pgcrypto](https://www.postgresql.org/docs/current/pgcrypto.html).

[Contents](README.md) · Previous: [13. Security hardening for production](13-security-hardening.md) · Next: [15. Migrating from the 5.x legacy stack](15-migrating-from-5x-legacy.md)
