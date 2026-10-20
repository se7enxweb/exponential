---
title: ezjscore server functions reference
audience: developer
category: extension-development
order: 230
summary: The settings of an ezjscore server function block, the call address and answer, the ezjscore/call policy, the server functions the kernel ships, and the JavaScript clients.
keywords: ezjscore, server function, ezjscServer, ezjscServerFunctions, ezjscore/call, Class, Functions, TemplateFunction, RequirePolicy, HardLimit, ezjscnode, Exp.io.call, $.ez, JSON
type: help
---
# ezjscore server functions reference

Part 8, reference 19. A server function is a PHP method the browser can call through `/ezjscore/call/...` and get JSON
back. Building one is explained in [Ajax with ezjscore server functions](../part-3-extension-developer/ajax-with-ezjscore-server-functions.md)
and the tutorial [An Ajax server function, by hand](../part-3-extension-developer/tutorials/by-hand-ajax-server-function.md).
This page lists the settings, the built-in functions and the clients.

## The settings block

In your extension's `settings/ezjscore.ini.append.php`, one block per server name:

| Key | Meaning |
|---|---|
| `[ezjscServer_<name>]` | the block; `<name>` is the server name used in calls |
| `Class` | the class whose public static methods are the functions (extends `ezjscServerFunctions`) |
| `File` | the class's file, when it is not autoloaded |
| `TemplateFunction=true` | the functions are templates, `templates/<name>/<function>.tpl`, instead of PHP methods |
| `Functions[]` | the function list names this block belongs to, used by the `ezjscore/call` policy's limitation |
| `RequirePolicy` | `enabled`: callers also need the `ezjscore/call` policy for this function list |
| `HardLimit` | (`ezjscnode` only) the most items a subtree call returns |

## The address

```text
<root>/ezjscore/call/<server>::<function>[::<arg1>[::<arg2> ...]]
```

The parts after the function name arrive in `$args`. POST data is read with `eZHTTPTool` in the method. The answer,
with an `Accept: application/json` header, is `{"error_text":"...","content":...}`.

## Access

- Every call needs the policy `ezjscore/call`; its limitation **FunctionList** names the function lists a role may
  call.
- Each function must still check what it does: a function that reads content checks that the user can read it; one
  that changes data requires POST and the form token ([ezformtoken and CSRF](ezformtoken-and-csrf.md)).
- The kernel's `ezjscnode` functions are readable by anyone who may read the node unless `RequirePolicy=enabled`;
  [backend ezjscore services](../../../bc/6.0/backend_ezjscore_services.md) explains the switch.

## Server functions the kernel ships

| Call | Returns |
|---|---|
| `ezjsc::time` | the server time (a quick test) |
| `ezjscnode::subtree::<nodeID>[::<limit>::<offset>...]` | a node list as JSON |
| `ezjsctemplate::<template>` | a template rendered as the answer |
| `expsubitems::columns::<nodeID>`, `expsubitems::rows::...` | the admin sub-items table's data |
| `expdebugbar::...` | the debug bar's functions (settings, caches, log) |

The comments of `extension/ezjscore/settings/ezjscore.ini` give each block's test address.

## Clients

| Client | Loaded with | Call |
|---|---|---|
| Exponential UI | `exp::io` (on every admin page) | `Exp.io.call( 'myserver::myfunction', [ arg1 ], { data: { ... } } )` returns a Promise; POST by default |
| jQuery | `ezjsc::jquery`, `ezjsc::jqueryio` | `$.ez( 'myserver::myfunction::arg1', postData, callback )` |

Both send the form token with POST requests from pages that carry it.

## Related pages

- Previous: [Content functions from PHP](content-functions-from-php.md)
- Next: [ezformtoken and CSRF protection](ezformtoken-and-csrf.md)
- [ezjscore](../../../features/6.0/extensions/ezjscore.md)
- [The expui JavaScript API](expui-javascript-api.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
