---
title: Functions versus operators
audience: developer, designer, app-creator
category: development
order: 120
summary: The two kinds of named things in the template language compared - template functions are tags that write output and control the template, operators compute values - with their registration arrays, the methods each PHP class implements, block functions and children, interpreted processing versus compiled transformation, and when to write which.
keywords: template functions, template operators, eZTemplateFunctionArray, eZTemplateOperatorArray, process, modify, hasChildren, functionList, operatorList, templateNodeTransformation, operatorTemplateHints, compiler, block function
type: help
---
# Functions versus operators

The template language has two kinds of named building blocks. A **function** is a tag: `{if}`, `{foreach}`,
`{include}`, `{cache-block}`. An **operator** produces a value inside an expression: `wash`, `fetch`, `concat`,
`count`. They are registered in separate lists, implemented by different methods and compiled differently. Knowing
which is which explains most "why does this not work here" questions.

## Contents

1. [At a glance](#at-a-glance)
2. [The same task both ways](#the-same-task-both-ways)
3. [From function output to an operator](#from-function-output-to-an-operator)
4. [Registration](#registration)
5. [The PHP side](#the-php-side)
6. [Interpreted and compiled](#interpreted-and-compiled)
7. [Which one to write](#which-one-to-write)
8. [Related pages](#related-pages)

## At a glance

| | Function | Operator |
|---|---|---|
| Written as | a tag: `{name params}`, often with `{/name}` | in an expression: `name( ... )` or `\|name( ... )` |
| Parameters | `name=value` pairs, or its own grammar (`foreach ... as ...`) | positional, in parentheses |
| Produces | output text, variables, control flow | a value |
| Can have children | yes, block functions (`if`, `foreach`, `cache-block`) | no |
| Usable inside an expression | no | yes, nested and chained |
| Registered in | `$eZTemplateFunctionArray` | `$eZTemplateOperatorArray` |
| Main PHP method | `process()` | `modify()` |
| Compile hook | `templateNodeTransformation()` | `operatorTemplateHints()` and a transformation method |
| On alpha's `site` siteaccess | 48 registered | 395 registered |

## The same task both ways

Many tasks can be done with either, and the result is the same text:

```tpl
{def $tags = array( 'news', 'sport', 'weather' )}
function: {foreach $tags as $t}{$t}{delimiter}, {/delimiter}{/foreach}
operator: {$tags|implode( ', ' )}
function: {if $tags|count|gt( 2 )}many{else}few{/if}
operator: {cond( $tags|count|gt( 2 ), 'many', 'few' )}
```

Output on alpha:

```text
function: news, sport, weatheroperator: news, sport, weather
function: manyoperator: many
```

The function form is easier to read when the branches or the loop body hold markup; the operator form fits inside a
parameter or an attribute value.

## From function output to an operator

The output of functions is text, and `set-block` turns text back into a value that operators can work on:

```tpl
{set-block variable=list}{foreach array( 'a', 'b', 'c' ) as $x}<{$x}>{/foreach}{/set-block}
[{$list}] [{$list|count_chars}] [{$list|upcase}]
```

Output on alpha:

```text
[<a><b><c>] [9] [<A><B><C>]
```

A function cannot be used where a value is expected. Inside an expression the parser only knows operators, so `if( ... )`
is looked up as an operator:

```tpl
[{concat( 'a', if( true() ) )}]
```

Output on alpha:

```text
[a]
```

Logged by the template engine:

```text
WARNING : Operator 'if' is not registered
```

## Registration

Both kinds are listed in an `eztemplateautoload.php` file that `eZTemplate::autoload()` includes from every path of
`site.ini` [TemplateSettings] `AutoloadPathList` and every extension in `ExtensionAutoloadPath`
(`<extension>/autoloads/eztemplateautoload.php`). The template library's own file,
`lib/eztemplate/classes/eztemplateautoload.php`, shows both forms:

```php
$eZTemplateOperatorArray[] = array( 'class' => 'eZTemplateTextOperator',
                                    'operator_names' => array( 'concat', 'indent' ) );

$eZTemplateFunctionArray[] = array( 'class' => 'eZTemplateIfFunction',
                                    'function_names' => array( 'if' ),
                                    'function_attributes' => array( 'elseif',
                                                                    'else' ) );
```

`function_attributes` names the child tags that belong to the function (`elseif` and `else` of `if`; `delimiter`,
`break`, `continue` and `skip` of the loops; `case` of `switch`). Entries are only remembered at first; the class is
loaded the first time one of its names is used (`loadAndRegisterOperators()`, `loadAndRegisterFunctions()`). A name
registered twice keeps the entry read last, which is how an extension can replace a kernel operator; the
page [Overriding kernel operators](../template-operators/overriding-kernel-operators.md) covers that.

The kernel's view functions (`node_view_gui`, `attribute_view_gui` and the other `*_gui` functions) are all one class,
`eZObjectForwarder` in `kernel/common/ezobjectforwarder.php`, configured by the rules in
`kernel/common/eztemplateautoload.php`.

## The PHP side

An **operator class** answers:

- `operatorList()`: the names it handles;
- `namedParameterPerOperator()` and `namedParameterList()`: the parameters, with `type`, `required` and `default`;
- `modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )`:
  reads the input from `$operatorValue` and writes the result back into it.

A **function class** answers:

- `functionList()`: the names it handles;
- `hasChildren()`: whether it is a block (true, false, or an array per name);
- `process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )`:
  appends output to `$textElements`, sets variables, and processes its children with `$tpl->processNode()` as often as
  it likes; this is how loops repeat their body.

## Interpreted and compiled

When `site.ini` [TemplateSettings] `TemplateCompile` is enabled (the default), each template is turned into a PHP file
once (`lib/eztemplate/classes/eztemplatecompiler.php`). Both kinds can take part:

- A function with `'tree-transformation' => true` in `functionTemplateHints()` replaces its node by generated PHP in
  `templateNodeTransformation()`. `foreach`, `if`, `def` and `set` do this. A function without it is called through
  `process()` from the compiled file.
- An operator with `'element-transformation' => true` in `operatorTemplateHints()` generates PHP for its call, through
  the method named in `element-transformation-func` (for example `phpMapTransformation` for `upcase`). Others are called
  through `modify()` at run time.

Either way the output must be the same as when the template is interpreted. The examples of these pages were rendered
both ways on alpha and compared; the real differences found are noted on
[Sections versus foreach](sections-vs-foreach.md#a-difference-in-compiled-templates) and in the [foreach](control-structures-reference.md#foreach) reference.

## Which one to write

- Write an **operator** to compute, format or look up a value: it composes with every other operator and can be used
  in any parameter. Most extension needs are operators.
- Write a **function** only to control output: to repeat or skip a block, to capture or cache it, or to render
  something with its own template. Functions are harder to write well, because they handle children, namespaces and
  compilation.
- Write a **fetch function** (in a module's `function_definition.php`) to give templates data from the database:
  `fetch( 'yourmodule', 'yourfunction', hash( ... ) )`. See
  [Writing your own fetch function](../creators/app-creators/writing-a-fetch-function.md).

## Related pages

- Previous: [Operators in expressions](operators-in-expressions.md)
- Next: [Control structures reference](control-structures-reference.md)
- [Function reference](../designers/templates/function-reference.md) and [Operator reference](../designers/templates/operator-reference.md)
- [Writing template operators](../creators/app-creators/template-operators.md)
- [A custom template operator](../../examples/cookbook/custom-template-operator.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
