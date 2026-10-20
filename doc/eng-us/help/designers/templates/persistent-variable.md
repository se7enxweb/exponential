---
title: The persistent variable
audience: designer, developer
category: templates
order: 650
summary: Passing values from a view template to the pagelayout with persistent_variable: setting it as a hash, reading it from $module_result.content_info.persistent_variable, why it survives the view cache, ezpagedata_set as a helper, and examples (hide a column, body class, page title).
keywords: persistent_variable, view to pagelayout, content_info, ezpagedata_set, pass variable to pagelayout, hide left menu, body class from view, view cache safe
type: help
---
# The persistent variable

This page is for designers who need the content template to influence the frame: hide a side column on a landing page,
add a body class for a class, change the page title. The view template runs before the pagelayout and cannot reach it
directly; the **persistent variable** carries the message.

## Contents

1. [Setting it](#setting-it)
2. [Reading it](#reading-it)
3. [Why "persistent"](#why-persistent)
4. [Helpers](#helpers)
5. [Examples in the shipped designs](#examples-in-the-shipped-designs)

## Setting it

In a full view (or an override of it), set the template variable `persistent_variable` to a hash in the root scope:

```tpl
{set scope=global persistent_variable=hash( 'body_class', 'landing', 'left_menu', false() )}
```

The content view reads it after rendering (`kernel/classes/eznodeviewfunctions.php`: the kernel sets it to `false`
before the view runs and copies any other value into `content_info`).

## Reading it

In the pagelayout:

```tpl
{def $pv = cond( and( is_array( $module_result.content_info ), is_set( $module_result.content_info.persistent_variable ) ),
                 $module_result.content_info.persistent_variable, false() )}
<body class="{if and( $pv, is_set( $pv.body_class ) )}{$pv.body_class|wash}{/if}">
```

## Why "persistent"

The value is stored with the view cache entry (the kernel adds it to the cache keys), so a page served from the view
cache delivers the same value as when it was rendered. A variable set any other way would be lost on a cache hit.

## Helpers

- `ezcss_require` and `ezscript_require` use the persistent variable to pass stylesheet and script lists.
- The ezwebin extension's `ezpagedata_set( 'key', value )` and `ezpagedata_append` write into it, and `ezpagedata()`
  reads it back ([The ezwebin design as a base](../theme-design/ezwebin-as-a-base.md)).

## Examples in the shipped designs

admin4's pagelayout reads `persistent_variable.left_menu` and `extra_menu`, so an edit page template can ask for its own
left column (`design/admin4/templates/pagelayout.tpl`).

## Related pages

- [Module result and content info](module-result-and-content-info.md)
- [Full view](full-view.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
