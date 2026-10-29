---
title: Writing code that runs on PHP 8.0
audience: php-developer, developer
category: php-development
order: 60
summary: The rules for PHP code that must run on PHP 8.0, the stock PHP of RHEL 9 (and, without support, on 7.4 for the kernel) - the syntax you may not use, how lib/phpcompat.php and function_exists() guards work, arguments added in later versions, the HTML escaping flags, and how to check your code with PHP 8.0 and PHPCompatibility.
keywords: PHP 8.0, compatibility, phpcompat.php, function_exists, PHP_VERSION_ID, array_is_list, fputcsv, fsync, enums, readonly, never, first-class callable, htmlspecialchars flags, PHPCompatibility, testVersion, lint80
type: help
---
# Writing code that runs on PHP 8.0

This page is for PHP developers who write kernel code or extensions that ship with Exponential. Every such file must
run on PHP 8.0, the stock PHP of RHEL 9, and on 8.5, the newest; the kernel itself is also usable without support on PHP 7.4 from 6.0.15 ([Supported PHP versions](supported-php-versions.md#php-80-supported-php-74-usable-without-support)). The rules are short, and the tools check them,
but CI runs PHPUnit only on 8.1 and later: on 8.0 your code is checked by syntax and boot only, so get it right as you
write it.

## Contents

1. [Syntax you may not use](#syntax-you-may-not-use)
2. [Functions newer than PHP 8.0](#functions-newer-than-php-80)
3. [Arguments that later versions added](#arguments-that-later-versions-added)
4. [Defaults that changed: HTML escaping](#defaults-that-changed-html-escaping)
5. [Checking your code](#checking-your-code)
6. [Examples from the kernel](#examples-from-the-kernel)

## Syntax you may not use

PHP 8.0 cannot parse these, so one occurrence makes the whole file fail to load:

| Syntax | Since | Write instead |
|---|---|---|
| `enum Status { ... }` | 8.1 | class constants |
| `readonly` properties and classes | 8.1, 8.2 | a private property with a getter |
| `never` return type | 8.1 | no return type (the kernel style has none anyway) |
| `new` in initializers (`function f( $x = new Foo() )`) | 8.1 | `$x = null`, then `$x = $x ?? new Foo()` |
| first-class callables (`strlen(...)`) | 8.1 | `'strlen'` or `array( $object, 'method' )` or a closure |
| intersection types (`A&B`) | 8.1 | no type (see [Why the code has no type declarations](why-no-type-declarations.md)) |
| typed class constants | 8.3 | an untyped constant |

Attributes such as `#[\ReturnTypeWillChange]` and `#[\Override]` are harmless on 8.0: PHP 8.0 reads them as attributes
and ignores unknown ones.

## Functions newer than PHP 8.0

There are two ways, and the kernel uses both:

1. **A function that can be written in PHP with the same result goes into `lib/phpcompat.php`**, inside
   `if ( !function_exists( '...' ) )`. `autoload.php` loads the file only when `PHP_VERSION_ID < 80100`, so on 8.1 and
   later it is not even opened and PHP's own function runs. Today it defines `array_is_list()`.

   ```php
   if ( !function_exists( 'array_is_list' ) )
   {
       function array_is_list( array $array )
       {
           $i = 0;
           foreach ( $array as $key => $unused )
           {
               if ( $key !== $i++ )
                   return false;
           }
           return true;
       }
   }
   ```

2. **A function that cannot be written in PHP is called behind `function_exists()`**, with a fallback that is correct
   on 8.0:

   ```php
   fflush( $handle );
   if ( function_exists( 'fsync' ) )
       fsync( $handle );
   ```

   The kernel does this for `fsync()` (8.1), `memory_reset_peak_usage()` (8.2), `str_increment()` and
   `str_decrement()` (8.3) and image functions such as `imageavif()`.

## Arguments that later versions added

A function that exists on 8.0 but gained a parameter later throws an `ArgumentCountError` on 8.0 when you pass it.
Branch on `PHP_VERSION_ID`:

```php
if ( PHP_VERSION_ID >= 80100 )
    fputcsv( $handle, $row, ',', '"', '\\', "\r\n" );
else
{
    // PHP 8.0: no $eol parameter; write the line and fix its ending afterwards
}
```

This is how `kernel/classes/subitems/expsubitemscsvexport.php` writes CSV on every version.

## Defaults that changed: HTML escaping

PHP 8.1 changed the default flags of `htmlspecialchars()`, `htmlentities()`, `html_entity_decode()` and
`htmlspecialchars_decode()` to `ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401`. PHP 8.0's default leaves `'` unescaped, so
a value in a single-quoted HTML attribute would not be safe on 8.0. **Always pass the flags explicitly:**

```php
$html = htmlspecialchars( $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, 'UTF-8' );
```

The template operator `wash` and 27 other kernel and extension files pass them since 6.0.15, so every version gives the
same output ([Escaping output from PHP](escaping-output-from-php.md)).

## Checking your code

| Check | Command | Catches |
|---|---|---|
| PHP 8.0 syntax of changed files | `make lint80` (finds the binary as `EXP_PHP80`, `php8.0`, `php80`, or `PHP80=/path`) | parse errors from newer syntax |
| PHP 8.0 syntax of every file | `make lint80-all PHP80=/path/to/php8.0` | the same, everywhere, as CI's `php80` job |
| PHPCompatibility | `make phpcs` (`testVersion` `8.0-` in `phpcs.xml.dist`) | newer functions and arguments, removed and deprecated functions |
| Boot on 8.0 | `php bin/php/ezexec.php tests/bin/bootsmoke.php` with PHP 8.0 | a class that cannot load |
| Install on 8.0 | `composer install --no-dev --prefer-dist` with PHP 8.0, in a clean checkout | a dependency whose minimum is too high |

`bootsmoke.php` loads the classes of `autoload/ezp_kernel.php` and the extension map (leaving out the command, view and
cronjob classes and the tests, whose syntax is linted instead), so a fatal error at class load
time, such as a method signature that no longer matches its parent, shows up even without a test that uses the class.

## Examples from the kernel

The 6.0.15 work found these, and the same mistakes are common in extensions
([PHP 8.0 support](../../bc/6.0/php-8.0-support.md)):

| Mistake | Effect on PHP 8 | Fix |
|---|---|---|
| `implode( $pieces, $glue )` | `TypeError` since 8.0 | `implode( $glue, $pieces )` |
| `crypt()` without a salt | `ArgumentCountError` since 8.0 | a random value from `random_bytes()` |
| a by-reference parameter where the parent's is by value | fatal error when the class loads | match the parent's signature |
| a subclass method with fewer parameters than the parent's | fatal error when the class loads | add the parameters |
| `each()`, `split()`, `create_function()` | removed | `foreach`, `explode()`, a closure (`phpcs.xml.dist` forbids them) |

## Related pages

- [Supported PHP versions: 8.0 to 8.5](supported-php-versions.md)
- [PHP 8 migration notes for your code](php-8-migration-notes-for-your-code.md)
- [Removed PHP functions](../extension-development/part-4-upgrading/removed-php-functions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
