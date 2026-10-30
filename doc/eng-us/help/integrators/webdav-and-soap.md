---
title: WebDAV and SOAP
audience: integrator, developer, admin
category: integration
order: 120
summary: The two older protocols Exponential still speaks - WebDAV for mounting content as a drive, and a SOAP 1.1 server that extensions add functions to - how to switch each on, how they authenticate, their known gaps, and when to use them in a new integration.
keywords: WebDAV, index_webdav.php, webdav.php, webdav.ini, EnableWebDAV, StartNode, FolderClasses, SOAP, index_soap.php, soap.php, soap.ini, EnableSOAP, SOAPExtensions, registerFunction, eZSOAPServer, eZSOAPClient, legacy protocols
type: help
---
# WebDAV and SOAP

This page is for integrators who meet a requirement such as "editors save files from their desktop into the site" or
"our middleware speaks SOAP". Exponential ships a WebDAV server (`index_webdav.php`) and a SOAP 1.1 server (`index_soap.php`).
Both were called `webdav.php` and `soap.php` before 6.0.15; those names only forward now.
Both are **off by default**, both have known gaps, and both are worth considering only when the other side cannot use
something newer. Their status against the standards is in
[API and protocol standards](../../standards/apis-and-protocols.md#webdav-rfc-4918).

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../examples/web-servers/nginx.md), [LiteSpeed](../../examples/web-servers/litespeed.md),
> [Caddy](../../examples/web-servers/caddy.md), [IIS](../../examples/web-servers/iis.md), [Velocity](../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../hosting/panels/plesk.md), [cPanel](../hosting/panels/cpanel.md), [DirectAdmin](../hosting/panels/directadmin.md), [ISPConfig](../hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md).

## Contents

1. [WebDAV](#webdav)
2. [SOAP](#soap)
3. [Calling another system's SOAP service](#calling-another-systems-soap-service)
4. [When to use which](#when-to-use-which)

## WebDAV

> **Important: the entry script was renamed in 6.0.15**
> The WebDAV server is `index_webdav.php` (it was `webdav.php` until 6.0.14). `webdav.php` only forwards to it,
> writes a warning once a day, and will be removed in a later major version. Change your virtual host (HTTP **and**
> HTTPS) and rewrite rules, then test them: [The WebDAV entry script is now index_webdav.php](../../bc/6.0/webdav-entry-script-index-webdav.md).

The entry point is `index_webdav.php` at the installation root. It runs the Zeta Components WebDAV server
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
> Your web server must send WebDAV requests to `index_webdav.php`: on the main host a rule such as
> `RewriteRule ^index_webdav\.php - [L]` (shipped commented out in `.htaccess_root`), or a separate virtual host such as
> `webdav.example.com` that sends every request to it. The rules for Apache, Plesk, nginx and IIS, and a test with
> `curl -X PROPFIND` (expect `207 Multi-Status`), are on
> [the change page](../../bc/6.0/webdav-entry-script-index-webdav.md). Velocity does not serve WebDAV; use Apache or
> nginx with PHP-FPM for it. Test with the client your customer will really use before promising it; desktop clients
> differ in how they handle locks.

### Permissions (since 6.0.15)

What a WebDAV user may see and change is decided as on the web, in two steps:

| Request | Who may | Answer when refused |
|---|---|---|
| The root `/` (the list of siteaccesses) | everybody, without signing in | - |
| Anything below `/<siteaccess>/`, reading too | a signed-in user with `user/login` for that siteaccess (the policy's SiteAccess limitation) | `401` without or with wrong credentials, `403` without `user/login` there |
| Reading a folder or file | `content/read` on the node | `404`: a node the user may not read is not listed and not shown |
| PUT, MKCOL | `content/create` (a new file or folder) or `content/edit` (a replaced file) below the folder | `403`; nothing is stored |
| DELETE | `content/remove` | `403`; nothing is removed |
| COPY, MOVE | the rights of the same copy or move in the administration (`content/create` at the destination) | `403` (inside a `207` multistatus answer, as WebDAV clients expect it); nothing is copied or moved. COPY or MOVE between two siteaccesses is always refused |

Until 6.0.15 every read was granted before the policy check, and a refused PUT, MKCOL, DELETE, COPY or MOVE was
answered with `201` or `204`: the client showed a success and the change was lost. Check that WebDAV users have
`user/login` for the siteaccess they use (`/<siteaccess>/` in the WebDAV path), as they need for the web.

"Verify it's you" is never asked on WebDAV: it belongs to actions in the web interface. A second sign-in step is asked
only where the site opts WebDAV in (`Guard[]=webdav`), see
[second sign-in step on other sign-in paths](../../features/6.0/second-factor-on-other-sign-in-paths.md); an account
without its `ezuser_setting` row cannot sign in.

## SOAP

The entry point is `index_soap.php` at the installation root; until 6.0.15 it was `soap.php`, which now only forwards
to it and writes a notice to `var/log/warning.log` once a day (see
[the SOAP entry script is now index_soap.php](../../bc/6.0/soap-entry-script-index-soap.md)). The library is `lib/ezsoap/classes/` (`eZSOAPServer`, `eZSOAPClient`, envelope,
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

For each listed extension, `index_soap.php` includes `extension/<extension>/soap/initialize.php`, where the extension
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

When the request carries HTTP basic credentials, `index_soap.php` signs that user in before the call
(`eZUser::loginUser()`), so the function runs with the user's policies. Without credentials it runs as anonymous.
A second sign-in step never applies to SOAP: an account that needs one signs in with an app password
([the second step on the other sign-in paths](../../features/6.0/second-factor-on-other-sign-in-paths.md)).

### Letting it through the web server

The shipped rewrite rules send every unknown address to `index.php`, so with SOAP on the web server must let
`index_soap.php` through: uncomment `RewriteRule ^index_soap\.php - [L]` in `.htaccess` (from `.htaccess_root`), add
`RewriteRule ^/index_soap\.php - [L]` to the HTTPS virtual host, or a `location = /index_soap.php` in nginx. Restrict it
to the addresses of the systems that call it. Then check it:

```bash
curl -sS -i 'https://www.example.com/index_soap.php?wsdl'
```

The answer is `200` with "Error: this web page does only understand POST methods" (the server has no WSDL); a `404`
or a page of the site means the rule is missing. The lines for Apache, Plesk, nginx, the other servers, a `POST` test
with a SOAP envelope and the log check for clients still calling `soap.php` are on
[the SOAP entry script is now index_soap.php](../../bc/6.0/soap-entry-script-index-soap.md).

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
- [The SOAP entry script is now index_soap.php](../../bc/6.0/soap-entry-script-index-soap.md)
- [Syndication between installations](syndication-between-installations.md)
- [Integration surfaces at a glance](integration-surfaces-at-a-glance.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
