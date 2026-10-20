---
title: Custom modules and views
audience: app-creator, developer
category: app-creation
order: 100
summary: Build the screens of an application as module views - registering a module, ordered and unordered parameters, handling POST actions, returning a result, redirecting, errors, and the class-based form of a view.
keywords: module, view, module.php, ViewList, params, unordered_params, single_post_actions, Result, redirectTo, handleError, ModuleView, eZModule, app creator
type: help
---
# Custom modules and views

This page is for application creators who add screens to Exponential. A **module** is the first part of an address
(`/expdemo/...`), a **view** the second (`/expdemo/latest/...`). The module's `module.php` declares its views and
policies; each view is a script (or a class) that returns a result.

## Contents

1. [Registering a module](#registering-a-module)
2. [Parameters](#parameters)
3. [Handling forms: actions](#handling-forms-actions)
4. [The result](#the-result)
5. [Redirects and errors](#redirects-and-errors)
6. [The class-based view](#the-class-based-view)
7. [Overriding a kernel view](#overriding-a-kernel-view)

## Registering a module

```ini
# extension/expdemo/settings/module.ini.append.php
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=expdemo
ModuleList[]=expdemo
*/ ?>
```

Modules live in `extension/expdemo/modules/<module>/`. The complete list of `module.php` keys is in
[module.php reference](module-definition-reference.md).

## Parameters

```php
$ViewList['item'] = array(
    'script' => 'item.php',
    'functions' => array( 'read' ),
    'params' => array( 'ItemID' ),                      // /expdemo/item/42
    'unordered_params' => array( 'offset' => 'Offset' ) // /expdemo/item/42/(offset)/20
);
```

In the view, `$Params['ItemID']` and `$Params['Offset']` hold the values, as strings from the address: cast and
validate them (`(int)`, allow lists) before use ([Input validation and output escaping](input-validation-and-escaping.md)).

## Handling forms: actions

Declare the buttons of a form as actions:

```php
$ViewList['edit'] = array(
    'script' => 'edit.php',
    'functions' => array( 'edit' ),
    'params' => array( 'ItemID' ),
    'single_post_actions' => array( 'StoreButton' => 'Store', 'CancelButton' => 'Cancel' ),
    'post_action_parameters' => array( 'Store' => array( 'Title' => 'ItemTitle' ) ) );
```

```php
$Module = $Params['Module'];
if ( $Module->isCurrentAction( 'Cancel' ) )
    return $Module->redirectTo( '/expdemo/list' );
if ( $Module->isCurrentAction( 'Store' ) && $Module->hasActionParameter( 'Title' ) )
{
    $title = trim( (string)$Module->actionParameter( 'Title' ) );
    // validate, store, then redirect (POST - redirect - GET)
}
```

Forms must post with the form token, which `ezformtoken` adds to `method="post"` forms automatically
([Forms, CSRF protection and HTTP input](forms-csrf-and-http-input.md)).

## The result

```php
$tpl = eZTemplate::factory();
$tpl->setVariable( 'item', $item );
$Result = array(
    'content' => $tpl->fetch( 'design:expdemo/item.tpl' ),
    'path' => array( array( 'text' => 'Items', 'url' => 'expdemo/list' ),
                     array( 'text' => $item->attribute( 'title' ), 'url' => false ) ) );
```

`content` goes into the pagelayout, `path` becomes the breadcrumb and title. More keys and pagelayout choices:
[View results, templates and the pagelayout](view-results-and-the-pagelayout.md).

## Redirects and errors

| Need | Call |
|---|---|
| Redirect to an address | `return $Module->redirectTo( '/expdemo/list' );` |
| Redirect to another view of the module | `$Module->redirectToView( 'list', array() );` |
| Not found | `return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );` |
| Access denied | `return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );` |

Redirect targets that come from the request must be checked; the kernel's safe redirect helpers only follow addresses of
the site ([Safe redirects](../../../features/6.0/safe-redirects.md)).

## The class-based view

New code writes the view as a class extending `Exponential\Runnable\ModuleView`; the view file becomes one line that
calls it. The class can be tested and re-implemented by settings
([A module view](../../../bc/6.0/cli_cronjob_view_abstractions.md#a-module-view)).

## Overriding a kernel view

An extension can override the templates or the views of a kernel module
([Extension module override](../../../features/6.0/extension-module-override.md)). Prefer adding your own module; an
override must follow every kernel update.

## Related pages

- [Your first extension](your-first-extension.md)
- [Policy functions and limitations](policy-functions-and-limitations.md)
- [Module and view pattern](../../../standards/patterns/module-view.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
