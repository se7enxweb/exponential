---
title: Text operators
audience: developer, designer, app-creator
category: development
order: 200
summary: concat and indent from eZTemplateTextOperator - joining values into one string and indenting multi-line text with spaces, tabs or a custom filler, the object trap of concat in compiled templates, with examples rendered on alpha.
keywords: concat, indent, string concatenation, join, eZTemplateTextOperator
type: help
---
# Text operators

The template language has no string concatenation operator such as `.` or `~`; `concat` does that job. It and
`indent` are registered by `eZTemplateTextOperator` (`lib/eztemplate/classes/eztemplatetextoperator.php`) through
`lib/eztemplate/classes/eztemplateautoload.php`.

## Contents

1. [concat](#concat)
2. [indent](#indent)
3. [Related pages](#related-pages)

## concat

### concat

Joins the piped input (when there is one) and every parameter into one string, in order. Numbers are converted to
text.

| Parameter | Type | Required | Default |
|---|---|---|---|
| values | string or number | no | none |

Input: optional, it comes first. Returns a string. `concat` does not escape, so wash the result, or each untrusted
part, before printing it in HTML. Generated reference: [concat](../../examples/template-operators/concat.md).

The example uses the Running topic on alpha (node 163):

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 163 ) )}
{concat( 'Topic: ', $node.name, ' (node ', $node.node_id, ')' )|wash}
{'/'|concat( $node.url_alias )}
{concat( 'class ', 'ng-', $node.class_identifier )}
```

Output on alpha:

```text
Topic: Running (node 163)
/running
class ng-ng_topic
```

**Objects.** The interpreter (`modify()`) silently skips a parameter that is an object:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 163 ) )}
[{concat( 'object skipped: ', $node )}]
```

Output on alpha:

```text
[object skipped: ]
```

The compiled form (`concatTransformation()`) joins the values with PHP's `.` operator, and PHP cannot turn a node
into a string. On alpha the same template, compiled, stopped with
`Error: Object of class eZContentObjectTreeNode could not be converted to string` and produced no output at all, not
even the text before it. Always pass the attribute you mean (`$node.name`), never the object.

## indent

### indent

Puts a filler in front of the first line and after every newline of the input.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `indent_count` | integer | yes | `false` |
| `indent_type` | identifier: `space`, `tab` or `custom` | no | `space` |
| `indent_filler` | string (with `custom`) | no | `false` |

Any other type gives spaces. A negative count writes the error "The value of the "count" argument is negative" to the
debug log and leaves the text unchanged. Returns a string. Useful for plain-text output such as mail bodies; in HTML,
whitespace collapses. Generated reference: [indent](../../examples/template-operators/indent.md).

```tpl
{def $text = "line one
line two"}
[{$text|indent( 4 )}]
[{$text|indent( 2, 'custom', '> ' )}]
[{$text|indent( 1, 'tab' )|bin2hex|extract_left( 4 )}] (09 = a tab)
```

Output on alpha:

```text
[    line one
    line two]
[> > line one
> > line two]
[096c] (09 = a tab)
```

## Related pages

- [String operators](string-operators.md): `wash`, `shorten`, `trim`
- [PHP string functions: encode and format](php-string-functions-encode-and-format.md): `sprintf` for formatted text
- [Array operators](array-operators.md): `append` on strings and `implode`
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
