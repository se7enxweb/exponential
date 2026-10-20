---
title: Dynamic properties
audience: developer
category: extension-development
order: 90
summary: PHP 8.2 deprecates properties a class does not declare. How the kernel handles them for persistent objects, how to find them in your own classes, and when to declare, use an array, or allow them.
keywords: dynamic properties, PHP 8.2, AllowDynamicProperties, declare properties, eZPersistentObject, __get, __set, stdClass, deprecated, upgrade
type: help
---
# Dynamic properties

Part 4, chapter 9. In PHP before 8.2 you could set any property on any object: `$this->cache = array();` created
`cache` if the class did not declare it. PHP 8.2 reports that as deprecated:

```text
Deprecated: Creation of dynamic property myOldExtHandler::$cache is deprecated
```

Old extensions do this everywhere, because PHP 4 and PHP 5 code often listed properties only in comments. This
chapter explains what the kernel already does for you, and how to fix your own classes.

## What the kernel already handles

Three kernel classes allow dynamic properties on purpose, with PHP's `#[AllowDynamicProperties]` attribute:

| Class | Why |
|---|---|
| `eZPersistentObject` (`kernel/classes/ezpersistentobject.php`) | the fields of a row are set as properties by name |
| `eZWorkflowProcess` | the same, for workflow processes |
| `eZINI` (`lib/ezutils/classes/ezini.php`) | old code sets properties on settings objects |

`eZPersistentObject` goes further: it has `__get()`, `__set()`, `__isset()` and `__unset()` methods that keep
undeclared fields in a protected array, so a subclass that relies on dynamic fields does not raise the deprecation.
The kernel did this in 6.0.4 ([PHP 8 support](../../../bc/6.0/php8.md)).

So a class of yours that extends `eZPersistentObject` usually needs nothing. Any other class does.

## Find them

PHP tells you only when the line runs. To find them before, compare what a class sets with what it declares. This
is a rough but useful start:

```bash
cd extension/myoldext
for f in $(grep -rlE '^\s*class\s' --include='*.php' .); do
  set=$(grep -oE '\$this->[A-Za-z_][A-Za-z0-9_]*\s*=[^=]' "$f" | sed -E 's/\$this->([A-Za-z0-9_]+).*/\1/' | sort -u)
  for p in $set; do
    grep -qE "(public|protected|private|var)\s+(\??[A-Za-z_\\]+\s+)?\\\$$p\b" "$f" || echo "$f: \$$p"
  done
done
```

It prints every `$this->x = ...` whose `x` the same file does not declare. A property declared in a parent class
appears too; check those against the parent.

The certain way is to run the extension on PHP 8.2 or later with the debug output on and every page, view and script
used once.

## Fix 1: declare the property (the usual fix)

```php
// before
class myOldExtHandler
{
    function __construct()
    {
        $this->cache = array();
        $this->ini = eZINI::instance( 'myoldext.ini' );
    }
}

// after
class myOldExtHandler
{
    /** @var array */
    protected $cache = array();

    /** @var eZINI */
    protected $ini;

    function __construct()
    {
        $this->ini = eZINI::instance( 'myoldext.ini' );
    }
}
```

Choose the visibility on purpose:

- `public` if templates read it. The template engine reads objects through `attribute()` and `hasAttribute()`
  (see [Template operators](../part-3-extension-developer/template-operators.md)), but some old classes expose public
  properties directly.
- `protected` for everything else. A subclass can still use it.

Old PHP 4 code declares properties with `var`. `var` still works and means `public`; you can leave it, or write
`public` for clarity.

## Fix 2: use an array for data that really is dynamic

When the names come from data (an import file's columns, a form's fields), do not create a property per name. Keep
them in one declared array:

```php
<?php
class myOldExtRecord
{
    /** @var array<string, mixed> */
    protected $fields = array();

    public function set( string $name, $value ): void
    {
        $this->fields[$name] = $value;
    }

    public function get( string $name )
    {
        return $this->fields[$name] ?? null;
    }

    public function attributes(): array
    {
        return array_keys( $this->fields );
    }

    public function hasAttribute( $name ): bool
    {
        return array_key_exists( $name, $this->fields );
    }

    public function attribute( $name )
    {
        return $this->get( $name );
    }
}
```

The last three methods let templates read the fields as `$record.title`, the way the template engine reads kernel
objects.

## Fix 3: allow them, when you must

For a class whose whole design depends on dynamic properties, and which you cannot change now, mark it:

```php
#[AllowDynamicProperties]
class myOldExtLegacyContainer
{
}
```

On PHP 8.0 and 8.1 an attribute PHP does not know is ignored, so this runs everywhere. Treat it as a note to
yourself: the class still needs a real fix.

## Objects of other classes

The deprecation is about the class of the object, not about who sets the property. Setting a property on a kernel
object from your extension is the same problem:

```php
// deprecated: eZContentObjectTreeNode does not declare myoldextScore
// (eZContentObjectTreeNode extends eZPersistentObject, so in Exponential 6 this lands in its backing array)
$node->myoldextScore = 5;
```

It happens to be quiet for persistent objects in Exponential 6, but it is fragile: the value is not a field, is not
stored, and may vanish when the kernel object is fetched again. Keep your own data in your own structure, for
example an array keyed by node ID.

`stdClass` and objects from `json_decode()` allow dynamic properties by definition and are not affected.

## Related pages

- Previous: [Nullable parameters and type declarations](nullable-parameters-and-types.md)
- Next: [Database code for PHP 8 and every engine](database-code-for-php-8.md)
- [Persistent objects and your own tables](../part-3-extension-developer/persistent-objects-and-tables.md)
- [Deprecate dynamic properties](https://www.php.net/manual/en/migration82.deprecated.php) in the PHP 8.2 migration guide
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
