---
title: Old-style constructors and static calls
audience: developer
category: extension-development
order: 70
summary: PHP 8 no longer runs a method named after its class as the constructor, and stops on instance methods called statically. How to find both in an old extension and fix them without breaking code that calls the old names.
keywords: constructor, __construct, PHP 4 constructor, old-style constructor, static call, non-static method, Error, template operator, ezfind, upgrade
type: help
---
# Old-style constructors and static calls

Part 4, chapter 7. PHP 4 had no `__construct()`: the constructor was the method with the class's name. PHP 5 and 7
still accepted that, and PHP 8 does not. Many extensions written in the 2000s use the old form, and almost every one
of their template operators does, because the kernel's own examples did. This chapter shows how the failure looks,
how to find every case, and the fix this project uses. The second half covers its sibling, the static call to an
instance method.

## Why it is dangerous

A missing constructor does not stop anything when the object is built. PHP 8 simply does not call the method, so
the object exists, but its properties were never set. The error comes later, somewhere else, and points at the wrong
place.

A template operator is the classic case. This is the operator class of the str_replace extension (se7enxweb/str_replace),
as it is in October 2026:

```php
class MyStrReplaceOperator
{
    /*!
     Constructor
    */
    function MyStrReplaceOperator()
    {
        $this->Operators = array( 'ezstr_replace');
    }

    function operatorList()
    {
        return $this->Operators;
    }
    // ...
}
```

On PHP 8, `new MyStrReplaceOperator()` leaves `$this->Operators` unset, `operatorList()` returns `null`, and the
template engine reports "Object problem with operator" on the first template that uses the operator. Nothing in that
message mentions a constructor.

In se7enxweb/ezfind, commit 7ff9824 fixed five classes with the same problem. Its message lists the symptoms, which
show how far from the cause they can appear:

| Class | What went wrong on PHP 8 |
|---|---|
| `eZFindResultNode` | reading a search result node ended in an error |
| `eZFindResultObject` | the parent constructor ran with the wrong data |
| `ezfSearchResultInfo` | facets, errors and the response header of a search were empty |
| `ezfUpdateSearchIndexSolr` | indexing from the command line stopped at once |
| `ezfModuleFunctionCollection` | an empty constructor, made a real one |

## Find every case

A method is an old-style constructor when its name equals its class's name, case-insensitively, in a class
without `__construct()`. This loop prints every class that has a method of its own name:

```bash
cd extension/myoldext
grep -rhoE '^\s*(abstract\s+|final\s+)?class\s+[A-Za-z0-9_]+' --include='*.php' . | awk '{print $NF}' | sort -u | while read c; do
  grep -rliE "function\s+&?\s*$c\s*\(" --include='*.php' . | sed "s|^|$c: |"
done
```

Check each file it prints. PHPCompatibility for PHP_CodeSniffer reports the same thing, if you already use it
([Tests and CI for an upgraded extension](tests-and-ci-for-an-upgraded-extension.md)).

## The fix

Rename the method to `__construct()`, and keep the old name as a method that calls it, for code that calls the old
name directly. That is what se7enxweb/ezfind, commit 7ff9824, did:

```php
// before
function ezfSearchResultInfo( array $resultArray )
{
    $this->ResultArray = $resultArray;
}

// after
function __construct( array $resultArray )
{
    $this->ResultArray = $resultArray;
}

/**
 * The constructor under its old name, which PHP 8 no longer calls on "new"; kept for code that calls it.
 *
 * @deprecated Use the constructor
 */
function ezfSearchResultInfo( array $resultArray )
{
    $this->__construct( $resultArray );
}
```

Why keep the old name? Child classes, and other extensions, may call `parent::ezfSearchResultInfo( ... )` or
`$this->ezfSearchResultInfo( ... )`. Without the wrapper, those become fatal errors.

For the template operator above, the whole fix is:

```php
<?php
class MyStrReplaceOperator
{
    public $Operators;

    function __construct()
    {
        $this->Operators = array( 'ezstr_replace' );
    }

    /**
     * @deprecated Use the constructor
     */
    function MyStrReplaceOperator()
    {
        $this->__construct();
    }

    function operatorList()
    {
        return $this->Operators;
    }
}
```

Note the declared property `public $Operators;`: without it, PHP 8.2 reports a dynamic property
([Dynamic properties](dynamic-properties.md)).

### Classes with a parent

Look at what the old constructor did with its parent. There are three cases:

| The old constructor | What the new one does |
|---|---|
| called `$this->ParentClass( ... )` or `parent::ParentClass( ... )` | calls `parent::__construct( ... )` with the same arguments |
| did not call the parent at all | does not call it either (in ezfind, `eZFindResultObject` keeps it that way on purpose) |
| did not exist; only the parent had one | nothing to do, the parent's `__construct()` runs |

If the parent is a kernel class, its constructor is already `__construct()` in Exponential 6. For example,
`eZPersistentObject` subclasses call `parent::__construct( $row )`.

## Static calls to instance methods

PHP 5 let you call an instance method as `ClassName::method()`, with a notice, as long as it did not touch `$this`.
PHP 8 throws an `Error`:

```text
Uncaught Error: Non-static method eZSelectionType::classAttributeContent() cannot be called statically
```

From se7enxweb/sevenx_themes_media, commit f7d93c0:

```php
// before
$classContent = eZSelectionType::classAttributeContent( $classAttr );

// after
$classContent = $classAttr->attribute( 'content' );
```

There are three ways to fix such a call, in this order of preference:

1. **Use the higher-level API.** As above: the class attribute knows its datatype and returns its content.
2. **Get an instance.** Many kernel classes have one: `eZINI::instance()`, `eZDB::instance()`,
   `eZHTTPTool::instance()`, `eZLocale::instance()`, `eZTemplate::factory()`, or a fetch:
   `eZContentObject::fetch( $id )`, `eZContentObjectTreeNode::fetch( $nodeID )`.
3. **Make your own method static**, but only your own, and only if it does not use `$this`.

To find candidates, list the static-looking calls to kernel classes and check each method's declaration:

```bash
grep -rnoE '\beZ[A-Za-z]+::[a-zA-Z_]+\s*\(' --include='*.php' extension/myoldext/ | sort -u
grep -n "function classAttributeContent" kernel/classes/datatypes/ezselection/ezselectiontype.php
```

A declaration without `static` means the call needs an instance.

### The reverse: $this in a static method

Old code sometimes declares a method `static` and still uses `$this` inside, which worked when the method happened to
be called on an object. PHP 8 stops with "Using $this when not in object context". Remove `static`, or replace
`$this->` with `self::` or `static::` if the method really is static.

## Checklist

- [ ] Every class with a method of its own name has a `__construct()`.
- [ ] The old name stays as a deprecated wrapper.
- [ ] Each new constructor calls the parent exactly when the old one did.
- [ ] Every property set in a constructor is declared.
- [ ] No `ClassName::method()` call to a method declared without `static`.

## Related pages

- Previous: [Removed PHP functions and their replacements](removed-php-functions.md)
- Next: [Nullable parameters and type declarations](nullable-parameters-and-types.md)
- [Template operators](../part-3-extension-developer/template-operators.md)
- [Constructors and destructors](https://www.php.net/manual/en/language.oop5.decon.php) in the PHP manual
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
