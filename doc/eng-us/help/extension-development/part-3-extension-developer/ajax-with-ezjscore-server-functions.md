---
title: Ajax with ezjscore server functions
audience: developer, app-creator, designer
category: extension-development
order: 240
summary: Answer ajax calls with an ezjscore server function: settings, a class of static functions, the call address and its JSON envelope, Exp.io, policies, POST and the form token.
keywords: ajax, ezjscore, server function, ezjscServer, ezjscServerFunctions, ezjscore/call, Exp.io, Exp.io.call, $.ez, json, error_text, FunctionList, PermissionPrFunction, RequirePolicy, form token, POST
type: help
---
# Ajax with ezjscore server functions

Part 3, chapter 24. When a page needs data without reloading (a rating, a live search, a list that loads more), the
browser calls an **ezjscore server function**: a static method of your class, reachable at
`/ezjscore/call/<server>::<function>::<arg1>::<arg2>`. This chapter builds one; the method was run with the kernel
loaded, and the address format and JSON envelope were checked on a running installation with the kernel's own
`ezjsc::time` function.

## Settings

`extension/mysite_hello/settings/ezjscore.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ezjscServer_mysitehello]
Class=mysiteHelloServerFunctions

*/ ?>
```

The block name after `ezjscServer_` is the **server name** used in calls. `Class` names your class (autoloaded);
`File` can name its file when it is not autoloaded. These keys and the access keys below are documented in
`extension/ezjscore/settings/ezjscore.ini`.

## The class

```php
<?php
/**
 * ezjscore server functions of mysite_hello:
 *   GET  <root>/ezjscore/call/mysitehello::greet::Ada   -> {"error_text":"","content":{"greeting":"Hello, Ada!"}}
 *   JavaScript: Exp.io.call( 'mysitehello::greet', [ 'Ada' ] ).then( function ( content ) { ... } )
 */
class mysiteHelloServerFunctions extends ezjscServerFunctions
{
    public static function greet( $args )
    {
        $name = isset( $args[0] ) && is_string( $args[0] ) && $args[0] !== '' ? $args[0] : 'world';
        // the browser inserts this as text, but escape anyway: a server function may be called from anywhere
        return array( 'greeting' => 'Hello, ' . htmlspecialchars( $name, ENT_QUOTES, 'UTF-8' ) . '!' );
    }
}
```

Each public static method is a function; `$args` holds the parts of the address after the function name. Called with
`'Ada <b>'` it returned `{"greeting":"Hello, Ada &lt;b&gt;!"}`.

## The address and the answer

```text
GET /ezjscore/call/mysitehello::greet::Ada
Accept: application/json
```

answers with an envelope, as the kernel's `ezjsc::time` does on a live installation:

```json
{"error_text":"","content":{"greeting":"Hello, Ada!"}}
```

`content` is what your method returned; `error_text` is filled when the call failed or was refused. Without the JSON
`Accept` header the view answers with the plain content.

## Calling it from JavaScript

The administration and expui-enabled designs have `Exp.io` (extension `expui`):

```javascript
Exp.io.call( 'mysitehello::greet', [ 'Ada' ] ).then( function ( content ) {
    document.querySelector( '[data-mysite-greeting]' ).textContent = content.greeting;
}, function ( error ) {
    // error.kind: signedout, refused, server, network, timeout or invalid
} );
```

Load it with `{ezscript_require( array( 'exp::core', 'exp::io', 'mysite_hello.js' ) )}`. `Exp.io.call()` adds the
form token, rejects on `error_text`, and resolves with `content`. Older code that uses `$.ez()` keeps working through
expui's compatibility layer (`exp/compat.js`). Insert results with `textContent`, not `innerHTML`, unless you built
the HTML safely.

## Who may call it

Without further settings, every visitor who may use the `ezjscore/call` view can call your function. Restrict it:

```ini
[ezjscServer]
FunctionList[]=mysitehello_greet

[ezjscServer_mysitehello]
Class=mysiteHelloServerFunctions
Functions[]=mysitehello
PermissionPrFunction=enabled
RequirePolicy=enabled
```

| Key | Meaning |
|---|---|
| `[ezjscServer] FunctionList[]` | names offered as limitation values of the policy `ezjscore/call` in the role editor |
| `Functions[]` | the names a user needs |
| `PermissionPrFunction=enabled` | the needed name is `<name>_<function>` per function (`mysitehello_greet`) |
| `RequirePolicy=enabled` | check `Functions[]`, and answer 403 when every call of a request is refused |

The `ezstarrating` extension is a real example (`FunctionList[]=ezstarrating_rate`, `PermissionPrFunction=enabled`).
Then grant `ezjscore/call` with that function to the roles that need it.

## Changing data: POST and the form token

A function that changes anything must refuse GET: an `<img src=".../ezjscore/call/...">` on any page would otherwise
call it in a visitor's name. `ezstarrating`'s `rate()` returns early unless the request is a POST, which the form token
check then covers. `Exp.io.call()` sends a POST with the form token by default (pass `{ method: 'GET' }` for a read-only call), and `Exp.io.form()` posts a form with its token.

## Template functions instead of PHP

A server block can set `TemplateFunction=true` to answer with a template `templates/<class>/<function>.tpl` instead of a
method (`ezjscore.ini` documents it). Useful for returning rendered HTML fragments.

## RAD

**Handler extension: ajax function** (`/setup/handlerextension/ajaxfunction`) writes the class and the settings; see
[By hand: an ezjscore server function](tutorials/by-hand-ajax-server-function.md).

## Related pages

- Previous: [CSS and JavaScript in an extension](css-and-javascript-in-an-extension.md)
- Next: [Translations in an extension](translations-in-an-extension.md)
- [ezjscore server functions reference](../part-8-reference/ezjscore-server-functions-reference.md)
- [Backend ezjscore services](../../../bc/6.0/backend_ezjscore_services.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
