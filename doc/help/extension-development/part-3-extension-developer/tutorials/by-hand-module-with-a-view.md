---
title: "By hand: a module with a view"
audience: developer, app-creator, designer
category: extension-development
order: 130
summary: The page /hello/world/<name>: module.php with a policy, a view that validates its parameter and uses the fetch function, a template in the standard design, all verified.
keywords: tutorial, by hand, module, view, module.php, ViewList, FunctionList, policy, Params, template, design standard, Result, path
type: help
---
# By hand: a module with a view

Tutorial 12. You add a page to `mysite_hello`: `/hello/world/Ada` greets Ada and lists the newest articles, using the
fetch collection of the previous tutorial. The files passed `php -l`; with the kernel loaded, the module was found by
`eZModule::exists( 'hello' )` with its view, and the template rendered as shown.

## 1. module.php

`extension/mysite_hello/modules/hello/module.php` (the folder already exists from the fetch function, and
`ExtensionRepositories[]=mysite_hello` registers it):

```php
<?php
$Module = array( 'name' => 'Hello' );

$ViewList = array();
$ViewList['world'] = array(
    'script' => 'world.php',
    'params' => array( 'Name' ),
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart' );

$FunctionList = array();
$FunctionList['read'] = array();
```

The view requires the policy `hello/read`.

## 2. The view

`extension/mysite_hello/modules/hello/world.php`:

```php
<?php
/** @var eZModule $Module */
$Module = $Params['Module'];

$name = isset( $Params['Name'] ) && is_string( $Params['Name'] ) && $Params['Name'] !== ''
      ? $Params['Name'] : 'world';

$tpl = eZTemplate::factory();
$tpl->setVariable( 'name', $name );
$tpl->setVariable( 'newest', mysiteHelloFunctionCollection::fetchNewest( 'article', 2, 3 )['result'] );

$Result = array();
$Result['content'] = $tpl->fetch( 'design:hello/world.tpl' );
$Result['path'] = array( array( 'text' => ezpI18n::tr( 'extension/mysite_hello', 'Hello' ), 'url' => false ) );
```

## 3. The template

`extension/mysite_hello/design/standard/templates/hello/world.tpl`:

```text
<section class="mysite-hello">
    <h1>{'Hello, %name!'|i18n( 'extension/mysite_hello',, hash( '%name', $name|wash ) )}</h1>
    {if $newest|count}
    <h2>{'Newest articles'|i18n( 'extension/mysite_hello' )}</h2>
    <ul>
    {foreach $newest as $node}
        <li><a href={$node.url_alias|ezurl}>{$node.name|wash}</a></li>
    {/foreach}
    </ul>
    {/if}
</section>
```

and register the design in `extension/mysite_hello/settings/design.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite_hello

*/ ?>
```

Rendered with the name `Ada <b>`, the heading came out as `Hello, Ada &lt;b&gt;!`: the name is escaped before it goes
into the translated sentence.

## 4. Switch on, grant, open

```bash
php -l extension/mysite_hello/modules/hello/module.php
php -l extension/mysite_hello/modules/hello/world.php
php bin/php/ezcache.php --clear-all
```

Grant `hello/read` to the Anonymous role (or another role) in **Users > Roles and policies**, then open
`/hello/world/Ada`. Without the policy you get the access denied page, which is the point of step 1.

## 5. If it does not work

| Symptom | Fix |
|---|---|
| "The requested module does not exist" | `ExtensionRepositories[]`, caches |
| Access denied | grant `hello/read` |
| Layout with an empty content area | the template path, `DesignExtensions[]`, and `--clear-id=template-override` for a new template |

## 6. Commit

```bash
git -C extension/mysite_hello add modules design settings
git -C extension/mysite_hello commit -m "Added: The page hello/world, greeting a visitor and listing the newest articles"
```

## Related pages

- Previous: [By hand: a fetch function](by-hand-fetch-function.md)
- Next: [By hand: a module protected by a policy](by-hand-module-with-a-policy.md)
- [Modules and views](../modules-and-views.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
