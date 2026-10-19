---
title: A custom module and view
audience: developer
category: examples
order: 50
summary: Add the module expexample with a list view that takes an ordered and an unordered URL parameter and a policy function with a section limitation, plus its templates, settings and the role that grants it.
keywords: module, view, module.php, ViewList, params, unordered_params, functions, FunctionList, policy, limitation, Section, ExtensionRepositories, ModuleList, eZModule, Result, handleError, hasAccessTo
type: example
---
# A custom module and view

A module is the first part of an address that is not a URL alias: `/content/view/full/2` runs the view `view` of the
module `content`. You add the module **expexample** with two views:

- `/expexample/list/<NodeID>` and `/expexample/list/<NodeID>/offset/<n>`: the children of a node, ten a page, for
  users with the policy `expexample/read`, which can be limited to sections;
- `/expexample/about`: a page for users with `expexample/about`.

Prerequisites: the extension of [An extension skeleton](extension-skeleton.md), switched on;
[A custom template operator](custom-template-operator.md) for `exp_initials`, which the list template uses (or remove
that line).

## 1. Tell the kernel about the module

`extension/expexample/settings/module.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
# Look for modules/<module>/module.php in this extension
ExtensionRepositories[]=expexample
# The known module names: keeps a URL alias from taking over the module's address
ModuleList[]=expexample

*/ ?>
```

## 2. module.php

`extension/expexample/modules/expexample/module.php` declares the views, their parameters and the policy functions:

```php
<?php
/**
 * The expexample module: /expexample/list/<NodeID>[/offset/<n>] and /expexample/about.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$Module = array( 'name' => 'Example',
                 'variable_params' => false );

$ViewList = array();

// /expexample/list/2, /expexample/list/2/offset/20: the children of a node, 10 a page
$ViewList['list'] = array(
    'script' => 'list.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart',
    'params' => array( 'NodeID' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );

// /expexample/about: a page without parameters, for users with the policy expexample/about
$ViewList['about'] = array(
    'script' => 'about.php',
    'functions' => array( 'about' ),
    'params' => array() );

// The policy functions of the module, granted in Roles and policies.
// read takes a limitation by section: a role can allow the list of some sections only.
$SectionID = array(
    'name' => 'Section',
    'values' => array(),
    'class' => 'eZSection',
    'function' => 'fetchList',
    'parameter' => array( false ) );

$FunctionList = array();
$FunctionList['read'] = array( 'Section' => $SectionID );
$FunctionList['about'] = array();
```

| Key | Meaning |
|---|---|
| `params` | ordered parameters: the next segments of the address, as `$Params['NodeID']` |
| `unordered_params` | `<name in the address>` => `<name in $Params>`: `/offset/20` anywhere after the ordered ones gives `$Params['Offset']` = `20`, `false` when absent |
| `functions` | the policy functions a user needs; the kernel checks them before the script runs |
| `default_navigation_part` | the tab of the administration that is highlighted |
| `$FunctionList[<function>]` | the limitations a policy of that function can have; an empty array: none |

View parameters in parentheses (`/(offset)/20`, `/(color)/red`) need no declaration: they arrive in
`$Params['UserParameters']` and in templates as `$view_parameters`.

A view without `functions` can be opened by anyone who reaches the site. Declare a function for every view that shows
anything that is not public.

## 3. The views

`extension/expexample/modules/expexample/list.php`:

```php
<?php
/**
 * The view expexample/list: the children of a node the current user may read.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$Module = $Params['Module'];
$nodeID = (int)$Params['NodeID'];
$offset = max( 0, (int)$Params['Offset'] );   // false when the address has no /offset/<n>
$limit  = 10;

$node = eZContentObjectTreeNode::fetch( $nodeID );
if ( !$node instanceof eZContentObjectTreeNode )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
if ( !$node->canRead() )
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );

// The section limitation of expexample/read: the policy may allow some sections only
$access = eZUser::currentUser()->hasAccessTo( 'expexample', 'read' );
if ( $access['accessWord'] === 'limited' )
{
    $allowed = array();
    foreach ( $access['policies'] as $limitations )
    {
        if ( !isset( $limitations['Section'] ) )
        {
            $allowed = false;   // a policy without the limitation allows every section
            break;
        }
        $allowed = array_merge( $allowed, $limitations['Section'] );
    }
    if ( $allowed !== false && !in_array( $node->attribute( 'object' )->attribute( 'section_id' ), $allowed ) )
        return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}

$children = $node->subTree( array( 'Depth' => 1,
                                   'DepthOperator' => 'eq',
                                   'Offset' => $offset,
                                   'Limit' => $limit,
                                   'SortBy' => $node->sortArray() ) );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'node', $node );
$tpl->setVariable( 'children', $children );
$tpl->setVariable( 'offset', $offset );
$tpl->setVariable( 'limit', $limit );
$tpl->setVariable( 'total', $node->subTreeCount( array( 'Depth' => 1, 'DepthOperator' => 'eq' ) ) );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:expexample/list.tpl' );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'extension/expexample', 'Example list' ), 'url' => false ),
                         array( 'text' => $node->attribute( 'name' ), 'url' => false ) );
```

`subTree()` checks `content/read` for each child itself, so the list holds only what the user may see.
`hasAccessTo()` answers `yes`, `no` or `limited` with the user's policies; the kernel has already refused `no`.

`extension/expexample/modules/expexample/about.php`:

```php
<?php
/**
 * The view expexample/about.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$info = expexampleInfo::info();

$tpl = eZTemplate::factory();
$tpl->setVariable( 'version', $info['Version'] );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:expexample/about.tpl' );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'extension/expexample', 'About the example' ), 'url' => false ) );
```

`$Result['content']` is the page body, which the pagelayout puts around; `$Result['path']` is the breadcrumb.

## 4. The templates

`extension/expexample/design/standard/templates/expexample/list.tpl`:

```tpl
{* The view expexample/list. Variables: $node, $children, $offset, $limit, $total *}
<section class="expexample-list">
    <h1>{$node.name|wash}</h1>
    <p>{'%count items'|i18n( 'extension/expexample', '', hash( '%count', $total ) )}</p>
    <ul>
    {foreach $children as $child}
        <li>
            <a href={$child.url_alias|ezurl}>{$child.name|wash}</a>
            <span class="expexample-initials">{$child.name|exp_initials}</span>
        </li>
    {/foreach}
    </ul>
    <nav class="expexample-pages">
    {if $offset|gt( 0 )}
        <a href={concat( 'expexample/list/', $node.node_id, '/offset/', max( sub( $offset, $limit ), 0 ) )|ezurl}>{'Previous'|i18n( 'extension/expexample' )}</a>
    {/if}
    {if sum( $offset, $limit )|lt( $total )}
        <a href={concat( 'expexample/list/', $node.node_id, '/offset/', sum( $offset, $limit ) )|ezurl}>{'Next'|i18n( 'extension/expexample' )}</a>
    {/if}
    </nav>
</section>
```

`extension/expexample/design/standard/templates/expexample/about.tpl`:

```tpl
{* The view expexample/about. Variables: $version *}
<section class="expexample-about">
    <h1>{'About the example'|i18n( 'extension/expexample' )}</h1>
    <p>{'Version %version'|i18n( 'extension/expexample', '', hash( '%version', $version|wash ) )}</p>
</section>
```

Templates of a view shown in the administration follow the admin styling rules: shared component classes, no
`<style>` and no `style=""`, no inline script.

## 5. Grant the policy

1. Clear the caches and regenerate the autoloads:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

2. **Users > Roles and policies**: edit a role, **New policy**, module *expexample*, function *read*; set a
   **Section** limitation or none; save. Assign the role to the users or groups.
3. For visitors of the public site, give the *Anonymous* role `expexample/read` as well; otherwise they are asked to
   sign in.

Check from the shell which users have it: `./console exp:access:check` (see [Roles and policies](../../guides/roles-and-policies.md)).

## 6. Check it

- `/expexample/list/2` lists the children of the root node, ten at a time; **Next** goes to `/expexample/list/2/offset/10`.
- A user whose policy is limited to another section gets the access denied page.
- `/expexample/about` shows the version; without the policy, the sign-in page or access denied.
- `/expexample/nothing` answers the kernel's *view not found* page.

Checked for this page (2026-10-18): `php -l` on the three PHP files; `bin/php/eztemplatecheck.php` reports both
templates valid (syntax). The module was not run against a database for this page.

## Common mistakes

| Symptom | Cause |
|---|---|
| *Module not found* | `ExtensionRepositories[]` missing in the extension's `module.ini.append.php`, extension inactive, or the INI cache |
| Everybody can open the view | the view has no `functions` entry |
| `$Params['Offset']` is always `false` | the address uses `/(offset)/10` (a view parameter, in `UserParameters`) instead of `/offset/10` |
| The policy has no *Section* limitation to choose | `$FunctionList['read']` is `array()`; the limitation array must name `class` and `function` that return the values |

## Derived from

- `kernel/exphelp/module.php`, `kernel/section/module.php`, `kernel/content/module.php` (the `$SectionID` limitation)
- `lib/ezutils/classes/ezmodule.php` (`params`, `unordered_params`, `UserParameters`)
- `kernel/classes/ezcontentobjecttreenode.php` (`fetch()`, `canRead()`, `subTree()`, `subTreeCount()`, `sortArray()`)
- `doc/guides/extensions.md`, section 6.1; `doc/bc/6.0/cli_cronjob_view_abstractions.md` (the class form of a view)

## Related pages

- [Cookbook](README.md)
- [Module views](../module-views/README.md) and [policy functions](../policies/README.md): every kernel view and
  function, as generated from the code
- [Commands, cronjob parts and module views as classes](../../bc/6.0/cli_cronjob_view_abstractions.md#a-module-view):
  the same view written as a class
- [An extension can replace a kernel module](../../features/6.0/extension-module-override.md),
  [Request rules](../../features/6.0/request-rules.md)
- [Translations](translations.md) for the texts of the templates
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
