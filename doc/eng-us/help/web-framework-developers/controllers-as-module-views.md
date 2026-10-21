---
title: "Controllers: module views"
audience: web-framework-developer, developer
category: web-frameworks
order: 80
summary: Where a framework has controllers and actions, Exponential has module views - a view script or an Exponential\Runnable\ModuleView class that receives the address parameters and returns a result array - how to write one, keep it thin with services, wrap it with events, and change a kernel view without copying it.
keywords: controller, action, module view, ModuleView, Exponential\Runnable\ModuleView, Params, Result, eZTemplate, runnable, Implementation, RunnableSettings, thin controller, service, web framework developer
type: help
---
# Controllers: module views

This page is for developers who write controllers. In Exponential the unit that handles one address is a **view** of a
module. Classic views are PHP scripts that the kernel includes with `$Params` and `$Module` set; since 6.0.15 a view is
best written as a class extending `Exponential\Runnable\ModuleView`, with a one-line script that calls it
([Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md)).

## Contents

1. [Controller and view side by side](#controller-and-view-side-by-side)
2. [The script form](#the-script-form)
3. [The class form](#the-class-form)
4. [Keeping it thin](#keeping-it-thin)
5. [Before and after the view](#before-and-after-the-view)
6. [Changing a kernel view](#changing-a-kernel-view)

## Controller and view side by side

| Framework controller | Exponential module view |
|---|---|
| A class with action methods | one view per address; a class per view in the class form |
| Route parameters as arguments | `$Params['ItemID']` (ordered and unordered parameters) |
| `Request` object injected | `eZHTTPTool::instance()` for GET, POST and session variables; module actions for buttons |
| Returns a `Response` | returns a `$Result` array; the kernel puts `content` into the pagelayout |
| Authorization attribute or middleware | `'functions' => array( 'read' )` in `module.php`, checked before the view runs |
| Template rendering helper | `eZTemplate::factory()`, `setVariable()`, `fetch( 'design:...' )` |

## The script form

```php
<?php
// extension/expdemo/modules/expdemo/item.php
$Module = $Params['Module'];
$item = expDemoItem::fetch( (int)$Params['ItemID'] );
if ( !$item )
    return $Module->handleError( eZError::KERNEL_NOT_FOUND, 'kernel' );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'item', $item );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:expdemo/item.tpl' );
$Result['path'] = array( array( 'text' => 'Items', 'url' => 'expdemo/list' ),
                         array( 'text' => $item->attribute( 'title' ), 'url' => false ) );
```

`expDemoItem::fetch()` stands for a static method of your own persistent object class
([ORM: eZPersistentObject](orm-and-ezpersistentobject.md)). The result keys are explained on
[Responses, redirects, errors and JSON](responses-redirects-and-errors.md).

## The class form

From [A module view](../../bc/6.0/cli_cronjob_view_abstractions.md#a-module-view):

```php
<?php
// extension/myext/classes/runnable/views/report/show.php
namespace Exponential\View\Extension\Myext\Report;

class Show extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Module = $scope['Params']['Module'];
        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'report', \myReportService::build( $scope['Params']['Month'] ) );
        return array( 'content' => $tpl->fetch( 'design:report/show.tpl' ),
                      'path' => array( array( 'text' => 'Report', 'url' => false ) ) );
    }
}
```

```php
<?php
// extension/myext/modules/report/show.php (named in module.php ViewList as before)
return \Exponential\View\Extension\Myext\Report\Show::main( __FILE__, get_defined_vars() );
```

The class can be unit tested, wrapped by events and re-implemented by settings. Remember to regenerate the extension
autoloads after adding the class (`php bin/php/ezpgenerateautoloads.php -e`).

## Keeping it thin

The kernel itself puts work that a view, a command and a cronjob part share into service classes, for example
`Exponential\Service\Trash` for the trash view, `bin/php/trashpurge.php` and the `trashpurge` cronjob part
([Services](../../bc/6.0/cli_cronjob_view_abstractions.md#services-one-implementation-for-the-view-the-command-and-the-cronjob-part)).
Do the same: parse and check input in the view, call a plain class for the work, render the result. The plain class is
what you test without a database ([Testing](testing.md)).

## Before and after the view

Every class-based view runs between two events: `runnable/view/before` (notify) and `runnable/view/after` (a filter
that receives and must return the result). Register listeners in `site.ini [RunnableSettings] Listeners[]`. That is
the nearest thing to controller-level interceptors such as Spring's `HandlerInterceptor` or a Symfony
`kernel.controller` listener ([Events and listeners](events-and-listeners.md)).

## Changing a kernel view

| Need | Mechanism |
|---|---|
| Change one kernel view's behavior | `site.ini [RunnableSettings] Implementation[<view class>]=<your subclass>`; the subclass must extend the class it replaces |
| Replace the views or templates of a whole kernel module | an extension module override ([Extension module override](../../features/6.0/extension-module-override.md)) |
| Only change the markup | a template override, no PHP at all ([Template resolution](template-resolution-designs-and-overrides.md)) |

```ini
# settings/override/site.ini.append.php, or an extension's settings/site.ini.append.php
[RunnableSettings]
Implementation[Exponential\View\Kernel\Content\History]=myHistoryView
```

No kernel file is copied, so the next release updates the original underneath your subclass.

## Related pages

- [Custom modules and views](../creators/app-creators/custom-modules-and-views.md)
- [Routing: modules, views and parameters](routing-with-modules-and-views.md)
- [Module view templates](../template-language/module-view-templates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
