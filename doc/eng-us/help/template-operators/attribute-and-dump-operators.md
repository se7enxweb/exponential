---
title: Attribute and dump operators
audience: developer, designer, app-creator
category: development
order: 220
summary: attribute and dump from eZTemplateAttributeOperator - listing what an object, a hash or an array offers to templates, with or without values, to a depth, as an HTML table or as text, and why their output must never reach a public page; examples rendered on alpha.
keywords: attribute, dump, debug, inspect, object attributes, data_map, ezpAttributeOperatorManager, AttributeOperator, DefaultFormatter, eZTemplateAttributeOperator
type: help
---
# Attribute and dump operators

When you do not know what a variable holds, `attribute` and `dump` list it. Both are registered by
`eZTemplateAttributeOperator` (`lib/eztemplate/classes/eztemplateattributeoperator.php`) through
`lib/eztemplate/classes/eztemplateautoload.php`. They walk arrays and every object that has the `attributes()` and
`attribute()` methods (nodes, content objects, attributes, users and the other persistent objects), one level per
step, and print one line per key.

The output is produced by a formatter chosen in `template.ini`:

```ini
[AttributeOperator]
DefaultFormatter=html
OutputFormatter[html]=ezpAttributeOperatorHTMLFormatter
OutputFormatter[text]=ezpAttributeOperatorTextFormatter
```

`ezpAttributeOperatorManager::getOutputFormatter()` (`kernel/private/eztemplate/ezpattributeoperatormanager.php`) falls
back to `DefaultFormatter` for a name that is not registered. An extension can add a formatter class that implements
`ezpAttributeOperatorFormatterInterface` and list it under `OutputFormatter[]`.

## Contents

1. [attribute](#attribute)
2. [dump](#dump)
3. [Safety](#safety)
4. [Related pages](#related-pages)

## attribute

### attribute

Lists the keys of the piped array or object, by default **without** values and two levels deep.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `show_values` | string: `show` to include values | no | `''` (names and types only) |
| `max_val` | number: levels to descend | no | `2` |
| `format` | `html` or `text` | no | `[AttributeOperator] DefaultFormatter` |

Input: the value to inspect, piped. Returns the formatted listing; a scalar or an empty array is returned as the
formatter exports it. Generated reference: [attribute](../../examples/template-operators/attribute.md).

The fields of the Fit & Healthy front page (node 89), one level, as text:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
{$node.data_map|attribute( 'show', 1, 'text' )}
```

Output on alpha:

```text
title (object[eZContentObjectAttribute]=Object)
metadata (object[eZContentObjectAttribute]=Object)
teaser_title (object[eZContentObjectAttribute]=Object)
teaser_intro (object[eZContentObjectAttribute]=Object)
full_intro (object[eZContentObjectAttribute]=Object)
image (object[eZContentObjectAttribute]=Object)
tags (object[eZContentObjectAttribute]=Object)
```

Without `show`, only names and types:

```tpl
{def $hash = hash( 'title', 'Fit & Healthy', 'tags', array( 'fit', 'healthy' ) )}
{$hash|attribute( '', 2, 'text' )}
```

Output on alpha:

```text
title (string)
tags (array)
    0 (string)
    1 (string)
```

## dump

### dump

The same walk with other defaults: values **shown**, one level deep.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `show_values` | string: `show` to include values | no | `'show'` |
| `max_val` | number: levels to descend | no | `1` |
| `format` | `html` or `text` | no | `[AttributeOperator] DefaultFormatter` |

Generated reference: [dump](../../examples/template-operators/dump.md).

```tpl
{def $hash = hash( 'title', 'Fit & Healthy', 'tags', array( 'fit', 'healthy' ) )}
{$hash|dump( 'show', 2, 'text' )}
```

Output on alpha:

```text
title (string='Fit & Healthy')
tags (array=Array(2))
    0 (string='fit')
    1 (string='healthy')
```

With the default formatter, `html`, the result is a table:

```tpl
{hash( 'node_id', 89, 'name', 'Fit & Healthy' )|dump}
```

Output on alpha:

```text
<table><tr><th align="left">Attribute</th>
<th align="left">Type</th>
<th align="left">Value</th>
</tr>
<tr><td>node_id</td>
<td>integer</td>
<td>89</td>
</tr>
<tr><td>name</td>
<td>string</td>
<td>'Fit & Healthy'</td>
</tr>
</table>
```

A scalar comes back as it was:

```tpl
[{'plain text'|dump( 'show', 1, 'text' )}] [{42|attribute( 'show', 1, 'text' )}]
```

Output on alpha:

```text
['plain text'] [42]
```

## Safety

- The HTML formatter does not escape values: `'Fit & Healthy'` above came out with a bare `&`. A value that holds
  markup or script is printed as it is. Use these operators on a development installation, or inside a block that only
  administrators see, and remove them before a template goes live.
- A deep level on a node walks into its object, its versions, its parents and their attributes: every level multiplies
  the number of database queries. Start with `max_val` 1.
- Some attributes are computed when they are read. Listing them with values runs that code, so a `dump` can be slow
  even when the page is small.

The examples were rendered by the interpreter and again with the template compiler on alpha; both gave the same output.

## Related pages

- [Template debugging](../designers/templates/template-debugging.md)
- [Nodes, objects and attributes](../designers/templates/nodes-objects-attributes.md)
- [Type operators](type-operators.md): `get_type` and `get_class` for one value
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
