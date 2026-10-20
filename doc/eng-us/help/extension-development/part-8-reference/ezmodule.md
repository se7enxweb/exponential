---
title: eZModule and module results
audience: developer
category: extension-development
order: 130
summary: The module.php arrays, the $Params a view gets, the $Result it returns, and the eZModule methods views use - redirectTo(), redirectToView(), handleError(), isCurrentAction(), actionParameter().
keywords: eZModule, module.php, ViewList, FunctionList, Params, Result, redirectTo, redirectToView, handleError, isCurrentAction, actionParameter, currentAction, view, module result
type: help
---
# eZModule and module results

Part 8, reference 13. A module is a folder under `modules/` with a `module.php` and one script (or class) per view.
`eZModule` (`lib/ezutils/classes/ezmodule.php`) loads it and runs the views. The tutorial is
[Modules and views](../part-3-extension-developer/modules-and-views.md); this page is the reference.

## module.php

| Variable | Holds |
|---|---|
| `$Module` | `array( 'name' => 'myext' )`, optionally `'variable_params' => true` |
| `$ViewList` | one entry per view: `'script'`, `'params'` (ordered parameters), `'unordered_params'`, `'functions'` (policy functions), `'default_navigation_part'`, `'single_post_actions'`, `'post_action_parameters'`, `'ui_context'` |
| `$FunctionList` | the policy functions of the module, with their limitations |

## What a view gets

A view script finds `$Params`:

| Key | Holds |
|---|---|
| `$Params['Module']` | the `eZModule` object |
| `$Params['<Name>']` | each ordered parameter named in `'params'` |
| `$Params['UserParameters']` | the `(name)/value` pairs of the URL ([View parameters](../part-3-extension-developer/view-parameters.md)) |

## What a view returns

A view sets `$Result` (or, as a class, returns it from `run()`):

| Key | Meaning |
|---|---|
| `content` | the page body |
| `path` | the breadcrumb, a list of `array( 'text' => ..., 'url' => ... )` |
| `navigation_part` | the active top tab in the administration |
| `pagelayout` | `false` for no frame, or another pagelayout template |
| `content_info` | for content views, information about the node |

Details: [View results, the path and the pagelayout](../part-3-extension-developer/view-results-and-the-pagelayout.md).

## eZModule methods views use

| Method | Use |
|---|---|
| `redirectTo( $uri )` | redirect to an address of the site; `return $Module->redirectTo( '/myext/list' );` |
| `redirectToView( $viewName = '', $parameters = array(), ... )` | redirect to another view of the same module |
| `redirect( $moduleName, $viewName, $parameters = array(), ... )` | redirect to a view of another module |
| `handleError( $errorCode, $errorType = false, $parameters = array(), $userParameters = false )` | show the kernel's error page; `return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );` |
| `isCurrentAction( $actionName, $view = '' )` | whether a form button mapped by `single_post_actions` was pressed |
| `actionParameter( $parameterName, $view = '' )`, `hasActionParameter( ... )` | the values mapped by `post_action_parameters` |
| `currentView()`, `currentAction( $view = '' )` | the current view and action |
| `setTitle( $title )` | the page title |

Static helpers: `eZModule::exists( $moduleName, ... )` and `eZModule::findModule( $moduleName, ... )` find a module by
name.

## Forms with actions

```php
// module.php
$ViewList['edit'] = array(
    'script' => 'edit.php',
    'params' => array( 'ItemID' ),
    'functions' => array( 'manage' ),
    'single_post_actions' => array( 'StoreButton' => 'Store', 'CancelButton' => 'Cancel' ),
    'post_action_parameters' => array( 'Store' => array( 'Name' => 'Name' ) ),
);
```

```php
<?php
// edit.php
$Module = $Params['Module'];
if ( $Module->isCurrentAction( 'Cancel' ) )
{
    return $Module->redirectTo( '/myext/list' );
}
if ( $Module->isCurrentAction( 'Store' ) )
{
    $name = trim( (string)$Module->actionParameter( 'Name' ) );
    // validate and store
}
```

## Error codes

`eZError` (`kernel/classes/ezerror.php`) holds the codes for `handleError()`: see
[Error codes and module results](error-codes-and-module-results.md).

## Related pages

- Previous: [eZTemplate from PHP](eztemplate.md)
- Next: [eZScript and eZCLI](ezscript-and-ezcli.md)
- [Modules and views](../part-3-extension-developer/modules-and-views.md)
- [Policy functions and access](../part-3-extension-developer/policy-functions-and-access.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
