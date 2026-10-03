# Backend services over ezjscore (expservices)

`extension/expservices` turns the ezjscore server functions into a library of remote services that offer the
features of Exponential to remote admin apps (GTK, Qt, KDE/GNOME, iOS, Android, shell) and to JavaScript
frontends. Version 0.1.0 (first draft, 2026-10-02).

## Overview

### What an ezjscore server function is

ezjscore (`extension/ezjscore`) lets a page call PHP over HTTP. A server function is a public static method of a class
that is registered in `ezjscore.ini` as a block `[ezjscServer_<name>]` (`Class=<class>`). It is called as

    <root>/ezjscore/call/<name>::<function>[::<arg>[::<arg>...]]

(the admin siteaccess has the same URL below `/admin/`). The router (`ezjscServerRouter`) refuses any class that has no
block in `ezjscore.ini`, calls `<class>::<function>( array $args )` and answers in the format of the `Accept`
header or `?ContentType=json|xml|text`. `ezjscore/call` itself does not check a policy: every function checks for
itself. Several calls can be sent in one request: POST `ezjscServer_function_arguments` with the calls joined by `@SEPARATOR$`.

The ezjscore answer wraps the return value: `{"error_text": "", "content": <what the function returned>}`.
expservices functions return the envelope below as `content`.

### The classes: one domain, one class, one block

Every domain is a class `exp<Domain>Services` extending `expServiceBase`, registered in
`extension/expservices/settings/ezjscore.ini.append.php` as `[ezjscServer_exp<domain>]`, so a service of the domain
`system` is called `ezjscore/call/expsystem::version`. Each public static method is a service and is declared in the
class's `public static $services`:

    'version' => array( 'summary' => '...', 'access' => 'public' | 'user' | array( 'setup', 'system_info' ),
                        'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '...' )

`ezjscServerRouter::call()` hands every class that extends `expServiceBase` to `expServiceBase::invoke()`, which
checks that the service is declared, calls it, and turns every exception into the error envelope.

### Discovery

| Call | Answers |
|---|---|
| `expservices::catalog[::<domain>]` | every service: domain, method, summary, access, write, args, returns, call |
| `expservices::schema::<domain>` | the services of one domain |
| `expservices::service::<domain>::<method>` | one descriptor |
| `expservices::domains` | the domains with their class and service count |
| `expservices::version` | the expservices, envelope and Exponential versions |

The domain is the part after `exp` of the block name (`session`, `system`, `ini`, `cache`, ...). The calls to the
discovery functions are public; every other service checks its own `access`.

### The envelope

    { "ok": true,  "data": <the result>, "meta": { ... } }
    { "ok": false, "error": { "code": 403, "message": "No access to setup/managecache" } }

Lists are paged: `data` is the items and `meta` is `total`, `offset`, `limit`, `count`, `has_more`.

### Authentication

Sign in once with `expsession::login` (POST `username`, `password`); the response sets the session cookie, which
a non-browser client keeps in a cookie jar and sends with every call. `expsession::whoami` says who the session is,
`expsession::token` returns the form token needed for writes, `expsession::ping` keeps the session alive,
`expsession::logout` ends it. An API-token option (no cookie) is future work.

    curl -c jar -d 'username=admin' --data-urlencode 'password=...' https://site/ezjscore/call/expsession::login
    curl -b jar https://site/ezjscore/call/expsession::whoami

### Access

`'access'` of a service is `public` (anybody), `user` (any signed-in user) or `array( module, function )`, a policy of
the user (`eZUser::hasAccessTo`; a limited policy passes the check, and the service then checks the object, e.g. `canRead()`
of a node). A missing login answers 401, a missing policy 403.

### Paging

Services that list take `limit` and `offset` arguments (`expsystem::phpextensions::10::20` is 10 items from 20).
The default and the maximum are in `expservices.ini [Paging]` (25 and 200).

### Errors

| Code | Meaning |
|---|---|
| 400 | bad or missing arguments |
| 401 | login needed |
| 403 | no policy, no POST, or a wrong form token |
| 404 | no such service, node, object ... |
| 409 | conflict |
| 422 | the data is not valid |
| 500 | the service failed (logged to the debug output, never a PHP fatal) |

### Writes

A service with `'write' => true` needs a POST, the form token (field `ezxform_token` or header `X-CSRF-Token`; get it
from `expsession::token`), and its policy; arguments come in the POST body (`expServiceBase::post()`), not in the URL.
Every successful write records the audit event `service.<domain>.<method>` (`expAudit`), besides the events the
kernel operation writes itself. `expservices.ini [Writes]` can switch the token check (tests only) and the audit off.

### Writing a service

    class expFooServices extends expServiceBase
    {
        public static $services = array( 'bar' => array( 'summary' => '...', 'access' => array( 'content', 'read' ),
            'write' => false, 'args' => array( 'node_id' => 'int' ), 'returns' => '...' ) );

        public static function bar( $args )
        {
            self::guard( __FUNCTION__ );
            $node = self::node( self::arg( $args, 0, 'int' ) );
            return self::ok( array( 'name' => $node->attribute( 'name' ) ) );
        }
    }

Helpers: `arg()`, `post()`, `paging()`/`pageOf()`, `ok()`, `page()`, `guard()`, `audit()`, `node()`, `can()`.
Register the class in `ezjscore.ini.append.php`, run `php bin/php/ezpgenerateautoloads.php -e`.

### Testing a call

    php ai/bin/one/expservices_call.php expsystem::version        # as admin, from the command line
    php vendor/bin/phpunit tests/tests/extension/expservices/

## Domains

