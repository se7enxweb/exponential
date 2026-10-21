---
title: ezjscore server functions for integrations
audience: integrator, developer
category: integration
order: 90
summary: When a small JSON endpoint of your own is the right integration surface, how to write one as an ezjscore server function, how to protect it with the ezjscore/call policy and the form token, and when to use expservices or a REST route instead.
keywords: ezjscore, server function, ezjscServerFunctions, ezjscore.ini, ezjscServer, /ezjscore/call, JSON endpoint, ezjscore/call policy, FunctionList, form token, Ajax, integration endpoint
type: help
---
# ezjscore server functions for integrations

This page is for integration developers who need one small endpoint of their own: a stock level for the product
page, a status the external system polls, a lookup an embedded widget calls. An **ezjscore server function** is a
public static method of your class, reachable at `/ezjscore/call/<server>::<function>[::<arg> ...]` and answered as
JSON. It is the lightest way to put an HTTP endpoint into an extension.

## Contents

1. [Which endpoint style to choose](#which-endpoint-style-to-choose)
2. [A server function in an integration extension](#a-server-function-in-an-integration-extension)
3. [Protecting it](#protecting-it)
4. [Calling it from another system](#calling-it-from-another-system)
5. [Checklist](#checklist)

## Which endpoint style to choose

| Style | Choose it when | Credentials |
|---|---|---|
| Plain ezjscore server function | Browser widgets and simple lookups on the same site; you check access yourself | Session; policy `ezjscore/call` |
| `expservices` domain of your own | Other systems call it; you want declared access, paging, token support, audit and a catalog entry | Session or `expt_...` token |
| REST controller in your extension | The endpoint belongs to the `/api/` family, with OAuth tokens and API keys | OAuth token, API key, basic |

For a machine-to-machine endpoint the `expservices` domain is usually the better choice
([Remote services with expservices](remote-services-with-expservices.md#your-own-service-domain)).

## A server function in an integration extension

`extension/myintegration/settings/ezjscore.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ezjscServer_myintegration]
Class=myIntegrationServerFunctions
# the function list name that roles are limited by (policy ezjscore/call, limitation FunctionList)
Functions[]=myintegration

*/ ?>
```

`extension/myintegration/classes/myintegrationserverfunctions.php`:

```php
<?php
/**
 * GET <root>/ezjscore/call/myintegration::stock::<remote id>
 * Returns the stock level the ERP import stored on the product object.
 */
class myIntegrationServerFunctions extends ezjscServerFunctions
{
    public static function stock( $args )
    {
        $remoteID = isset( $args[0] ) ? (string)$args[0] : '';
        $object = eZContentObject::fetchByRemoteID( $remoteID );
        if ( !$object instanceof eZContentObject || !$object->canRead() )
        {
            // the same answer for "missing" and "not allowed", so nothing leaks
            return array( 'found' => false );
        }
        $dataMap = $object->dataMap();
        $stock = isset( $dataMap['stock'] ) ? (int)$dataMap['stock']->toString() : null;
        return array( 'found' => true, 'stock' => $stock );
    }
}
```

Regenerate the autoloads (`php bin/php/ezpgenerateautoloads.php -e`) and clear the INI cache
(`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`); reload PHP-FPM and restart Velocity so both runtimes
know the class.

## Protecting it

- **The policy.** Every call needs `ezjscore/call`; its limitation **FunctionList** names the function lists a role
  may call. Give the anonymous role your list only when the answer is public.
- **Check what the function does.** A function that reads content checks `canRead()`; a function that changes data
  requires `POST` and the form token ([ezformtoken and CSRF](../extension-development/part-8-reference/ezformtoken-and-csrf.md)).
- **Validate arguments.** Every `$args` part comes from the address; cast IDs with `(int)` and check strings.
- **Return only what is needed.** Never return passwords, tokens or personal data the caller has no use for.
- **Content Security Policy.** A browser widget on another domain cannot call the endpoint across origins unless you
  design for it; prefer calling it from your own server.

## Calling it from another system

```bash
curl -s -H 'Accept: application/json' 'https://example.com/ezjscore/call/myintegration::stock::erp-product-10442'
# {"error_text":"","content":{"found":true,"stock":12}}
```

With the `Accept: application/json` header the answer is the envelope; `error_text` is filled when the call failed
or was refused. From the browser, `Exp.io.call( 'myintegration::stock', [ 'erp-product-10442' ] )` returns a Promise
(admin and `expui` designs), and `$.ez()` works where `ezjsc::jqueryio` is loaded.

## Checklist

- [ ] The server block, class and `Functions[]` list in your extension.
- [ ] A role with `ezjscore/call` limited to your function list, given only to who needs it.
- [ ] Reads check `canRead()`; writes require `POST` and the form token.
- [ ] Arguments validated and cast.
- [ ] The answer documented in the interface specification.

## Related pages

- [ezjscore server functions reference](../extension-development/part-8-reference/ezjscore-server-functions-reference.md)
- [Ajax with ezjscore server functions](../extension-development/part-3-extension-developer/ajax-with-ezjscore-server-functions.md)
- [JSON endpoints with ezjscore](../creators/app-creators/json-endpoints-with-ezjscore.md)
- [ezjscore](../../features/6.0/extensions/ezjscore.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
