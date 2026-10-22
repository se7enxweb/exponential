# Data tables: module tables as part of the system (6.0.15)

Read this page if you write or maintain an extension with tables of its own (a persistent class, a module over it), if
you want to keep such a table in another database than the installation's, or if you administer the databases an
installation talks to. The passwords of those databases live in the [secret store](secret-store.md).

## In short

| | |
|---|---|
| What a data table is | The table of a persistent class: a PHP class that extends `eZPersistentObject` and describes its table in `definition()` (fields, keys, the table name). The kernel has hundreds of them; most extensions add their own. |
| What 6.0.15 adds | A catalogue of every such class with its table, fields, keys and relations to content; a description of each table as its database has it, and where it differs from the definition; named database connections (a SQLite file, MySQL/MariaDB, PostgreSQL, Oracle, SQL Server, MongoDB, here or remote); and persistent classes that read and write on such a connection instead of the installation's database. |
| Where | **Setup > Data tables** (module `datatable`): Data tables, Connections. **Setup > Secrets** (module `secrets`) for their passwords. |
| No command needed | The kernel tables are created from the Data tables page, the catalogue is made when the page is first opened, connections are made and tested in the browser. |
| Classes | `expDataTableCatalogue`, `expDataTableDescriber`, `expDataConnections`, `expDataConnection`, `expKernelTableSchema`, `expPDODB`; `eZPersistentObject::dbFor()` |
| Settings | `datatable.ini` (`[DataTableSettings]`, `[ConnectionSettings]`, `[Connection_<id>]`, `[ClassSettings]`, `[Relation_<class>_<column>]`) |

## The catalogue

`expDataTableCatalogue` lists every persistent class of the installation:

1. It reads the class lists of the autoloader, the kernel's (`autoload/ezp_kernel.php`) and the extensions'
   (`var/autoload/ezp_extension.php`). Test classes and namespaced classes are left out.
2. It reads each file as text and keeps only the files that declare a `definition()` method and a class that
   `extends` another. No file is loaded to find that out.
3. It loads the remaining classes and keeps those that are subclasses of `eZPersistentObject`, are not abstract,
   declare `definition()` themselves (a class that inherits its parent's definition is the parent's table, listed
   once) and whose definition names a table and fields.
4. A class whose `definition()` fails is listed with the error message instead of breaking the page.

Each entry has the class, the table, the file, its origin (`kernel` or the extension's name), the fields (type,
default, required, short name, foreign class and attribute), the keys, the increment key, the default sort, the
function attributes, the connection it uses (empty for the installation's database) and its relations.

The catalogue is kept in the cache directory (`<cache>/datatable/catalogue.json`, the directory name is
`[DataTableSettings] CatalogueCacheDir`) and made again when one of the autoload arrays or `settings/datatable.ini`
changes. **Make the catalogue again** on the Data tables page (policy `datatable/manage`) makes it at once.
`[DataTableSettings] IgnoreClasses[]` leaves classes out, for example one whose `definition()` needs a request.

```php
$catalogue = expDataTableCatalogue::instance();
$catalogue->classes();                    // class => entry
$catalogue->entry( 'eZViewCounter' );     // one entry, or null
$catalogue->byTable( 'ezview_counter' );  // the classes of a table
$catalogue->byOrigin();                   // kernel first, then each extension
$catalogue->pointingAt( 'node' );         // classes with a column that holds a node id
```

### Relations

A relation says that a column holds the id of something else: a content object, a node, a content class, a user, a
section, or a row of another table. They are taken from, in this order (the first one for a column wins):

| Source | How it is written | Shown as |
|---|---|---|
| The definition's `relations` key | `'relations' => array( 'node_id' => array( 'class' => 'eZContentObjectTreeNode', 'field' => 'node_id' ) )`; an optional `'kind'` (`object`, `node`, `class`, `user`, `section`, `table`) | the definition |
| A field's foreign key | `'foreign_class' => 'eZUser', 'foreign_attribute' => 'contentobject_id'` in the field | a foreign key of a field |
| `datatable.ini` | a block `[Relation_<class>_<column>]` with `Kind=`, `Class=` and `Field=` | datatable.ini |
| The column's name | `contentobject_id`, `object_id`, `*_object_id` (object); `node_id`, `main_node_id`, `parent_node_id`, `*_node_id` (node); `contentclass_id`, `class_id` (class); `user_id`, `creator_id`, `owner_id`, `modifier_id` (user); `section_id` (section) | inferred from the name |

The kind follows from the target class: `eZContentObject` is an object, `eZContentObjectTreeNode` a node,
`eZContentClass` a class, `eZUser` a user, `eZSection` a section, any other class a table. A table's own counted-up
key is never taken for a relation. `[DataTableSettings] InferRelations=disabled` switches the guess by name off.

```ini
[Relation_myArchiveRow_contentobject_id]
Kind=object
Class=eZContentObject
Field=id
```

The `relations` key was already part of the definition format (`eZViewCounter` and `eZTipafriendCounter` use it), and
`eZPersistentObject::storeObject()` still reads it as before.

## Describe: the table as its database has it

The page of a class (**Data tables**, then the class) shows its definition next to its table:
`expDataTableDescriber::describe( $entry )` asks the table's own connection

- whether the table exists, from the engine's catalogue (`information_schema` on MySQL, PostgreSQL and SQL Server,
  `sqlite_master` and `PRAGMA table_info` on SQLite, `user_tables` and `user_tab_columns` on Oracle,
  `listCollectionNames()` on MongoDB). Existence is never inferred from a query result: MongoDB answers an empty list
  for a collection that does not exist, and MySQL raises;
- its columns with type, whether they may be empty, and their default. On MongoDB the fields of a sample of documents
  (`[DataTableSettings] MongoSampleSize`, 50);
- the number of rows;
- where table and definition differ:

| Difference | Meaning |
|---|---|
| `missing_table` | The definition's table is not in the database |
| `missing_column` | A field of the definition has no column (not reported on MongoDB, where a sample only has the fields its documents have) |
| `extra_column` | A column the definition does not know: harmless, listed so it is seen |
| `type_mismatch` | An integer field over a text column, or a text field over a number column (not reported on SQLite, which stores any type in any column) |

A field with a short name is matched by it when the long name has no column. The page also shows the relations and
the first 20 rows; columns whose names say they hold a secret (`password`, `hash`, `token`, `secret`, `salt`,
`ciphertext`, `private_key`, `api_key`, `key`) are not shown.

## Connections

A connection is a database the installation can use besides its own. There are three kinds, listed together on
**Setup > Data tables > Connections**:

| Kind | Where it comes from | In the administration |
|---|---|---|
| `site` | The installation's own database (`site.ini [DatabaseSettings]`) | always there, can be tested |
| Declared | A block `[Connection_<identifier>]` of `datatable.ini`, in the settings or an extension | read-only, can be tested |
| Made in the administration | A row of the table `exp_data_connection` | made, changed, tested and removed there (policy `datatable/connections`) |

A declared connection cannot be overridden by a row of the same identifier. An identifier is lower case letters,
digits and `_`, starting with a letter, at most 64 characters; `site` is reserved.

```ini
[Connection_archive]
Name=Archive database
Engine=mysql
Server=db2.example.com
Port=3306
Database=archive
User=archive
Secret=env:ARCHIVE_DB_PASSWORD
Charset=utf8mb4
ReadOnly=false
```

Further keys of a declared connection: `Socket`, `DSN` (a PDO data source name), `Dialect`, `Driver`, `Description`.

### Engines

| Engine | Through | Notes |
|---|---|---|
| `sqlite` | the kernel's SQLite driver (`sqlite3`) | `Database` is the file, relative to the installation; it must end in `.db`, `.sqlite` or `.sqlite3` and be inside one of `[ConnectionSettings] SQLiteDirectories[]` (default `var/datatable`, made when missing). A path that leaves them (`..`, a link elsewhere) is refused. The file is created on first use. |
| `mysql` | the kernel's MySQL/MariaDB driver | `mariadb` is accepted as the same engine |
| `postgresql` | the kernel's PostgreSQL driver | |
| `oracle` | the ezoracle driver when the extension is active and PHP has oci8, else PDO `oci` | `Database` is the service name |
| `sqlserver` | PDO `sqlsrv`, `dblib` or `odbc` (the first one PHP has, or the one named as driver) | untested here: the server this was built on has no SQL Server driver |
| `mongodb` | the kernel's MongoDB driver (`expMongoDB`) | |

An engine the kernel has no driver for goes through `expPDODB`, a generic database driver over PDO: queries, limit
and offset in the engine's dialect, transactions, the last inserted id, escaping, the table list. So every connection
is a database object of the same kind as the installation's own, whatever its engine.
`[ConnectionSettings] Engines[]` limits the engines that may be chosen; the Connections page lists each engine with
whether this server can use it and, when not, why (the missing PHP extension or driver).

`[ConnectionSettings] AllowedHosts[]` limits the servers a connection may name (empty, or `*`: any).
`[ConnectionSettings] ConnectTimeout` (5 seconds, at most 60) is how long a connection waits for its server.

### The password is a secret's name

A connection never holds a password. Its `Secret` (the "Password: the name of a secret" field) names a secret of the
[secret store](secret-store.md): a secret stored there, encrypted, or `env:VARIABLE` for an environment variable of
the PHP process. On the connection's page a password can be typed in for a test only; with **Store it in the secret
store when saving** (policy `secrets/manage` too) it is stored, encrypted, under the name given, or under
`datatable.<identifier>` when no name was given, and the connection keeps that name. A DSN that holds a password is
refused.

### Test connection

**Test** on the list and **Test connection** on a connection's page open the connection, ask the server's version
and its tables, and close it again. The result says the driver (`ez` for the kernel's own, `pdo`), the time in
milliseconds, the version and the number of tables, or the error. A server that does not answer on its port within the
timeout is reported as such before the driver is asked (a driver can wait a minute otherwise). Every message is
masked: the value of the connection's secret, and of a password typed in for the test, never appears.

```php
$result = expDataConnections::test( 'archive' );
// ok, engine, via, version, tables, ms, error
```

### Using a connection in code

```php
$db = expDataConnections::db( 'archive' );     // the database of the connection, an eZDBInterface
$rows = $db->arrayQuery( 'SELECT id, title FROM archive_item', array( 'limit' => 10 ) );
```

`db()` opens each connection once per request and keeps it for the rest of it; `db( 'site' )` and `db( '' )` give the
installation's database. A connection that does not exist or cannot be opened throws a `RuntimeException` whose
message names the connection and never holds a secret's value.

On Exponential Velocity a worker serves one request after the other: the connections of a request are closed when the
next request begins, so a worker never keeps a socket from one request to the next, nor one its parent opened before
it was forked.

## Persistent classes on a connection

A persistent class names its connection in its definition:

```php
public static function definition()
{
    return array(
        'fields' => array( ... ),
        'keys' => array( 'id' ),
        'increment_key' => 'id',
        'class_name' => 'myArchiveItem',
        'name' => 'archive_item',
        'connection' => 'archive',
    );
}
```

or the settings name it for a class that does not (an extension's class, without editing it):

```ini
[ClassSettings]
Connection[myArchiveItem]=archive
```

`eZPersistentObject` then reads and writes the class's rows there: `fetchObject()`, `fetchObjectList()`, `count()`,
`store()`, `remove()`, `removeObject()`, `updateObjectList()`, `newObjectOrder()` and `reorderObject()` all ask
`eZPersistentObject::dbFor( $definition )`. Everything the call builds is escaped for that connection, not for the
installation's database (MySQL and SQLite escape a quote differently): the conditions, the arrays of values, the
reordering and the short field names. A class of the installation's database read in the middle of such a call (a
function attribute, a relation) goes back to the installation's database for that read.

A class without a connection costs two `isset()` checks and one lookup more than before; nothing else changes for it.

The settings of `[ClassSettings]` are read once per process. On Velocity a change of them reaches the workers with
the next restart.

## The administration

| Page | Address | Policy |
|---|---|---|
| Data tables: the catalogue, by kernel and extension, filtered by words and origin | `datatable/dashboard` | `datatable/read` |
| One class: definition, connection, table, differences, relations, first rows | `datatable/table/<class>` | `datatable/read` |
| Connections, and the engines of this server | `datatable/connections` | `datatable/connections` |
| One connection: new, change, test, remove | `datatable/connection/<identifier>` (`new` for a new one) | `datatable/connections` |
| Secrets | `secrets/list` | `secrets/manage` |

| Policy | Allows |
|---|---|
| `datatable/read` | the catalogue and the page of each class |
| `datatable/manage` | creating the kernel tables, making the catalogue again |
| `datatable/connections` | making, changing, testing and removing connections |
| `datatable/sync` | the synchronisation between tables and content (see "Coming next") |

Only the Administrator role has them in a new installation. The Setup menu has a group **Data tables** with
**Data tables**, **Connections** and **Secrets**.

## The kernel tables

| Table | Holds |
|---|---|
| `exp_data_connection` | the connections made in the administration: identifier (unique), name, engine, server, port, database, user, the secret's name, options (JSON: charset, socket, driver, DSN), description, read-only, who made and changed it and when |
| `exp_secret` | the [secret store](secret-store.md) |

A new installation has both from the kernel schema. An installation made before 6.0.15 gets them from the update
files of MySQL, PostgreSQL and SQLite (`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, which create them
only when they are missing), or from the Data tables page: while one of them is missing the page says so, and
**Create the tables** (policy `datatable/manage`) creates them on the installation's database through the engine's
schema handler, on every engine the kernel supports, MongoDB included. `expKernelTableSchema` does this for any table
of a `.dba` schema:

```php
expKernelTableSchema::exists( 'exp_secret' );          // true, false, or null when it cannot be told
expKernelTableSchema::install( array( 'exp_data_connection', 'exp_secret' ), null, null, $messages );
expKernelTableSchema::tables( $db );                   // every table of a connection, from its catalogue
```

## Example: a class on its own SQLite file

1. **Setup > Data tables**: if the page says the tables are missing, **Create the tables**.
2. **Connections > New connection**: identifier `archive`, engine SQLite, database `var/datatable/archive.sqlite`.
   **Test connection** reports the SQLite version and 0 tables; **Save**.
3. The extension's class names the connection in its definition (`'connection' => 'archive'`), or the settings do
   (`[ClassSettings] Connection[myArchiveItem]=archive`).
4. The table is created on that connection, for example from the extension's `share/db_schema.dba`:

   ```php
   $db = expDataConnections::db( 'archive' );
   expKernelTableSchema::install( array( 'archive_item' ), $db, 'extension/myarchive/share/db_schema.dba' );
   ```

5. From then on the class works as any other:

   ```php
   $item = new myArchiveItem( array( 'title' => "O'Brien's notes", 'node_id' => 42 ) );
   $item->store();
   $items = myArchiveItem::fetchObjectList( myArchiveItem::definition(), null, array( 'node_id' => 42 ) );
   ```

6. **Data tables** lists `myArchiveItem` with the connection `archive`; its page shows the table in the SQLite file,
   its rows, and `node_id` as a relation to a node.

## Coming next

The next steps of the data tables build on this layer:

- installing and upgrading a table's schema from a `.dba` file on any connection from the administration, with the SQL
  shown first, per engine;
- the relations in the content view: the rows of every data table that point at the item shown;
- a RAD wizard that makes a new module table (the persistent class, its schema per engine, the administration pages
  with their policies, fetch functions and a JSON endpoint) from a list of fields, a content class or the fields of an
  import source;
- the synchronisation between tables and content: a one-off copy either way, a scheduled one-way sync by unique keys, a
  live mirror on publish, move and remove, and a two-way sync with conflict rules and a queue of conflicts to resolve
  in the administration.

## Tests

In `tests/tests/kernel/classes/datatable/` (testsuite kernel-classes, no installation needed): the catalogue, the
description and its differences, the connections (values, checks, plans, data source names, testing on SQLite),
persistent classes on a SQLite connection (store, fetch, update, remove, escaping), and the two kernel tables, which
must be the same in `share/db_schema.dba`, the persistent classes, the three kernel schemas and the three update files.
`tests/tests/lib/ezdb/expPDODBTest.php` tests the PDO driver on pdo_sqlite and the SQL of the other dialects.
