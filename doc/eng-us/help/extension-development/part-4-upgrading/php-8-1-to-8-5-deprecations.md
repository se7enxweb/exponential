---
title: PHP 8.1 to 8.5 deprecations
audience: developer
category: extension-development
order: 50
summary: The deprecations each PHP release from 8.1 to 8.5 added that old extension code meets, how they show in the debug output, and the fix for each that still runs on PHP 8.0.
keywords: deprecated, PHP 8.1, PHP 8.2, PHP 8.3, PHP 8.4, PHP 8.5, ReturnTypeWillChange, null to string functions, utf8_decode, curl_close, imagedestroy, http_response_header, implicitly nullable
type: help
---
# PHP 8.1 to 8.5 deprecations

Part 4, chapter 5. A deprecation does not stop your code. It is PHP telling you that a later version will. This
chapter lists, version by version, the deprecations old extension code meets most often, and a fix for each that
works on every PHP Exponential 6 supports, from 8.0 to 8.5.

## Why fix something that still works

- Exponential 6 shows deprecations in the debug output. A page full of them hides the warning you need to see.
- The next PHP version turns some of them into errors, on the day a hosting company upgrades its servers.
- Under Exponential Velocity, the application server, a long-running worker writes the same deprecation for every
  request, which fills the logs quickly.

## How to see them

With the debug output on, deprecations appear as notices with "Deprecated" in the text. On the command line:

```bash
php -d error_reporting=-1 -d display_errors=1 bin/php/ezexec.php extension/myoldext/bin/php/myscript.php 2>&1 | grep -i deprecated
```

Filter for your own files: the kernel and the shipped extensions are kept free of deprecations, so a line naming
`extension/myoldext/` is yours.

## PHP 8.1

| Deprecated | Typical old code | Fix that runs on 8.0 too |
|---|---|---|
| Passing `null` to a non-nullable parameter of a built-in function | `strlen( $row['title'] )` where the column is `NULL`; `trim( $http->postVariable( 'X' ) )` when the variable is missing | `strlen( (string)$row['title'] )`, or `$value ?? ''` |
| Return types of methods implementing built-in interfaces | `function current()` in a class implementing `Iterator`, `function offsetGet( $o )` in `ArrayAccess` | add `#[\ReturnTypeWillChange]` above the method, or the real return type (`mixed` exists since 8.0) |
| `Serializable` without `__serialize()` / `__unserialize()` | classes implementing `serialize()` and `unserialize()` only | add `__serialize()` and `__unserialize()` beside them |
| `strftime()`, `gmstrftime()` | date formatting in templates operators and mail | `date()` or `IntlDateFormatter`, or the kernel's `eZLocale` formatting |
| `FILTER_SANITIZE_STRING` | `filter_var( $x, FILTER_SANITIZE_STRING )` | escape on output instead (`htmlspecialchars()` or the template `wash` operator) |
| Implicit lossy conversion of a float to an integer key or argument | `$array[ $total / 2 ]` | `(int)( $total / 2 )` |

The kernel itself received `#[ReturnTypeWillChange]` on its SPL methods in 6.0.11 ([PHP 8 support](../../../bc/6.0/php8.md)).
An attribute is a comment to PHP 7 and a no-op to 8.0, so it is the safest fix:

```php
<?php
class myOldExtList implements Iterator
{
    private $items = array();
    private $position = 0;

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->items[$this->position];
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->position++;
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = 0;
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return isset( $this->items[$this->position] );
    }
}
```

## PHP 8.2

| Deprecated | Typical old code | Fix |
|---|---|---|
| Creating a property that the class does not declare | `$this->cache = array();` in a class without `public $cache;` | declare the property; see [Dynamic properties](dynamic-properties.md) |
| `utf8_encode()`, `utf8_decode()` | `strlen( utf8_decode( $s ) )` to count characters | `mb_strlen( $s, 'UTF-8' )`, `mb_convert_encoding()` |
| `"${var}"` in strings | `"Hello ${name}"` | `"Hello {$name}"` |
| Callables written as `"self::method"`, `"parent::method"`, `array( 'static', 'm' )` | `call_user_func( 'self::helper' )` | `array( self::class, 'helper' )` or `array( $this, 'helper' )` |

From se7enxweb/sevenx_dse, commit b02ff09:

```php
// before
return strlen($string) != strlen(utf8_decode($string));

// after
return strlen($string) != mb_strlen($string, "UTF-8");
```

## PHP 8.3

| Deprecated | Typical old code | Fix |
|---|---|---|
| `get_class()` and `get_parent_class()` without an argument | `get_class()` inside a method | `static::class`, `self::class` or `get_class( $this )` |
| Increment and decrement of non-numeric strings | `$letter++` to go from "a" to "b" | `str_increment()` where PHP has it (8.3 and later), otherwise `chr( ord( $letter ) + 1 )` |

## PHP 8.4

| Deprecated | Typical old code | Fix |
|---|---|---|
| Implicitly nullable parameters | `function f( eZContentObject $o = null )` | `?eZContentObject $o = null`; see [Nullable parameters and type declarations](nullable-parameters-and-types.md) |
| The `E_STRICT` constant | `error_reporting( E_ALL \| E_STRICT )` | `error_reporting( E_ALL )` (the kernel stopped using it in 6.0.8) |
| `trigger_error()` with `E_USER_ERROR` | stopping a script with an error message | throw an exception, or write the error and `exit` |

The implicitly nullable parameter is by far the most common one; se7enxweb/expquery_translator, commit 8ddf68d,
changed 35 parameters in 27 files for it.

## PHP 8.5

| Deprecated | Typical old code | Fix that runs on 8.0 too |
|---|---|---|
| `curl_close()`, `curl_share_close()` | closing a cURL handle | nothing: since PHP 8.0 the handle is an object that is freed by itself |
| `imagedestroy()` | freeing a GD image | nothing, for the same reason |
| `finfo_close()`, `xml_parser_free()` | freeing a resource | nothing, for the same reason |
| The `$http_response_header` variable | reading headers after `file_get_contents()` on a URL | `http_get_last_response_headers()` where it exists |
| The backtick operator | `` `ls` `` | `shell_exec( 'ls' )`, better no shell at all |
| Non-canonical casts | `(integer)`, `(boolean)`, `(double)` | `(int)`, `(bool)`, `(float)` |

These functions did nothing since PHP 8.0, but they did free memory on PHP 7. If your extension must still run on
PHP 7 somewhere, keep the call for those versions only. From se7enxweb/cjw_tmv, commit 0bfa27b:

```php
// before
curl_close( $ch );

// after
if ( PHP_VERSION_ID < 80000 ) curl_close( $ch ); // no effect since PHP 8.0, deprecated in 8.5
```

For an extension that needs PHP 8.0 or later anyway, just remove the call.

The response headers, from se7enxweb/sevenx_dse, commit dbeacb3 (shortened):

```php
$responseHeaders = function_exists( 'http_get_last_response_headers' )
    ? ( http_get_last_response_headers() ?: array() )
    : ( get_defined_vars()['http_response_header'] ?? array() );
```

`get_defined_vars()` reads the old variable without naming it in code, so PHP 8.5 does not report it, and the line
still works on PHP 8.0 to 8.4.

## A policy for your extension

1. Choose the oldest PHP you support. For an extension of Exponential 6 that is 8.0, unless you have a reason to
   require more. Write it into `composer.json` ([Modernizing composer.json](modernizing-composer-json.md)).
2. Use no syntax newer than that version: on 8.0 no enums, no `readonly`, no `never`, no first-class callable syntax.
3. Call a function newer than that version only behind `function_exists()`, with a fallback.
4. Fix deprecations of every newer version as soon as they appear: they are always cheap at that point.

The kernel follows the same rules ([PHP 8.0 support](../../../bc/6.0/php-8.0-support.md)).

## Related pages

- Previous: [PHP 8 errors that stop old code](php-8-errors-that-stop-old-code.md)
- Next: [Removed PHP functions and their replacements](removed-php-functions.md)
- [PHP 8 support](../../../bc/6.0/php8.md)
- The PHP manual's migration guides: [8.1](https://www.php.net/manual/en/migration81.deprecated.php),
  [8.2](https://www.php.net/manual/en/migration82.deprecated.php),
  [8.3](https://www.php.net/manual/en/migration83.deprecated.php),
  [8.4](https://www.php.net/manual/en/migration84.deprecated.php)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
