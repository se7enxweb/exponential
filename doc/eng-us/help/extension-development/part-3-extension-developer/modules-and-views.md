---
title: Modules and views
audience: developer, app-creator
category: extension-development
order: 90
summary: Pages with their own address: module.php and every ViewList key, a view script and a view class, POST actions, redirects and errors, from eZModule and a real extension.
keywords: module, view, module.php, ViewList, FunctionList, script, params, unordered_params, functions, default_navigation_part, post_actions, single_post_actions, eZModule, redirectTo, handleError, ModuleView
type: help
---
# Modules and views

Part 3, chapter 9. Every page of Exponential that is not a content page is a **view** of a **module**:
`/user/login`, `/setup/extensions`, `/owner/change/123`. An extension adds pages by adding modules. This chapter
explains the definition file, the view script, and what a view returns. The class that runs it all is `eZModule`
(`lib/ezutils/classes/ezmodule.php`).

## Register the module folder

In `extension/mysite_hello/settings/module.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
ExtensionRepositories[]=mysite_hello

*/ ?>
```

Every folder under `extension/mysite_hello/modules/` is then a module. The address `/hello/world/Ada` runs the view
`world` of the module `hello` with the parameter `Ada`.

## module.php

A real definition, from se7enxweb/ezownerchange (`modules/owner/module.php`):

```php
<?php
$Module = array( 'name' => 'Owner', 'variable_params' => false );

$ViewList = array();
$ViewList['change'] = array(
    'script' => 'change.php',
    'functions' => array( 'change' ),
    'params' => array( 'ObjectID' ),
    'unordered_params' => array( 'group' => 'StartNode', 'node' => 'NodeID', 'scope' => 'Scope' ),
    'ui_context' => 'edit',
    'default_navigation_part' => 'ezcontentnavigationpart',
    'post_actions' => array( 'BrowseActionName' ),
    'single_post_actions' => array( 'BrowseCancelButton' => 'Cancel',
                                    'CancelButton' => 'Cancel',
                                    'BrowseOwnerButton' => 'BrowseOwner',
                                    'PreviewButton' => 'Preview',
                                    'ConfirmButton' => 'Confirm' ),
    'post_action_parameters' => array( 'Cancel' => array( 'CancelURI' => 'BrowseCancelURI' ) )
);

$FunctionList = array();
$FunctionList['change'] = array();
$FunctionList['subtree'] = array();
```

| Key | Meaning |
|---|---|
| `$Module['name']` | a readable name |
| `$Module['variable_params']` | `true` makes each parameter a variable of its own in the view script, besides `$Params` |
| `'script'` | the file of the view, next to `module.php` |
| `'params'` | ordered parameters: `/owner/change/123` gives `$Params['ObjectID'] = '123'` |
| `'unordered_params'` | named parameters anywhere after: `/(node)/59` gives `$Params['NodeID'] = '59'` |
| `'functions'` | the policy functions a user needs to open the view ([Policy functions and access checks](policy-functions-and-access.md)) |
| `'default_navigation_part'` | which top menu tab of the administration is active |
| `'ui_context'` | the interface context (`edit`, `navigation`, ...) the administration designs read |
| `'single_post_actions'` | a POST button name and the action it means |
| `'post_actions'` | POST variables whose value names the action |
| `'post_action_parameters'` | POST variables made available per action |
| `$FunctionList` | the policy functions this module offers to roles |

## A view script

`modules/hello/world.php`:

```php
<?php
/** @var eZModule $Module */
$Module = $Params['Module'];
$name = isset( $Params['Name'] ) && $Params['Name'] !== '' ? $Params['Name'] : 'world';

$tpl = eZTemplate::factory();
$tpl->setVariable( 'name', $name );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:hello/world.tpl' );
$Result['path'] = array( array( 'text' => 'Hello', 'url' => false ) );
```

The script gets `$Params` (with the module as `$Params['Module']`), and sets `$Result`. The template escapes what came
from the address: `{$name|wash}`. What `$Result` may contain is the next chapter but one,
[View results and the pagelayout](view-results-and-the-pagelayout.md).

## A view as a class

Since 6.0.15 a view can be a class; the plain script above keeps working. The class extends
`\Exponential\Runnable\ModuleView` (`kernel/private/classes/runnable/moduleview.php`) and returns the result:

```php
<?php
// extension/mysite_hello/classes/runnable/views/hello/world.php
namespace Exponential\View\Extension\MysiteHello\Hello;

class World extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $name = isset( $scope['Params']['Name'] ) && $scope['Params']['Name'] !== '' ? $scope['Params']['Name'] : 'world';
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'name', $name );
        return array( 'content' => $tpl->fetch( 'design:hello/world.tpl' ),
                      'path' => array( array( 'text' => 'Hello', 'url' => false ) ) );
    }
}
```

and the view file named in `module.php` becomes one line:

```php
<?php
return \Exponential\View\Extension\MysiteHello\Hello\World::main( __FILE__, get_defined_vars() );
```

Classes can be tested without a request and share code with commands and cronjob parts; see
[Commands, cronjob parts and module views as classes](../../../bc/6.0/cli_cronjob_view_abstractions.md).

## Forms and actions

A view that handles a form reads the action the module derived from the POST:

```php
<?php
$Module = $Params['Module'];
$http = eZHTTPTool::instance();

if ( $Module->isCurrentAction( 'Cancel' ) )
{
    return $Module->redirectTo( '/content/dashboard' );
}
if ( $Module->isCurrentAction( 'Confirm' ) )
{
    $comment = $http->postVariable( 'Comment', '' );
    // ... do the work, then redirect so a reload does not post again
    return $Module->redirectToView( 'world', array( 'Ada' ) );
}
```

With `single_post_actions`, a POST containing `ConfirmButton` makes `isCurrentAction( 'Confirm' )` true. Forms that
change anything must carry the form token (the `ezformtoken` extension adds it to forms and checks it); see
[ezformtoken and CSRF protection](../part-8-reference/ezformtoken-and-csrf.md).

## Errors

```php
<?php
$object = eZContentObject::fetch( (int)$Params['ObjectID'] );
if ( !$object instanceof eZContentObject )
{
    return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );
}
if ( !$object->canRead() )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}
```

`eZError` (`kernel/classes/ezerror.php`) has the codes: `KERNEL_ACCESS_DENIED` (1), `KERNEL_NOT_FOUND` (2),
`KERNEL_NOT_AVAILABLE` (3), `KERNEL_MOVED` (4), `KERNEL_LANGUAGE_NOT_FOUND` (5), `KERNEL_FORM_TOKEN_REFUSED` (6),
`KERNEL_MODULE_NOT_FOUND` (20), `KERNEL_MODULE_VIEW_NOT_FOUND` (21) and more. The kernel shows the matching error page.

## Check it

1. `php -l` every file.
2. Activate, clear caches ([Activating an extension](activating-an-extension.md)).
3. Open `/hello/world/Ada`.
4. If it says the module does not exist: `./console exp:ini where module.ini/ModuleSettings/ExtensionRepositories`.

## Replacing a kernel module

An extension can replace a whole kernel module with one of its own; see
[An extension can replace a kernel module](../../../features/6.0/extension-module-override.md). Add a view to an
existing module with the RAD wizard **View for an existing module**.

## RAD

**Module extension** (`/setup/moduleextension`) writes a module with views, templates, policies and settings;
**Module wizard** (`/setup/modulewizard`) writes a module over existing database tables. See
[With RAD: a module with views](tutorials/rad-module.md).

## Related pages

- Previous: [Autoloads and class naming](autoloads-and-class-naming.md)
- Next: [View parameters, unordered parameters and POST](view-parameters.md)
- [By hand: a module with a view](tutorials/by-hand-module-with-a-view.md)
- [eZModule and module results](../part-8-reference/ezmodule.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
