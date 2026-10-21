---
title: ezjscore JSON endpoints in PHP
audience: php-developer, developer
category: php-development
order: 310
summary: Writing ezjscore server functions in PHP - the [ezjscServer_<name>] block, the static method that receives the arguments array, the answer envelope, permissions with Functions[] and RequirePolicy, POST writes with the form token, several calls in one request, and when to write an expservices service instead.
keywords: ezjscore, server function, ezjscServer, ezjscServerRouter, ezjscore/call, JSON, ContentType=json, Functions, FunctionList, RequirePolicy, X-CSRF-Token, POST, expservices, expServiceBase, AJAX, endpoint
type: help
---
# ezjscore JSON endpoints in PHP

This page is for PHP developers who give their own JavaScript data or actions over HTTP: autocomplete, counters, small
saves. `ezjscore` (`extension/ezjscore`) calls a registered static PHP method and answers in JSON, XML or text. The
JavaScript side and a first example are on
[JSON endpoints with ezjscore](../creators/app-creators/json-endpoints-with-ezjscore.md).

## Contents

1. [How a call reaches your method](#how-a-call-reaches-your-method)
2. [Registering the class](#registering-the-class)
3. [The method](#the-method)
4. [Permissions](#permissions)
5. [Writes](#writes)
6. [Several calls in one request](#several-calls-in-one-request)
7. [expservices: the structured form](#expservices-the-structured-form)
8. [Testing](#testing)

## How a call reaches your method

```text
<root>/ezjscore/call/<name>::<function>[::<arg>[::<arg>...]]?ContentType=json
```

The router `ezjscServerRouter` (`extension/ezjscore/classes/ezjscserverrouter.php`) refuses any name without a
`[ezjscServer_<name>]` block, calls `<class>::<function>( array $args )` with the `::`-separated arguments as strings,
and wraps the return value:

```json
{"error_text": "", "content": <what your method returned>}
```

The format follows the `Accept` header or `?ContentType=json|xml|text`. The kernel ships a test call:
`<root>/ezjscore/call/ezjsc::time`.

## Registering the class

`extension/acmereport/settings/ezjscore.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[ezjscServer_acmereport]
Class=acmeReportServerFunctions
Functions[]=acmereport
RequirePolicy=enabled
*/ ?>
```

| Key | Meaning |
|---|---|
| `Class` | the class; without it the block name is used |
| `File` | optional; without it the autoload map finds the class |
| `TemplateFunction` | answer with a template instead of a PHP method |
| `Functions[]` | the `[ezjscServer] FunctionList[]` entries the user needs (policy `ezjscore/call`, limitation `FunctionList`) |
| `PermissionPrFunction` | check one `FunctionList` entry per function (`<name>_<function>`) |
| `RequirePolicy` | not set: refused calls answer with an error text; `enabled`: a request whose calls are all refused answers 403; `disabled`: not checked |

The comments of `[ezjscServer]` in `extension/ezjscore/settings/ezjscore.ini` describe each key. Add your entry to
`[ezjscServer] FunctionList[]` when you use `Functions[]`.

## The method

```php
<?php
class acmeReportServerFunctions
{
    /**
     * acmereport::count::<status>
     *
     * @param array $args the ::-separated arguments, as strings
     * @return array
     */
    public static function count( $args )
    {
        $access = eZUser::currentUser()->hasAccessTo( 'acmereport', 'read' );
        if ( $access['accessWord'] === 'no' )
            throw new Exception( 'Access denied' );

        $status = isset( $args[0] ) ? (string)$args[0] : '';
        if ( !in_array( $status, array( 'open', 'closed' ), true ) )
            throw new Exception( 'Unknown status' );

        return array( 'count' => (int)eZPersistentObject::count( acmeReportItem::definition(),
                                                                 array( 'status' => $status ) ) );
    }
}
```

- Every argument is a string from the URL: cast it and check it against an allow list.
- Return data (arrays, numbers, strings), not HTML. If you must return markup, escape it.
- An exception becomes the `error_text` of the answer; keep its message free of internals.

## Permissions

`ezjscore/call` does not check a policy for your function by itself. Use both layers:

1. `Functions[]` with `RequirePolicy=enabled` in the block, so a user without the `ezjscore/call` policy for your
   entry is refused before your code runs.
2. A check inside the method for the module policy that the data belongs to (`hasAccessTo()`, `canRead()` on nodes).

## Writes

A method that changes data must refuse anything but POST and rely on the form token:

```php
if ( $_SERVER['REQUEST_METHOD'] !== 'POST' )
    throw new Exception( 'POST required' );
$http = eZHTTPTool::instance();
$title = trim( (string)$http->postVariable( 'title', '' ) );
```

For signed-in users, `ezformtoken` checks the token on every POST before the router runs: the JavaScript sends it as
the field `ezxform_token` or the header `X-CSRF-Token` ([CSRF protection and ezformtoken](csrf-and-ezformtoken.md)).

## Several calls in one request

A POST of `ezjscServer_function_arguments` with several calls joined by `@SEPARATOR$` runs them in one request. Each
call is checked like a single one.

## expservices: the structured form

For services that scripts, apps and other front ends call (not only your own pages), the `expservices` extension builds
on the same router with one class per domain extending `expServiceBase`, a declared `public static $services` list,
discovery (`expservices::catalog`), a fixed envelope (`{"ok": true, "data": ...}`), paging, error codes, API tokens and an
audit event for every write ([Backend services over ezjscore](../../bc/6.0/backend_ezjscore_services.md)):

```php
class expFooServices extends expServiceBase
{
    public static $services = array(
        'bar' => array( 'summary' => '...', 'access' => array( 'content', 'read' ),
                        'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '...' ) );

    public static function bar( $args )
    {
        self::guard( __FUNCTION__ );
        $node = self::node( self::arg( $args, 0, 'int' ) );
        return self::ok( array( 'name' => $node->attribute( 'name' ) ) );
    }
}
```

Declare `$services` as the property's value in the class body, never by an assignment after the class: a persistent
Velocity worker puts statics back to their declared values between requests, and an assignment outside the class does
not run again ([Static state traps](static-state-traps.md)).

## Testing

```bash
curl -s 'https://www.example.com/ezjscore/call/acmereport::count::open?ContentType=json'
php vendor/bin/phpunit tests/tests/extension/expservices/
```

Test the method itself in a DB-less test by calling it with an arguments array.

## Related pages

- [ezjscore server functions reference](../extension-development/part-8-reference/ezjscore-server-functions-reference.md)
- [Ajax with ezjscore server functions](../extension-development/part-3-extension-developer/ajax-with-ezjscore-server-functions.md)
- [REST and remote services from PHP](rest-and-remote-services-from-php.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
