---
title: WebDAV and SOAP
audience: integrator, developer, admin
category: integration
order: 120
summary: The two older protocols Exponential still speaks - WebDAV for mounting content as a drive, and a SOAP 1.1 server that extensions add functions to - how to switch each on, how they authenticate, their known gaps, and when to use them in a new integration.
keywords: WebDAV, webdav.php, webdav.ini, EnableWebDAV, StartNode, FolderClasses, SOAP, soap.php, soap.ini, EnableSOAP, SOAPExtensions, registerFunction, eZSOAPServer, eZSOAPClient, legacy protocols
type: help
---
# WebDAV and SOAP

This page is for integrators who meet a requirement such as "editors save files from their desktop into the site" or
"our middleware speaks SOAP". Exponential ships a WebDAV server (`webdav.php`) and a SOAP 1.1 server (`soap.php`).
Both are **off by default**, both have known gaps, and both are worth considering only when the other side cannot use
something newer. Their status against the standards is in
[API and protocol standards](../../standards/apis-and-protocols.md#webdav-rfc-4918).

## Contents

1. [WebDAV](#webdav)
2. [SOAP](#soap)
3. [Calling another system's SOAP service](#calling-another-systems-soap-service)
4. [When to use which](#when-to-use-which)

## WebDAV

The entry point is `webdav.php` at the installation root. It runs the Zeta Components WebDAV server
(`zetacomponents/webdav ~1.2`) with the content backend `kernel/private/classes/webdav/ezwebdavcontentbackend.php` and
basic authentication against Exponential users (`ezwebdavcontentbackendauth.php`).

Switch it on in `settings/override/webdav.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[GeneralSettings]
EnableWebDAV=true
# the node WebDAV starts at (2 = the content root)
StartNode=2
# classes shown as folders
FolderClasses[]
FolderClasses[]=folder
*/ ?>
```

| Point | Detail |
|---|---|
| Authentication | Basic authentication with an Exponential login; serve it over HTTPS only |
| Rights | The user's policies apply; give WebDAV users a role limited to the subtrees they may change |
| What works | WebDAV class 1 methods (COPY, DELETE, GET, HEAD, MKCOL, MOVE, OPTIONS, PROPFIND, PROPPATCH, PUT), with recorded regression cases in `tests/tests/kernel/private/webdav/regression/` |
| Known gaps | Locking (class 2) is incomplete: the lock authorizer's methods are empty; whether LOCK and UNLOCK work with real clients is unverified. The regression recordings are not run by CI |

> **Note**
> Your web server must send WebDAV requests to `webdav.php` (a rewrite rule or a separate virtual host). Test with the
> client your customer will really use before promising it; desktop clients differ in how they handle locks.

### Permissions (since 6.0.15)

What a WebDAV user may see and change is decided as on the web, in two steps:

| Request | Who may | Answer when refused |
|---|---|---|
| The root `/` (the list of siteaccesses) | everybody, without signing in | - |
| Anything below `/<siteaccess>/`, reading too | a signed-in user with `user/login` for that siteaccess (the policy's SiteAccess limitation) | `401` without or with wrong credentials, `403` without `user/login` there |
| Reading a folder or file | `content/read` on the node | `404`: a node the user may not read is not listed and not shown |
| PUT, MKCOL | `content/create` (a new file or folder) or `content/edit` (a replaced file) below the folder | `403`; nothing is stored |
| DELETE | `content/remove` | `403`; nothing is removed |
| COPY, MOVE | the rights of the same copy or move in the administration (`content/create` at the destination) | `403`; COPY or MOVE between two siteaccesses is always refused |

Until 6.0.15 every read was granted before the policy check, and a refused PUT, MKCOL, DELETE, COPY or MOVE was
answered with `201` or `204`: the client showed a success and the change was lost. Check that WebDAV users have
`user/login` for the siteaccess they use (`/<siteaccess>/` in the WebDAV path), as they need for the web.

"Verify it's you" is never asked on WebDAV: it belongs to actions in the web interface. A second sign-in step is asked
only where the site opts WebDAV in (`Guard[]=webdav`), see
[second sign-in step on other sign-in paths](../../features/6.0/second-factor-on-other-sign-in-paths.md); an account
without its `ezuser_setting` row cannot sign in.

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
| Editors drag files into a folder of the site from their desktop | WebDAV, after testing the client; or the upload features of the administration |
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
