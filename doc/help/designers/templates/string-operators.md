---
title: String operators
audience: designer, developer
category: templates
order: 180
summary: The classic string operators of the template library (upcase, downcase, upfirst, upword, trim, shorten, wrap, pad, simplify, count_chars, count_words, nl2br, concat, indent, break, chr, ord) with examples, and where the newer PHP-based string operators are documented.
keywords: string operators, upcase, downcase, upfirst, upword, trim, shorten, wrap, pad, simplify, count_words, count_chars, nl2br, concat, indent, text operators
type: help
---
# String operators

This page is for designers who shape text in templates. These operators come from `eZTemplateStringOperator`,
`eZTemplateTextOperator` and `eZTemplateNl2BrOperator` (`lib/eztemplate/classes/`). Since 6.0.13, about sixty PHP string
functions are operators as well ([PHP string functions as operators](string-template-operators.md)).

## Contents

1. [Case](#case)
2. [Length and trimming](#length-and-trimming)
3. [Joining and layout](#joining-and-layout)
4. [Counting](#counting)
5. [Escaping comes last](#escaping-comes-last)

## Case

| Operator | Result for `'hello world'` |
|---|---|
| `upcase` | `HELLO WORLD` |
| `downcase` | `hello world` |
| `upfirst` | `Hello world` |
| `upword` | `Hello World` |

Prefer CSS `text-transform` for purely visual case: screen readers and copy-paste keep the original text.

## Length and trimming

```tpl
{$node.name|shorten( 60 )}                 {* at most 60 characters, '...' added when cut *}
{$text|shorten( 120, '…' )}                {* own ending *}
{$text|trim}                               {* spaces removed at both ends *}
{$text|simplify}                           {* runs of spaces reduced to one *}
{$code|pad( 6, '0' )}                      {* str_pad: '42' becomes '420000' *}
{$text|wrap( 72 )}                         {* wordwrap at 72 characters *}
```

`shorten` defaults: length 80, sequence `...`, cut on the right (from the operator's code). For rich text, shorten the
plain text: `{$node.data_map.intro.content.output.output_text|strip_tags|shorten( 160 )|wash}`.

## Joining and layout

```tpl
{concat( $first, ' ', $last )}
{$text|nl2br}            {* line breaks to <br>: wash first *}
{$text|break}            {* the same as nl2br in the string operator *}
{'x'|indent( 4 )}        {* indentation *}
```

`chr` and `ord` convert between characters and codes.

## Counting

```tpl
{$text|count_words}
{$text|count_chars}       {* multibyte aware: mb_strlen when available *}
```

## Escaping comes last

Shape first, then wash: `{$text|shorten( 100 )|wash}`. For `nl2br`, wash before it so the `<br>` survives:
`{$text|wash|nl2br}` ([Wash and escaping](wash-and-escaping.md)).

## Related pages

- [PHP string functions as operators](string-template-operators.md)
- [Wash and escaping](wash-and-escaping.md)
- [Operator reference](operator-reference.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
