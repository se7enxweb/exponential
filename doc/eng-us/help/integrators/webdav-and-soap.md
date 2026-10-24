---
title: WebDAV and SOAP
audience: integrator, developer, admin
category: integration
order: 120
summary: The SOAP 1.1 server that extensions add functions to - how to switch it on, how it authenticates, and when to use it in a new integration - and what replaces the WebDAV server, which is no longer part of Exponential.
keywords: SOAP, soap.php, soap.ini, EnableSOAP, SOAPExtensions, registerFunction, eZSOAPServer, eZSOAPClient, legacy protocols, WebDAV, webdav.php, removed
type: help
---
# WebDAV and SOAP

This page is for integrators who meet a requirement such as "our middleware speaks SOAP" or "editors save files from
their desktop into the site". Exponential ships a SOAP 1.1 server (`soap.php`). It is **off by default**, has known
gaps, and is worth considering only when the other side cannot use something newer. Its status against the standards
is in [API and protocol standards](../../standards/apis-and-protocols.md#soap-11).

## Contents

1. [WebDAV](#webdav)
2. [SOAP](#soap)
3. [Calling another system's SOAP service](#calling-another-systems-soap-service)
4. [When to use which](#when-to-use-which)

## WebDAV

Exponential no longer contains a WebDAV server. `webdav.php`, `kernel/private/classes/webdav/`, `settings/webdav.ini`
and the package `zetacomponents/webdav` were removed on 24 October 2026: the server was off by default, its locking was
never finished and its authorizer let every read through. For editors who save files into the site, use the upload
features of the administration (multi-upload, the rich text editor's upload dialog) or a scheduled import. A site that
still needs WebDAV keeps the removed files in an extension of its own and requires `zetacomponents/webdav` itself
([changelog](../../../changelogs/6.0/6.0.15.md#2026-10-24-the-webdav-server-is-removed)).

## SOAP

The entry point is `soap.php`. The library is `lib/ezsoap/classes/` (`eZSOAPServer`, `eZSOAPClient`, envelope,
codec, fault), SOAP 1.1 only, without WSDL. Switch it on and add the extensions that register functions, in
`settings/override/soap.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[GeneralSettings]
EnableSOAP=true
# enabled: the default siteaccess; disabled: matched from the request like any page
UseDefaultAccess=enabled

[ExtensionSettings]
SOAPExtensions[]=myintegration
*/ ?>
```

For each listed extension, `soap.php` includes `extension/<extension>/soap/initialize.php`, where the extension
registers its functions with the server object `$server` (an `eZSOAPServer`). The `syndication` extension is the real
example:

```php
<?php
// extension/myintegration/soap/initialize.php
$server->registerFunction( 'myIntegrationStock', array( 'remoteID' => 'string' ) );

function myIntegrationStock( $remoteID )
{
    $object = eZContentObject::fetchByRemoteID( $remoteID );
    if ( !$object instanceof eZContentObject || !$object->canRead() )
        return 0;
    $dataMap = $object->dataMap();
    return isset( $dataMap['stock'] ) ? (int)$dataMap['stock']->toString() : 0;
}
```

When the request carries HTTP basic credentials, `soap.php` signs that user in before the call
(`eZUser::loginUser()`), so the function runs with the user's policies. Without credentials it runs as anonymous.

## Calling another system's SOAP service

`eZSOAPClient` sends SOAP 1.1 requests and verifies TLS certificates by default (tested in
`tests/tests/lib/ezsoap/eZSOAPClientTlsOptionsTest.php`; see [SOAP client TLS](../../features/6.0/soap-client-tls.md)).
For a modern SOAP service with WSDL, a SOAP 1.2 endpoint or WS-Security, PHP's own `SoapClient` (the `soap` PHP
extension) is usually the better tool inside your extension.

## When to use which

| Requirement | Use |
|---|---|
| Editors put files from their desktop into the site | The upload features of the administration (multi-upload); WebDAV is no longer part of Exponential |
| A middleware product can only call SOAP | The SOAP server with functions of your extension |
| Two Exponential installations share content | `syndication`, which uses SOAP underneath ([page](syndication-between-installations.md)) |
| Anything new | The REST API, `expservices` or a server function of your own |

## Related pages

- [API and protocol standards](../../standards/apis-and-protocols.md)
- [SOAP client TLS](../../features/6.0/soap-client-tls.md)
- [Syndication between installations](syndication-between-installations.md)
- [Integration surfaces at a glance](integration-surfaces-at-a-glance.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
