---
title: "Inside the core: permissions and persistent objects"
audience: developer
category: books
order: 500
summary: How a module decides access (hasAccessTo, the limitation list, error codes) and how kernel classes store themselves through eZPersistentObject, with today's static calls and links to the developer pages.
keywords: permissions, policy, limitation, hasAccessTo, eZUser, error codes, eZPersistentObject, definition, fields, function_attributes, fetchObject, fetchObjectList, store, Learning book
type: help
---
# Inside the core: permissions and persistent objects

Chapter 4 of the book looks inside the kernel classes (`kernel/classes`) so that you can write extensions. Two ideas carry
the chapter. Both are unchanged in shape; the PHP around them is modern now.

## Permissions in code

A role holds policies; a policy names a module and a function (or all) with limitations ([roles and policies](roles-and-policies-step-by-step.md)).
A module declares which limitations each of its functions understands in its definition file:

```php
$FunctionList['read'] = array( 'Class' => $ClassID, 'Section' => $SectionID, 'Node' => $Node, 'Subtree' => $Subtree );
```

(the real `kernel/content/module.php` lists more: owner, state, language.) The check that the main script runs:

```php
$user = eZUser::currentUser();
$access = $user->hasAccessTo( $module->attribute( 'name' ), $functionName );
```

`hasAccessTo()` returns an array whose `accessWord` is `yes`, `no` or `limited`. For `yes` the user has full rights; for `no`
access is denied; for `limited` the policies come back as a list of limitations, and the classes that fetch content (such as
`eZContentObjectTreeNode`) turn them into SQL, so that only the allowed objects are fetched. The book's example of
`subTreeCount()` building `contentclass_id in (...)` for a Class limitation is still how it is done. Note the book's `&` and
`include_once`: references and includes are gone (autoloading finds the class).

Common error codes are constants of `eZError` now: `KERNEL_ACCESS_DENIED` (1), `KERNEL_NOT_FOUND` (2),
`KERNEL_NOT_AVAILABLE` (3), `KERNEL_MODULE_NOT_FOUND` (20), `KERNEL_MODULE_VIEW_NOT_FOUND` (21) and others.

Read next: [Policy functions and access](../extension-development/part-3-extension-developer/policy-functions-and-access.md).

## Persistent objects

`eZPersistentObject` is the thin layer between the code and the database. A class that extends it describes its table in a
`definition()` function, and gets `store()`, `sync()`, `fetchObject()`, `fetchObjectList()` and `removeObject()` for free. The
array `definition()` returns holds:

| Key | Meaning |
|---|---|
| `fields` | database field => the object attribute: its name, type (`integer`, `string`...), default value and whether it is required |
| `keys` | the primary key fields |
| `function_attributes` | template attributes that call methods (so `$object.owner` calls `owner()`) |
| `increment_key` | the auto-increment field |
| `class_name`, `name` | the PHP class and the table |
| `sort`, `grouping`, `set_functions` | default order, grouping, setters |

An object is used by its attributes: `$object->attribute( 'name' )`, `setAttribute( 'name', 'x' )`, `store()`. The same
names work in templates, which is why the template language and the PHP API feel alike. Fetching:

```php
$group = eZPersistentObject::fetchObject( eZContentClassGroup::definition(), null, array( 'name' => $name ) );
```

Read next: [Persistent objects and tables](../extension-development/part-3-extension-developer/persistent-objects-and-tables.md)
and [Database schema and DBA files](../extension-development/part-3-extension-developer/database-schema-and-dba-files.md).
When you write SQL by hand, escape strings with the database object's `escapeString()` and cast numbers.

Next: [inside the core: classes, workflows, notifications, search](inside-the-core-classes-and-services.md).

## Source and license

Adapted from chapter 4, "A Glimpse Inside the Core", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: shortened; PHP 4 code replaced by current calls; class names, settings and constants checked against Exponential 6.
