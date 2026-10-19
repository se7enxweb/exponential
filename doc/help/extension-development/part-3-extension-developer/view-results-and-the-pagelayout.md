---
title: View results, the path and the pagelayout
audience: developer, app-creator, designer
category: extension-development
order: 120
summary: What a view returns in $Result - content, path, navigation part, a different or no pagelayout - how the pagelayout receives it as $module_result, and how to answer with JSON or a file instead of a page.
keywords: Result, module result, module_result, content, path, navigation_part, pagelayout, pagelayout false, content_info, persistent_variable, JSON, cleanExit, popup, breadcrumb
type: help
---
# View results, the path and the pagelayout

Part 3, chapter 12. A view does not print a page. It returns a **result**, and the kernel wraps it in the
**pagelayout**, the template of the site's frame (header, menu, footer). This chapter lists what a result can hold and
how the pagelayout uses it. The code is `ezpKernelWeb` (`kernel/private/classes/ezpkernelweb.php`).

## The result array

| Key | Meaning |
|---|---|
| `content` | the HTML of the page body, usually `$tpl->fetch( 'design:...' )` |
| `path` | the breadcrumb: a list of `array( 'text' => ..., 'url' => ... )`, `url` `false` for the current page |
| `navigation_part` | which top tab of the administration is active (else the view's `default_navigation_part`) |
| `pagelayout` | `false` for no frame; a template name to use another frame |
| `content_info` | for content views, information about the node shown (class, section, node ID ...) |

```php
<?php
$Result = array();
$Result['content'] = $tpl->fetch( 'design:hello/world.tpl' );
$Result['path'] = array(
    array( 'text' => ezpI18n::tr( 'mysite_hello', 'Hello' ), 'url' => '/hello/list' ),
    array( 'text' => $name, 'url' => false )
);
```

A view written as a class returns the same array from `run()`.

## In the pagelayout: $module_result

The pagelayout gets the result as `$module_result`, with keys the kernel adds: `uri`, `ui_context`, `ui_component`,
`is_mobile_device`. It prints the body with:

```text
{$module_result.content}
```

(no `wash`: it is HTML the view built), and the breadcrumb from `$module_result.path`. A design's pagelayout is
`templates/pagelayout.tpl`; the administration designs have their own.

## Another frame, or none

```php
<?php
$Result['pagelayout'] = false;                    // only the content: a fragment, a popup body
$Result['pagelayout'] = 'popup_pagelayout.tpl';   // another frame, found as design:popup_pagelayout.tpl
```

A value with a `:` is used as a full template resource. The kernel's own content tree menu returns
`$Result['pagelayout'] = false` (`kernel/private/classes/views/content/treemenu.php`).

## Answering with JSON or a file

For data, not a page, write the output and end the request:

```php
<?php
header( 'Content-Type: application/json; charset=utf-8' );
echo json_encode( array( 'name' => $name, 'count' => $count ) );
eZExecution::cleanExit();
```

`eZExecution::cleanExit()` runs the kernel's shutdown properly. Under Velocity it ends the request by throwing;
**never wrap a download and `cleanExit()` in a `try { } catch ( Exception $e )`**, or the page is appended to your
output. For ajax calls, an ezjscore server function is usually the better tool:
[Ajax with ezjscore server functions](ajax-with-ezjscore-server-functions.md).

## Titles and meta data

The page title in the browser is built by the pagelayout from the path (`title_path` in some designs) and the site
name. A view that wants a specific title puts it in the last path element.

## Content views and persistent variables

Content views (`node/view/full.tpl` and overrides) can set values for the pagelayout with
`{set scope=global persistent_variable=hash( 'left_menu', false() )}`; they arrive in `$module_result.content_info.persistent_variable`.
Module views simply put what the pagelayout needs into the result or into template variables.

## Related pages

- Previous: [Policy functions and access checks](policy-functions-and-access.md)
- Next: [Template operators](template-operators.md)
- [eZModule and module results](../part-8-reference/ezmodule.md)
- [Error codes and module results](../part-8-reference/error-codes-and-module-results.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
