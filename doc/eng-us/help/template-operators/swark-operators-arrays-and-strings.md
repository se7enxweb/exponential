---
title: "Swark operators: arrays, strings and URLs"
audience: developer, designer, app-creator
category: development
order: 431
summary: Every array, string and URL operator of the swark extension - sort, rsort, asort, arsort, ksort, krsort, shuffle, array_search, range, set_array_element, remove_array_element, split_by_length, shortenw, preg_match, preg_replace, strpos, strrpos, substr, ltrim, rtrim, str_replace, json_encode, uri_path_segment, add_view_parameters, modify_view_parameter - with parameters, return values and examples rendered on alpha.
keywords: swark, sort, rsort, asort, arsort, ksort, krsort, shuffle, array_search, range, set_array_element, remove_array_element, split_by_length, shortenw, preg_match, preg_replace, uri_path_segment, add_view_parameters, modify_view_parameter
type: help
---
# Swark operators: arrays, strings and URLs

The classes are in `extension/swark/autoloads/classes/`, one per operator; how they are registered and why some names
behave differently on alpha is explained in [The swark operators](swark-operators.md). Parameter defaults are
strings, as the base class reads them from `name=value`. All of these operators take their input from the value
before `|` and return a new value; none of them changes anything outside the template.

## Contents

1. [Sorting](#sorting): [sort](#sort), [rsort](#rsort), [asort](#asort), [arsort](#arsort), [ksort](#ksort),
   [krsort](#krsort), [shuffle](#shuffle)
2. [Arrays](#arrays): [array_search](#array_search), [range](#range), [set_array_element](#set_array_element),
   [remove_array_element](#remove_array_element), [split_by_length](#split_by_length)
3. [Strings](#strings): [shortenw](#shortenw), [preg_match](#preg_match), [preg_replace](#preg_replace),
   [strpos](#strpos), [strrpos](#strrpos), [substr](#substr), [ltrim](#ltrim), [rtrim](#rtrim),
   [str_replace](#str_replace), [json_encode](#json_encode)
4. [URLs and view parameters](#urls-and-view-parameters): [uri_path_segment](#uri_path_segment),
   [add_view_parameters](#add_view_parameters), [modify_view_parameter](#modify_view_parameter)

## Sorting

None of the sorting operators takes parameters. The input is an array; the sorted copy is returned.

### sort

PHP `sort()`: values ascending, keys renumbered.

### rsort

PHP `rsort()`: values descending, keys renumbered.

### asort

PHP `asort()`: values ascending, keys kept.

### arsort

PHP `arsort()`: values descending, keys kept.

### ksort

PHP `ksort()`: keys ascending.

### krsort

PHP `krsort()`: keys descending.

### shuffle

PHP `shuffle()` when the input is an array (other input is returned unchanged). Never use it inside cached output:
the order is frozen until the cache expires.

```tpl
{def $h = hash( 'b', 2, 'c', 3, 'a', 1 )
     $l = array( 'pear', 'apple', 'fig' )}
sort:    {$l|sort|implode( ' ' )}
rsort:   {$l|rsort|implode( ' ' )}
asort:   {foreach $h|asort as $k => $v}{$k}={$v} {/foreach}

arsort:  {foreach $h|arsort as $k => $v}{$k}={$v} {/foreach}

ksort:   {foreach $h|ksort as $k => $v}{$k}={$v} {/foreach}

krsort:  {foreach $h|krsort as $k => $v}{$k}={$v} {/foreach}

shuffle keeps {$l|shuffle|count} items
```

Output on alpha:

```text
sort:    apple fig pear
rsort:   pear fig apple
asort:   a=1 b=2 c=3 
arsort:  c=3 b=2 a=1 
ksort:   a=1 b=2 c=3 
krsort:  c=3 b=2 a=1 
shuffle keeps 3 items
```

## Arrays

### array_search

| Parameter | Required | Default |
|---|---|---|
| `search` | yes | |
| `not_found_key` | no | `-1` |

Returns the key of the first element equal to `search` (PHP `array_search()`, loose comparison), or `not_found_key`.

### range

| Parameter | Required | Default |
|---|---|---|
| `min` | yes | |
| `max` | yes | |
| `step` | no | `1` |

No input. Returns the numbers from `min` to `max`; the sign of `step` is adjusted to the direction, and a `step` of 0
or `min` equal to `max` gives `array( min )`.

### set_array_element

| Parameter | Required |
|---|---|
| `key` | yes |
| `value` | yes |

Returns the input array with `key` set to `value`.

### remove_array_element

| Parameter | Required |
|---|---|
| `key` | yes |

Returns the input array without `key`.

### split_by_length

| Parameter | Required | Default |
|---|---|---|
| `length` | no | `1` |

Cuts a string into pieces of `length` bytes. An array of lengths is used in turn (`array( 4, 2, 2 )` splits a
date-like string). The cut is by bytes (PHP `substr()`), so it can split a multibyte character.

```tpl
{def $l = array( 'pear', 'apple', 'fig' )
     $h = hash( 'a', 1, 'b', 2 )}
1 array_search apple: {$l|array_search( 'apple' )}, kiwi: {$l|array_search( 'kiwi' )}, kiwi with default: {$l|array_search( 'kiwi', 'none' )}
2 range: {range( 1, 10, 3 )|implode( ',' )} / {range( 5, 1 )|implode( ',' )} / {range( 0, 1, 0.25 )|implode( ',' )}
3 set_array_element: {foreach $h|set_array_element( 'c', 3 ) as $k => $v}{$k}={$v} {/foreach}

4 remove_array_element: {foreach $h|remove_array_element( 'a' ) as $k => $v}{$k}={$v} {/foreach}

5 split_by_length: {'2026101912'|split_by_length( array( 4, 2, 2 ) )|implode( '-' )} / {'abcdef'|split_by_length( 2 )|implode( ' ' )}
```

Output on alpha:

```text
1 array_search apple: 1, kiwi: -1, kiwi with default: none
2 range: 1,4,7,10 / 5,4,3,2,1 / 0,0.25,0.5,0.75,1
3 set_array_element: a=1 b=2 c=3 
4 remove_array_element: b=2 
5 split_by_length: 2026-10-19-12 / ab cd ef
```

## Strings

### shortenw

| Parameter | Required | Default |
|---|---|---|
| `length` | yes | |
| `ellipsis` | no | `...` |

Shortens to at most `length` bytes including the ellipsis, cutting back to the last space so that no word is broken.
Unlike the kernel's `shorten`, it never cuts inside a word (when there is a space to cut at).

### preg_match

| Parameter | Required |
|---|---|
| `pattern` | yes |

Returns the **number of matches** (it calls `preg_match_all()`), not the matches themselves.

### preg_replace

| Parameter | Required |
|---|---|
| `search` | yes |
| `replace` | yes |

PHP `preg_replace( search, replace, input )`. The pattern is used as given: never build it from request input.

```tpl
{def $text = 'The quick brown fox jumps over the lazy dog'}
1 shortenw: {$text|shortenw( 20 )} / {$text|shortenw( 20, ' [more]' )}
2 preg_match counts: {$text|preg_match( '/o/' )}
3 preg_replace: {$text|preg_replace( '/\s+/', '_' )}
4 uri_path_segment: {'/recipes/salads/green-salad?x=1'|uri_path_segment} / {'/recipes/salads/green-salad'|uri_path_segment( 0 )} / {'https://example.com/a/b/'|uri_path_segment( -2 )}
5 add_view_parameters: {'/recipes/'|add_view_parameters( hash( 'offset', 20, 'tag', 'vegan' ) )}
6 modify_view_parameter: {'/recipes/(offset)/20/(tag)/vegan'|modify_view_parameter( 'offset', 40 )} / {'/recipes/(offset)/20'|modify_view_parameter( 'offset', false() )} / {'/recipes'|modify_view_parameter( 'tag', 'soup' )}
```

Output on alpha:

```text
1 shortenw: The quick brown... / The quick [more]
2 preg_match counts: 4
3 preg_replace: The_quick_brown_fox_jumps_over_the_lazy_dog
4 uri_path_segment: green-salad / recipes / a
5 add_view_parameters: /recipes/(offset)/20/(tag)/vegan
6 modify_view_parameter: /recipes/(offset)/40/(tag)/vegan / /recipes / /recipes/(tag)/soup
```

### strpos

Parameters `needle` (required) and `offset` (default `0`). Returns the position, or **-1** when not found. On alpha
the kernel's `strpos` (returning `false`) often answers instead; see
[Ownership can change during a request](swark-operators.md#ownership-can-change-during-a-request).

### strrpos

Parameter `needle`. The last position, or -1. The same caveat as `strpos`.

### substr

Parameters `start` (required) and `length` (default `0`, meaning "to the end"). The same caveat.

### ltrim

Parameter `charlist` (default empty: whitespace). The same caveat.

### rtrim

Parameter `charlist` (default empty: whitespace). The same caveat.

### str_replace

Parameters `search` and `replace`: PHP `str_replace()` on the input. On alpha the name belongs to owsimpleoperator,
which is registered later.

### json_encode

Parameter `value`: the input, or `value` when there is no input, as JSON (PHP `json_encode()`, or an encoder of its
own where that function is missing). On alpha the name belongs to ezjscore; see
[ezjscore operators in depth](ezjscore-operators.md#json_encode).

## URLs and view parameters

### uri_path_segment

| Parameter | Required | Default |
|---|---|---|
| `index` | no | `-1` (the last segment) |

Returns one segment of the path of a URL or path (scheme, host, query and fragment are ignored); a negative index
counts from the end. `false` when there is no such segment.

### add_view_parameters

| Parameter | Required |
|---|---|
| `view_parameters` | yes |

Appends `/(key)/value` for every entry of a hash to the input path (trailing slash removed first). The values are not
encoded: wash or `urlencode` user values before.

### modify_view_parameter

| Parameter | Required |
|---|---|
| `parameter` | yes |
| `value` | yes |

Sets one view parameter in a path: replaces its value when present, appends it when not, and removes it when `value`
is `false()`. An empty input becomes `/`. Useful for paging and filter links built from the current URL.

The examples of these three are lines 4 to 6 of the strings example above.

## Related pages

- [The swark operators](swark-operators.md)
- [Swark operators: request and system](swark-operators-request-and-system.md)
- [Array operators for designers](../designers/templates/array-operators.md)
- [Pagination](../designers/templates/pagination.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
