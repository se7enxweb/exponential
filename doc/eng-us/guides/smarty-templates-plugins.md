# Smarty templates: writing operators and functions for .stpl

This page is for PHP developers who want to give templates something new: a value to compute, a piece of markup to
render. In Exponential you do that with a template operator or a template function, registered by your extension.
Written once, it works in `.tpl` and in `.stpl` templates: the bridge finds every operator and function the template
system knows. This page explains how the bridge finds them, which choices decide how they are called from Smarty, and
walks through an operator of each kind and a function, with the registration and a test.

## How the bridge finds operators and functions

`expSmartyExtension` (`lib/smarty/classes/expsmartyextension.php`) is a Smarty 5 extension. Smarty asks it for a
modifier, a function tag or a block tag whenever a compiled template needs one that Smarty does not have itself:

- **Modifiers** (`$x|name:a`, and `name(a, b)` in an expression, which Smarty compiles as `a|name:b`):
  1. Smarty's own modifiers first;
  2. the bridge's helpers: `fetch`, `expattr`, `expvar`, `expvoid`, `expsequence`, `expslice`, `expsectionloop`,
     `exprange`, `expcolumn`;
  3. `exp_<name>` when no operator of that full name exists: the operator `<name>`;
  4. any operator registered in the template system of the siteaccess (`eZTemplate::factory()->Operators`), by its
     name. Each call goes to `expSmartyRenderer::callOperator()`, which runs the operator through
     `eZTemplate::processOperator()`, exactly as a `.tpl` does.
- **Function tags** (`{name a=... b=...}`): the bridge's tags, the names in `[SmartySettings] FunctionList[]`, then any
  registered template function without a body (`expSmartyFunction`, which runs it through a generated `.tpl`).
- **Block tags** (`{name}...{/name}`): the bridge's blocks, then any registered template function
  (`expSmartyBlockFunction`, which hands the rendered body to the function as its children).

The answers are remembered per PHP process. There is no setting to register Smarty plugins of your own (the Smarty
instance is created by `expSmartyRenderer::smarty()` with the bridge's extension only); an operator or a function is
the supported way, and it has the advantage of working in both engines.

## Choosing the name

- **Prefix it with your extension**: `myext_initials`, `myext_badge`. Names are global to the siteaccess; an extension
  registered later replaces an operator of the same name (the media theme's `image` replaces the kernel's).
- **Avoid the names of Smarty's modifiers** (`count`, `escape`, `replace`, `truncate`, ... the full list is in
  [the operators guide](smarty-templates-operators.md#the-exp_-prefix)): such an operator works, but `.stpl` authors
  must write it with `exp_` in front.
- **A name that starts with `exp_` is called by that name** (`exp_path`, `exp_environment`). Use it only if your
  operator really is meant to be `exp_...`; never name one `exp_<a Smarty modifier>`.
- **No hyphen in a function name**: `{myext-badge}` cannot be a Smarty tag. Letters, digits and underscores.

## Choosing how the operator takes its input

This is the one decision that matters for `.stpl`. A Smarty modifier and a Smarty function call are the same call
(`op(a, b)` is `a|op:b`), so the bridge needs to know whether the first value is the operator's input or its first
parameter. It reads the operator's `operatorTemplateHints()` (`expSmartyRenderer::operatorInputMode()`):

| Your hints for the operator | Mode | In a `.stpl` |
|---|---|---|
| none, or `'input' => true` without `input-as-parameter`, or `'input-as-parameter' => 'always'` | **input** | `$x\|op:a` passes `$x` as the operator value; without an input write `op(null, a)` |
| `'input' => false` | **param** | the first value becomes the first parameter: `op(a, b)` = `a\|op:b` |
| `'input-as-parameter' => true` | **param** | the same; your `modify()` must read the first parameter when the operator value is `null` |
| class extends `eZTemplateControlOperator` | **param** | as `cond` and `first_set` |
| none, and the name is in `[SmartySettings] ParameterOperatorList[]` of `smarty.ini` | **param** | `op(a, b)` as in the `.tpl`; see below |

Pick **input** when the operator transforms a value (`$name|myext_initials`), **param** when it computes from several
values with none of them special (`myext_clamp($value, 1, 10)`). Do not declare `'input-as-parameter' => true` unless
`modify()` really reads its input from the first parameter at run time: the kernel's `nl2br` and `indent` declare it but
read the operator value, and the bridge has to list them as exceptions (`expSmartyRenderer::INPUT_AT_RUN_TIME`). Your
operator should not need that list.

### Operators without hints that read their parameters

An operator without template hints is an **input** operator to the bridge, since nothing tells it otherwise. That is
wrong for an operator whose `modify()` never reads the operator value and works from its parameters alone, such as a
lookup `myext_setting('Group', 'Name')`: in a `.stpl`, `{myext_setting('Group', 'Name')}` would pass `'Group'` as
the input and `'Name'` as the first parameter. Two ways to fix it:

- give the operator hints: `'input' => false` (the better fix, which the template compiler uses too);
- or, for an operator you cannot change, list it in `settings/smarty.ini` (or an extension's
  `settings/smarty.ini.append.php`):

  ```ini
  [SmartySettings]
  ParameterOperatorList[]=myext_setting
  ```

A listed operator is a **param** operator: `{myext_setting('Group', 'Name')}` and `{'Group'|myext_setting:'Name'}` are
the call of the `.tpl` `myext_setting( 'Group', 'Name' )`. A `null` in front, `myext_setting(null, 'Group', 'Name')`
(the form the converter writes for an input operator), is left out, and so is a `null` piped into it, so templates
converted before the operator was listed keep working; `myext_setting()` has no parameters. The converter leaves out a
value piped into a listed operator in a `.tpl` (`$x|myext_setting('Group', 'Name')`), since the template system
ignores it too.

The kernel lists its own operators of this kind in `expSmartyRenderer::PARAMETER_OPERATORS`: `ezhttp`,
`ezhttp_hasvariable`, `icon_info`, `alphabet`, `content_structure_tree`, `csp_nonce`, `exp_demo_mode`,
`exp_environment`, `exp_environment_badge`, `ezmodule`, `eztoc`, the role operators (`has_role` ...),
`installation_name`, `is_production_system`, `language_switcher`, `module_params`, `pdf`, `topmenu`, the transfer
operators (`transfer_tools` ...), `treemenu` and `exp_smarty_render`. The setting adds to that list.

The list is read once per process: after a change, deploy (which restarts the PHP workers). List only operators whose
`modify()` never reads `$operatorValue`; an operator that reads it in one case and not in another is an input operator.

Values reach `modify()` as the template system has them: Smarty's proxies are replaced by the real objects, arrays are
unwrapped, and `expvoid()` arrives as an empty parameter (the parameter's default applies). What `modify()` returns is
wrapped for Smarty again: an object with `attribute()` and `hasAttribute()` becomes readable with dots
(`$result.name`).

## Example 1: an input operator

`extension/myext/classes/myextinitialsoperator.php`:

```php
<?php
/**
 * {$name|myext_initials}, {$name|myext_initials:'.'}: the initials of a name.
 */
class myExtInitialsOperator
{
    function operatorList()
    {
        return array( 'myext_initials' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'myext_initials' => array(
            'separator' => array( 'type' => 'string', 'required' => false, 'default' => '' ),
        ) );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                     &$operatorValue, $namedParameters, $placement )
    {
        $initials = array();
        foreach ( preg_split( '/\s+/u', trim( (string)$operatorValue ), -1, PREG_SPLIT_NO_EMPTY ) as $word )
            $initials[] = mb_strtoupper( mb_substr( $word, 0, 1 ) );
        $operatorValue = implode( $namedParameters['separator'], $initials );
    }
}
```

No template hints: an **input** operator. In a `.tpl` and a `.stpl`:

```
{$node.object.owner.name|myext_initials}          {* .tpl: JD *}
{$node.object.owner.name|myext_initials('.')}     {* .tpl: J.D *}
```

```smarty
{$node.object.owner.name|myext_initials}          {* .stpl: JD *}
{$node.object.owner.name|myext_initials:'.'}      {* .stpl: J.D *}
{myext_initials(null, 'Jane Doe')}                {* .stpl, without an input: an empty result, as the .tpl myext_initials('Jane Doe') *}
```

## Example 2: a param operator

```php
<?php
/**
 * myext_clamp( value, min, max ): the value kept between min and max.
 */
class myExtClampOperator
{
    function operatorList()
    {
        return array( 'myext_clamp' );
    }

    function operatorTemplateHints()
    {
        return array( 'myext_clamp' => array( 'input' => false, 'output' => true, 'parameters' => 3 ) );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'myext_clamp' => array(
            'value' => array( 'type' => 'integer', 'required' => true, 'default' => 0 ),
            'min' => array( 'type' => 'integer', 'required' => true, 'default' => 0 ),
            'max' => array( 'type' => 'integer', 'required' => true, 'default' => 0 ),
        ) );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                     &$operatorValue, $namedParameters, $placement )
    {
        $operatorValue = max( (int)$namedParameters['min'], min( (int)$namedParameters['max'], (int)$namedParameters['value'] ) );
    }
}
```

`'input' => false`: a **param** operator. `myext_clamp($page, 1, $page_count)` works the same in both engines. In a
`.stpl`, `$page|myext_clamp:1:$page_count` is the same call; in a `.tpl` the pipe would be ignored, so write the
function form in templates meant for both.

## Registering them

`extension/myext/autoloads/eztemplateautoload.php`:

```php
<?php

$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/myext/classes/myextinitialsoperator.php',
                                    'class' => 'myExtInitialsOperator',
                                    'operator_names' => array( 'myext_initials' ) );
$eZTemplateOperatorArray[] = array( 'script' => 'extension/myext/classes/myextclampoperator.php',
                                    'class' => 'myExtClampOperator',
                                    'operator_names' => array( 'myext_clamp' ) );
```

`extension/myext/settings/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=myext

*/ ?>
```

List every name the class handles in `operator_names`: registration is lazy, and a name missing there resolves only on
pages where another name of the class was used first. Then activate the extension, regenerate the autoloads, clear
the caches and reload the servers:

```bash
php bin/php/ezpgenerateautoloads.php -e
./console exp:velocity deploy --allow-root-user
```

The bridge remembers per process which names exist, so a worker that started before the extension was active does not
know the new operator until it is restarted; the deploy does it.

## Example 3: a template function, and a block

A function renders markup with named parameters. `extension/myext/classes/myextbadgefunction.php`:

```php
<?php
/**
 * {myext_badge text='New'}: a badge.
 */
class myExtBadgeFunction
{
    function functionList()
    {
        return array( 'myext_badge' );
    }

    function hasChildren()
    {
        return false;
    }

    function process( $tpl, &$textElements, $functionName, $functionChildren, $functionParameters,
                      $functionPlacement, $rootNamespace, $currentNamespace )
    {
        $text = isset( $functionParameters['text'] )
            ? (string)$tpl->elementValue( $functionParameters['text'], $rootNamespace, $currentNamespace, $functionPlacement )
            : '';
        $textElements[] = '<span class="myext-badge">' . htmlspecialchars( $text, ENT_QUOTES ) . '</span>';
    }
}
```

Registered with `$eZTemplateFunctionArray[] = array( 'script' => ..., 'class' => 'myExtBadgeFunction',
'function_names' => array( 'myext_badge' ) );` in the same autoload file. It is `{myext_badge text='New'}` in both
engines; from a `.stpl` it runs through a generated `.tpl` in a namespace that holds only its parameters, so it must
take everything it needs as a parameter.

With `hasChildren()` returning `true`, the function has a body, `{myext_box}...{/myext_box}`, and the bridge makes it
a Smarty block: the body is rendered first, and `process()` gets it as one text node in `$functionChildren`
(`array( array( eZTemplate::NODE_TEXT, false, '<the rendered body>' ) )`). A function that processes its children
with `$tpl->processNode()` or reads their text works; one that inspects their structure (looks for a particular child
function) sees only the text in a `.stpl`. `ExplBlockFunction` of `extension/explayouts` is a real block function
that works in both.

## Testing in both engines

Render the same case as a `.tpl` and as a `.stpl` through the template system, the way
`tests/tests/lib/smarty/expSmartyTemplateRenderTest.php` does: write each to a file of its own below `var/tmp/`, render
it with `eZTemplate::factory()->fetch( $file )`, and compare. `expSmartyOperatorParityTest` runs every operator case of
the template engine's own tests through both; add your operator's cases to a test of your extension in the same way.
Without Smarty 5 installed, those tests skip the Smarty half.

## Related pages

- [Operator reference](smarty-templates-operator-reference.md): the modes of every shipped operator
- [Template functions and blocks](smarty-templates-functions.md)
- [Extensions](extensions.md): building and releasing an extension

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
