# Data tables: module tables as part of the system (6.0.15)

Read this page if you write or maintain an extension with tables of its own (a persistent class, a module over it), if
you want to keep such a table in another database than the installation's, or if you administer the databases an
installation talks to. The passwords of those databases live in the [secret store](secret-store.md).

## In short

| | |
|---|---|
| What a data table is | The table of a persistent class: a PHP class that extends `eZPersistentObject` and describes its table in `definition()` (fields, keys, the table name). The kernel has hundreds of them; most extensions add their own. |
| What 6.0.15 adds | A catalogue of every such class with its table, fields, keys and relations to content; a description of each table as its database has it, and where it differs from the definition; named database connections (a SQLite file, MySQL/MariaDB, PostgreSQL, Oracle, SQL Server, MongoDB, here or remote); and persistent classes that read and write on such a connection instead of the installation's database. Then: a table's schema created or upgraded on any connection from the administration, its rows browsed, the rows that point at an item shown in the content view, one writer for every tool that puts rows into a table, and fetch functions for templates. And a wizard that makes a new table with its class, schema, administration, policies, fetch functions and JSON endpoint, and installs it from the browser. And the synchronisation between tables and content: once, scheduled or live (on publish, move and removal), in either direction or both, with conflict rules and a queue of conflicts to resolve. |
| Where | **Setup > Data tables** (module `datatable`): Data tables, Connections, Synchronisations. **Setup > Secrets** (module `secrets`) for their passwords. **Setup > RAD > Module table** (`setup/moduletable`) for a new table. |
| No command needed | The kernel tables are created from the Data tables page, the catalogue is made when the page is first opened, connections are made and tested in the browser. |
| Classes | `expDataTableCatalogue`, `expDataTableDescriber`, `expDataConnections`, `expDataConnection`, `expKernelTableSchema`, `expDataTableSchema`, `expDataTableWriter`, `expDataTableRelation`, `expDataTableFunctionCollection`, `expPDODB`; `eZPersistentObject::dbFor()`; `expModuleTableWizard`, `expModuleTableCode`, `expDataTableFieldSource`, `expDataTableFieldSources`; `expDataSync`, `expDataSyncDefinition`, `expDataSyncLink`, `expDataSyncConflict`, `expDataSyncTask`, `expDataSyncListener`, `expDataTableMirrorType` |
| Settings | `datatable.ini` (`[DataTableSettings]`, `[ConnectionSettings]`, `[Connection_<id>]`, `[ClassSettings]`, `[Relation_<class>_<column>]`); `cronjob.ini` (the part `datatable`, and `datatablesync.php` in `frequent`); `workflow.ini` (`event_expdatatablemirror`) |

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
| The column's name | `contentobject_id`, `object_id`, `*_object_id` (object); `node_id`, `main_node_id`, `parent_node_id`, `*_node_id` (node); `contentclass_id`, `class_id` (class); `user_id`, `creator_id`, `owner_id`, `modifier_id`, and `creator_`, `modifier_`, `owner_`, `author_` or `editor_` followed by `contentobject_id` or `object_id` (user); `section_id` (section) | inferred from the name |

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

## Schema: creating and upgrading tables on a connection

`expDataTableSchema` puts a schema on a connection. The schema comes from:

- a persistent class's `definition()` (`expDataTableSchema::fromDefinition()`): `integer` becomes `int(11)`, `float`
  a float, `text` a long text, anything else a `varchar(255)` (other lengths through the second argument); the
  counted-up key is an auto-increment column, the keys are the primary key, each relation column gets an index (the
  content view asks "which rows point at this item"), and the optional `'unique' => array( '<index>' => array( columns ) )`
  of the definition gives unique indexes. Index names are at most 30 characters (Oracle's limit);
- an extension's `share/db_schema.dba` (`expKernelTableSchema::read()`).

`plan()` says, table by table, what applying the schema would do, with the SQL, and runs nothing:

| Action | When |
|---|---|
| `create` | the table is not there |
| `upgrade` | columns or indexes are missing (they are added) |
| `none` | the table is as the schema says, or only differs in what is held |
| `unknown` | the connection does not list its tables to this user |

Dropping a column or an index the schema does not have, and changing a column or an index that differs, are **held**:
listed, not done, unless asked (`'drop' => true`, `'alter' => true`; the two boxes of the page). `apply()` runs the
plan and stops a table at its first failing statement.

```php
$schema = expDataTableSchema::fromDefinition( myArchiveRow::definition() );
$db = expDataConnections::db( 'archive' );
$plan = expDataTableSchema::plan( $schema, $db );             // action, statements, held, notes per table
$result = expDataTableSchema::apply( $schema, $db );          // ok, tables => action, ok, done, errors
$sql = expDataTableSchema::installSql( $schema );             // engine => SQL, for sql/<engine>/schema.sql
```

| Engine | Create | Upgrade |
|---|---|---|
| MySQL/MariaDB, PostgreSQL, SQLite | the kernel's schema handlers (`eZMysqlSchema`, `eZPgsqlSchema`, `eZSQLiteSchema`) | `eZDbSchemaChecker::diff()` against the live table and the handler's upgrade SQL |
| Oracle | ezoracle's schema handler when the extension is active; otherwise (PDO oci) the statements of `oracleCreate()`: `VARCHAR2(n CHAR)`, `CLOB`, `BINARY_DOUBLE`, and a sequence with a `BEFORE INSERT` trigger for the counted-up key, named as `expPDODB::oracleSequence()` reads it | through ezoracle as above; through PDO the missing columns are added |
| SQL Server | `sqlServerCreate()`: `INT IDENTITY(1,1)`, `NVARCHAR(n)`, `NVARCHAR(MAX)`, bracketed names | the missing columns are added |
| MongoDB | the collection is made by its first write; the indexes by the schema handler (`expMongoSchema`) | the indexes are made again when missing; documents need no column |

The page **Setup > Data tables > Schema** (`datatable/schema`, policy `datatable/manage`) takes a class (of an
extension) or an extension's schema file, and a connection (the class's own by default; a read-only connection cannot
be chosen). **Show what would be done** lists the plan with its SQL; **Apply** runs it and reports each table. The
install SQL of every engine (MySQL, PostgreSQL, SQLite, Oracle, SQL Server) is shown below, to copy into an extension's
`sql/<engine>/schema.sql`. A class's page links to it as **Create the table** or **Upgrade the table**.

## Rows

`datatable/rows/<class>` (policy `datatable/read`) lists the rows of a class's table, 25 a page, on the class's
connection. `/(<field>)/<value>` keeps the rows where one field has one value (the content view's tab links there).
A value of a relation column links to what it points at: the node's or the object's page, the class, the section, or
the rows of the other table. Columns whose names say they hold a secret (`password`, `hash`, `token`, `secret`, `salt`,
`ciphertext`, `private_key`, `api_key`, `key`) are left out, here and in the first rows of a class's page. The page
only reads.

## Relations in the content view

The node view of the administration has a tab **Data tables** (`admininterface.ini [AdditionalTab_datatable]`) for a
user with `datatable/read`, shown only when some row points at the node or its object. It lists, per table and
column, how many rows point there and the first ten, with a link to all of them
(`expDataTableRelation::pointingAt()`, the fetch function `pointing_at`).

Only the tables of extensions and of named connections are asked: the kernel's own tables relate to content
everywhere, and a class of an extension over a kernel table is left out too. Only declared relations are asked (the
definition's `relations`, a field's `foreign_class`, a `[Relation_*]` block), one count per relation and node view;
`datatable.ini [DataTableSettings] ContentTabInferred=enabled` also asks the relations inferred from column names.

`expDataTableRelation::target( $relation, $value )` gives what a value points at (an `eZContentObject`,
`eZContentObjectTreeNode`, `eZContentClass`, `eZUser`, `eZSection`, or the row of the other table) and `url()` its
address in the administration.

## Writing rows

Every tool that puts rows into a data table (an import, the synchronisation, a module of its own) goes through
`expDataTableWriter`, never raw SQL: it checks the rows against the class's definition and writes on the class's
connection.

```php
$writer = new expDataTableWriter( 'myArchiveRow', array( 'keys' => array( 'remote_id' ), 'mode' => 'upsert' ) );
$result = $writer->write( array(
    array( 'remote_id' => 'a-1', 'title' => 'First', 'node_id' => '@node:4f9c2e…' ),
    array( 'remote_id' => 'a-2', 'title' => 'Second', 'contentobject_id' => '@path:news/second' ),
) );
// inserted, updated, unchanged, skipped, failed, errors (list), ids (row number => key)
```

| Option | Meaning |
|---|---|
| `keys` | the fields a row is matched by; the class's keys when none are given (the counted-up key is left out of several) |
| `mode` | `insert` (only new rows), `update` (only existing ones), `upsert` (both, the default), `replace` (an existing row is removed and written again) |
| `dry_run` | check and count, write nothing |
| `chunk` | rows per transaction (default 200) |
| `unknown` | `refuse` (an unknown field fails the row, the default) or `ignore` |

Values are typed by the definition (`integer`, `float`; a date in an integer field becomes a timestamp; an array in a
text field becomes JSON). A row that does not change anything is counted as unchanged and not written. A relation
column may hold a reference instead of an id: `@object:<remote id>`, `@node:<remote id>` or `@path:<url alias>`; a
reference nothing has fails the row. Each chunk is one transaction; when the database refuses a row, the chunk is
rolled back and written again row by row, each in a transaction of its own, so that bad row fails alone and the others
are written. An error names the row's number and the field, never a value.

On SQLite, `eZPersistentObject` writes a new row with `INSERT OR REPLACE`: a row whose unique column another row has
already replaces that row instead of failing (MySQL and PostgreSQL refuse it). Match by the unique column (`keys`) to
update the existing row instead.

A class the catalogue does not list yet (made after the catalogue was built) is read from its own definition.

## In templates

The module `datatable` has five fetch functions (`kernel/datatable/function_definition.php`,
`expDataTableFunctionCollection`):

```
{def $rows = fetch( 'datatable', 'list', hash( 'class', 'myArchiveRow',
                                             'conditions', hash( 'state', 'open' ),
                                             'sort', array( 'created', false() ),
                                             'offset', 0, 'limit', 20 ) )}
{fetch( 'datatable', 'count', hash( 'class', 'myArchiveRow', 'conditions', hash( 'state', 'open' ) ) )}
{def $row = fetch( 'datatable', 'row', hash( 'class', 'myArchiveRow', 'key', hash( 'id', 42 ) ) )}
{def $found = fetch( 'datatable', 'pointing_at', hash( 'object_id', $node.contentobject_id, 'node_id', $node.node_id ) )}
{if fetch( 'datatable', 'can', hash( 'function', 'read' ) )}…{/if}
```

`list`, `count` and `row` read the classes of extensions and of named connections for anyone who may see the
template; a class of the kernel's own database only for a user with `datatable/read`. Conditions are equality on
fields of the class (other keys are ignored), `sort` is a field and ascending (`true()`) or descending, `limit` is
between 1 and 500. `row` needs every key it is given to be a field. `pointing_at` needs `datatable/read`; `can` answers
for `read`, `manage`, `connections` and `sync`.

## In the registry

The data tables are a tool of the transfer registry ([transfer-registry.md](transfer-registry.md)):
`[Service_transfer.datatable]` in `kernelservices.ini`, named **Data tables**, direction both, scopes data, class and
subtree, entry page `datatable/dashboard` (policy `datatable/read`), the `secrets` module belonging to it, two Setup
entries ("Create or upgrade a table on a database connection…" with `datatable/manage`, "Browse the tables of the
persistent classes…"), and 7x SQLI Import as its round-trip partner, so each appears in the other's **Looking for…?**
box.

## The administration

| Page | Address | Policy |
|---|---|---|
| Data tables: the catalogue, by kernel and extension, filtered by words and origin | `datatable/dashboard` | `datatable/read` |
| One class: definition, connection, table, differences, relations, first rows | `datatable/table/<class>` | `datatable/read` |
| Connections, and the engines of this server | `datatable/connections` | `datatable/connections` |
| One connection: new, change, test, remove | `datatable/connection/<identifier>` (`new` for a new one) | `datatable/connections` |
| The rows of one table, 25 a page, filtered by one field | `datatable/rows/<class>`, `datatable/rows/<class>/(<field>)/<value>` | `datatable/read` |
| Create or upgrade tables on a connection | `datatable/schema`, `datatable/schema/(class)/<class>`, `datatable/schema/(extension)/<name>` | `datatable/manage` |
| Synchronisations, their runs, Run now; creating their tables | `datatable/syncs` | `datatable/sync` |
| One synchronisation: new, change, run, remove | `datatable/sync/<id>` (`new` for a new one, `new/(class)/<class>/(content_class)/<identifier>` from another tool) | `datatable/sync` |
| The open conflicts of the rule "ask" | `datatable/conflicts`, `datatable/conflicts/(sync)/<id>` | `datatable/sync` |
| Secrets | `secrets/list` | `secrets/manage` |

| Policy | Allows |
|---|---|
| `datatable/read` | the catalogue, the page and the rows of each class, the **Data tables** tab of the content view, the fetch functions on the kernel's tables |
| `datatable/manage` | creating the kernel tables, making the catalogue again, creating and upgrading tables on a connection |
| `datatable/connections` | making, changing, testing and removing connections |
| `datatable/sync` | the synchronisations between tables and content: make, change, run and remove them, resolve their conflicts |

Only the Administrator role has them in a new installation. The Setup menu has a group **Data tables** with
**Data tables**, **Connections**, **Secrets** and **Synchronisations**.

## The kernel tables

| Table | Holds |
|---|---|
| `exp_data_connection` | the connections made in the administration: identifier (unique), name, engine, server, port, database, user, the secret's name, options (JSON: charset, socket, driver, DSN), description, read-only, who made and changed it and when |
| `exp_secret` | the [secret store](secret-store.md) |
| `exp_data_sync` | the synchronisations: name, direction, run mode (`run_mode`: once, scheduled, live), class, content class, node, map (JSON), key columns, conflict rule, interval, last and next run, the last run's history row, state, options (JSON) |
| `exp_data_sync_link` | the pairs of a row (its key) and an object of a synchronisation, with the hashes and times of both sides at the last run; the key is unique per synchronisation |
| `exp_data_sync_conflict` | the conflicts of the rule "ask": the pair, the values of both sides (JSON), when it was found, its state (open, resolved), how and by whom it was resolved |

A new installation has them from the kernel schema. An installation made before 6.0.15 gets them from the update
files of MySQL, PostgreSQL and SQLite (`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`, which create them
only when they are missing), or from the administration: while one of them is missing the page says so, and
**Create the tables** creates them on the installation's database through the engine's schema handler, on every
engine the kernel supports, MongoDB included: the connections and the secret store on the Data tables page (policy
`datatable/manage`), the three tables of the synchronisation on the Synchronisations page (policy `datatable/sync`). `expKernelTableSchema` does this for any table
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

## The module table wizard

**Setup > RAD > Module table** (`setup/moduletable`, policy `setup/setup`) makes a NEW data table and everything around
it, as a new extension or added to an existing one. It is `expModuleTableWizard` (the page) and `expModuleTableCode`
(the code it writes), beside the module extension wizard, which does the same for tables that exist already.

### Where the fields come from

| Source | Reference | What it gives |
|---|---|---|
| A list typed on the page | (none) | One field per line: `name type[(length)] [required] [unique]`, `#` starts a comment |
| A content class | the class identifier | `contentobject_id` (unique), `node_id`, `remote_id`, `name`, `published`, `modified`, then one column per attribute, typed by its datatype |
| An existing table | `connection:table`, e.g. `site:my_table` | the columns as the database describes them |
| A field source of an extension | what the source asks for (an import's id ...) | the source's fields |

The types are `string` (with a length, 255 by default), `text`, `integer`, `float`, `boolean`, `date`, `datetime`,
`json`, and the relations to content `object` (a content object id), `node` (a node id) and `user` (a user's object
id). Every table also gets `id`, a key the database counts up. The page can add `contentobject_id` and/or `node_id` to
tie each row to content, and `created` and `modified`. **Copy its fields into the list** turns the fields of a class,
a table or a source into the editable list.

```
title string(120) required
remote_id string(64) unique
price float
published date
article object
notes text
```

A link can open the wizard with a source already chosen: `setup/moduletable/(source)/<source id>/(ref)/<reference>`, or
`setup/moduletable/(class)/<class identifier>` for a content class. This is how a catalogue card of 7x SQLI Import opens
it for an import's fields.

An extension offers its own source as a kernel service tagged `datatable.field_source`, a class implementing
`expDataTableFieldSource`: `dataTableSourceName()`, `dataTableReferences()` (a list to choose from, or none),
`dataTableFields( array( 'ref' => ... ) )` (the fields as above: name, label, type, length, required, unique) and
`dataTableSourceLabel()`. The id of the source is the service id without `datatable.field_source.`.

```ini
[Service_datatable.field_source.myimport]
Class=myImportFieldSource
Scope=worker
Tags[]=datatable.field_source
Description=The fields of an import of my tool.
```

### What it writes

| Part | Files |
|---|---|
| Persistent class | `classes/<class>.php`: `definition()` with the connection (when it is not the site's database), the relations of the `object`, `node` and `user` fields, the unique keys; `fields()`, `create()`, `fetch()`, `fetchList()`, `fetchListCount()`, `target( <field> )` and one `<field>Target()` per relation (function attribute `<field>_target`), `toArray()` and `fromInput()`, which types the values of a form or a JSON body and checks required, unique, numbers, JSON and lengths |
| Schema and install SQL | `share/db_schema.dba`, `sql/<engine>/schema.sql` for MySQL, PostgreSQL, SQLite, Oracle and SQL Server |
| Module | `modules/<module>/module.php` with the views `list`, `edit`, `remove` and `json` and the policies `read` and `edit` |
| Admin templates | `design/admin4/templates/<module>/{list,edit,remove}.tpl` and the same files in `design/admin`: the admin4 components only, no `<style>` block and no `style` attribute |
| Fetch functions | `modules/<module>/function_definition.php` and `classes/<class>functioncollection.php`: `fetch( <module>, 'list'|'count'|'row' )` |
| Settings | `module.ini.append.php` (the module), `design.ini.append.php` (the design extension), `menu.ini.append.php` (a link in the **Data tables** group of the Setup menu for users with `<module>/read`) |
| New extension only | `ezinfo.php`, `extension.xml`, `composer.json`, `LICENSE` |
| README | `README.md` of a new extension, `doc/<table>.md` in an existing one |

The page shows every file before anything is written. A table name starting with `ez` or `exp_` is refused on the
site's database; on a named connection the name is free.

### A new extension or an existing one

When the extension does not exist, **Write the extension** writes all files below `extension/<name>`. When it exists,
**Add to the extension** adds what is missing and changes nothing that is there:

- a file the extension has already is left as it is and named on the page;
- `settings/module.ini.append.php`, `design.ini.append.php` and `menu.ini.append.php` get the wizard's block appended
  once (marked `# module table wizard: <module>`), inside the file's PHP wrapper;
- `share/db_schema.dba` gets the new table beside the tables it has;
- `sql/<engine>/schema.sql` gets the table's statements appended once.

**Download as zip** hands the same files over instead, for a server whose `extension/` the web server cannot write.

### Installing from the page

| Button | What it does |
|---|---|
| **Create the table** / **Bring the table up to date** | Creates the table on its connection from the schema, or adds what is missing (see [Schema](#schema-creating-and-upgrading-tables-on-a-connection)) |
| **Activate the extension** | The kernel's own activation: `ActiveExtensions[]=<name>` is added to `settings/override/site.ini.append.php`, a copy of the file is kept first in `var/<site>/backups/settings-override/`, then the caches are cleared and the autoload arrays regenerated |
| **Refresh: autoloads and caches** | For an extension that is active already: regenerates the autoload arrays and clears the INI, template, override and user caches |

No step needs the command line. The roles that should see or change the rows get `<module>/read` and `<module>/edit`.

### The JSON endpoint

`<module>/json` answers in JSON (`{"data": ...}`, or `{"error": ..., "problems": [...]}`) with the HTTP status:

| Request | Answer |
|---|---|
| `GET <module>/json?offset=0&limit=50&<field>=<value>` | The rows (at most 500), filtered by fields: `data`, `total`, `offset`, `limit` |
| `GET <module>/json/<id>` | One row, 404 when there is none |
| `POST <module>/json` | A new row from a JSON object (`Content-Type: application/json`) or form fields: 201 |
| `PUT <module>/json/<id>` | A change: 200; also `POST` with `_method=PUT` |
| `DELETE <module>/json/<id>` | A removal: 200; also `POST` with `_method=DELETE` |

Reading needs `<module>/read` (the view's policy), writing `<module>/edit` (403 without it). A row that cannot be
stored is 422 with the problems of `fromInput()`. A write from a page of the site carries the form token like every
POST (the header `X-CSRF-Token`).

## Synchronisation

A synchronisation keeps a data table and content of one class in step (**Setup > Data tables > Synchronisations**,
`datatable/syncs`, policy `datatable/sync`). It names:

| | |
|---|---|
| The data table | a persistent class of the catalogue (its connection is used) |
| The content | a content class and a node: the subtree that is read for the table, and the parent of objects made from rows |
| The map | one line per column, `column = source` |
| The key | the mapped column(s) a row is matched by, e.g. `contentobject_id` or `remote_id` |
| The direction | `content_to_table`, `table_to_content` or `two_way` |
| How it runs | `once` (when started), `scheduled` (every `interval` seconds), `live` (at once on every publish, move and removal, and on the schedule too) |
| The conflict rule | what a two-way run does when both sides changed: `newest`, `content`, `table` or `ask` |
| Options | remove the row of an object that is removed or leaves the subtree (`remove_missing`); the language of objects made from rows (`language`) |

A source is an attribute identifier of the content class, or one of `@object_id`, `@node_id`, `@parent_node_id`,
`@remote_id`, `@node_remote_id`, `@name`, `@published`, `@modified`, `@class`, `@section`, `@owner` and `@path` (the
node's URL alias). An attribute is read with its datatype's `toString()` and written with `fromString()`; the `@`
sources are written to the table only, never to content. **Propose the map** fills the map with the columns named
like an attribute (or `attr_<identifier>`, as the module table wizard names them) or like a known source
(`contentobject_id`, `node_id`, `remote_id`, `name`, `published`, `modified` ...).

```text
contentobject_id = @object_id
node_id = @node_id
remote_id = @remote_id
name = @name
modified = @modified
title = title
intro = intro
```

What a run does:

| Direction | |
|---|---|
| Content to table | Every object of the class below the node (its main location, hidden ones too) becomes a row, upserted by the key through `expDataTableWriter`. A row whose values are those of its object is left alone. With `remove_missing`, the row of an object that is no longer there (removed, moved out of the subtree) is removed. |
| Table to content | Every row becomes an object below the node: a row already paired with an object, or whose `@object_id` or `@remote_id` column names one, updates it (a new version, published); any other row makes a new object (with the row's remote id when one is mapped). The row then learns its object: the columns mapped to `@object_id`, `@node_id` and the other `@` sources are written back. |
| Both ways | Each pair of row and object is compared with the last run (the hashes of both sides, `exp_data_sync_link`): only the content changed, the row follows; only the row changed, the content follows; both changed, the conflict rule decides. A row or an object without its pair is paired by the key, or made on the other side. The content is never removed by a synchronisation. |

Values are compared as the table's fields type them: an empty relation is `0` in an integer column and `''` as an
attribute's string, and both mean the same. Only the attributes whose value differs are written to an object, so an
image or a relation list is never written again from its own string. Content that a synchronisation writes is not
mirrored back by the live mirror.

Every run goes through the transfer runner (the job queue, the content jobs or the request itself) and is recorded in
the run history of the import and export tools (tool `transfer.datatable`); the page of a synchronisation lists its
last runs. **Run now** (on the list) and **Save and run now** (on its page) start one. The cronjob part `datatable`
(`cronjobs/datatablesync.php`, also in the `frequent` group) starts every enabled scheduled or live synchronisation
whose interval has passed: `php runcronjobs.php datatable`.

```php
$sync = expDataSync::define( array( 'name' => 'Articles to the archive', 'direction' => 'content_to_table', 'mode' => 'scheduled',
                                    'class' => 'myArchiveRow', 'content_class' => 'article', 'subtree' => 2,
                                    'map' => "contentobject_id = @object_id\ntitle = title", 'keys' => 'contentobject_id',
                                    'conflict' => 'newest', 'interval' => 3600 ), $problems );
$run = expDataSync::start( $sync );     // through the transfer runner; $run['engine'], $run['run_id']
$counts = expDataSync::run( $sync );    // the same work in this process
```

## Live mirror

The kernel notifies three events (`ezpEvent`), which any listener can take (`site.ini [Event] Listeners[]`):

| Event | Arguments | When |
|---|---|---|
| `content/publish` | object id, version | a version was published |
| `content/move` | node id, object id, old parent, new parent | a node was moved |
| `content/remove` | object ids, node ids | locations were removed |

For the live mirror the kernel calls `expDataSyncListener` itself, in every process (a web request, a command, a
queue worker), at publish, move, location removal, purge and trash. The listener notes the objects and does the work
at the end of the request, after the publish transaction, and only when a live synchronisation exists: each object is
written to the tables of the live synchronisations of its class whose subtree holds it. An object that left the
subtree, went to the trash, was purged or lost its last location loses its rows (unless the synchronisation keeps
them). A failure is logged and never reaches the editor. A long process mirrors every 50 objects.

The workflow event **Mirror to data tables** (`expdatatablemirror`, on content publish after) does the same as a step
of a workflow, for the synchronisations ticked in the event (or every synchronisation of the object's class when none
is ticked), whatever their mode: for a site that wants the mirror after an approval, or before a notification. It never
stops a workflow.

## Conflicts

A two-way run finds a conflict when both the content and the row changed since the last run. The conflict rule
decides:

| Rule | |
|---|---|
| `newest` | the later change wins: the object's modification time against the row's (a column mapped to `@modified`, or a column named `modified`); a table without a time loses |
| `content` | the content wins: the row is written over |
| `table` | the table wins: the object gets a new version with the row's values |
| `ask` | nothing is written; the conflict waits in the queue |

**Setup > Data tables > Synchronisations > Conflicts** (`datatable/conflicts`) lists the open conflicts with the
values that differ, and **Take the content** or **Take the table** resolves one: the other side is written over, the
pair remembers both sides, and the conflict is closed with who resolved it and how.

## Tests

In `tests/tests/kernel/classes/datatable/` (testsuite kernel-classes, no installation needed): the catalogue, the
description and its differences, the connections (values, checks, plans, data source names, testing on SQLite),
persistent classes on a SQLite connection (store, fetch, update, remove, escaping), and the two kernel tables, which
must be the same in `share/db_schema.dba`, the persistent classes, the three kernel schemas and the three update files.
The schema (definitions to `.dba`, the SQL of every engine, plan and apply on SQLite: create, add a column, held drops),
the writer (types, modes, dry run, a refused row failing alone, references, the class map), the relations and the
fetch functions, and the registry entry with the content view's tab are tested there too, on a SQLite connection with
a SQLite database in memory as the site's. The module table wizard is tested there as well: the field list and the
field sources, the settings and their problems, every file it writes (every PHP file passes `php -l`, no template holds
inline styles), the definition it makes, adding a table to an existing extension (files left alone, settings and
schema files extended once), and the generated class storing, reading and checking rows on a SQLite connection.
The synchronisation is tested there too: its definitions (values, the map and its text, the problems), the hashes,
keys, conflict rules and typed values, the definitions, pairs and conflicts on a SQLite database (with the values that
differ), the live mirror's listener (what it notes and when it flushes), the cronjob part, the workflow event type, and
the kernel's calls of the listener; its three tables must be the same everywhere as the others. What reads or
publishes content (a run in each direction, a conflict resolved, the live mirror after a publish and a removal) is
tested in a throwaway installation.
`tests/tests/lib/ezdb/expPDODBTest.php` tests the PDO driver on pdo_sqlite and the SQL of the other dialects.
