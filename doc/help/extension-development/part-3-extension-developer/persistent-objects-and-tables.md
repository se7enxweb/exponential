---
title: Persistent objects and your own tables
audience: developer, app-creator
category: extension-development
order: 260
summary: Your extension's own table through eZPersistentObject: definition(), create, store, fetch, lists with conditions and limits, count, remove, template attributes, transactions.
keywords: eZPersistentObject, definition, fields, keys, increment_key, function_attributes, fetchObject, fetchObjectList, count, store, remove, removeObject, own table, database, transaction, eZDB
type: help
---
# Persistent objects and your own tables

Part 3, chapter 26. Content belongs in content classes. Data that is not content (log entries, settings per user, a
queue, counters) belongs in a table of your extension, accessed through `eZPersistentObject`
(`kernel/classes/ezpersistentobject.php`), the class every kernel table class extends. It writes safe SQL for every
supported database, so you rarely write SQL yourself.

## The class

`extension/mysite_hello/classes/mysitehellogreeting.php`:

```php
<?php
/**
 * One greeting, stored in the table mysite_hello_greeting.
 */
class mysiteHelloGreeting extends eZPersistentObject
{
    public static function definition()
    {
        return array(
            'fields' => array(
                'id'      => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'name'    => array( 'name' => 'Name', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'user_id' => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'created' => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true )
            ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'function_attributes' => array( 'user' => 'user' ),
            'class_name' => 'mysiteHelloGreeting',
            'sort' => array( 'created' => 'desc' ),
            'name' => 'mysite_hello_greeting'
        );
    }

    /** Creates an unsaved greeting for the current user. */
    public static function create( $name )
    {
        return new self( array( 'name' => (string)$name,
                                'user_id' => eZUser::currentUserID(),
                                'created' => time() ) );
    }

    public static function fetch( $id )
    {
        return eZPersistentObject::fetchObject( self::definition(), null, array( 'id' => (int)$id ) );
    }

    /** The newest greetings, newest first. */
    public static function fetchNewest( $limit = 10, $offset = 0 )
    {
        return eZPersistentObject::fetchObjectList( self::definition(), null, null,
                                                    array( 'created' => 'desc' ),
                                                    array( 'offset' => (int)$offset, 'length' => (int)$limit ) );
    }

    public static function countByUser( $userID )
    {
        return eZPersistentObject::count( self::definition(), array( 'user_id' => (int)$userID ) );
    }

    /** function attribute "user": the user who greeted */
    public function user()
    {
        return eZUser::fetch( $this->attribute( 'user_id' ) );
    }
}
```

The class passed `php -l`; with the kernel loaded, `create( 'Ada' )` gave an object with the name and the current user,
and the same `fetchObjectList()` and `count()` calls, run read-only against the kernel's `eZSection` table class,
returned objects and counts as expected.

## The definition

| Key | Meaning |
|---|---|
| `fields` | column name => `name` (the attribute name in PHP, CamelCase), `datatype` (`integer`, `string`, `float`), `default`, `required` |
| `keys` | the primary key columns |
| `increment_key` | the auto-increment column, filled after the first `store()` |
| `function_attributes` | attributes computed by a method: `'user' => 'user'` makes `$greeting.user` work in templates |
| `class_name` | the class to create for fetched rows |
| `sort` | the default sorting |
| `name` | the table name: prefix it with your extension's name |

## Using it

```php
<?php
$db = eZDB::instance();
$db->begin();
$greeting = mysiteHelloGreeting::create( 'Ada' );
$greeting->store();                         // INSERT; $greeting->attribute( 'id' ) is now set
$greeting->setAttribute( 'name', 'Ada L.' );
$greeting->store();                         // UPDATE
$db->commit();

$latest = mysiteHelloGreeting::fetchNewest( 5 );
$mine = mysiteHelloGreeting::countByUser( eZUser::currentUserID() );
$greeting->remove();                        // DELETE
```

| Method | Signature (from the kernel) |
|---|---|
| `fetchObject()` | `( $def, $field_filters, $conds, $asObject = true, $grouping = null, $custom_fields = null )` |
| `fetchObjectList()` | `( $def, $field_filters = null, $conds = [], $sorts = null, $limit = null, $asObject = true, $grouping = false, $custom_fields = null, $custom_tables = null, $custom_conds = null )` |
| `count()` | `( $def, $conds = null, $field = null )` |
| `store()` | `( $fieldFilters = null )` |
| `remove()` | `( $conditions = null, $extraConditions = null )` |
| `removeObject()` | `( $def, $conditions = null, $extraConditions = null )`, static |

Conditions are `array( 'column' => value )`; values are escaped for you. Other operators:
`array( 'created' => array( '>', $since ) )`, `array( 'id' => array( array( 1, 2, 3 ) ) )` for an IN list. `$limit` is
`array( 'offset' => ..., 'length' => ... )`.

## In templates

Give templates access through a fetch function ([Fetch functions](fetch-functions.md)) returning these objects; then
`{$greeting.name|wash}` and `{$greeting.user.login}` work through `attribute()`.

## Transactions

Wrap several writes in `$db->begin()` and `$db->commit()`; on an error, `$db->rollback()`. Transactions nest: the kernel
counts them and commits at the outermost level.

## The table itself

The table must exist before any of this works; shipping and upgrading it is the next chapter,
[Database schema, .dba files and upgrades](database-schema-and-dba-files.md).

## When not to

- Data that editors maintain, version and translate is **content**: use a class and attributes.
- Data per attribute of content (many tags per field) can be your own table, but the datatype must copy and delete its
  rows with versions ([Datatype storage, validation and templates](datatype-storage-validation-and-templates.md)).

## RAD

**Module wizard** (`/setup/modulewizard`) writes a module over existing tables, including the persistent object classes.

## Related pages

- Previous: [Translations in an extension](translations-in-an-extension.md)
- Next: [Database schema, .dba files and upgrades](database-schema-and-dba-files.md)
- [eZPersistentObject](../part-8-reference/ezpersistentobject.md), [eZDB: the database layer](../part-8-reference/ezdb.md)
- [By hand: your own table with eZPersistentObject](tutorials/by-hand-own-table-with-persistent-object.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
