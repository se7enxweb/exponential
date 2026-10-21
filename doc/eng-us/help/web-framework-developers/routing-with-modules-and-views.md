---
title: "Routing: modules, views and parameters"
audience: web-framework-developer, developer
category: web-frameworks
order: 60
summary: How Exponential maps an address to code - the module/view convention instead of a route table, ordered and unordered parameters, registering a module, generating URLs with ezurl and eZURI::build, HTTP methods and actions, and what a router user will miss.
keywords: routing, routes, module, view, ViewList, params, unordered_params, module.ini, ModuleList, ExtensionRepositories, ezurl, eZURI::build, URL generation, router, web framework developer
type: help
---
# Routing: modules, views and parameters

This page is for developers used to a route table (`routes.yaml`, `routes/web.php`, `urls.py`, `config/routes.rb`).
Exponential has no route table. An address is read as `/<module>/<view>/<parameters>`, and the module's `module.php`
declares which views exist and which parameters they take. Pretty addresses are a separate layer, URL aliases, that
is translated to a module address before this step ([Pretty URLs](url-aliases-and-wildcards.md)).

## Contents

1. [The convention](#the-convention)
2. [Registering a module](#registering-a-module)
3. [Parameters](#parameters)
4. [HTTP methods and actions](#http-methods-and-actions)
5. [Generating URLs](#generating-urls)
6. [What a router user will miss](#what-a-router-user-will-miss)

## The convention

| Address | Module | View | Parameters |
|---|---|---|---|
| `/content/view/full/2` | `content` | `view` | `full`, `2` |
| `/user/login` | `user` | `login` | none |
| `/expdemo/item/42/(offset)/20` | `expdemo` | `item` | `ItemID=42`, `Offset=20` |

With URI siteaccess matching, the siteaccess name comes first (`/admin/content/view/full/2`) and is removed before the
module is looked up ([Siteaccesses](siteaccesses-hosts-and-tenants.md)).

## Registering a module

A module is a directory with a `module.php` in a module repository. The kernel's repositories are
`settings/module.ini [ModuleSettings] ModuleRepositories[]` (`kernel`, `kernel/private/modules`); an extension adds
its own and lists the module, which makes it reachable:

```ini
# extension/expdemo/settings/module.ini.append.php
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=expdemo
ModuleList[]=expdemo
*/ ?>
```

```php
<?php
// extension/expdemo/modules/expdemo/module.php
$Module = array( 'name' => 'Demo' );

$ViewList = array();
$ViewList['item'] = array(
    'script' => 'item.php',
    'functions' => array( 'read' ),
    'params' => array( 'ItemID' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );

$FunctionList = array();
$FunctionList['read'] = array();
```

Every key of `$ViewList` entries is listed in the [module.php reference](../creators/app-creators/module-definition-reference.md).
After adding a module, clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`) and, under Velocity, deploy
([Servers and deployment](servers-and-deployment.md)).

## Parameters

| Kind | Declared as | Address | In the view |
|---|---|---|---|
| Ordered | `'params' => array( 'ItemID' )` | `/expdemo/item/42` | `$Params['ItemID']` is `'42'` |
| Unordered (named) | `'unordered_params' => array( 'offset' => 'Offset' )` | `/expdemo/item/42/(offset)/20` | `$Params['Offset']` is `'20'` |
| View parameters for templates | any `(name)/value` pair on content views | `/content/view/full/2/(color)/red` | `$view_parameters.color` in templates ([View parameters](../template-language/view-parameters.md)) |

Parameters arrive as strings. There are no type constraints or requirements in the declaration: cast and validate in
the view (`(int)`, allow lists) ([Validation](validation.md)).

## HTTP methods and actions

A view answers every method; there is no `methods: [POST]` in the declaration. Forms are handled as **actions**: the
view declares which POST button means which action, and the code asks `$Module->isCurrentAction( 'Store' )`:

```php
$ViewList['edit'] = array(
    'script' => 'edit.php',
    'functions' => array( 'manage' ),
    'params' => array( 'ItemID' ),
    'single_post_actions' => array( 'StoreButton' => 'Store', 'CancelButton' => 'Cancel' ),
    'post_action_parameters' => array( 'Store' => array( 'Title' => 'ItemTitle' ) ) );
```

Check the method yourself where it matters (a JSON endpoint that must only accept POST), and let `ezformtoken`
protect every POST ([Forms and CSRF](forms-and-csrf-protection.md)).

## Generating URLs

| Where | How |
|---|---|
| Templates | `{'expdemo/item/42'\|ezurl}` prints a quoted, escaped URL with the siteaccess prefix; `ezurl( 'no' )` without quotes; `ezurl( 'no', 'full' )` absolute ([URL operators](../template-operators/url-operators.md)) |
| Content in templates | `{$node.url_alias\|ezurl}` links to the node's URL alias |
| PHP | `eZURI::build( '/expdemo/item/42' )`; `eZURI::build( $url, true )` for an absolute URL; a third argument `false` for a URL that goes into a header or JSON ([For developers](../../guides/urls-and-aliases.md#for-developers)) |

There are no route names: the module/view path is the name. Keep the paths of your module in one helper class if many
places build them.

## What a router user will miss

- **Route names and reverse routing by name**: build the module path instead.
- **Requirements, defaults and converters per parameter**: do it in the view.
- **Method-specific routes**: use actions, or check the method in the view.
- **Annotation or attribute routes**: `module.php` is the only declaration.
- **Arbitrary pretty paths for your module views**: URL aliases point at content nodes; for your own views use
  global URL aliases or wildcards to give a module address a friendlier one ([Pretty URLs](url-aliases-and-wildcards.md)).

## Related pages

- [Controllers: module views](controllers-as-module-views.md)
- [Custom modules and views](../creators/app-creators/custom-modules-and-views.md)
- [Module and view pattern](../../standards/patterns/module-view.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
