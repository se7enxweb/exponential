---
title: "By hand: an ezjscore server function"
audience: developer, app-creator, designer
category: extension-development
order: 190
summary: Answer an ajax call: ezjscServer settings, a server function class, a script that calls it with Exp.io and writes with textContent, a policy for it, and testing the address.
keywords: tutorial, by hand, ajax, ezjscore, server function, ezjscServerFunctions, ezjscServer, Exp.io.call, ezscript_require, exp::io, JSON, FunctionList, PermissionPrFunction, RequirePolicy
type: help
---
# By hand: an ezjscore server function

Tutorial 18. A button on the hello page asks the server for a greeting without reloading. The server function was run
with the kernel loaded, the JavaScript passed `node --check`, and the JSON envelope was checked on a running
installation with the kernel's own `ezjsc::time` function.

## 1. The settings

`extension/mysite_hello/settings/ezjscore.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ezjscServer]
FunctionList[]=mysitehello_greet

[ezjscServer_mysitehello]
Class=mysiteHelloServerFunctions
Functions[]=mysitehello
PermissionPrFunction=enabled
RequirePolicy=enabled

*/ ?>
```

The server is called `mysitehello`; calling its `greet` function needs the policy `ezjscore/call` with the function
list value `mysitehello_greet`.

## 2. The class

`extension/mysite_hello/classes/mysitehelloserverfunctions.php`:

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

Called with `'Ada <b>'` it returned `{"greeting":"Hello, Ada &lt;b&gt;!"}`.

## 3. The script

`extension/mysite_hello/design/standard/javascript/mysite_hello.js`:

```javascript
document.addEventListener( 'click', function ( e ) {
    var button = e.target.closest( '[data-mysite-ask]' );
    if ( !button || !window.Exp || !Exp.io )
        return;
    var out = document.querySelector( '[data-mysite-answer]' );
    Exp.io.call( 'mysitehello::greet', [ button.getAttribute( 'data-mysite-ask' ) ], { method: 'GET' } ).then(
        function ( content ) { if ( out ) out.textContent = content.greeting; },
        function ( error ) { if ( out ) out.textContent = error.message; }
    );
} );
```

The call is a read, so it uses GET. A server function that changes data must accept POST only (the `Exp.io` default)
and is covered by the form token.

## 4. The template

In `hello/world.tpl` of tutorial 12, add:

```text
{ezscript_require( array( 'exp::core', 'exp::io', 'mysite_hello.js' ) )}
<button type="button" class="mysite-ask" data-mysite-ask="{$name|wash}">{'Ask the server'|i18n( 'extension/mysite_hello' )}</button>
<output data-mysite-answer></output>
```

No inline script, no `onclick`: it works under the administration's Content Security Policy as well.

## 5. Try it

```bash
php -l extension/mysite_hello/classes/mysitehelloserverfunctions.php
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

Grant `ezjscore/call` with the function `mysitehello_greet` to the role that views the page. Then open the address
directly, sending the JSON header:

```bash
curl -s -H 'Accept: application/json' https://www.example.com/ezjscore/call/mysitehello::greet::Ada
```

The answer is `{"error_text":"","content":{"greeting":"Hello, Ada!"}}` for a user who may call it; for others the
`error_text` says why. On the page, the button writes the greeting into the output.

## Related pages

- Previous: [By hand: your own table with eZPersistentObject](by-hand-own-table-with-persistent-object.md)
- Next: [By hand: a rich text custom tag](by-hand-custom-tag.md)
- [Ajax with ezjscore server functions](../ajax-with-ezjscore-server-functions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
