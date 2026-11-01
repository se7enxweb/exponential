# Migrations: SQL and settings

This page is the guide to the migrations that change the database schema, rows and settings: the portable schema and
data steps that every engine understands, SQL migration files with a file per engine, the `sql` step, and the INI
migrations and `settings` step that change `settings/override` and `settings/siteaccess/<name>`. Every key of the step
types is on their generated reference pages: [`schema`](../examples/migration-steps/schema.md),
[`data`](../examples/migration-steps/data.md), [`sql`](../examples/migration-steps/sql.md) and
[`settings`](../examples/migration-steps/settings.md).

[Guides](README.md) · [Migrations](migrations.md) · Related: [YAML migration steps](migrations-yaml-steps.md),
[The file format](../specifications/6.0/migration-file-format.md), [Rollback and captions](migrations-rollback-and-captions.md)

## Which one to use

| You want to | Use | Runs on |
|---|---|---|
| add a table, a column or an index | the step type `schema` in a YAML migration | every engine, through the kernel's schema handlers |
| add, change or remove rows | the step type `data` | every engine, values quoted by the connection |
| run SQL you wrote for one engine | an SQL migration (`.sql`, `.<engine>.sql`, `.mongodb.json`) or the step type `sql` | the engines you wrote it for |
| change a setting | an INI migration (`.ini`) or the step type `settings` | the files, not the database |

Prefer `schema` and `data`: written once, they run on MySQL/MariaDB, PostgreSQL, SQLite, Oracle and MongoDB alike, and
each of them looks before it changes anything, so a migration that stopped half way can simply run again.

## The portable schema: the step type `schema`

```yaml
# description: Ratings of news articles
steps:
    - type: schema
      mode: create_table
      table: exp_news_rating
      columns:
          id: { type: auto_increment }
          contentobject_id: { type: int, not_null: true, default: 0 }
          rating: { type: int, not_null: true, default: 0 }
          comment: { type: varchar, length: 255, default: "" }
      indexes:
          exp_news_rating_object: { columns: [ contentobject_id ] }
    - type: schema
      mode: add_column
      table: exp_news_rating
      column: created
      definition: { type: int, not_null: true, default: 0 }
    - type: schema
      mode: add_index
      table: exp_news_rating
      index: exp_news_rating_created
      columns: [ created ]
```

The columns are written as the kernel writes its own tables in `share/db_schema.dba`: `type` is one of
`auto_increment`, `int`, `bigint`, `float`, `double`, `decimal` (length `"10,2"`), `char`, `varchar` (both with a
length) and `longtext` (`text` is taken as `longtext`); `not_null` and `default` as there. An `auto_increment` column
is the primary key unless `primary_key` names others.

The step hands the table to the engine's schema handler (`eZDbSchema`), the same code that installs the kernel's tables
(`expKernelTableSchema`) and that the upgrade steps of `exp:upgrade` use to add columns: `CREATE TABLE` with
`AUTO_INCREMENT` on MySQL/MariaDB, a sequence on PostgreSQL and Oracle, `AUTOINCREMENT` on SQLite, and on MongoDB a
collection with its indexes. Before it changes anything, it asks the engine's catalogue (`expUpgradeCatalog`):
a table, a column, or an index on the same columns that is there already is reported as done, and so is something to
drop that is gone.

| Mode | Undoes itself on a rollback |
|---|---|
| `create_table` | yes: drops the table |
| `add_column` | yes: drops the column |
| `add_index` | yes: drops the index |
| `drop_table`, `drop_column`, `drop_index` | no: give the migration a `down:`, or restore its caption |

On MongoDB a collection has no columns: `add_column` gives the documents that lack the field its default (when the
column has one), `drop_column` removes the field from every document. On SQLite before 3.35, which has no
`DROP COLUMN`, the column is dropped the way SQLite's documentation describes: a new table without it, the rows copied,
the old table dropped and the new one renamed, its other indexes made again.

## Rows: the step type `data`

```yaml
    - type: data
      mode: ensure                  # insert, ensure, update, delete
      table: ezsite_data
      key: [ name ]                 # the columns that find a row
      rows:
          - { name: news_list_limit, value: "20" }
    - type: data
      mode: insert
      table: exp_news_rating
      rows:
          - { contentobject_id: "${ref.article}", rating: 5, comment: "It's good" }
      id_column: id
      references: { rating: id }
    - type: data
      mode: update
      table: ezcontentclass
      where: { identifier: [ news, blog_post ] }   # equal; a list: one of; null: is null
      values: { always_available: 1 }
```

Values are strings, numbers, `true`/`false` (1 and 0) or `null`. On the SQL engines the connection quotes them
(`eZDB::escapeString()`), so a value from a reference is safe here; on MongoDB they are stored as they are, through the
kernel's driver (the counter of an id field is used as for any other insert). `update` and `delete` need `where`, or
`all: true` for every row. The step does not undo itself.

## SQL migration files

An SQL migration is a file, or a set of files with the same id, next to your other migrations:

```text
migrations/20261107100000_news_index.sql             every SQL engine
migrations/20261107100000_news_index.mysql.sql       MySQL/MariaDB only (wins over the portable file)
migrations/20261107100000_news_index.pgsql.sql       PostgreSQL (.postgresql.sql works too)
migrations/20261107100000_news_index.sqlite.sql      SQLite
migrations/20261107100000_news_index.oracle.sql      Oracle
migrations/20261107100000_news_index.mongodb.json    MongoDB
```

```sql
-- description: An index for the news list
-- requires: 20261030090000_news_class
CREATE INDEX exp_news_list ON ezcontentobject ( published );
-- down
DROP INDEX exp_news_list;
```

The engine's own file runs, else the portable `.sql` file. A portable file runs on the SQL engines only: without a
`.mongodb.json` next to it (or `engines:` in the header) the migration is not for MongoDB, and the runner leaves it
out there with that reason. Statements end with `;` at the end of a line; lines that start with `--` are comments.
The part after a line `-- down` undoes the migration (`exp:migration:rollback`); without it, only the caption taken
before the run takes it back. When one engine's file has a down part and another has none, `exp:migration:check`
warns.

A `.mongodb.json` file is a list of commands, or `{ "up": [ ... ], "down": [ ... ] }`:

```json
{
    "up": [
        { "create_index": "ezcontentobject", "keys": { "published": 1 }, "name": "exp_news_list" }
    ],
    "down": [
        { "drop_index": "ezcontentobject", "name": "exp_news_list" }
    ]
}
```

The commands are `insert` (with `documents`), `update` (`filter`, `set`, `unset`), `delete` (`filter`),
`create_collection`, `drop_collection`, `create_index` (`keys`, `name`, `unique`) and `drop_index` (`name`).

An SQL migration runs as a YAML migration of one `sql` step that names its file, so its plan, dry run, log, record and
checksum work as for every other migration. The checksum is that of the file that runs on the engine.

## The step type `sql`

The same in a YAML migration, next to other steps:

```yaml
    - type: sql
      sql: "UPDATE ezcontentclass SET always_available = 1 WHERE identifier = 'news';"
      mongodb:
          - { update: ezcontentclass, filter: { identifier: news }, set: { always_available: 1 } }
      missing: fail                 # or skip, on an engine the step has nothing for
    - type: sql
      file: sql/news_cleanup.sql    # a file next to the migration; sql/news_cleanup.mysql.sql wins on MySQL
```

`sql:` is for every SQL engine; `mysql:`, `postgresql:`, `sqlite:` and `oracle:` win over it on their engine. A
placeholder (`${ref.name}`) in the text is replaced as it is, without quotes: put values into rows with `data`, not by
writing them into SQL.

## What is not transactional

Each migration runs in a transaction where the engine has one, and a failure rolls it back. How much that covers
depends on the engine:

| Engine | Data | Schema changes (`CREATE`, `ALTER`, `DROP`, `RENAME`, `TRUNCATE`) |
|---|---|---|
| PostgreSQL, SQLite | rolled back | rolled back with the rest |
| MySQL/MariaDB, Oracle | rolled back | committed at once: they stay when a later statement fails |
| MongoDB | stays | stays (no transactions) |

On MySQL/MariaDB and Oracle the step says so in the log ("committed the schema change at once"), and the runner adds
to the failure "rolled back what could be: mysql keeps schema changes made before the failure". Restore the caption
taken before the run, or write the migration so it can run again: `schema` steps check before they change anything.

A statement that cannot run in a transaction (`CREATE INDEX CONCURRENTLY` on PostgreSQL, `VACUUM` on SQLite) needs a
migration without one: `-- transactional: no` in the header of an SQL file (`# transactional: no` in an INI or YAML
file, or `transactional: false` in a YAML document).

## Settings: INI migrations and the step type `settings`

```ini
#?ini charset="utf-8"?
# description: News settings and the admin's start page
# environments: staging, production

[news.ini/NewsSettings]
ListLimit=20
ShownClasses[]
ShownClasses[]=article
ShownClasses[]=blog_post

[siteaccess:admin site.ini/SiteSettings]
DefaultPage=content/dashboard

[site.ini/ExtensionSettings]
ActiveExtensions[]=expnews
-DebugByIP
```

Each block names its file (`[news.ini/NewsSettings]` is `settings/override/news.ini.append.php`) and, for a siteaccess,
the siteaccess (`[siteaccess:admin site.ini/SiteSettings]`). `Var=value` sets a value, `Var[]` followed by
`Var[]=...` makes the array exactly these values, `Var[]=value` alone adds a value, `Var[key]=value` sets a key of a
hash, and a line starting with `-` removes (`-Var`, `-Var[key]`, `-Var[]=value`). The same as a step:

```yaml
    - type: settings
      file: site.ini
      siteaccess: admin             # without: settings/override
      set:
          SiteSettings: { DefaultPage: content/dashboard }
          RegionalSettings: { SiteLanguageList: [ eng-GB, ger-DE ] }
      add:
          ExtensionSettings: { ActiveExtensions: [ expnews ] }
      remove:
          DebugSettings: [ DebugByIP ]
```

Settings are written only to `settings/override` and `settings/siteaccess/<name>`: never the shipped
`settings/*.ini`, never an extension's settings. The kernel's INI editor (`expIniEditor`, the one `./console exp:ini`
uses) writes them:

- only the lines that change; comments and the order of the file stay;
- a copy of the file as it was first, under `var/backup/ini/<time>/` and readable by its owner only;
- an atomic write that keeps the file's owner, group and mode;
- each changed variable in the audit log (`system.setting.write`, a secret recorded as `[secret]`);
- nothing at all when the file says so already, so a second run changes nothing.

A dry run lists the changes and writes nothing; a secret's value is masked there. The INI cache is cleared after the
run. Settings steps do not undo themselves: a rollback restores the caption taken before the run, which includes the
settings folder. With `ManagedByDeployment=enabled` in `site.ini [ConfigurationSettings]`, the web interface refuses
the write; command line runs still write.

## Read on

- [All step types](../examples/migration-steps/README.md), one generated page each
- [The file format](../specifications/6.0/migration-file-format.md): the SQL migration and the INI migration
- [YAML migration steps](migrations-yaml-steps.md)
- [Rollback and captions](migrations-rollback-and-captions.md)
