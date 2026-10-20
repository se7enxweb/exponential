---
title: Array operators
audience: designer, developer
category: templates
order: 190
summary: Building and working with arrays and hashes in templates: array, hash, append, prepend, merge, contains, extract, extract_left, extract_right, implode, explode, reverse, unique, insert, remove, repeat, begins_with, ends_with, compare, array_sum, count.
keywords: array operators, array, hash, append, prepend, merge, contains, extract, extract_left, extract_right, implode, explode, reverse, unique, insert, remove, repeat, array_sum, count, list handling
type: help
---
# Array operators

This page is for designers who build lists and look things up in them. The operators come from
`eZTemplateArrayOperator` (`lib/eztemplate/classes/eztemplatearrayoperator.php`). Most also work on strings.

## Contents

1. [Building](#building)
2. [Testing](#testing)
3. [Taking parts](#taking-parts)
4. [Joining and splitting](#joining-and-splitting)
5. [Changing](#changing)
6. [Older names](#older-names)

## Building

```tpl
{def $sizes = array( 'small', 'medium', 'large' )}
{def $labels = hash( 'small', 'S', 'medium', 'M', 'large', 'L' )}
{set $sizes = $sizes|append( 'xlarge' )}
{set $sizes = $sizes|prepend( 'tiny' )}
{def $all = $sizes|merge( $more_sizes )}
```

`append`, `prepend` and `merge` return a new array; assign it with `{set}`.

## Testing

| Expression | True when |
|---|---|
| `$list\|contains( 'x' )` | the array (or string) contains the value |
| `$text\|begins_with( 'http' )` | starts with |
| `$text\|ends_with( '.pdf' )` | ends with |
| `$a\|compare( $b )` | the two are equal |
| `$list\|count` | (a number) the number of items; `0` counts as false in `{if}` |

## Taking parts

```tpl
{$list|extract( 2, 3 )}        {* 3 items from position 2 *}
{$list|extract_left( 3 )}      {* the first 3 *}
{$list|extract_right( 2 )}     {* the last 2 *}
{$list|reverse}
{$list|unique}
```

## Joining and splitting

```tpl
{$tags|implode( ', ' )}                         {* array to string *}
{$csv|explode( ',' )}                           {* string to array *}
{$user.role_id_list|implode( ',' )}             {* a common cache-block key *}
```

## Changing

```tpl
{$list|insert( 1, 'new' )}      {* insert at position 1 *}
{$list|remove( 0, 1 )}          {* remove 1 item at position 0 *}
{'-'|repeat( 10 )}              {* '----------' *}
{$numbers|array_sum}
```

## Older names

`array_append`, `array_prepend` and `array_merge` are marked deprecated in the autoload file; use `append`, `prepend` and
`merge`.

## Related pages

- [Foreach loops](foreach-loops.md)
- [Operator reference](operator-reference.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
