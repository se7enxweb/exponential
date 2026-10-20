---
title: "PHP string functions: search and compare"
audience: developer, designer, app-creator
category: development
order: 160
summary: The searching, comparing and similarity operators of eZTemplateStringsOperator (strlen, strpos, stripos, strrpos, strripos, strstr, stristr, strrchr, strpbrk, strspn, strcspn, substr_count, str_contains, str_starts_with, str_ends_with, strcmp, strcasecmp, strncmp, strncasecmp, strnatcmp, strnatcasecmp, strcoll, substr_compare, levenshtein, similar_text, soundex, metaphone), how their parameters map to PHP, with examples rendered on alpha.
keywords: strlen, strpos, stripos, strrpos, strripos, strstr, stristr, strrchr, strpbrk, strspn, strcspn, substr_count, str_contains, str_starts_with, str_ends_with, strcmp, strcasecmp, strncmp, strncasecmp, strnatcmp, strnatcasecmp, strcoll, substr_compare, levenshtein, similar_text, soundex, metaphone, eZTemplateStringsOperator
type: help
---
# PHP string functions: search and compare

`eZTemplateStringsOperator` (`lib/eztemplate/classes/eztemplatestringsoperator.php`) makes 60 PHP string functions
available as template operators under their PHP names. This page covers the 27 that measure, search and compare
strings. The others are on [PHP string functions: transform](php-string-functions-transform.md) and
[PHP string functions: encode and format](php-string-functions-encode-and-format.md).

## Contents

1. [How the PHP functions are mapped](#how-the-php-functions-are-mapped)
2. [Length and position](#length-and-position)
3. [Finding substrings](#finding-substrings)
4. [Contains, starts with, ends with](#contains-starts-with-ends-with)
5. [Comparing](#comparing)
6. [Similarity](#similarity)
7. [Related pages](#related-pages)

## How the PHP functions are mapped

The class comment states the rule, and `modify()` follows it:

- The **piped input** is the PHP function's main string argument (cast to a string).
- The **template parameters** are the remaining PHP arguments, in order, each cast to the type PHP expects.
- There are no named parameters: every argument is positional (`operatorTemplateHints()` declares none).
- A function that returns `false` for "not found" returns `false` in the template too. Test it with
  `choose`, `eq( false() )` or `is_boolean`, because position 0 is also a valid answer.
- A missing required argument logs a warning such as "Missing required parameter: needle" and leaves the input
  unchanged.

Only the names in `lib/eztemplate/classes/eztemplateautoload.php` are registered. Functions that already have a
template operator of their own (`trim`, `nl2br`, `md5` and others listed in the class comment) are not duplicated
here.

In the examples, `choose( 'false', 'true' )` prints a boolean as a word.

## Length and position

### strlen

PHP `strlen()`: the length in **bytes**. For characters use [count_chars](string-operators.md#count_chars).
No parameters. Returns an integer. Generated reference: [strlen](../../examples/template-operators/strlen.md).

### strpos, stripos, strrpos, strripos

The position of the first (`strpos`, case-insensitive `stripos`) or last (`strrpos`, case-insensitive `strripos`)
occurrence of a needle, counted in bytes from 0, or `false`.

| Parameter | Type | Required | Default |
|---|---|---|---|
| needle | string | yes | none |
| offset | integer | no | PHP's 0 |

Returns an integer or `false`. Generated references: [strpos](../../examples/template-operators/strpos.md),
[stripos](../../examples/template-operators/stripos.md), [strrpos](../../examples/template-operators/strrpos.md),
[strripos](../../examples/template-operators/strripos.md).

```tpl
{def $s = 'Fit & Healthy: healthy eating'}
strlen = {$s|strlen}
strpos( 'healthy' ) = {$s|strpos( 'healthy' )}
stripos( 'healthy' ) = {$s|stripos( 'healthy' )}
strrpos( 'h' ) = {$s|strrpos( 'h' )}
strripos( 'HEALTHY' ) = {$s|strripos( 'HEALTHY' )}
strpos( 'healthy', 20 ) = [{$s|strpos( 'healthy', 20 )|choose( 'false', 'true' )}] (false: not found after 20)
```

Output on alpha:

```text
strlen = 29
strpos( 'healthy' ) = 15
stripos( 'healthy' ) = 6
strrpos( 'h' ) = 20
strripos( 'HEALTHY' ) = 15
strpos( 'healthy', 20 ) = [false] (false: not found after 20)
```

## Finding substrings

### strstr and stristr

The part of the input from the first occurrence of the needle on (`strstr`; case-insensitive `stristr`), or, with
the second parameter true, the part **before** it. `false` when the needle is not found.

| Parameter | Type | Required | Default |
|---|---|---|---|
| needle | string | yes | none |
| before_needle | boolean | no | PHP's `false` |

Generated references: [strstr](../../examples/template-operators/strstr.md), [stristr](../../examples/template-operators/stristr.md).

### strrchr

The part from the **last** occurrence of a character on, or `false`. Parameter: needle (string, required).
Generated reference: [strrchr](../../examples/template-operators/strrchr.md).

### strpbrk

The part from the first character that is in the list, or `false`. Parameter: char_list (string, required).
Generated reference: [strpbrk](../../examples/template-operators/strpbrk.md).

### strspn and strcspn

`strspn`: the length of the initial segment made only of characters in the mask. `strcspn`: the length of the initial
segment made of characters **not** in the mask.

| Parameter | Type | Required | Default |
|---|---|---|---|
| mask | string | yes | none |
| offset | integer | no | PHP's 0 |
| length | integer | no | PHP's `null` (to the end) |

Returns an integer. Generated references: [strspn](../../examples/template-operators/strspn.md),
[strcspn](../../examples/template-operators/strcspn.md).

### substr_count

How often the needle occurs (not overlapping).

| Parameter | Type | Required | Default |
|---|---|---|---|
| needle | string | yes | none |
| offset | integer | no | PHP's 0 |
| length | integer | no | PHP's `null` |

Returns an integer. Generated reference: [substr_count](../../examples/template-operators/substr_count.md).

```tpl
{def $s = 'recipes/pasta/almond-french-toast'}
strstr( '/' ) = {$s|strstr( '/' )}
strstr( '/', true() ) = {$s|strstr( '/', true() )}
stristr( 'PASTA' ) = {$s|stristr( 'PASTA' )}
strrchr( '/' ) = {$s|strrchr( '/' )}
strpbrk( '-/' ) = {$s|strpbrk( '-/' )}
strspn( 'abcdefghijklmnopqrstuvwxyz' ) = {$s|strspn( 'abcdefghijklmnopqrstuvwxyz' )}
strcspn( '/' ) = {$s|strcspn( '/' )}
substr_count( '-' ) = {$s|substr_count( '-' )}
```

Output on alpha:

```text
strstr( '/' ) = /pasta/almond-french-toast
strstr( '/', true() ) = recipes
stristr( 'PASTA' ) = pasta/almond-french-toast
strrchr( '/' ) = /almond-french-toast
strpbrk( '-/' ) = /pasta/almond-french-toast
strspn( 'abcdefghijklmnopqrstuvwxyz' ) = 7
strcspn( '/' ) = 7
substr_count( '-' ) = 2
```

## Contains, starts with, ends with

### str_contains, str_starts_with, str_ends_with

PHP 8's `str_contains()`, `str_starts_with()` and `str_ends_with()`: case-sensitive tests on bytes. The class has a
fallback with `strpos()`/`substr()` for a PHP that lacks them. An empty needle is always found.

| Parameter | Type | Required | Default |
|---|---|---|---|
| needle | string | yes | none |

Returns a boolean. The array operators `contains`, `begins_with` and `ends_with` do the same for strings with `mb_*`
functions ([Array operators](array-operators.md)). Generated references:
[str_contains](../../examples/template-operators/str_contains.md),
[str_starts_with](../../examples/template-operators/str_starts_with.md),
[str_ends_with](../../examples/template-operators/str_ends_with.md).

```tpl
{def $file = 'Healthy-Breakfast.JPG'}
str_contains( 'Breakfast' ) = {$file|str_contains( 'Breakfast' )|choose( 'false', 'true' )}
str_starts_with( 'Healthy' ) = {$file|str_starts_with( 'Healthy' )|choose( 'false', 'true' )}
str_ends_with( '.jpg' ) = {$file|str_ends_with( '.jpg' )|choose( 'false', 'true' )}
str_ends_with( '.JPG' ) = {$file|str_ends_with( '.JPG' )|choose( 'false', 'true' )}
```

Output on alpha:

```text
str_contains( 'Breakfast' ) = true
str_starts_with( 'Healthy' ) = true
str_ends_with( '.jpg' ) = false
str_ends_with( '.JPG' ) = true
```

## Comparing

All comparison functions return 0 when the strings are equal, a negative number when the input sorts first and a
positive number when it sorts after. They are the operators to use for ordering text, because the logic operators
`lt` and `gt` compare strings by their length ([Logic operators](logic-operators.md#lt-gt-le-and-ge)).

### strcmp and strcasecmp

Binary comparison, case-sensitive (`strcmp`) or not (`strcasecmp`). Parameter: string2 (string, required).
Generated references: [strcmp](../../examples/template-operators/strcmp.md), [strcasecmp](../../examples/template-operators/strcasecmp.md).

### strncmp and strncasecmp

The same, on the first n bytes only.

| Parameter | Type | Required | Default |
|---|---|---|---|
| string2 | string | yes | none |
| n | integer | yes | none |

Without both it logs "Missing required parameters: string2 and n". Generated references:
[strncmp](../../examples/template-operators/strncmp.md), [strncasecmp](../../examples/template-operators/strncasecmp.md).

### strnatcmp and strnatcasecmp

"Natural order" comparison, which treats runs of digits as numbers: `img2` sorts before `img10`. Parameter: string2
(string, required). Generated references: [strnatcmp](../../examples/template-operators/strnatcmp.md),
[strnatcasecmp](../../examples/template-operators/strnatcasecmp.md).

### strcoll

Comparison by the current locale (`LC_COLLATE`). Parameter: string2 (string, required).
Generated reference: [strcoll](../../examples/template-operators/strcoll.md).

### substr_compare

Compares the input from an offset with another string.

| Parameter | Type | Required | Default |
|---|---|---|---|
| string | string | yes | none |
| offset | integer | yes | none |
| length | integer | no | PHP's `null` |
| case_insensitive | boolean | no | PHP's `false` |

Generated reference: [substr_compare](../../examples/template-operators/substr_compare.md).

```tpl
'apple'|strcmp( 'banana' ) = {'apple'|strcmp( 'banana' )}
'Apple'|strcasecmp( 'apple' ) = {'Apple'|strcasecmp( 'apple' )}
'img12'|strcmp( 'img10' ) = {'img12'|strcmp( 'img10' )}
'img2'|strcmp( 'img10' ) = {'img2'|strcmp( 'img10' )}
'img2'|strnatcmp( 'img10' ) = {'img2'|strnatcmp( 'img10' )}
'IMG2'|strnatcasecmp( 'img10' ) = {'IMG2'|strnatcasecmp( 'img10' )}
'Healthy'|strncmp( 'Health', 6 ) = {'Healthy'|strncmp( 'Health', 6 )}
'HEALTHY'|strncasecmp( 'health', 6 ) = {'HEALTHY'|strncasecmp( 'health', 6 )}
'b'|strcoll( 'a' ) = {'b'|strcoll( 'a' )}
'Hello world'|substr_compare( 'world', 6 ) = {'Hello world'|substr_compare( 'world', 6 )}
'Hello WORLD'|substr_compare( 'world', 6, 5, true() ) = {'Hello WORLD'|substr_compare( 'world', 6, 5, true() )}
```

Output on alpha:

```text
'apple'|strcmp( 'banana' ) = -1
'Apple'|strcasecmp( 'apple' ) = 0
'img12'|strcmp( 'img10' ) = 2
'img2'|strcmp( 'img10' ) = 1
'img2'|strnatcmp( 'img10' ) = -1
'IMG2'|strnatcasecmp( 'img10' ) = -1
'Healthy'|strncmp( 'Health', 6 ) = 0
'HEALTHY'|strncasecmp( 'health', 6 ) = 0
'b'|strcoll( 'a' ) = 1
'Hello world'|substr_compare( 'world', 6 ) = 0
'Hello WORLD'|substr_compare( 'world', 6, 5, true() ) = 0
```

A test for "the same word, ignoring case":

```tpl
{def $a = 'Running'
     $b = 'running'}
{if $a|strcasecmp( $b )|eq( 0 )}"{$a}" and "{$b}" are the same word, ignoring case.{/if}
```

Output on alpha:

```text
"Running" and "running" are the same word, ignoring case.
```

Without its required parameter a comparison leaves the input as it was and logs a warning:

```tpl
[{'abc'|strcmp}]
```

Output on alpha:

```text
[abc]
```

Logged by the template engine:

```text
WARNING strcmp: Missing required parameter: string2
```

## Similarity

### levenshtein

The edit distance between the input and another string. Parameter: string2 (string, required). Returns an integer.
Generated reference: [levenshtein](../../examples/template-operators/levenshtein.md).

### similar_text

The number of matching characters (PHP `similar_text()` without its percent argument). Parameter: string2 (string,
required). Returns an integer. Generated reference: [similar_text](../../examples/template-operators/similar_text.md).

### soundex and metaphone

Phonetic keys for English words: `soundex` (no parameters) and `metaphone` (optional max_phonemes, integer).
Return strings. Generated references: [soundex](../../examples/template-operators/soundex.md),
[metaphone](../../examples/template-operators/metaphone.md).

```tpl
'recipe'|levenshtein( 'recipes' ) = {'recipe'|levenshtein( 'recipes' )}
'healthy'|similar_text( 'wealthy' ) = {'healthy'|similar_text( 'wealthy' )}
'Robert'|soundex = {'Robert'|soundex}, 'Rupert'|soundex = {'Rupert'|soundex}
'Thompson'|metaphone = {'Thompson'|metaphone}
'Thompson'|metaphone( 2 ) = {'Thompson'|metaphone( 2 )}
```

Output on alpha:

```text
'recipe'|levenshtein( 'recipes' ) = 1
'healthy'|similar_text( 'wealthy' ) = 6
'Robert'|soundex = R163, 'Rupert'|soundex = R163
'Thompson'|metaphone = 0MPSN
'Thompson'|metaphone( 2 ) = 0M
```

## Related pages

- [PHP string functions: transform](php-string-functions-transform.md)
- [PHP string functions: encode and format](php-string-functions-encode-and-format.md)
- [String operators](string-operators.md): `wash`, `shorten`, `upcase` and the other native operators
- [String operators for designers](../designers/templates/string-template-operators.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
