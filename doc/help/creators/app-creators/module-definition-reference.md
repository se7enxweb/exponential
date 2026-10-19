---
title: "module.php reference: views, parameters and functions"
audience: app-creator, developer
category: app-creation
order: 110
summary: Every key of a module definition - $Module, $ViewList entries (script, functions, params, unordered_params, actions, navigation part, ui context) and $FunctionList with limitations - with the kernel code that reads each one.
keywords: module.php, ViewList, FunctionList, params, unordered_params, single_post_actions, post_actions, post_action_parameters, default_navigation_part, ui_context, functions, limitations, eZModule, app creator
type: help
---
# module.php reference: views, parameters and functions

This page is for application creators who write `module.php` files. It lists the keys the kernel reads and what each
does. The reader is `lib/ezutils/classes/ezmodule.php` (`eZModule`); kernel modules in `kernel/<module>/module.php` are
the best examples.

## Contents

1. [$Module](#module)
2. [$ViewList entries](#viewlist-entries)
3. [$FunctionList and limitations](#functionlist-and-limitations)
4. [A complete example](#a-complete-example)

## $Module

```php
$Module = array( 'name' => 'Demo', 'variable_params' => false );
```

| Key | Meaning |
|---|---|
| `name` | the module's display name |
| `variable_params` | `true` makes each view parameter a variable of its own in the view script, besides `$Params` |

## $ViewList entries

| Key | Meaning |
|---|---|
| `script` | the view file in the module directory |
| `functions` | policy functions the user needs (any of them); checked before the script runs |
| `params` | ordered parameters from the address: `/module/view/<p1>/<p2>` |
| `unordered_params` | named parameters `/(name)/value`, mapped to `$Params` keys: `array( 'offset' => 'Offset' )` |
| `single_post_actions` | POST button name to action: `array( 'StoreButton' => 'Store' )` |
| `post_actions` | POST variables whose value is the action name |
| `post_action_parameters` | per action, POST variables made available as action parameters |
| `default_action` | the action when no button matched |
| `default_navigation_part` | which admin top menu tab is active (`ezcontentnavigationpart`, `ezsetupnavigationpart` ...) |
| `ui_context` | `edit`, `administration`, `browse` ...: tells designs what kind of page it is |
| `ui_component` | the component name for the same purpose |

In the view: `$Params['Module']->isCurrentAction( 'Store' )`, `->hasActionParameter( 'Title' )`,
`->actionParameter( 'Title' )`.

## $FunctionList and limitations

```php
$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['edit'] = array( 'Section' => $SectionLimitation );
```

A function with an empty array has no limitations: a role either has it or not. A limitation is described as in
`kernel/content/module.php`:

```php
$SectionLimitation = array(
    'name' => 'Section',
    'values' => array(),
    'class' => 'eZSection',
    'function' => 'fetchList',
    'parameter' => array( false ) );
```

`values` lists fixed choices (`array( array( 'Name' => 'Self', 'value' => '1' ) )`); `class`, `function` and
`parameter` name a static method that returns the choices for the role editor. Your code must apply the limitation
itself ([Policy functions and limitations](policy-functions-and-limitations.md)).

Functions appear in **Users > Roles > New policy** once the module is registered and the caches are cleared.

## A complete example

```php
<?php
$Module = array( 'name' => 'Demo' );

$ViewList = array();
$ViewList['list'] = array(
    'script' => 'list.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart',
    'params' => array(),
    'unordered_params' => array( 'offset' => 'Offset' ) );
$ViewList['edit'] = array(
    'script' => 'edit.php',
    'functions' => array( 'edit' ),
    'ui_context' => 'edit',
    'params' => array( 'ItemID' ),
    'single_post_actions' => array( 'StoreButton' => 'Store', 'CancelButton' => 'Cancel' ),
    'post_action_parameters' => array( 'Store' => array( 'Title' => 'ItemTitle' ) ) );

$FunctionList = array();
$FunctionList['read'] = array();
$FunctionList['edit'] = array();
```

## Related pages

- [Custom modules and views](custom-modules-and-views.md)
- [Policy functions and limitations](policy-functions-and-limitations.md)
- [Policy function pattern](../../../standards/patterns/policy-function.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
