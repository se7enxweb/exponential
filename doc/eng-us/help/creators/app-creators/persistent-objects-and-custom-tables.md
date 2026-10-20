---
title: Persistent objects and custom tables
audience: app-creator, developer
category: app-creation
order: 210
summary: Store application data in your own tables with eZPersistentObject - the definition() array, fetchObject and fetchObjectList, store and remove, function attributes for templates, and when to use tables instead of content classes.
keywords: eZPersistentObject, custom tables, definition, fetchObject, fetchObjectList, store, remove, increment_key, function_attributes, active record, database, app creator
type: help
---
# Persistent objects and custom tables

This page is for application creators who store data that is not content: logs, settings per user, imported records,
counters. `eZPersistentObject` (`kernel/classes/ezpersistentobject.php`) maps a table to a class, works on every
database Exponential supports, and makes rows readable in templates.

## Contents

1. [Content class or own table?](#content-class-or-own-table)
2. [The class](#the-class)
3. [Reading](#reading)
4. [Writing](#writing)
5. [In templates](#in-templates)
6. [Creating the table](#creating-the-table)

## Content class or own table?

| Choose a content class when | Choose a table when |
|---|---|
| editors create and edit it in the administration | only code writes it |
| it needs versions, translations, permissions per item | none of that is needed |
| it appears as pages | it is internal data |
| volumes are moderate | there are millions of rows or many writes per second |

## The class

```php
<?php
class expDemoItem extends eZPersistentObject
{
    public static function definition()
    {
        return array(
            'fields' => array(
                'id'         => array( 'name' => 'ID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'user_id'    => array( 'name' => 'UserID', 'datatype' => 'integer', 'default' => 0, 'required' => true ),
                'title'      => array( 'name' => 'Title', 'datatype' => 'string', 'default' => '', 'required' => true ),
                'created'    => array( 'name' => 'Created', 'datatype' => 'integer', 'default' => 0, 'required' => true ) ),
            'keys' => array( 'id' ),
            'increment_key' => 'id',
            'function_attributes' => array( 'user' => 'user' ),
            'class_name' => 'expDemoItem',
            'sort' => array( 'created' => 'desc' ),
            'name' => 'expdemo_item' );
    }

    public function user()
    {
        return eZUser::fetch( $this->attribute( 'user_id' ) );
    }
}
```

The kernel's own classes look the same (`eZSection::definition()` in `kernel/classes/ezsection.php`).

## Reading

```php
$item  = eZPersistentObject::fetchObject( expDemoItem::definition(), null, array( 'id' => (int)$id ) );
$items = eZPersistentObject::fetchObjectList( expDemoItem::definition(), null,
                                              array( 'user_id' => (int)$userID ),
                                              array( 'created' => 'desc' ),
                                              array( 'offset' => 0, 'length' => 20 ) );
$count = eZPersistentObject::count( expDemoItem::definition(), array( 'user_id' => (int)$userID ) );
```

Conditions are arrays, never SQL strings: values are escaped for you.

## Writing

```php
$item = new expDemoItem( array( 'user_id' => $userID, 'title' => $title, 'created' => time() ) );
$item->store();                     // insert; the id is set from increment_key
$item->setAttribute( 'title', $newTitle );
$item->store();                     // update
$item->remove();                    // delete
eZPersistentObject::removeObject( expDemoItem::definition(), array( 'user_id' => (int)$userID ) );
```

Wrap related writes in a transaction (`$db = eZDB::instance(); $db->begin(); ... $db->commit();`)
([Database access and SQL](database-access.md)).

## In templates

Fields and `function_attributes` are readable as `$item.title`, `$item.user.login`. Return persistent objects from fetch
functions to give templates your data ([Writing your own fetch function](writing-a-fetch-function.md)).

## Creating the table

Ship the table as a schema file of the extension and an installer command; keep one definition for every database
([Database schema and .dba files](schema-and-dba-files.md)).

## Related pages

- [Database access and SQL](database-access.md)
- [Active record pattern](../../../standards/patterns/active-record.md)
- [Databases and SQL standard](../../../standards/databases-and-sql.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
