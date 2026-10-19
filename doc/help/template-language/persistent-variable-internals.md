---
title: The persistent variable, inside
audience: developer, designer, app-creator
category: development
order: 330
summary: How $persistent_variable travels from a node view template to the pagelayout - the kernel sets it to false, reads it back from the root namespace after rendering, stores it in content_info and in the content view cache, and makes it an override key - and why scope='global' matters, proven with namespaces on alpha.
keywords: persistent_variable, scope global, content_info, view cache, pagelayout, namespace, set, override key, eZNodeviewfunctions, ezpagedata
type: help
---
# The persistent variable, inside

A node view template runs before the pagelayout and, when the page comes from the view cache, does not run at all.
The persistent variable is the one channel from the view template to the pagelayout that works in both cases. The
designer's how-to is [The persistent variable](../designers/templates/persistent-variable.md); this page shows the
kernel code behind it.

## Contents

1. [The path through the kernel](#the-path-through-the-kernel)
2. [Why scope='global'](#why-scopeglobal)
3. [What survives the view cache](#what-survives-the-view-cache)
4. [Helpers in extensions](#helpers-in-extensions)
5. [Related pages](#related-pages)

## The path through the kernel

All in `eZNodeviewfunctions::generateNodeViewData()` (`kernel/classes/eznodeviewfunctions.php`):

1. Before the view template runs: `$tpl->setVariable( 'persistent_variable', false );`
2. The template is fetched: `$Result['content'] = $tpl->fetch( 'design:node/view/' . $viewMode . '.tpl' );`
3. Afterwards the kernel reads the variable back from the **root namespace** and stores it:

```php
$contentInfoArray['persistent_variable'] = false;
if ( $tpl->variable( 'persistent_variable' ) !== false )
{
    $contentInfoArray['persistent_variable'] = $tpl->variable( 'persistent_variable' );
    $keyArray[] = array( 'persistent_variable', $contentInfoArray['persistent_variable'] );
    $res->setKeys( $keyArray );
}
```

4. `content_info` goes into `$Result`, so the pagelayout reads `$module_result.content_info.persistent_variable`.
5. The value also becomes the override key `persistent_variable`, which `override.ini` can match on.

The same kernel step can be replayed in the example renderer: define the variable as false in the root namespace (as
step 1 does), include a template that sets it with `scope='global'`, then read it back:

```tpl
{* the kernel does this before it fetches node/view/<viewmode>.tpl *}
{def $persistent_variable = false()}
{include uri='file:var/tmp/examples/tm-persistent/view-part.inc'}

after the view template: body_class={$persistent_variable.body_class}, hide_sidebar={$persistent_variable.hide_sidebar|choose( 'false', 'true' )}
```

Output on alpha:

```text
<article>...</article>

after the view template: body_class=article, hide_sidebar=true
```

The included part (`view-part.inc` beside the example) is:

```tpl
{* stands in for node/view/full.tpl: sets the variable in the global namespace *}
{set scope='global' persistent_variable=hash( 'body_class', 'article', 'hide_sidebar', true() )}
<article>...</article>
```

## Why scope='global'

`set` without a scope looks for the variable in the template's **current namespace** (scope `relative`,
`lib/eztemplate/classes/eztemplatesetfunction.php`). The node view template itself runs in the root namespace, so a
plain `{set persistent_variable=...}` there would work. But a template that is included with a `name=`, or that runs
through `node_view_gui` (namespace `NodeView`), `content_view_gui` (`ContentView`) or `attribute_view_gui`
(`ContentAttribute`), has a namespace of its own (`kernel/common/eztemplateautoload.php` declares these), and there
the variable does not exist:

```tpl
{def $persistent_variable = false()}
{include uri='file:var/tmp/examples/tm-persistent/view-part-local.inc' name=part}

included with name=part, without scope: {if $persistent_variable}set{else}still false{/if}


{include uri='file:var/tmp/examples/tm-persistent/view-part.inc' name=part}

included with name=part, with scope='global': {$persistent_variable.body_class}
```

Output on alpha:

```text

included with name=part, without scope: still false
<article>...</article>

included with name=part, with scope='global': article
```

Logged by the template engine:

```text
WARNING set: Variable 'part:persistent_variable' doesn't exist, cannot set
```

The included part used here sets the variable without a scope:

```tpl
{* the same assignment, without scope='global' *}
{set persistent_variable=hash( 'body_class', 'article' )}
```

`scope='global'` writes to the global namespace (`''`) from anywhere, which is where step 3 reads (`scope='root'` means the
root namespace of the current template, which inside an include is not always the same). Use it
always, so the assignment keeps working when the template is moved into an include.

Note what `set` does not do: it never creates a variable. Only `def` does, and a `def` of a name that already exists
logs "already defined" and assigns anyway (`lib/eztemplate/classes/eztemplatedeffunction.php`).

## What survives the view cache

`content_info`, with the persistent variable in it, is part of the `$Result` that the content view cache stores. On a
cache hit `eZNodeviewfunctions::contentViewRetrieve()` returns the stored `$Result` and sets the override keys again
from `content_info`, `persistent_variable` included. The view template does not run, but the pagelayout gets the
value it set when the cache was made.

This is also the limit: the value is whatever the template set when the cache file was written. Anything that
varies per visitor or per request must not go into it; it would be served to every later visitor until the cache
expires.

## Helpers in extensions

- The ezwebin extension's `ezpagedata_set( key, value )` and `ezpagedata_append( key, value )`
  (`extension/ezwebin/autoloads/ezpagedata.php`) call `ezjscPackerTemplateFunctions::setPersistentArray()` of
  ezjscore, which writes into the same variable; `ezpagedata()` in the pagelayout merges
  `content_info.persistent_variable` into its page data.
- The admin designs read keys such as `left_menu` and `extra_menu` from it (for example
  `design/admin/templates/pagelayout.tpl`).
- `design/standard/templates/page_head.tpl` writes one `<meta name>` tag per entry of `$site.meta`, and takes the
  content from the persistent variable when it has a key of the same name (a view template can set its own
  `description`, for example).

## Related pages

- [The persistent variable](../designers/templates/persistent-variable.md)
- [Module view templates](module-view-templates.md)
- [Module result reference](module-result-reference.md)
- [Variables and def](../designers/templates/variables-and-def.md)
- [Template caches](template-caches.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
