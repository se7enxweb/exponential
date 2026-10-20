---
title: Your first extension
audience: app-creator, developer
category: app-creation
order: 60
summary: Build a working extension in fifteen minutes - by hand or with the RAD module wizard - with a module, a view, a policy function, a template and settings, then activate it, give it a role and check it in the browser.
keywords: first extension, tutorial, module, view, template, policy, module.php, ActiveExtensions, RAD, module wizard, hello world, app creator
type: help
---
# Your first extension

This page is for application creators who want a running extension quickly. You build `expdemo`, an extension with one
module, one view that lists the latest articles, one policy function and one template. You can let **Setup > RAD**
write it ([RAD tools](rad-tools.md)) or type it yourself as below; both give the same result.

## Contents

1. [The files](#the-files)
2. [Activate and register](#activate-and-register)
3. [Give it a role](#give-it-a-role)
4. [Check it](#check-it)
5. [What you learned](#what-you-learned)

## The files

```text
extension/expdemo/
├── extension.xml
├── ezinfo.php
├── modules/expdemo/module.php
├── modules/expdemo/latest.php
├── design/standard/templates/expdemo/latest.tpl
└── settings/module.ini.append.php
```

`settings/module.ini.append.php` tells the kernel the extension has modules:

```ini
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=expdemo
ModuleList[]=expdemo
*/ ?>
```

`modules/expdemo/module.php` declares the module, its view and its policy function:

```php
<?php
$Module = array( 'name' => 'Demo' );

$ViewList = array();
$ViewList['latest'] = array(
    'script' => 'latest.php',
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart',
    'params' => array( 'Limit' ) );

$FunctionList = array();
$FunctionList['read'] = array();
```

`modules/expdemo/latest.php` is the view; it fetches content and renders a template:

```php
<?php
/** @var array $Params set by eZModule */
$limit = isset( $Params['Limit'] ) ? max( 1, min( 50, (int)$Params['Limit'] ) ) : 10;

$nodes = eZContentObjectTreeNode::subTreeByNodeID(
    array( 'ClassFilterType' => 'include',
           'ClassFilterArray' => array( 'article' ),
           'SortBy' => array( 'published', false ),
           'Limit' => $limit ),
    (int)eZINI::instance( 'content.ini' )->variable( 'NodeSettings', 'RootNode' ) );

$tpl = eZTemplate::factory();
$tpl->setVariable( 'nodes', $nodes ?: array() );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:expdemo/latest.tpl' );
$Result['path'] = array( array( 'text' => 'Latest articles', 'url' => false ) );
```

`subTreeByNodeID()` checks `content/read` for the current user, so the list only shows what the user may read.

`design/standard/templates/expdemo/latest.tpl`:

```text
<h1>{'Latest articles'|i18n( 'extension/expdemo' )}</h1>
<ul>
{foreach $nodes as $node}
  <li><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></li>
{/foreach}
</ul>
```

`ezinfo.php` and `extension.xml` describe the extension for the about page
([Anatomy of an extension](extension-anatomy.md)).

## Activate and register

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=expdemo
```

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=design_base,template-override --allow-root-user
```

Under Velocity: `php bin/php/console exp:velocity deploy`.

## Give it a role

In **Users > Roles**, add the policy `expdemo / read` to the role that should see the page (Anonymous for a public page).
Without it, the view answers "access denied": the module's policy is checked before the script runs.

## Check it

Open `/expdemo/latest/5` on the site. Expected: a heading and five links. Then `/expdemo/latest/abc`: the cast to
`int` makes it 1, never an error. Logged out without the policy: access denied.

## What you learned

| Piece | Lesson |
|---|---|
| `module.ini.append.php` | modules of an extension are registered by settings |
| `module.php` | views, parameters and policy functions are declared, not coded |
| the view | `$Params` in, `$Result` out; templates do the HTML |
| the policy | access is checked by the kernel before your code |
| the template | `|wash` on everything you print |

Next: [Custom modules and views](custom-modules-and-views.md), and the class-based form of views
([Commands, cronjob parts and module views as classes](../../../bc/6.0/cli_cronjob_view_abstractions.md)).

## Related pages

- [Anatomy of an extension](extension-anatomy.md)
- [module.php reference](module-definition-reference.md)
- [RAD tools: building faster](rad-tools.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
