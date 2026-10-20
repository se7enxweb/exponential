---
title: Custom template functions
audience: developer, app-creator
category: development
order: 500
summary: Writing a template function (a brace tag with named parameters and an optional body) in an extension - functionList, hasChildren, attributeList, process, evaluating parameters, rendering the body, registration in eztemplateautoload.php, compile-time hints - and extending or overriding the kernel's functions, with a sample function rendered on alpha.
keywords: template function, custom function, functionList, hasChildren, attributeList, process, processNode, elementValue, eZTemplateFunctionArray, function_names, function_attributes, functionTemplateHints, override function
type: help
---
# Custom template functions

A template function is a tag such as `{include ...}`, `{cache-block}...{/cache-block}` or `{def ...}`: it takes
named parameters, may have a body, and writes output or changes variables. Operators, by contrast, take a value and
return one ([Functions versus operators](functions-vs-operators.md)). This page shows how an extension adds a
function and what the engine expects, from `lib/eztemplate/classes/eztemplate.php` and
`lib/eztemplate/classes/eztemplatemultipassparser.php`.

## Contents

1. [When to write a function](#when-to-write-a-function)
2. [The sample function](#the-sample-function)
3. [The methods](#the-methods)
4. [Registering it](#registering-it)
5. [Using it](#using-it)
6. [Compile-time hints](#compile-time-hints)
7. [Extending and overriding kernel functions](#extending-and-overriding-kernel-functions)

## When to write a function

Write a function when the tag needs a body (wrap, repeat or capture the markup between an opening and an end tag),
must set variables in the caller's namespace, or controls what is rendered. For a value in, value out, write an
[operator](../template-operators/creating-an-operator-step-by-step.md); for reading data, a
[fetch function](../template-operators/fetch-functions-how-they-work.md). For reusable markup without PHP, an
`{include}` of a template part is simpler than either.

## The sample function

`{c11_box title=... class=...}body{/c11_box}` wraps its body in a `<section>` with an escaped heading. The class,
as an extension would ship it in `extension/myextension/autoloads/c11examplefunction.php`:

```php
class C11ExampleFunction
{
    /** The function names this class answers */
    function functionList()
    {
        return array( 'c11_box' );
    }

    /** true: the function has a body and an end tag {/c11_box} */
    function hasChildren()
    {
        return true;
    }

    function process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters,
                      $functionPlacement, $rootNamespace, $currentNamespace )
    {
        if ( !isset( $functionParameters['title'] ) )
        {
            $tpl->warning( $functionName, 'Missing parameter: title', $functionPlacement );
            return;
        }
        // parameters arrive unevaluated: evaluate each one
        $title = $tpl->elementValue( $functionParameters['title'], $rootNamespace, $currentNamespace, $functionPlacement );
        $class = isset( $functionParameters['class'] )
               ? $tpl->elementValue( $functionParameters['class'], $rootNamespace, $currentNamespace, $functionPlacement )
               : 'box';

        // render the body in the caller's namespaces
        $body = array();
        foreach ( (array)$functionChildren as $child )
            $tpl->processNode( $child, $body, $rootNamespace, $currentNamespace );

        $textElements[] = '<section class="' . htmlspecialchars( $class ) . '"><h2>' . htmlspecialchars( $title ) . '</h2>'
                        . implode( '', $body ) . '</section>';
    }
}
```

## The methods

| Method | Required | What the engine does with it |
|---|---|---|
| `functionList()` | yes | registers the object under each returned name when the class is loaded (and takes those names back from any other class, as for operators) |
| `hasChildren()` | yes | `true`: the tag has a body and an end tag; `false`: a single tag. May return an array `name => bool` for classes with several functions. The parser calls it without checking that it exists (`eZTemplate::hasChildren()`), so a class without it ends in a PHP error |
| `attributeList()` | no | names of sub-tags allowed inside the body, `name => hasChildren`, such as `elseif`/`else` of `if`, `delimiter`/`skip`/`break` of `foreach`, `case` of `switch` |
| `process()` | yes | renders the function, see below |
| `functionTemplateHints()` | no | tells the compiler whether the function can be turned into compiled code, see below |

`process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )`:

- `$textElements`: append output strings to this array (do not echo);
- `$functionChildren`: the parsed body, a list of nodes; render them with
  `$tpl->processNode( $child, $textElements, $rootNamespace, $currentNamespace )`, into your own array when you
  want to post-process the body, as the sample does;
- `$functionParameters`: the named parameters **unevaluated**, keyed by name; evaluate with
  `$tpl->elementValue( $functionParameters['name'], $rootNamespace, $currentNamespace, $functionPlacement )`. A
  parameter the template did not write is not set;
- variables: `$tpl->setVariable( $name, $value, $currentNamespace )` and `$tpl->variable( $name, $currentNamespace )`
  read and write the caller's variables (`eZTemplateBlockFunction` uses them for `set-block`);
- `$functionPlacement`: where the tag is, for `$tpl->warning()` and `$tpl->error()`.

## Registering it

In `extension/myextension/autoloads/eztemplateautoload.php`, next to any operators:

```php
<?php
$eZTemplateFunctionArray = array();
$eZTemplateFunctionArray[] = array( 'script' => 'extension/myextension/autoloads/c11examplefunction.php',
                                    'class' => 'C11ExampleFunction',
                                    'function_names' => array( 'c11_box' ) );
```

Keys: `class` or `function` (a PHP function returning the object), `script`, `function_names` or
`function_names_function`, and `function_attributes` (the sub-tag names, so the parser knows them before the class
is loaded; the library registers `if`'s `elseif` and `else` this way). The extension also needs
`[TemplateSettings] ExtensionAutoloadPath[]=myextension` in its `settings/site.ini.append.php`; see
[Registering operators](../template-operators/registering-operators.md), which covers both arrays.

## Using it

Not run on alpha as an installed extension: the script that rendered these examples registered the entry above on
the template engine in memory, with the class in a scratch folder. Interpreted and compiled results were the same.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
{c11_box title=$node.name class='teaser'}<p>{$node.children_count} children</p>{/c11_box}
{c11_box title='Plain'}<p>Default class</p>{/c11_box}
```

Output on alpha:

```text
<section class="teaser"><h2>Fit &amp; Healthy</h2><p>16 children</p></section><section class="box"><h2>Plain</h2><p>Default class</p></section>
```

A missing parameter, handled by the function's own check:

```tpl
{c11_box}<p>no title</p>{/c11_box}
```

Output on alpha:

```text
(no output)
```

Logged by the template engine:

```text
WARNING c11_box: Missing parameter: title
```

A function name nobody registered: the parser treats an unknown tag as a block (its default when no class answers
the name), so the body is swallowed too, and the engine logs a warning:

```tpl
{c11_unknown title='x'}<p>body</p>{/c11_unknown}
```

Output on alpha:

```text
(no output)
```

Logged by the template engine:

```text
WARNING : Function "c11_unknown" is not registered
```

## Compile-time hints

`functionTemplateHints()` returns, per function name, keys such as `parameters`, `static`, `tree-transformation`
(the function rewrites its own node at compile time through `templateNodeTransformation()`, as
`eZTemplateDelimitFunction` does for `ldelim`/`rdelim`) and the related transformation settings the library's
functions use. A function without hints, like the sample, is compiled into a run-time call of its `process()`,
which gives the same result and is the safe choice. Add hints only with a test that compares interpreted and
compiled output; the reasons are the same as for operators
([operatorTemplateHints](../template-operators/operator-class-reference.md#operatortemplatehints)).

## Extending and overriding kernel functions

Function names follow the operator rules exactly: the last `eztemplateautoload.php` entry for a name wins, and a
class registers all names of its `functionList()` when it is loaded
([Registration order and overrides](../template-operators/registration-order-and-overrides.md)). On alpha no
function name is registered twice. To change a kernel or library function from an extension:

- subclass the class that owns the name (for example `eZTemplateBlockFunction`, which owns `set-block`,
  `append-block` and `run-once`), override `process()` for the name you change and call `parent::process()`
  for the others;
- register the subclass for **all** names of the parent class, and keep `function_attributes` and `hasChildren()`
  consistent with the parent;
- drop the parent's `functionTemplateHints()` entry for the name you change, or compiled templates keep the
  parent's code ([the operator case, shown on alpha](../template-operators/overriding-kernel-operators.md#drop-the-compile-time-code-you-change)).

Not demonstrated with a function on alpha: the override of a function name uses the same code path as for
operators (`registerAutoloadFunctions()`, `registerFunctionsInternal()`), which
[Overriding kernel operators](../template-operators/overriding-kernel-operators.md) shows rendered. Overriding
the language's own control functions (`if`, `foreach`, `def`, `include`) changes every template on the
siteaccess; prefer a new function name.

## Related pages

- [Functions versus operators](functions-vs-operators.md)
- [Function reference](../designers/templates/function-reference.md): every function of the library and the kernel
- [Registering operators](../template-operators/registering-operators.md)
- [The operator class interface](../template-operators/operator-class-reference.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
