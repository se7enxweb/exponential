---
title: "eZHTTPTool: requests, sessions and redirects"
audience: developer
category: extension-development
order: 100
summary: Read POST and GET variables, keep values in the session and redirect with eZHTTPTool - postVariable() with a fallback, sessionVariable(), redirect() - and the module redirects views prefer.
keywords: eZHTTPTool, instance, postVariable, hasPostVariable, getVariable, hasGetVariable, variable, setSessionVariable, sessionVariable, hasSessionVariable, removeSessionVariable, redirect, redirectTo, request
type: help
---
# eZHTTPTool: requests, sessions and redirects

Part 8, reference 10. `eZHTTPTool` (`lib/ezutils/classes/ezhttptool.php`) is how kernel code and extensions read the
request and the session. Use it instead of `$_POST`, `$_GET` and `$_SESSION`: it handles the session start, logs
missing variables, and keeps your code testable.

```php
<?php
$http = eZHTTPTool::instance();
```

## Request variables

| Method | Returns |
|---|---|
| `hasPostVariable( $var )`, `hasGetVariable( $var )`, `hasVariable( $var )` | whether it was sent (`hasVariable` checks POST, then GET) |
| `postVariable( $var, $fallbackValue = null )` | the POST value, or the fallback |
| `getVariable( $var, $fallbackValue = null )` | the GET value, or the fallback |
| `variable( $var, $fallbackValue = null )` | POST, else GET, else the fallback |

When the variable is missing and the fallback is `null`, these write "Undefined post variable" to the debug output.
Pass a fallback for optional variables:

```php
<?php
$http = eZHTTPTool::instance();
$name = trim( (string)$http->postVariable( 'Name', '' ) );
$page = max( 1, (int)$http->getVariable( 'page', 1 ) );
$ids = array_map( 'intval', (array)$http->postVariable( 'SelectedIDArray', array() ) );
if ( $http->hasPostVariable( 'StoreButton' ) )
{
    // the form's Store button was pressed
}
```

Every value from the request is untrusted: cast numbers, check strings, and escape on output
([Security in extensions](../part-3-extension-developer/security-in-extensions.md)). In module views, the kernel also
checks the form token on POST ([ezformtoken and CSRF](ezformtoken-and-csrf.md)).

## Session

| Method | Use |
|---|---|
| `setSessionVariable( $name, $value )` | store a value for this user's session |
| `sessionVariable( $name, $fallbackValue = null )` | read it |
| `hasSessionVariable( $name, $forceStart = true )` | whether it exists |
| `removeSessionVariable( $name )` | remove it |

Prefix the names with your extension's name (`MyExtLastFilter`). Keep sessions small: they are read on every request
of that user.

## Redirects

In a module view, redirect through the module, which the kernel then carries out at the right moment:

```php
return $Module->redirectTo( '/myext/list' );
```

`eZModule` also has `redirectToView( $viewName, $parameters )` and `redirect( $moduleName, $viewName, $parameters )`
([eZModule and module results](ezmodule.md)).

Outside a module (a script, a handler), `eZHTTPTool::redirect( $path, $parameters = array(), $status = false, ... )`
sends the redirect at once. Never redirect to an address taken from the request without checking that it is on your
own site; the kernel limits redirect hosts (`eZModule::allowedRedirectHosts()`).

## Fetching other sites

`eZHTTPTool::getDataByURL( $url, $justCheckURL = false, $userAgent = false, $forceGetRequest = false )` fetches a URL.
For anything more than a simple GET, use cURL directly, with timeouts.

## Related pages

- Previous: [eZUser and access checks](ezuser-and-access.md)
- Next: [eZSys and eZExtension](ezsys-and-ezextension.md)
- [View parameters](../part-3-extension-developer/view-parameters.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
