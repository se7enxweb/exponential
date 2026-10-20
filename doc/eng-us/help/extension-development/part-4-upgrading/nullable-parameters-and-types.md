---
title: Nullable parameters and type declarations
audience: developer
category: extension-development
order: 80
summary: Fix the most common PHP 8.4 deprecation, implicitly nullable parameters, and add type declarations to old code safely, with examples from expsite_api, expquery_translator and ezfind.
keywords: nullable, ?Type, implicitly nullable, PHP 8.4, type declarations, optional before required, default null, mixed, union types, upgrade
type: help
---
# Nullable parameters and type declarations

Part 4, chapter 8. This chapter covers the deprecation you will see most often in old code on PHP 8.4 and later,
the implicitly nullable parameter, and its close relative, an optional parameter before a required one. It also
gives rules for adding type declarations to old code without breaking callers.

## The deprecation

```text
Deprecated: myOldExtHelper::render(): Implicitly marking parameter $node as nullable is deprecated,
the explicit nullable type must be used instead
```

It comes from a parameter that has a class or a type, and `null` as its default:

```php
function render( eZContentObjectTreeNode $node = null )
```

PHP has always allowed `null` here because of the default. PHP 8.4 wants you to say so in the type.

## The fix

Add a question mark:

```php
function render( ?eZContentObjectTreeNode $node = null )
```

That is all. `?Type` works since PHP 7.1, so the fix runs on every PHP Exponential 6 supports, and callers are not
affected: they could pass `null` before and they still can.

From se7enxweb/expsite_api, commit 0f708f4:

```php
// before
public function __construct( eZContentObject $object = null )

// after
public function __construct( ?eZContentObject $object = null )
```

The commit message names the reason it mattered: the old form was reported "on every request that builds one (the
search page does)". se7enxweb/expquery_translator, commit 8ddf68d, made the same change to 35 parameters in 27 files,
and verified it with `php -l` on PHP 8.0 and 8.5 and by running a query through the parser on both.

## Find every case

```bash
grep -rnE '[(,]\s*(array|callable|string|int|float|bool|iterable|object|self|[A-Z][A-Za-z0-9_\\]+)\s+&?\$[A-Za-z0-9_]+\s*=\s*null' --include='*.php' extension/myoldext/
```

Every hit without a `?` before the type, and without `|null` in a union type, is one to fix. PHP 8.4's own syntax
check reports them too:

```bash
find extension/myoldext -name '*.php' -print0 | xargs -0 -n1 php8.4 -d error_reporting=-1 -l 2>&1 | grep -i nullable
```

(Use the path of a PHP 8.4 or 8.5 binary on your system instead of `php8.4`.)

## Interfaces and parent classes

If the parameter belongs to a method that implements an interface or overrides a parent's method, fix the interface
or the parent first, then every implementation. In se7enxweb/expquery_translator the abstract
`Visitor::visit( Node $node, ?Visitor $subVisitor = null, $options = null )` and all its implementations changed
in the same commit. PHP accepts a child that is more permissive than the parent, so changing them in the same commit
keeps every step loadable.

If the parent is a kernel class, look at the kernel's current signature and copy it. The kernel's own methods were
changed for PHP 8.4 and 8.5 between 6.0.8 and 6.0.11 ([PHP 8 support](../../../bc/6.0/php8.md)), so an override
written against an older kernel can differ from its parent today.

## Optional parameters before required ones

The second deprecation in this family is a default value on a parameter that is followed by a required one:

```php
// a default that can never be used: $handlerParameters is required after it
protected function buildBoostFunctions( $boostFunctions = null, &$handlerParameters )
```

PHP 8.0 reports this as deprecated, because the default can never apply. From se7enxweb/ezfind, commit 4134cbb:

```php
// after: the default removed, the only caller passes both
protected function buildBoostFunctions( $boostFunctions, &$handlerParameters )
```

Before removing the default, check every caller. If some caller leaves out the first argument, it was already broken.

## Adding type declarations to old code

Type declarations make errors appear where they are made instead of three calls later. Adding them to an old
extension is worth it, but only where you control every caller.

| Where | Add types? |
|---|---|
| Private and protected methods of your own classes | yes |
| Public methods other extensions may call | carefully: a stricter type breaks callers that passed something else |
| Methods that override kernel methods | no: copy the parent's signature exactly |
| Template operator `modify()`, datatype methods, workflow `execute()` | no: the kernel calls them with the signature of the base class |
| Return types of methods implementing PHP interfaces (`Iterator`, `ArrayAccess`, `Countable`) | yes, or `#[\ReturnTypeWillChange]` ([PHP 8.1 to 8.5 deprecations](php-8-1-to-8-5-deprecations.md)) |

Types you can use on PHP 8.0, the oldest PHP of Exponential 6: scalar types, `?Type`, union types (`int|string`),
`mixed`, `static` as a return type. Types you cannot use on 8.0: `never`, intersection types, `null` or `false` as a
type of their own, `readonly` properties, enums.

Kernel values are often `false` where newer code would use `null`. `eZContentObject::fetch()` returns `null` when
nothing is found, but many older kernel functions return `false`. A return type `?eZContentObject` on your own
wrapper is wrong if it passes such a `false` through. Check what the kernel method returns before you declare it:

```php
<?php
class myOldExtObjectLoader
{
    /**
     * @return eZContentObject|null
     */
    public static function load( int $objectID ): ?eZContentObject
    {
        $object = eZContentObject::fetch( $objectID );
        return $object instanceof eZContentObject ? $object : null;
    }
}
```

The `instanceof` check turns whatever the kernel returned into the type you promised.

## Related pages

- Previous: [Old-style constructors and static calls](old-constructors-and-static-calls.md)
- Next: [Dynamic properties](dynamic-properties.md)
- [PHP 8.1 to 8.5 deprecations](php-8-1-to-8-5-deprecations.md)
- [Type declarations](https://www.php.net/manual/en/language.types.declarations.php) in the PHP manual
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
