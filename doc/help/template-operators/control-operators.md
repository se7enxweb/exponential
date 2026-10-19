---
title: Control operators
audience: developer, designer, app-creator
category: development
order: 130
summary: cond and first_set from eZTemplateControlOperator - inline conditions and fallbacks that evaluate only what they need, how the interpreter and the compiled template differ for a false default, with examples rendered on alpha.
keywords: cond, first_set, condition, ternary, fallback, default value, eZTemplateControlOperator
type: help
---
# Control operators

`cond` and `first_set` are the template language's answer to a ternary (`a ? b : c`) and a null-coalescing
(`a ?? b`) expression. Both are registered by `eZTemplateControlOperator`
(`lib/eztemplate/classes/eztemplatecontroloperator.php`) through `lib/eztemplate/classes/eztemplateautoload.php`.
Neither takes a piped input nor named parameters; both evaluate their parameters lazily, from left to right, and stop
as soon as they have an answer. The compiler turns both into nested PHP `if` blocks (`condTransform()`), so they cost
no operator call in a compiled template.

## Contents

1. [cond](#cond)
2. [first_set](#first_set)
3. [Related pages](#related-pages)

## cond

### cond

Takes pairs of parameters, condition and value. It returns the value of the first pair whose condition is true. An
odd last parameter is the default, used when no condition matched. With a single parameter it returns that parameter.

| Parameter | Type | Required | Default |
|---|---|---|---|
| condition, value pairs | mixed | yes | none |
| default (odd last parameter) | mixed | no | none |

Input: none. Returns the chosen value, or nothing when no condition matched and there is no default.
Generated reference: [cond](../../examples/template-operators/cond.md).

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 144 ) )}
{cond( $node.children_count|gt( 10 ), 'many', $node.children_count|gt( 0 ), 'some', 'none' )} children ({$node.children_count})
{cond( false(), 'never', 'the default' )}
{cond( 'only one parameter' )}
```

Output on alpha:

```text
many children (18)
the default
only one parameter
```

A common use is a class attribute in a loop, here on the children of the Fit & Healthy front page (node 89):

```tpl
{foreach fetch( 'content', 'list', hash( 'parent_node_id', 89, 'limit', 4, 'sort_by', array( 'priority', true() ) ) ) as $i => $child}
<li class="{cond( $i|eq( 0 ), 'first', $child.class_identifier|eq( 'ng_topic' ), 'topic', 'item' )}">{$child.name|wash}</li>
{/foreach}
```

Output on alpha:

```text
<li class="first">Fitness</li><li class="topic">Workout</li><li class="item">Fit &amp; Healthy site info - [SITEINFO]</li><li class="item">Tour</li>
```

**A default that is itself false.** The interpreter (`modify()`) evaluates the default like a condition and uses it
only when it is true, so a default of `0`, `''` or `false()` gives nothing. The compiled form assigns the default
without testing it. The example below was rendered by the interpreter:

```tpl
[{cond( false(), 'x', 0 )}]
```

Output on alpha:

```text
[]
```

Compiled on alpha, where template compiling is enabled, the same line printed `[0]`.

Output on alpha with the template compiler (compiled into a scratch folder):

```text
[0]
```

To get a reliable `0`, test the case explicitly: `cond( $x, 'x', true(), 0 )`.

## first_set

### first_set

Returns the first parameter that is set, that is, not `null`. Undefined variables and missing hash keys are allowed
here and log nothing (the interpreter asks for them with the existence check of `elementValue()`, the compiled code
uses `isset()`). When none is set it returns `null`. A constant parameter ends the search in the compiled code, so put
the literal fallback last.

| Parameter | Type | Required | Default |
|---|---|---|---|
| candidates | mixed | yes | none |

Input: none. Returns the first value that is not `null`. Note that `0`, `''` and `false` count as set.
Generated reference: [first_set](../../examples/template-operators/first_set.md).

```tpl
{def $view_parameters = hash( 'offset', 20 )}
offset = {first_set( $view_parameters.offset, 0 )}
limit = {first_set( $view_parameters.limit, $undefined_limit, 10 )}
title = {first_set( $no_such_variable, 'Untitled' )}
```

Output on alpha:

```text
offset = 20
limit = 10
title = Untitled
```

This is the usual way to read an optional parameter of an included template or an optional view parameter such as
`$view_parameters.offset`.

## Related pages

- [Cond and first_set for designers](../designers/templates/cond-and-first-set.md)
- [Logic operators](logic-operators.md): `choose`, `and`, `or`
- [Variables and def](../designers/templates/variables-and-def.md): `default` for optional include parameters
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
