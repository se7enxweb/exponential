---
title: "By hand: your own table with eZPersistentObject"
audience: developer, app-creator
category: extension-development
order: 180
summary: A greetings table: an engine-neutral db_schema.dba, the SQL the kernel generates from it for three databases, installing it, the class, and using it from a page.
keywords: tutorial, by hand, eZPersistentObject, own table, db_schema.dba, eZDbSchema, generateSchemaFile, ezsqlinsertschema, mysql, postgresql, sqlite, store, fetchObjectList
type: help
---
# By hand: your own table with eZPersistentObject

Tutorial 17. You give `mysite_hello` a table of greetings: the schema in the engine-neutral `.dba` format, installed on
your database, and the class `mysiteHelloGreeting` that reads and writes it. The `.dba` file passed `php -l`, and the
SQL shown below is what the kernel's schema handlers (`eZDbSchema`) generated from it.

## 1. The schema

`extension/mysite_hello/share/db_schema.dba`:

```php
<?php
// This array contains the database schema
$schema = array (
  'mysite_hello_greeting' =>
  array (
    'name' => 'mysite_hello_greeting',
    'fields' =>
    array (
      'id' => array ( 'type' => 'auto_increment', 'default' => false ),
      'name' => array ( 'length' => 100, 'type' => 'varchar', 'not_null' => '1', 'default' => '' ),
      'user_id' => array ( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
      'created' => array ( 'length' => 11, 'type' => 'int', 'not_null' => '1', 'default' => 0 ),
    ),
    'indexes' =>
    array (
      'PRIMARY' => array ( 'type' => 'primary', 'fields' => array ( 0 => 'id' ) ),
      'mysite_hello_greeting_created' => array ( 'type' => 'non-unique', 'fields' => array ( 0 => 'created' ) ),
    ),
  ),
  '_info' => array ( 'format' => 'generic' ),
);
?>
```

## 2. What the kernel makes of it

`eZDbSchema::instance( array( 'type' => <engine>, ... ) )->generateSchemaFile( $schema )` turned it into:

MySQL:

```text
CREATE TABLE mysite_hello_greeting (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) NOT NULL DEFAULT '',
  user_id int(11) NOT NULL DEFAULT '0',
  created int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY ( id ),
  KEY mysite_hello_greeting_created ( created )
);
```

PostgreSQL:

```text
CREATE SEQUENCE IF NOT EXISTS mysite_hello_greeting_id_seq
  START 1
  INCREMENT 1
  MAXVALUE 9223372036854775807
  MINVALUE 1
  CACHE 1;
CREATE TABLE IF NOT EXISTS mysite_hello_greeting (
  id integer DEFAULT nextval('mysite_hello_greeting_id_seq'::text) NOT NULL,
  name character varying(100) DEFAULT ''::character varying NOT NULL,
  user_id integer DEFAULT 0 NOT NULL,
  created integer DEFAULT 0 NOT NULL
);
CREATE INDEX mysite_hello_greeting_created ON mysite_hello_greeting USING btree ( created );
ALTER TABLE ONLY mysite_hello_greeting ADD CONSTRAINT mysite_hello_greeting_pkey PRIMARY KEY ( id );
```

SQLite:

```text
CREATE TABLE mysite_hello_greeting (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name varchar(100) NOT NULL DEFAULT '',
  user_id INTEGER(11) NOT NULL DEFAULT '0',
  created INTEGER(11) NOT NULL DEFAULT '0'
);
CREATE INDEX mysite_hello_greeting_created ON mysite_hello_greeting ( created );
```

One file, three engines: that is why extensions ship the `.dba` (and may add the per-engine SQL in `sql/<engine>/schema.sql`
for people who prefer SQL).

## 3. Install it

On your development database (the command's options are listed by `--help`):

```bash
php bin/php/ezsqlinsertschema.php --type=<mysql|postgresql|sqlite> --user=<user> extension/mysite_hello/share/db_schema.dba <database>
```

For a real extension, ship an idempotent install command instead, as `expqueue` does with
`./console ext:expqueue:install-schema` ([Database schema, .dba files and upgrades](../database-schema-and-dba-files.md)).

## 4. The class

Copy `mysiteHelloGreeting` from [Persistent objects and your own tables](../persistent-objects-and-tables.md#the-class)
into `extension/mysite_hello/classes/mysitehellogreeting.php`, then `php bin/php/ezpgenerateautoloads.php -e`.

## 5. Use it

In the compose page of tutorial 13, store the greeting when the preview is valid, and redirect:

```php
if ( $preview !== false && $http->hasPostVariable( 'SaveButton' ) )
{
    $greeting = mysiteHelloGreeting::create( $preview );
    $greeting->store();
    return $Module->redirectToView( 'compose' );
}
```

(and add `'SaveButton' => 'Save'` to the view's `single_post_actions` with a button in the form). List the newest
greetings with `mysiteHelloGreeting::fetchNewest( 10 )` and give them to the template.

## Related pages

- Previous: [By hand: a cronjob part](by-hand-cronjob-part.md)
- Next: [By hand: an ezjscore server function](by-hand-ajax-server-function.md)
- [Persistent objects and your own tables](../persistent-objects-and-tables.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
