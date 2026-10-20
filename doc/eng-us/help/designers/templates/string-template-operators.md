---
title: PHP string functions as operators
audience: designer, developer
category: templates
order: 300
summary: The sixty-odd PHP string functions available as template operators since 6.0.13 (strlen, substr, str_contains, sprintf, number_format, strip_tags, ...), how the piped value maps to PHP's first argument, and designer-friendly examples.
keywords: php string operators, strlen, substr, str_contains, str_starts_with, sprintf, number_format, strip_tags, ucfirst, lcfirst, strpos, str_replace, eZTemplateStringsOperator
type: help
---
# PHP string functions as operators

This page is for designers who need a text function the classic operators do not have. Since release 6.0.13, the
class `eZTemplateStringsOperator` (`lib/eztemplate/classes/eztemplatestringsoperator.php`) makes about sixty PHP string
functions available in templates. The full list and the details are on the feature page
[PHP string functions as template operators](../../../features/6.0/string-template-operators.md).

## Contents

1. [How the mapping works](#how-the-mapping-works)
2. [Useful for designers](#useful-for-designers)
3. [Notes](#notes)

## How the mapping works

The piped value is PHP's first argument; the template parameters follow in PHP's order:

```tpl
{def $text = 'Hello, Exponential'}
{$text|strlen}                    {* 18 *}
{$text|substr( 0, 5 )}            {* Hello *}
{$text|str_contains( 'Expo' )}    {* true *}
{$text|lcfirst}                   {* hello, Exponential *}
```

(examples from the feature page).

## Useful for designers

| Need | Operator |
|---|---|
| plain text from rich text | `strip_tags` (then `shorten` and `wash`) |
| first characters | `substr( 0, 1 )` for an initial |
| does a text start or end with | `str_starts_with`, `str_ends_with` |
| number with separators | `number_format( 2, ',', '.' )` (or `l10n( 'number' )` for the locale's format) |
| formatted text | `sprintf( '%02d', $n )` |
| position of a substring | `strpos`, `stripos` |
| split into characters | `str_split` |

## Notes

- Prefer the locale-aware `l10n` for numbers and dates shown to visitors ([Dates, numbers and l10n](dates-numbers-and-l10n.md)).
- These operators return raw text: wash before printing.
- A few functions exist only on newer PHP versions; the feature page says which.

## Related pages

- [String operators](string-operators.md)
- [Operator reference](operator-reference.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
