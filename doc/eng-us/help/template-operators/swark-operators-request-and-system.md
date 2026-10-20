---
title: "Swark operators: request and system"
audience: developer, designer, app-creator
category: development
order: 432
summary: The request and system operators of the swark extension - charset, current_siteaccess, current_layout, is_post_request, server, cookie, user_id_by_login, variable_names, debug, debug_attributes, redirect, return and clear_object_cache - with parameters, return values, which ones end the request or change state, and examples rendered on alpha.
keywords: swark, charset, current_siteaccess, current_layout, is_post_request, server, cookie, user_id_by_login, variable_names, debug, debug_attributes, redirect, return, clear_object_cache
type: help
---
# Swark operators: request and system

These swark operators read the request and the system, write to the debug output, or end the request. The classes are
in `extension/swark/autoloads/classes/`; see [The swark operators](swark-operators.md) for how they are registered.

## Contents

1. [Reading](#reading): [charset](#charset), [current_siteaccess](#current_siteaccess),
   [current_layout](#current_layout), [is_post_request](#is_post_request), [server](#server), [cookie](#cookie),
   [user_id_by_login](#user_id_by_login), [variable_names](#variable_names)
2. [Debug output](#debug-output): [debug](#debug), [debug_attributes](#debug_attributes)
3. [Operators with side effects](#operators-with-side-effects): [redirect](#redirect), [return](#return),
   [clear_object_cache](#clear_object_cache)

## Reading

### charset

No parameters, no input. Returns the HTTP charset (`eZTextCodec::httpCharset()`).

### current_siteaccess

No parameters. Returns the name of the current siteaccess (`$GLOBALS['eZCurrentAccess']['name']`).

### current_layout

No parameters. Returns the `layout` key of the design resource (set by the `layout/set` view), or `false`. Empty on
an ordinary page.

### is_post_request

No parameters. `true` when `$_SERVER['REQUEST_METHOD']` is `POST`.

### server

| Parameter | Required |
|---|---|
| `variable_name` | yes |

Returns `$_SERVER[<name in upper case>]`, or `false` with a debug warning when it is not set. The value is raw: wash
it, and never print server variables on a public page.

### cookie

| Parameter | Required |
|---|---|
| `name` | yes |

Reads `$_COOKIE[name]`, or `false` with a debug warning. It does not set cookies. The value comes from the visitor:
wash it. Not shown in the example because the command line has no cookies.

### user_id_by_login

| Parameter | Required |
|---|---|
| `login` | yes |

Returns the content object id of the user with that login (escaped SQL on `ezuser`), or `false`. When `login` is
empty the input is used, but because the parameter is required the piped form logs "Parameter 'login' missing"
warnings: call it as a function.

### variable_names

| Parameter | Required | Default |
|---|---|---|
| `in_debug` | no | `1` |

Lists the names of the template variables of every namespace (`$name` or `$namespace:name`). With the default the list
goes to the debug output as a notice; with `0` it is returned.

```tpl
{def $title = 'x' $count = 3}
1 charset: {charset()}
2 current_siteaccess: {current_siteaccess()}
3 current_layout: [{current_layout()}]
4 is_post_request: {is_post_request()|choose( 'no', 'yes' )}
5 server SCRIPT_NAME: {server( 'script_name' )}
6 user_id_by_login: admin={user_id_by_login( 'admin' )}, anonymous={user_id_by_login( 'anonymous' )}, nobody={user_id_by_login( 'nobody-here' )|get_type}
7 variable_names: {variable_names( 0 )}
```

Output on alpha:

```text
1 charset: utf-8
2 current_siteaccess: site
3 current_layout: []
4 is_post_request: no
5 server SCRIPT_NAME: bin/php/ezexec.php
6 user_id_by_login: admin=14, anonymous=10, nobody=boolean[false]
7 variable_names: $title, $count
```

## Debug output

### debug

| Parameter | Required | Default |
|---|---|---|
| `header` | no | `Debug operator` |

Writes the input to the debug output as a notice (`eZDebug::writeNotice()`) and returns nothing.

### debug_attributes

| Parameter | Required | Default |
|---|---|---|
| `header` | no | `Debug attributes operator` |
| `depth` | no | `2` |

Writes the attribute tree of the input, like the kernel's `attribute` operator, to the debug output. An integer first
parameter is taken as the depth.

Neither is shown in a rendered example: their output goes only to the debug output (shown when debug output is
enabled for the request), not into the page.

## Operators with side effects

Not run on alpha: these operators stop the request, send headers or empty caches, which a read-only example must not
do. Their behavior is described from the code.

### redirect

| Parameter | Required | Default |
|---|---|---|
| `url` | yes | |
| `status` | no | `302` |

Redirects with `eZHTTPTool::redirect()` and ends the request with `eZExecution::cleanExit()`. A URL without a scheme is
prefixed with the index directory. When `url` is `null` the input is used.

```tpl
{redirect( '/content/view/full/89', 301 )}
```

### return

| Parameter | Required | Default |
|---|---|---|
| `content_type` | no | empty |

Sends the input as the whole response: sets `Content-Type` (when given) and `Content-Length`, discards all output
buffers, prints the input and ends the request. Used to answer small JSON or text requests from a template.

```tpl
{json_encode( hash( 'ok', true() ) )|return( 'application/json' )}
```

### clear_object_cache

No parameters. Calls `eZContentObject::clearCache()`, which empties the in-memory cache of content objects of this
request (it does not touch the view cache on disk). Useful in long templates that load many objects.

## Related pages

- [The swark operators](swark-operators.md)
- [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md)
- [URL, design, INI and HTTP operators](url-operators.md) (`ezhttp`, `ezsys`)
- [Template debugging](../designers/templates/template-debugging.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
