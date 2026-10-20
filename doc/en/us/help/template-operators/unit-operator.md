---
title: The si unit operator
audience: developer, designer, app-creator
category: development
order: 240
summary: si from eZTemplateUnitOperator - formatting a number with a unit and a decimal or binary prefix (file sizes, lengths, weights) from units.ini, with every parameter, the auto prefix rule and examples rendered on alpha.
keywords: si, unit, file size, bytes, kB, MB, prefix, kibi, units.ini, UnitSettings, BinaryUnits, UseSIUnits, eZTemplateUnitOperator
type: help
---
# The si unit operator

`si` writes a number with a unit and a prefix: `5242880` bytes as `5.00 MB`, `1500` meters as `1.50 km`. It is
registered by `eZTemplateUnitOperator` (`lib/eztemplate/classes/eztemplateunitoperator.php`) through
`lib/eztemplate/classes/eztemplateautoload.php`. Units and prefixes come from `settings/units.ini`; the choice
between binary and decimal prefixes from `[UnitSettings]` in `site.ini`.

## Contents

1. [Parameters](#parameters)
2. [Units and prefixes](#units-and-prefixes)
3. [Examples](#examples)
4. [Related pages](#related-pages)

## Parameters

### si

| Parameter | Type | Required | Default |
|---|---|---|---|
| `unit` | string, a key of `[Base]` in `units.ini` | yes | `false` |
| `prefix` | `auto`, `decimal`, `binary`, `none` or a prefix name such as `kilo`, `mebi` | no | `auto` |
| `decimal_count` | integer | no | the locale's decimal count (0 with `none`) |
| `decimal_symbol` | string | no | the locale's decimal symbol |
| `thousands_separator` | string | no | the locale's thousands separator |

Input: the number, piped. Returns a string: the number, a space, the prefix symbol and the unit symbol. An unknown unit
logs "No such unit" and leaves the number unchanged; an unknown prefix logs "Prefix ... not found".
Generated reference: [si](../../examples/template-operators/si.md).

## Units and prefixes

`[Base]` in `units.ini` maps unit names to symbols: `meter` m, `gram` g, `second` s, `ampere` A, `kelvin` K,
`mole` mol, `candela` cd, `byte` B and `bit` b.

The prefix `auto` picks `binary` for the units in `[UnitSettings] BinaryUnits` (`byte;bit` on alpha) and `decimal` for
all others. Then:

- **decimal** uses `[DecimalPrefixes]` (powers of 10, from yocto to yotta, **including hecto and deca**) and takes the
  largest prefix not bigger than the number. That is why `150|si( 'meter' )` is `1.50 hm`: name a prefix when you want
  a familiar one.
- **binary** uses powers of 2. With `[UnitSettings] UseSIUnits=false`, as on alpha, it uses `[FakeBinaryPrefixes]`,
  which divide by 1024 but print the familiar `k`, `M`, `G`; with `true` it uses `[BinaryPrefixes]` and prints `Ki`,
  `Mi`, `Gi`.
- Numbers whose absolute value is below 10 get no prefix and are printed as they are.
- A named prefix (`kilo`, `mebi`...) is looked up in `[BinaryPrefixes]` first, then `[DecimalPrefixes]`; `none` prints
  the whole number with separators.

Negative numbers get the prefix of their magnitude (`-2000` seconds is `-2.00 ks`).

## Examples

File sizes:

```tpl
5|si( 'byte' ) = {5|si( 'byte' )}
2048|si( 'byte' ) = {2048|si( 'byte' )}
5242880|si( 'byte' ) = {5242880|si( 'byte' )}
5242880|si( 'byte', 'kibi' ) = {5242880|si( 'byte', 'kibi' )}
5242880|si( 'byte', 'mega' ) = {5242880|si( 'byte', 'mega' )}
5242880|si( 'byte', 'none' ) = {5242880|si( 'byte', 'none' )}
5242880|si( 'byte', 'auto', 1 ) = {5242880|si( 'byte', 'auto', 1 )}
```

Output on alpha:

```text
5|si( 'byte' ) = 5 B
2048|si( 'byte' ) = 2.00 kB
5242880|si( 'byte' ) = 5.00 MB
5242880|si( 'byte', 'kibi' ) = 5,120.00 KiB
5242880|si( 'byte', 'mega' ) = 5.24 MB
5242880|si( 'byte', 'none' ) = 5,242,880 B
5242880|si( 'byte', 'auto', 1 ) = 5.0 MB
```

Decimal units, a fixed prefix and a decimal symbol of your own:

```tpl
1500|si( 'meter' ) = {1500|si( 'meter' )}
150|si( 'meter' ) = {150|si( 'meter' )}
150|si( 'meter', 'kilo', 3 ) = {150|si( 'meter', 'kilo', 3 )}
2500|si( 'gram', 'kilo', 1, ',' ) = {2500|si( 'gram', 'kilo', 1, ',' )}
-2000|si( 'second' ) = {-2000|si( 'second' )}
```

Output on alpha:

```text
1500|si( 'meter' ) = 1.50 km
150|si( 'meter' ) = 1.50 hm
150|si( 'meter', 'kilo', 3 ) = 0.150 km
2500|si( 'gram', 'kilo', 1, ',' ) = 2,5 kg
-2000|si( 'second' ) = -2.00 ks
```

An unknown unit:

```tpl
[{100|si( 'furlong' )}]
```

Output on alpha:

```text
[100]
```

Logged by the template engine:

```text
WARNING eZTemplateUnitOperator: No such unit 'furlong'
```

The examples gave the same output interpreted and compiled on alpha.

## Related pages

- [Locale operators](locale-operators.md): `l10n( 'number' )` without a unit
- [PHP string functions: encode and format](php-string-functions-encode-and-format.md): `number_format`
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
