---
title: Math operators
audience: designer, developer
category: templates
order: 210
summary: Arithmetic in templates with sum, sub, mul, div, mod, inc, dec, min, max, abs, ceil, floor, round, int, float, count, roman and rand, with examples for paging, percentages and grids.
keywords: math operators, arithmetic, sum, sub, mul, div, mod, inc, dec, min, max, abs, ceil, floor, round, int, float, count, roman, rand, calculation
type: help
---
# Math operators

This page is a reference for calculations in templates. All come from `eZTemplateArithmeticOperator`
(`lib/eztemplate/classes/eztemplatearithmeticoperator.php`).

## Contents

1. [The operators](#the-operators)
2. [Examples](#examples)
3. [Notes](#notes)

## The operators

| Operator | Example | Result |
|---|---|---|
| `sum` | `sum( 2, 3, 4 )` or `2\|sum( 3 )` | 9, 5 |
| `sub` | `sub( 10, 3 )` | 7 |
| `mul` | `mul( 4, 2.5 )` | 10 |
| `div` | `div( 10, 4 )` | 2.5 |
| `mod` | `mod( 10, 4 )` | 2 |
| `inc`, `dec` | `$i\|inc` | `$i` plus or minus 1 |
| `min`, `max` | `max( $a, $b )` | the smaller / larger |
| `abs`, `ceil`, `floor`, `round` | `$x\|round` | |
| `int`, `float` | `$text\|int` | a number from a string |
| `count` | `$list\|count` | number of items (an array's count, a string's length) |
| `roman` | `4\|roman` | `IV` |
| `rand` | `rand( 1, 6 )` | a random number (do not use inside cached output) |

## Examples

Number of pages:

```tpl
{def $pages = ceil( div( $total, $limit ) )}
```

Offset of the current page:

```tpl
{def $offset = first_set( $view_parameters.offset, 0 )|int}
{def $page = sum( div( $offset, $limit )|floor, 1 )}
```

A percentage class for a meter (admin4 has `.a4-pct-0` to `.a4-pct-100` in steps of 4):

```tpl
{def $pct = mul( div( $done, $total ), 100 )|round}
<span class="a4-meter"><span class="a4-pct-{mul( div( $pct, 4 )|round, 4 )}"></span></span>
```

Columns for a grid:

```tpl
{if mod( $index, 3 )|eq( 0 )}<div class="row">{/if}
```

(CSS grid usually makes such counting unnecessary.)

## Notes

- Division by zero: test the divisor first.
- Template math is fine for presentation; business calculations (prices, taxes) belong in the shop's PHP.
- Inside a view-cached template or a cache-block, `rand` returns the same value until the cache is rebuilt.

## Related pages

- [Logic and comparison operators](logic-and-comparison-operators.md)
- [Pagination](pagination.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
