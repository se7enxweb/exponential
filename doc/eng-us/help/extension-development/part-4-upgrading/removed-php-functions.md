---
title: Removed PHP functions and their replacements
audience: developer
category: extension-development
order: 60
summary: The PHP functions old extensions call that PHP 7 and 8 removed - each(), create_function(), ereg(), split(), mysql_*(), magic quotes and others - with a tested replacement for each.
keywords: removed functions, each, create_function, ereg, split, mysql_query, money_format, get_magic_quotes_gpc, fgetss, mcrypt, replacement, upgrade
type: help
---
# Removed PHP functions and their replacements

Part 4, chapter 6. An extension written for PHP 5 may call functions that no longer exist. The result is
`Call to undefined function`, a fatal error, when the line runs. This chapter lists the functions old extensions use
most, the PHP version that removed them, and a replacement. Every function in the first table was checked to be
missing in PHP 8.0.

## Find them

```bash
grep -rnE '\b(each|create_function|split|spliti|ereg|eregi|ereg_replace|eregi_replace|mysql_[a-z_]+|money_format|get_magic_quotes_gpc|get_magic_quotes_runtime|set_magic_quotes_runtime|fgetss|call_user_method|session_register|mcrypt_[a-z_]+|is_real|hebrevc|convert_cyr_string)\s*\(' --include='*.php' extension/myoldext/
```

A hit inside a comment or inside a string is harmless; a hit preceded by `->` or `::` is a method of the same name,
not the PHP function.

## The table

| Removed | Since | Replacement |
|---|---|---|
| `each()` | 8.0 | `foreach`, or `key()` with `current()` and `next()` |
| `create_function()` | 8.0 | an anonymous function `function( $a ) { ... }` |
| `money_format()` | 8.0 | `NumberFormatter`, or the kernel's `eZLocale::instance()->formatCurrency()` |
| `get_magic_quotes_gpc()`, `get_magic_quotes_runtime()` | 8.0 | nothing: magic quotes were removed in 5.4; the result is always `false` |
| `fgetss()`, `gzgetss()` | 8.0 | `fgets()` with `strip_tags()` |
| `hebrevc()`, `convert_cyr_string()`, `restore_include_path()`, `ezmlm_hash()`, `image2wbmp()` | 8.0 | rarely used; see the PHP manual |
| `is_real()` | 8.0 | `is_float()` |
| `ereg()`, `eregi()`, `ereg_replace()`, `eregi_replace()`, `split()`, `spliti()` | 7.0 | `preg_match()`, `preg_replace()`, `explode()`, `preg_split()` |
| `mysql_*()` | 7.0 | the kernel's `eZDB` (see below), never `mysqli_*()` directly |
| `call_user_method()` | 7.0 | `call_user_func( array( $object, 'method' ) )` |
| `set_magic_quotes_runtime()` | 7.0 | nothing |
| `mcrypt_*()` | 7.2 | `openssl_encrypt()`, `sodium_crypto_secretbox()` |
| `session_register()` | 5.4 | `$_SESSION`, or the kernel's `eZHTTPTool::instance()->setSessionVariable()` |

## each()

The old idiom walks an array one entry at a time:

```php
// before
while ( list( $key, $value ) = each( $array ) )
{
    echo "$key=$value\n";
}

// after
foreach ( $array as $key => $value )
{
    echo "$key=$value\n";
}
```

Where the loop really depends on the array's internal pointer (it continues where an earlier loop stopped), keep the
pointer and replace only the call. That was the fix in the birthday extension during the PHP 8.0 review:

```php
<?php
function myOldExtNextPair( array &$array )
{
    $key = key( $array );
    if ( $key === null )
    {
        return false;
    }
    $value = current( $array );
    next( $array );
    return array( $key, $value );
}
```

## create_function()

`create_function()` built a function from a string with `eval()`, which is also why it was removed. Replace the string
with a real anonymous function:

```php
// before
usort( $nodes, create_function( '$a,$b', 'return strcmp( $a->attribute( "name" ), $b->attribute( "name" ) );' ) );

// after
usort( $nodes, function( $a, $b ) {
    return strcmp( $a->attribute( 'name' ), $b->attribute( 'name' ) );
} );
```

## ereg() and split()

POSIX regular expressions became PCRE ones. Add delimiters, and escape the delimiter if it appears in the pattern:

| Before | After |
|---|---|
| `ereg( '^[0-9]+$', $s )` | `preg_match( '/^[0-9]+$/', $s )` |
| `eregi( 'abc', $s )` | `preg_match( '/abc/i', $s )` |
| `ereg_replace( '[^a-z]', '', $s )` | `preg_replace( '/[^a-z]/', '', $s )` |
| `split( ',', $s )` | `explode( ',', $s )` (a plain string needs no regular expression) |
| `split( '[,;]', $s )` | `preg_split( '/[,;]/', $s )` |

`split()` with a plain separator was the fix in enhancedezbinaryfile during the PHP 8.0 review.

> **Warning**
> Check what `ereg()` returned before you rely on the replacement. `ereg()` returned the length of the match or
> `false`; `preg_match()` returns `1`, `0` or `false`. Code that compared the result with a number needs a second look.

## mysql_*()

An extension that talks to the database through `mysql_query()` was already wrong in its own time: it worked only on
MySQL and only on the kernel's connection by chance. Exponential 6 supports several database engines, and the
replacement is the kernel's database layer, which uses the site's connection:

```php
<?php
$db = eZDB::instance();
$id = (int)$http->postVariable( 'ItemID' );
$name = $db->escapeString( $http->postVariable( 'Name' ) );

$rows = $db->arrayQuery( "SELECT id, name FROM myoldext_item WHERE id = $id" );
$db->query( "UPDATE myoldext_item SET name = '$name' WHERE id = $id" );
```

Integers are cast with `(int)`, strings go through `escapeString()`. For anything more than a few queries, use
`eZPersistentObject` instead ([Persistent objects and your own tables](../part-3-extension-developer/persistent-objects-and-tables.md)).
The whole topic is in [Database code for PHP 8 and every engine](database-code-for-php-8.md).

## Magic quotes

Old code often wrapped every input in a check like this:

```php
// before
$value = get_magic_quotes_gpc() ? stripslashes( $_POST['value'] ) : $_POST['value'];

// after
$value = $http->postVariable( 'value' );
```

Magic quotes were removed in PHP 5.4, so the `stripslashes()` branch has not run for over a decade. Read input
through `eZHTTPTool` (`$http = eZHTTPTool::instance();`), which the kernel's modules already do.

## mcrypt

`mcrypt` was removed from PHP in 7.2. Data encrypted with it can be decrypted with OpenSSL only for some of its
ciphers and modes, and only with care. Plan this change separately: write a one-time script that decrypts with the
old method on a PHP 7.1 installation and re-encrypts with `sodium_crypto_secretbox()` or `openssl_encrypt()`.
The sources of this project do not contain such a migration, so test it on a copy of the data.

## When a library is the problem

Sometimes the removed function is inside a third-party library the extension ships (an old PDF writer, an old mail
library). Do not patch the library line by line. Look for a newer release of the same library, or for a maintained
replacement, and upgrade it as a whole, with its license. Write down in the extension's changelog which version you
moved to and why.

## Related pages

- Previous: [PHP 8.1 to 8.5 deprecations](php-8-1-to-8-5-deprecations.md)
- Next: [Old-style constructors and static calls](old-constructors-and-static-calls.md)
- [PHP 8.0 support](../../../bc/6.0/php-8.0-support.md)
- [Removed extensions and SAPIs](https://www.php.net/manual/en/migration70.removed-exts-sapis.php) in the PHP manual
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
