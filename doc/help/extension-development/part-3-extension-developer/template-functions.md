---
title: Template functions
audience: developer, app-creator, designer
category: extension-development
order: 150
summary: Block tags like {mysite_card}...{/mysite_card}: registration, the process() contract, evaluating parameters and children, a verified example, and when an operator or include is better.
keywords: template function, eZTemplateFunctionArray, function_names, process, functionList, hasChildren, elementValue, processNode, functionTemplateHints, block tag, custom tag
type: help
---
# Template functions

Part 3, chapter 15. Operators compute values. **Template functions** are tags with their own syntax, often with a body:
`{foreach}`, `{cache-block}`, `{debug-log}` are functions of the kernel. An extension can add its own. They are rarer
than operators, and usually an operator or an `{include}` is simpler, but for a wrapper around a block of template
code a function is the clean tool. The example below was rendered by the template engine.

## Registration

In the same `autoloads/eztemplateautoload.php` that registers operators (the plug is
`[TemplateSettings] ExtensionAutoloadPath[]`, see [Template operators](template-operators.md)):

```php
<?php
$eZTemplateFunctionArray = array();
$eZTemplateFunctionArray[] = array(
    'script' => 'extension/mysite_hello/classes/mysitehellocardfunction.php',
    'class' => 'mysiteHelloCardFunction',
    'function_names' => array( 'mysite_card' )
);
```

`eZTemplate::registerAutoloadFunctions()` registers the names; the class is loaded on first use. The `explayouts`
extension registers its `explblock` function exactly this way.

## The class

```php
<?php
/**
 * Template function of mysite_hello:
 *   {mysite_card title=$node.name}...content...{/mysite_card}
 * wraps its content in a card with an escaped title.
 */
class mysiteHelloCardFunction
{
    public function functionList()
    {
        return array( 'mysite_card' );
    }

    public function hasChildren()
    {
        return true;
    }

    public function process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters, $functionPlacement, $rootNamespace, $currentNamespace )
    {
        $title = '';
        if ( isset( $functionParameters['title'] ) )
        {
            $title = (string)$tpl->elementValue( $functionParameters['title'], $rootNamespace, $currentNamespace, $functionPlacement );
        }

        $children = array();
        if ( is_array( $functionChildren ) )
        {
            foreach ( $functionChildren as $child )
            {
                $tpl->processNode( $child, $children, $rootNamespace, $currentNamespace );
            }
        }

        $textElements[] = '<section class="mysite-card"><h2>' . htmlspecialchars( $title, ENT_QUOTES, 'UTF-8' ) . '</h2>'
                        . implode( '', $children ) . '</section>';
    }
}
```

| Method | Meaning |
|---|---|
| `functionList()` | the function names this class handles |
| `hasChildren()` | `true` when the tag has a body and a closing tag |
| `process( ... )` | produces output: append strings to `$textElements` |
| `$tpl->elementValue( $functionParameters[...], ... )` | evaluates a parameter (a variable, a string, an expression) |
| `$tpl->processNode( $child, $out, ... )` | renders one child node of the body into `$out` |
| `$tpl->missingParameter( $function, $name )`, `$tpl->error( $function, $text, $placement )` | report problems in the debug output |

## The result

```text
{def $t = 'Tom & <Jerry>'}
{mysite_card title=$t}<p>{mysite_greet( 'Ada' )}</p>{/mysite_card}
```

renders

```text
<section class="mysite-card"><h2>Tom &amp; &lt;Jerry&gt;</h2><p>Hello, Ada!</p></section>
```

Because the function builds HTML, it escapes the title itself.

## Compiled templates

Templates are compiled to PHP. A function without compile hints is called at run time through `process()`
(`eZTemplateCompiler` checks `functionTemplateHints()` and falls back when it is missing or has no
`tree-transformation`). That is correct and usually fast enough. The kernel's own functions, and `explblock`, add
`functionTemplateHints()` and a `templateNodeTransformation()` to compile into PHP directly; read
`lib/eztemplate/classes/eztemplatedelimitfunction.php` for a small kernel example before you try.

## When not to write a function

| You want | Better |
|---|---|
| a value | an operator |
| reusable HTML with parameters | `{include uri='design:parts/card.tpl' title=...}` |
| a wrapper with logic around a body | a template function |

## RAD

**Template extension** (`/setup/templateoperator`) can write a template function skeleton.

## Related pages

- Previous: [Fetch functions](fetch-functions.md)
- Next: [Datatypes](datatypes.md)
- Built-in template functions
- eZTemplate from PHP
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
