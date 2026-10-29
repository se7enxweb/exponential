# Uninstalling Exponential 6.0

This page removes an Exponential 6.0 installation from a server, and before that it exports everything the
installation holds: the database, the content in open formats, the storage files and images, the packages, the
users, the settings, the translations and your own code. Everything goes to disk in formats other software reads,
so a developer can rebuild the site elsewhere, on another Exponential, on another system or on no system at all.

Exponential has no vendor lock-in. Nothing in the steps below needs a license key, a vendor account, a
proprietary tool or anyone's permission. The same steps are the migration path: do sections 2 and 3, take the
export with you, and stop there if the old server keeps running.

Every command is run from the installation root (the directory that holds `index.php`, `autoload.php`, `bin/` and
`settings/`), as the user the site runs as. When you run a script as `root`, add `--allow-root-user`.
`./console` is a shortcut for `php bin/php/console` (see [Installing Exponential 6.0](INSTALL.md)).

## Contents

1. [Why there is no lock-in](#1-why-there-is-no-lock-in)
2. [Before you begin](#2-before-you-begin)
3. [Export all data](#3-export-all-data)
4. [Rebuild elsewhere](#4-rebuild-elsewhere)
5. [Stop the services and the cron jobs](#5-stop-the-services-and-the-cron-jobs)
6. [Remove the virtual host](#6-remove-the-virtual-host)
7. [Remove the database](#7-remove-the-database)
8. [Remove the files and the var storage](#8-remove-the-files-and-the-var-storage)
9. [Remove a single extension](#9-remove-a-single-extension)
10. [Related pages](#10-related-pages)

## 1. Why there is no lock-in

- **Free software.** The kernel is licensed under the GNU General Public License, version 2 or any later version
  (`"license": "GPL-2.0-or-later"` in `composer.json`; see [LICENSE](../../LICENSE.md)). The documentation is under the
  [GNU Free Documentation License 1.3](LICENSE-GFDL-1.3.txt). You have the whole source code, may run it anywhere and
  may read every line that wrote your data.
- **Standard SQL databases.** The data sits in SQLite, PostgreSQL, MariaDB, MySQL or Oracle (MongoDB is supported
  as well), in plain, documented tables (`ezcontentobject`, `ezcontentobject_attribute`, `ezcontentobject_tree`,
  `ezuser` ...). Any database tool reads them, and every engine has its own dump tool.
- **Open file formats.** Images and files are stored as ordinary files under `var/`, with their original names and
  types. Settings are plain INI text. Translations are Qt Linguist `.ts` XML files. Rich text is stored as XML.
  Packages are `.ezpkg` archives (tar with XML or JSON inside). Content exports are CSV, JSON or XML.
- **Your code stays yours.** Extensions, designs and templates are files in your own directories, under the license
  you give them.

## 2. Before you begin

1. **Decide what you keep.** Usually everything in section 3; at the least the database dump, the storage files
   and your settings.
2. **Freeze the content.** Maintenance mode answers visitors with a 503 page while you export, so nothing changes
   between the parts of the export:

   ```bash
   php bin/php/maintenance.php on --message="This site is moving" --allow-root-user
   php bin/php/maintenance.php status --allow-root-user
   ```

   `--allow-admin` keeps the administration (`/admin`) reachable; see [Maintenance mode](features/6.0/maintenance-mode.md).
3. **Make room.** The export needs about the size of the database plus the size of `var/`. Write it to one directory
   outside the web root, for example `/srv/export/example.com`, called `EXPORT` below:

   ```bash
   EXPORT=/srv/export/example.com
   mkdir -p "$EXPORT"
   ```

4. **Note the facts of the installation** for whoever rebuilds it:

   ```bash
   ./console exp:info --json --no-logo > "$EXPORT/system-card.json"
   ./console exp:systeminfo --json > "$EXPORT/system-information.json"
   ```

## 3. Export all data

### 3.1 The database

The complete database, with the tool of your database system. This dump alone is enough to restore the site.

| Database | Export |
|---|---|
| MySQL or MariaDB | `mysqldump --single-transaction --routines -u USER -p DATABASE > "$EXPORT/database.sql"` |
| PostgreSQL | `pg_dump -U USER DATABASE > "$EXPORT/database.sql"` |
| SQLite | `sqlite3 var/storage/sqlite3/exponential.db ".backup '$EXPORT/database.db'"` and `sqlite3 var/storage/sqlite3/exponential.db .dump > "$EXPORT/database.sql"` |

`exponential.db` is the default file name of `exp:install`; use the name of your own file (`ls var/storage/sqlite3/`).

A second, engine-neutral copy helps when the target is a different database system. `ezsqldumpschema.php` writes
the schema and the data as `.dba` files (a PHP array of tables, fields, indexes and rows) that
`ezsqlinsertschema.php` turns into the SQL of another engine (see [section 4](#4-rebuild-elsewhere)):

```bash
php bin/php/ezsqldumpschema.php --type=mysql --user=USER --output-array DATABASE "$EXPORT/db_schema.dba"
php bin/php/ezsqldumpschema.php --type=mysql --user=USER --output-array --output-types=data DATABASE "$EXPORT/db_data.dba"
```

`--type` is `mysql` or `postgresql` (add `--host`, `--port`, `--password` as needed). The data file holds every row
in memory while it is written: use it for small and medium sites, and the native dump above for any size.

### 3.2 Storage files and images

Every uploaded file and image is a file under the var directory of the site (`site.ini [FileSettings] VarDir`,
for example `var/site`): `storage/original/` holds the uploaded files, `storage/images/` the images and their
variations. Packages are in `var/storage/packages/`, an SQLite database in `var/storage/sqlite3/`.

```bash
tar czf "$EXPORT/var-storage.tar.gz" --exclude='var/cache' --exclude='var/*/cache' --exclude='var/tmp' var
```

On a cluster that keeps its files in the database (`file.ini [ClusteringSettings]`), move them back to disk first;
the command moves them, then copy `var/` as above:

```bash
php bin/php/clusterize.php -u --allow-root-user
```

`./console exp:contentcheck --help` lists the checks that find files the database names but the disk lacks, so you
know before the export whether every file is there ([Missing files](guides/missing-files.md)).

### 3.3 Content in open formats

The database dump has every version of every object. For other software, export the content as files as well.

**CSV per class, from the kernel.** `ezcsvexport.php` writes the subtree below a node as one CSV file per content
class:

```bash
php bin/php/ezcsvexport.php --storage-dir="$EXPORT/csv" 2 --allow-root-user     # node 2: the content tree
php bin/php/ezcsvexport.php --storage-dir="$EXPORT/csv-media" 43 --allow-root-user  # node 43: the media library
```

**A whole-site archive in CSV, JSON or XML, with the extension xrowextract** (in the default `composer.json`; the
**Export** tab of the admin when it is active). One file per class with object id, remote id, main node, parent node,
URL alias, dates and every attribute, plus `manifest.json`, every language as its own row:

```bash
php extension/xrowextract/bin/php/archive.php --list-sets --allow-root-user
php extension/xrowextract/bin/php/archive.php --set=everything --files=json --languages=all --format=tar.gz --output="$EXPORT" --allow-root-user
php extension/xrowextract/bin/php/archive.php --set=everything --files=xml --format=zip --output="$EXPORT" --allow-root-user
```

The set `everything` is the content tree, the media library and the users (nodes 2, 43 and 5 on a standard
installation). `--plain-text` adds the plain text of every rich text field; `--dry-run` shows what would be written.
Every option: [xrowextract](features/6.0/extensions/xrowextract.md).

**JSON over HTTP, with the REST API** (extension `ezprestapi`): every node and its fields as JSON, for a script on the
target side that pulls the content itself. See [REST API v1 and v2](guides/rest-api-v1-and-v2.md).

### 3.4 Packages (.ezpkg)

A package carries content classes, content with its files, and templates in one archive the package system of any
Exponential imports. Build one from the whole content tree with `ezpm.php`:

```bash
php ezpm.php create site-export 'Full content export' 1.0 install --allow-root-user
php ezpm.php add site-export ezcontentobject 1 --all-versions --allow-root-user
php ezpm.php export site-export -d "$EXPORT/packages" --format=json --allow-root-user
```

Node 1 is the true root: everything below it, with the classes and the template overrides (`--exclude-templates`
leaves them out). `--format=json` writes `package.json` and JSON documents with checksums
([the JSON package format](bc/6.0/package-json-format.md)); without it the classic XML format is written.

The packages already on the site (the site package, the class packages, your own) are exported one by one:

```bash
php ezpm.php list --allow-root-user
php ezpm.php export <package> -d "$EXPORT/packages" --allow-root-user
```

The same in the admin: **Setup** > **Packages**, a package's page, **Download**; **Create new package** builds content
object and content class packages with a wizard. See [Packages](guides/packages.md).

### 3.5 Users

Users are content objects of the user classes below the node **Users** (node 5 on a standard installation), so the
exports of 3.3 and 3.4 include them when they cover node 5 (the set `everything` does; with `ezcsvexport.php`, run it
for node 5 as well). The database dump has the accounts, the roles and the policies (`ezuser`, `ezrole`,
`ezpolicy`, `ezuser_role`).

Password hashes are standard PHP `password_hash()` hashes (bcrypt by default) that any PHP application verifies with
`password_verify()`, so users can keep their passwords on the new system. xrowextract writes them only with
`--password-hashes` and the policy `xrowextract/password_hash`; treat such a file as a secret. Without them, users set
a new password on the new system.

### 3.6 Settings

Settings are INI text files. Keep the ones you made; the shipped ones come with any new copy of the code:

```bash
tar czf "$EXPORT/settings.tar.gz" settings/override settings/siteaccess
```

`settings/override/` can hold passwords and keys (database, mail, API providers): store the archive like a secret.
Extension settings you changed live in the same two directories. The settings each siteaccess actually uses are also
shown on **Setup** > **Ini settings** in the admin ([The settings page](guides/settings-page.md)).

### 3.7 Translations

The translations of the interface are Qt Linguist `.ts` files (XML), one per locale, in `share/translations/<locale>/`
and in `extension/<extension>/translations/<locale>/`. Keep the ones you made or changed:

```bash
tar czf "$EXPORT/translations.tar.gz" share/translations extension/*/translations
```

The translations of the content are versions of the objects in each language: they are in the database dump, in the
xrowextract archive (`--languages=all`, one row per language) and in a package (all languages unless `--language`
limits them).

### 3.8 Your code and designs

Your own extensions, designs and templates, and the description of the code you ran:

```bash
tar czf "$EXPORT/code.tar.gz" extension design composer.json composer.lock
git log -1 --format='%H %d' > "$EXPORT/code-revision.txt"     # a Git installation
```

### 3.9 Check the export

```bash
ls -la "$EXPORT"
(cd "$EXPORT" && find . -type f ! -name SHA256SUMS -exec sha256sum {} + > SHA256SUMS)
```

Restore the database dump into an empty database on another machine and open a few pages before you remove
anything. An export you have not restored is only a hope.

## 4. Rebuild elsewhere

- **The same site on another server** (Exponential 6.0): get the code ([Installing Exponential 6.0](INSTALL.md),
  section 3), restore the database dump, unpack `var-storage.tar.gz`, `settings.tar.gz` and `code.tar.gz`, then run
  `php bin/php/ezpgenerateautoloads.php -e` and `php bin/php/ezcache.php --clear-all --allow-root-user`.
- **Another database system:** create an empty database on the new engine, then insert the `.dba` files of 3.1
  (schema first, then the data), and point `site.ini [DatabaseSettings]` in `settings/override/` at it:

  ```bash
  php bin/php/ezsqlinsertschema.php --type=postgresql --user=USER db_schema.dba NEWDATABASE
  php bin/php/ezsqlinsertschema.php --type=postgresql --user=USER --insert-types=data --schema-file=db_schema.dba db_data.dba NEWDATABASE
  ```

- **Content into a fresh installation:** import the `.ezpkg` of 3.4 (`php ezpm.php import <file>.ezpkg`, or
  **Setup** > **Packages** > **Import new package**), or load the CSV, JSON or XML of 3.3 with the extension `sqliimport` (more than
  60 source formats, in the default `composer.json`) or xrowextract's **Import content file**.
- **Another system:** the CSV, JSON and XML files, the `.ts` translations, the image files and the SQL dump are read by
  any language and any import tool. The table structure is documented by the schema itself
  (`share/db_schema.dba`) and by the `.dba` export.

## 5. Stop the services and the cron jobs

1. **The cron jobs.** Remove the block the installer wrote, then look for lines you added yourself:

   ```bash
   php bin/php/installcrontab.php --remove --dry-run --allow-root-user
   php bin/php/installcrontab.php --remove --apply --allow-root-user
   crontab -l | grep -n "$(pwd)"
   ```

   See [The installer's cron job](bc/6.0/installer-crontab.md).
2. **Exponential Velocity**, if it serves the site (its scheduler stops with it):

   ```bash
   ./console exp:velocity stop --allow-root-user
   ./console exp:velocity status --allow-root-user
   ```

   If you installed a boot service from the example unit, disable it: `systemctl disable --now <your-unit>`.
3. **Background workers** you started for the job queue or other extensions (a systemd unit or a supervisor
   program running `runcronjobs.php` or a console command): stop and disable them.
4. **PHP-FPM**: remove the pool of this site, or reload the service after removing the site in section 6.

## 6. Remove the virtual host

- **Velocity:** disable the site, then remove its file from `sites-available/` once it is disabled
  (`./console exp:velocity layout --allow-root-user` shows every file it uses):

  ```bash
  ./console exp:velocity site disable --apply <name> --allow-root-user
  ```

- **Apache, nginx or a hosting panel:** remove the virtual host or the server block of the site, its PHP-FPM pool,
  and the certificates made only for it; test the configuration (`apachectl configtest`, `nginx -t`) and reload.
- **DNS:** point the names elsewhere, or remove them, once the new site answers.

## 7. Remove the database

Only after the export has been restored somewhere else (3.9). Remove the database and the database user of the
site, and nothing else:

| Database | Remove |
|---|---|
| MySQL or MariaDB | `mysql -u root -p -e "DROP DATABASE DATABASE; DROP USER 'USER'@'localhost';"` |
| PostgreSQL | `dropdb -U postgres DATABASE` and `dropuser -U postgres USER` |
| SQLite | the database file under `var/storage/sqlite3/` goes with the files in section 8 |

A search engine with its own index (Solr for the extension ezfind) is removed with its core or collection.

## 8. Remove the files and the var storage

Everything of the installation is below its root: the code, `vendor/`, `extension/`, `settings/` and `var/` (the
storage, the caches, the logs and an SQLite database). Remove the var storage first if it lives on another volume
(`VarDir` can point outside the root), then the root directory itself. Nothing is written elsewhere, except:

- the configuration of Velocity in `/etc/vc/` (the files of this site in `sites-available/`) and its state in
  `/var/lib/vc/`, `/var/log/vc/` and `/var/cache/vc/`, when Velocity served the site;
- the virtual host, the PHP-FPM pool and the cron lines of sections 5 and 6;
- backups and exports you made yourself.

## 9. Remove a single extension

To remove one extension and keep the installation:

1. Export what it holds (its tables are in the database dump; many extensions have their own export, for example
   packages for classes, the newsletter's CSV exports).
2. Remove it from `ActiveExtensions[]` or `ActiveAccessExtensions[]` in `settings/override/site.ini.append.php` and
   the siteaccesses.
3. Remove its tables if it has an uninstall command (for example `./console exp:help:content uninstall --apply` for help
   maintained as content); read its own documentation for the tables it created.
4. Remove its code: `composer remove se7enxweb/<package>` when Composer installed it, or remove its directory under
   `extension/`.
5. Rebuild the class map and clear the caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

   With Velocity, `./console exp:velocity deploy --allow-root-user` does both and restarts the workers.

## 10. Related pages

- [Installing Exponential 6.0](INSTALL.md) and [Updating Exponential 6.0](UPDATE.md).
- [Operating a site](guides/operating-a-site.md): backup and restore.
- [Packages](guides/packages.md) and [the JSON package format](bc/6.0/package-json-format.md).
- [xrowextract](features/6.0/extensions/xrowextract.md): export, import and move content as files.
- [REST API v1 and v2](guides/rest-api-v1-and-v2.md).
- [About the license of this documentation](README-LICENSE.md).
