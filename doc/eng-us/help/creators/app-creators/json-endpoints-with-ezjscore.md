---
title: JSON endpoints with ezjscore
audience: app-creator, developer
category: app-creation
order: 340
summary: Give your pages' JavaScript a JSON back end with ezjscore server functions - the [ezjscServer_<name>] block, the static method, the call URL and answer envelope, permission checks, form tokens, and when to use expservices or REST instead.
keywords: ezjscore, server functions, ezjscServer, ezjscore/call, JSON, AJAX, ezjscServerRouter, RequirePolicy, FunctionList, JavaScript, app creator
type: help
---
# JSON endpoints with ezjscore

This page is for application creators whose pages need data from PHP without a page reload: autocomplete, live
counters, small saves. `ezjscore` calls registered PHP functions over HTTP and answers in JSON
([ezjscore](../../../features/6.0/extensions/ezjscore.md), [Backend ezjscore services](../../../bc/6.0/backend_ezjscore_services.md)).

## Contents

1. [How a call works](#how-a-call-works)
2. [Registering a server function](#registering-a-server-function)
3. [The class](#the-class)
4. [Calling it from JavaScript](#calling-it-from-javascript)
5. [Security](#security)
6. [ezjscore, expservices or REST?](#ezjscore-expservices-or-rest)

## How a call works

```text
<root>/ezjscore/call/<name>::<function>[::<arg>...]?ContentType=json
```

The router (`ezjscServerRouter`) refuses any name without a block in `ezjscore.ini`, calls `<class>::<function>( array
$args )` and wraps the return value: `{"error_text": "", "content": <your value>}`. A test function ships:
`<root>/ezjscore/call/ezjsc::time`.

## Registering a server function

```ini
# extension/expdemo/settings/ezjscore.ini.append.php
<?php /* #?ini charset="utf-8"?
[ezjscServer_expdemo]
Class=expDemoServerFunctions
*/ ?>
```

Optional keys (see the comments of `[ezjscServer]` in `extension/ezjscore/settings/ezjscore.ini`): `File`,
`TemplateFunction`, `Functions[]` (policy functions of `[ezjscServer] FunctionList[]` the user needs).

## The class

```php
<?php
class expDemoServerFunctions
{
    public static function count( array $args )
    {
        $user = eZUser::currentUser();
        $access = $user->hasAccessTo( 'expdemo', 'read' );
        if ( $access['accessWord'] === 'no' )
            throw new Exception( 'Access denied' );
        $userID = isset( $args[0] ) ? (int)$args[0] : 0;
        return array( 'count' => eZPersistentObject::count( expDemoItem::definition(), array( 'user_id' => $userID ) ) );
    }
}
```

`ezjscore/call` does not check a policy by itself: **every function checks for itself**.

## Calling it from JavaScript

```javascript
// extension/expdemo/design/standard/javascript/expdemo.js
fetch( root + 'ezjscore/call/expdemo::count::' + encodeURIComponent( userId ) + '?ContentType=json',
       { credentials: 'same-origin', headers: { 'Accept': 'application/json' } } )
    .then( function ( r ) { return r.json(); } )
    .then( function ( data ) { if ( !data.error_text ) show( data.content.count ); } );
```

`root` comes from a JSON data block in the pagelayout, not an inline script
([Content Security Policy for your code](content-security-policy.md)). Writes are POSTs and carry the form token.

## Security

- Check permissions in every function; validate and cast every argument.
- Return data, not HTML; if you must return HTML, escape it.
- Writes only by POST, with the form token.
- Do not expose more than the page needs.

## ezjscore, expservices or REST?

| For | Use |
|---|---|
| your own pages' JavaScript | ezjscore server functions |
| admin features from scripts, apps or other front ends | `expservices`, with discovery, one envelope and API tokens ([Remote services](remote-services-expservices.md)) |
| content for other programs, OAuth for third parties | REST ([REST API](rest-api.md)) |

## Related pages

- [Remote services with expservices](remote-services-expservices.md)
- [Stylesheets and scripts for your site](../website-creators/stylesheets-and-scripts.md)
- [Extension points: server-side ajax function](../../../bc/6.0/rad-extension-points.md#server-side-ajax-function)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
