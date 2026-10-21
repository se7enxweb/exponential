---
title: "MySQL and MariaDB: utf8mb4, the connection charset and InnoDB"
audience: dba
category: database-administration
order: 15
summary: The character set and storage engine details of Exponential on MySQL and MariaDB - why the database should be utf8mb4 while the connection uses utf8 (utf8mb3), what the charset warning means and how to see it, converting old databases with ezconvertdbcharset.php, and checking and converting table types with ezconvertmysqltabletype.php.
keywords: MySQL, MariaDB, utf8mb4, utf8mb3, utf8, connection charset, Charset, emoji, could not set the connection charset, AlwaysLog warning, ezconvertdbcharset, ezconvertmysqltabletype, InnoDB, MyISAM, default_storage_engine, latin1
type: help
---
# MySQL and MariaDB: utf8mb4, the connection charset and InnoDB

This page is for database administrators who look after the character set and the table types of an Exponential
database on MySQL or MariaDB, on a new install or on an old database brought forward. It answers: which character set
to create, what the connection really uses, what happens when the connection charset cannot be set, and how to check
and convert table types and character sets. Sources:
[Character set and storage engine](../../install/09-databases.md#character-set-and-storage-engine),
`lib/ezdb/classes/ezmysqlcharset.php` and the two conversion scripts.

## Contents

1. [Create the database in utf8mb4](#create-the-database-in-utf8mb4)
2. [The connection charset](#the-connection-charset)
3. [When the connection charset cannot be set](#when-the-connection-charset-cannot-be-set)
4. [InnoDB](#innodb)
5. [Converting an old database to UTF-8](#converting-an-old-database-to-utf-8)
6. [Checklist](#checklist)

## Create the database in utf8mb4

```sql
CREATE DATABASE exponential CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

The setup wizard checks the database's default character set and accepts `utf8mb4` as UTF-8
(`kernel/setup/steps/ezstep_installer.php`); it refuses a database in `latin1` or another single-byte character set
("cannot be used, the setup wizard wants to create the site in ..."). The installer creates its tables without a
character set of their own, so they take the database's default.

## The connection charset

`[DatabaseSettings] Charset=utf-8` (the default) is mapped to MySQL's `utf8` for the connection. MySQL and MariaDB
treat that name as `utf8mb3`, three bytes per character, so characters outside the Basic Multilingual Plane (emoji,
some CJK extensions) cannot pass through the connection even when the tables are `utf8mb4`. Keep the database in
`utf8mb4` anyway: it costs nothing and is ready for a connection change. The project documents this as a known gap of
the engine ([9.7 Comparison](../../install/09-databases.md#97-comparison)); there is no setting in 6.0.x that switches
the connection to `utf8mb4`.

## When the connection charset cannot be set

The driver asks the connection which charset it really has, tries once more, and if it still differs records:

```text
Connection warning: could not set the connection charset to 'utf8'; it is '...'. Text will be transliterated to that character set on its way out of the database.
```

The site keeps running, and text comes back with `?` in place of the characters the other charset lacks. It is a
warning, not an error: it is written to `var/log/warning.log` only when warnings are logged. Enable that while you set
up a MySQL site:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
AlwaysLog[]=warning
```

Then load a page and search `var/log/warning.log` for "could not set the connection charset". **Setup > System
information** also warns when the connection's character set is not UTF-8.

## InnoDB

Transactions need InnoDB, and the installer creates InnoDB tables when the server offers it. An older installation
may still have MyISAM tables:

```bash
php bin/php/ezconvertmysqltabletype.php --list --allow-root-user
php bin/php/ezconvertmysqltabletype.php --newtype=InnoDB --allow-root-user
```

The console name is `exp:ezconvertmysqltabletype`. The script refuses to run on any engine but MySQL and MariaDB
(`kernel/private/classes/commands/ezconvertmysqltabletype.php`). Take a backup first; converting a large table
rebuilds it.

The MySQL update files of an upgrade start with `SET default_storage_engine=InnoDB;`, which every current MySQL and
MariaDB accepts. Copies from before October 2026 said `SET storage_engine=InnoDB;`, a spelling MySQL removed in 5.7.5
and MariaDB in 12.0; take the current files ([Upgrades and the SQL update files](upgrades-and-sql-update-files.md)).

## Converting an old database to UTF-8

A database from an old release in a single-byte character set is converted with `bin/php/ezconvertdbcharset.php`
(`exp:ezconvertdbcharset`), "Changes your Exponential database tables to use UTF8". It supports MySQL, PostgreSQL and
Oracle (`checkDBDriver()` in `kernel/private/classes/commands/ezconvertdbcharset.php`) and refuses other engines.
Options from its `--help`:

| Option | Meaning |
|---|---|
| `--collation=VALUE` | Collation of the converted database; the default is `utf8_general_ci`, so pass `utf8mb4_unicode_ci` or your own choice explicitly |
| `--iconv-character-set=VALUE` | When the database and iconv name a charset differently (for example `windows-1252` for iconv and `iso-8859-1` for MySQL) |
| `--extra-xml-attributes`, `--extra-xml-data`, `--extra-serialized-data` | Custom datatypes and tables that keep XML or serialized data |
| `--skip-class-translations` | For databases from releases before content class translations |
| `--log-filename=VALUE` | Log the iconv conversions |

Run it on a copy first, after a backup, and compare a sample of texts with accents before and after. The upgrade notes
mention it as the tool for tables that are not UTF-8 yet ([11.3](../../install/11-upgrading.md#per-database-notes)).

## Checklist

- [ ] The database default is `utf8mb4`; every table is InnoDB.
- [ ] `var/log/warning.log` shows no "could not set the connection charset" with `AlwaysLog[]=warning` on.
- [ ] Editors know that emoji and other 4-byte characters cannot be stored on MySQL and MariaDB through this connection.
- [ ] Old update files with `SET storage_engine` are replaced by current ones.

## Related pages

- [Setting up MySQL and MariaDB](mysql-and-mariadb-setup.md)
- [Character sets and collations](character-sets-and-collations.md)
- [Upgrades and the SQL update files](upgrades-and-sql-update-files.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
