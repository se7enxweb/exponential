---
title: API and protocol standards
audience: developer, admin
category: standards
order: 120
summary: Which API and network protocol standards Exponential implements (OAuth 2.0, OpenID Connect, REST over HTTP, JSON, WebDAV, SOAP, RSS, Atom, sitemaps, SMTP, LDAP, webhooks), with evidence, status and gaps.
keywords: API, protocol, OAuth, OAuth 2.0, RFC 6749, RFC 6750, OpenID Connect, OIDC, PKCE, REST, HTTP, RFC 9110, status codes, JSON, RFC 8259, WebDAV, RFC 4918, SOAP, XML-RPC, RSS, RSS 2.0, Atom, RFC 4287, OPML, sitemaps, sitemaps.org, SMTP, RFC 5321, STARTTLS, RFC 8058, LDAP, RFC 4511, webhooks, HMAC, OpenAPI, MCP, Model Context Protocol, ezprestapi, API keys
type: reference
---
# API and protocol standards

This page is for developers who connect other systems to Exponential, and for administrators who must say which
protocols an installation speaks and how far each one goes. For every API and network protocol in the 6.0.x line it
says what the standard is, what Exponential implements, where the code, settings and tests are, and what is missing.

> **Note** A protocol listed here is not necessarily switched on. Several (SOAP, LDAP, the audit webhook) are off
> until an administrator configures them; the section of each protocol names the setting.

## Summary

| Standard | Version | Status | Evidence (short) |
|---|---|---|---|
| OAuth 2.0, authorization server for the REST API | RFC 6749 (2012) | **Partly complies** | `kernel/private/modules/oauth/`, `kernel/private/rest/classes/oauth/`, token endpoint `POST /api/auth/v1/oauth/token`; `state` and PKCE (RFC 7636, S256) since 6.0.15; no `token_type` |
| OAuth 2.0 Bearer tokens | RFC 6750 (2012) | **Partly complies** | `Authorization: Bearer` and `OAuth` accepted (`ezpOauthUtility`); token also accepted as a query parameter |
| OAuth 2.0 / OpenID Connect, client for social login | RFC 6749, RFC 7636, OIDC Core 1.0 | **Partly complies** | `sevenx_authentication_2fa` extension: authorization code flow with `state` and PKCE S256; ID tokens not validated |
| REST over HTTP | HTTP semantics, RFC 9110 | **Partly complies** | `ezprestapi` routes `/api/ezp/v1`, `/api/ezp/v2`; status classes in `kernel/private/rest/classes/` |
| JSON | RFC 8259 | **Complies** | `ezpRestJsonView` (`application/json`, UTF-8), `ezjscore` call interface |
| OpenAPI description | 3.x | **Not applicable** (kernel) | no OpenAPI document for the kernel's REST API; Velocity publishes one for its own handlers |
| WebDAV | RFC 4918 | **Partly complies** | `webdav.php`, `kernel/private/classes/webdav/` on `zetacomponents/webdav`; lock ownership not implemented |
| SOAP | 1.1 (W3C Note) | **Partly complies** | `lib/ezsoap/`, `soap.php`, `settings/soap.ini [GeneralSettings] EnableSOAP=false`; no WSDL |
| XML-RPC | 1999 specification | **Not applicable** | not implemented |
| RSS export and import | RSS 1.0, RSS 2.0 (export); 0.91 to 2.0 (import) | **Complies** | `kernel/classes/ezrssexport.php`, `ezrssimport.php` |
| Atom | RFC 4287 | **Partly complies** | export only (`rss_version` `ATOM`); no Atom import |
| Sitemaps | sitemaps.org 0.9 | **Complies** | `bcgooglesitemaps` extension writes `http://www.sitemaps.org/schemas/sitemap/0.9` |
| SMTP mail transport | RFC 5321 | **Complies** | `lib/ezutils/classes/ezsmtptransport.php` on `zetacomponents/mail`; STARTTLS available |
| One-click unsubscribe | RFC 8058 | **Complies** | `kernel/classes/mailpreferences/expmailgate.php` adds `List-Unsubscribe` and `List-Unsubscribe-Post` |
| LDAP authentication | RFC 4511 (LDAPv3) | **Partly complies** | `kernel/classes/datatypes/ezuser/ezldapuser.php`; default `LDAPVersion=2`; no StartTLS |
| Webhooks (outgoing, signed) | no formal standard | **Complies** with its own documented contract | `kernel/classes/audit/sinks/expauditwebhooksink.php`, HMAC-SHA-256 signature |
| Model Context Protocol | 2025 specification | **Not applicable** (kernel) | the kernel repository ships no MCP server |

## OAuth 2.0 (RFC 6749) and Bearer tokens (RFC 6750)

### What it is

The OAuth 2.0 Authorization Framework (IETF RFC 6749, October 2012) lets an application act for a user without
knowing the user's password. RFC 6750 defines how an access token is sent as a Bearer token. RFC 7636 (PKCE) and the
OAuth 2.0 Security Best Current Practice (RFC 9700, 2025) harden the flows.

### How Exponential complies

Exponential is an **authorization server** for its REST API:

- Clients are registered at Setup > oAuth admin (`/oauthadmin/list`, `kernel/private/modules/oauthadmin/`). Each gets
  a client identifier, a secret and a redirect address.
- The authorization endpoint is the view `oauth/authorize` (`kernel/private/modules/oauth/authorize.php`). The user
  signs in and needs the policy `oauth/authorize` ([upgrade note](../bc/6.0/oauth-authorize-policy.md)). Unknown
  clients and a `redirect_uri` that is not the client's end point get `400 Bad Request` with no redirect (RFC 6749,
  section 4.1.2.1), tested in `tests/tests/kernel/private/oauth/ezpOauthAuthorizeRedirectTest.php`.
- The token endpoint is `POST /api/auth/v1/oauth/token` (`kernel/private/rest/classes/controllers/oauth_token.php`).
  It accepts the grants `authorization_code` and `refresh_token` only (`validateGrantType()`), checks the client
  secret (`ezpRestClient::validateSecret()`) and answers `access_token`, `refresh_token` and `expires_in`.
- Tokens are 40 hexadecimal characters from `random_bytes()` (`ezpRestToken::generateToken()`); their lifetime is
  `settings/rest.ini [OAuthSettings] TokenTTL=3600`, that of a refresh token `RefreshTokenTTL=2592000` (30 days,
  `0` = no expiry; `ezpOauthUtility::refreshTokenExpired()`).
- A code or refresh token is redeemed only by the client it was issued to (`ezpOauthUtility::clientMatches()`), and
  only while its user is enabled and not locked by failed logins (`ezpOauthUtility::userMayUseToken()`); the same
  check runs for every resource request in `ezpOauthFilter`.
- A resource request carries the token as `Authorization: Bearer <token>` (RFC 6750), as `Authorization: OAuth <token>`
  (an older draft), or as the `oauth_token` parameter of a POST form body (RFC 6750 section 2.2,
  `AllowTokenInBody=enabled`); the `oauth_token` query parameter only with `AllowTokenInQuery=enabled`, off by
  default (RFC 6750 section 2.3; `ezpOauthUtility::tokenSources()` in `kernel/private/rest/classes/oauth/utility.php`).
- `rest.ini [Authentication] RequireAuthentication=enabled` and
  `AuthenticationStyle=ezpRestOauthAuthenticationStyle` are the defaults.
- Personal API keys (`Authorization: Bearer expk_...`) are a second credential on the same header, not an OAuth flow;
  see [Personal API keys](../guides/api-keys.md) and `kernel/private/rest/classes/auth/apikey_auth_filter.php`.

The client side guide is [REST API v1 and v2](../guides/rest-api-v1-and-v2.md), section 3.

### Status

**Partly complies.** The authorization code grant with refresh tokens works as RFC 6749 describes; several required
or recommended parts are missing.

### Known gaps

- Since 6.0.15 `state` is returned unchanged with the code, the token and the error, and PKCE (RFC 7636) is
  supported: `S256`, `plain` only when `AllowPlainPkce` is enabled, required for public clients (stored without a
  secret) by default ([PKCE and state](../bc/6.0/oauth-pkce.md)). These are no longer gaps.
- CORS for the REST interface exists since 6.0.15 and is off by default (`rest.ini [CorsSettings]`,
  [CORS for the REST API](../bc/6.0/rest-cors.md)).
- `response_type=token` (the implicit grant, discouraged by RFC 9700) is still accepted, and it returns the tokens in
  the query string instead of the URL fragment that RFC 6749 section 4.2.2 requires.
- The token response has no `token_type` (required by RFC 6749 section 5.1); `Cache-Control: no-store` is not set
  by the controller. **Unverified** whether a layer below adds it.
- No `client_credentials` or `password` grant, no token introspection (RFC 7662), no revocation endpoint (RFC 7009).
- The token in a query parameter, which RFC 6750 allows only as a last resort, is refused by default since 6.0.15
  (`AllowTokenInQuery=disabled`, [OAuth token hardening](../bc/6.0/oauth-token-hardening.md)). This is no longer a
  gap unless a site enables it.

## OpenID Connect and OAuth 2.0 as a client (social login)

### What it is

OpenID Connect Core 1.0 (OpenID Foundation, 2014) is an identity layer on OAuth 2.0: an ID token (a signed JWT) and a
userinfo endpoint tell the client who signed in.

### How Exponential complies

The kernel has no OpenID Connect provider and no OpenID Connect client. The `sevenx_authentication_2fa` extension,
which the root `composer.json` requires (`~1.0.12`), adds **social login** as an OAuth 2.0 client:

- Base class `eZOAuthUser` (`extension/sevenx_authentication_2fa/classes/ezoauthuser.php`): the authorization code
  grant; a random `state` kept in the session, bound to the provider and accepted once within 600 seconds
  (`STATE_TTL`); PKCE with `code_challenge_method=S256` where configured; an address the provider marks as
  not verified (`email_verified` false) is not trusted.
- Provider handlers in `login_handler/`: Google, Facebook, Meta, Instagram, Twitter and ID.me. The ID.me handler
  (`ezidmeuser.php`) is described as "OAuth 2.0 / OpenID Connect", sends a `nonce` and reads the OIDC `/userinfo`
  response.
- User documentation: [Two-factor login](../guides/two-factor-login.md), "Social login".

### Status

**Partly complies.** The OAuth 2.0 client side follows current practice (state, PKCE). OpenID Connect is used only for
its userinfo endpoint.

### Known gaps

- ID tokens are not requested or validated (no JWT signature check, no JWKS); identity comes from the userinfo call.
- No OpenID Connect discovery (`/.well-known/openid-configuration`); endpoints are configured per provider.
- Exponential cannot act as an OpenID Connect provider for other applications.

## REST over HTTP

### What it is

REST is an architectural style, not a standard. What can be checked is the use of HTTP itself: HTTP Semantics (IETF
RFC 9110, June 2022) defines methods and status codes.

### How Exponential complies

- The REST framework is in the kernel (`kernel/private/rest/classes/`: router, dispatcher, auth styles, views) on
  `zetacomponents/mvc-tools`. The content API is the `ezprestapi` extension (required as `~1.2.6`), routes
  `/api/ezp/v1/...` and `/api/ezp/v2/...`; reads answer at both versions, writes (create, update, delete) at v2 only.
  Every route checks the user's content policies (`expRestContentPermission`).
- Status codes: `ezpHttpResponseCodes` (`kernel/private/rest/classes/http_response_codes.php`) names 200, 400, 401,
  403, 404, 405 and 500; `kernel/private/rest/classes/status/response.php` also knows 201 and 204;
  `status/rate_limited.php` answers `429 Too Many Requests` with `Retry-After` for an API key over its limit
  (RFC 6585).
- `settings/rest.ini`: `[ApiProvider] ProviderClass[]`, `[Authentication]`, `[OAuthSettings]`, `[ApiKeySettings]`,
  `[CacheSettings]`.
- Tests: `tests/tests/kernel/private/rest/` (request parser, routes), `tests/tests/kernel/classes/apikey/`.

A second HTTP API is the `ezjscore` call interface `/ezjscore/call/<class>::<method>`, which the `expservices`
extension fills with named services ([Remote services and apps](../guides/remote-services-and-apps.md)).

### Status

**Partly complies.**

### Known gaps

- `GET /content/node/<id>/listAtom` answers 500 at v1 and v2 ([REST API v1 and v2](../guides/rest-api-v1-and-v2.md),
  "Known issue: listAtom").
- Which status code each write route returns (for example 201 on create) is **Unverified** from code; the guide shows
  the JSON body only.
- No conditional requests (`ETag`, `If-Match`) on writes were found. **Unverified.**

## JSON (RFC 8259)

### What it is

The JavaScript Object Notation (JSON) Data Interchange Format, IETF RFC 8259 (December 2017, also ECMA-404).

### How Exponential complies

- REST answers are written by `ezpRestJsonView` (`kernel/private/rest/classes/views/json.php`) with the content type
  `application/json` and charset UTF-8, through `ezcMvcJsonViewHandler`.
- The `ezjscore` call interface sends `Content-Type: application/json; charset=utf-8`
  (`extension/ezjscore/classes/runnable/views/ezjscore/call.php`).
- JSON is produced by PHP's `json_encode()`, which writes UTF-8 JSON text as RFC 8259 requires.

### Status

**Complies.**

### Known gaps

- None found. RFC 8259 defines no `charset` parameter for `application/json`; sending one is harmless.

## OpenAPI and JSON:API

The kernel's REST API has no OpenAPI description and does not follow JSON:API; neither standard applies to it.
Velocity, the separately packaged web server, publishes `/.well-known/openapi.json` for its own handlers
([Velocity discovery, federation and deploy](../features/6.0/velocity-discovery-federation-and-deploy.md)); that
document does not describe the content API. **Not applicable** to the kernel.

## WebDAV (RFC 4918)

### What it is

HTTP Extensions for Web Distributed Authoring and Versioning (WebDAV), IETF RFC 4918 (June 2007): methods such as
PROPFIND, PROPPATCH, MKCOL, COPY, MOVE, LOCK and UNLOCK. Class 1 is the basic set, class 2 adds locking.

### How Exponential complies

- Entry point `webdav.php` at the installation root; it uses the Zeta Components WebDAV server
  (`zetacomponents/webdav ~1.2` in `composer.json`) with the content backend
  `kernel/private/classes/webdav/ezwebdavcontentbackend.php` and the authentication backend
  `ezwebdavcontentbackendauth.php` (basic authentication against Exponential users).
- `settings/webdav.ini` (`[GeneralSettings]`, `[FolderSettings]`).
- The lock plugin is registered (`ezcWebdavLockPluginConfiguration` in `webdav.php`) and the backend declares
  `ezcWebdavLockBackend`.
- Request and response recordings for COPY, DELETE, GET, HEAD, MKCOL, MOVE, OPTIONS, PROPFIND, PROPPATCH and PUT are in
  `tests/tests/kernel/private/webdav/regression/`.
- History: [the WebDAV engine of 4.1](../features/4.1/webdav_readme.txt) and the design notes in
  [doc/eng-us/specifications/trunk/webdav](../specifications/trunk/webdav/readme.txt).

### Status

**Partly complies.** Class 1 methods are implemented; locking (class 2) is incomplete.

### Known gaps

- The lock authorizer's methods `assignLock()`, `ownsLock()` and `releaseLock()` in
  `ezwebdavcontentbackendauth.php` are empty (`@todo implement`); the backend's own comment says lock calls are still
  missing in `setProperty` and `removeProperty`. Whether LOCK and UNLOCK work with real clients is **Unverified**.
- The regression recordings have no `*Test.php` runner, so no PHPUnit suite in `phpunit.xml` runs them and CI does
  not test WebDAV.

## SOAP 1.1

### What it is

SOAP 1.1 is a W3C Note (May 2000) for XML messages over HTTP; SOAP 1.2 is a W3C Recommendation (2003, second edition
2007). WSDL describes SOAP services.

### How Exponential complies

- Library `lib/ezsoap/classes/` (client, server, envelope, codec, fault). `eZSOAPEnvelope` uses the SOAP 1.1
  namespaces `http://schemas.xmlsoap.org/soap/envelope/` and `.../soap/encoding/`.
- Server entry point `soap.php`; off by default: `settings/soap.ini [GeneralSettings] EnableSOAP=false`. Functions are
  added by extensions (`[ExtensionSettings] SOAPExtensions[]`). The `syndication` extension, required by the root
  `composer.json`, is a SOAP client and server between installations.
- The client verifies TLS certificates by default: `tests/tests/lib/ezsoap/eZSOAPClientTlsOptionsTest.php`
  (`testTheCertificateIsVerifiedByDefault`); further tests in `tests/tests/lib/ezsoap/` (suite `lib`).

### Status

**Partly complies.** SOAP 1.1 messages only.

### Known gaps

- No SOAP 1.2 and no WSDL generation or reading (a search of `lib/ezsoap` and `kernel/` for "wsdl" finds nothing).
- `[GeneralSettings] Logging` is marked "not supported yet".

## XML-RPC

Not implemented. The only reference in the kernel is the request classifier, which recognizes `xmlrpc.php` probes
as unwanted traffic (`kernel/classes/requestclassifier/exprequestrulesetprobes.php`). **Not applicable.**

## RSS, Atom and OPML

### What it is

RSS 2.0 (RSS Advisory Board, 2009 edition) and RSS 1.0 (RDF Site Summary, 2000) are web feed formats. Atom is the IETF
Atom Syndication Format, RFC 4287 (December 2005). OPML 2.0 lists feeds.

### How Exponential complies

- **Export** (`kernel/classes/ezrssexport.php`, `rssXmlContent()`): RSS 1.0 (`rss1`), RSS 2.0 (`rss2`) and Atom
  (`atom`) through the Zeta Components Feed library (`zetacomponents/feed ~1.4.6`); Apple Podcasts feeds
  (`ITUNES`) and OPML are written by Exponential itself. Feeds are served at `/rss/feed/<address>`.
- **Import** (`kernel/classes/ezrssimport.php`, cronjob part `kernel/private/classes/cronjobs/rssimport.php`): RSS
  0.91, 0.92, 1.0 and 2.0, read with external entities refused and the network switched off (`LIBXML_NONET`).
- Guides: [RSS feeds](../guides/rss-feeds.md), [RSS: Apple Podcasts feeds, a paged feed list and safer
  exports](../features/6.0/rss-podcast-and-feed-list.md), [OPML exports](../bc/6.0/opml.md).
- Tests: `tests/tests/kernel/classes/eZRSSExportTest.php`, `eZRSSExportOPMLItemTest.php`.

### Status

- RSS: **Complies.**
- Atom: **Partly complies** (export only).

### Known gaps

- Atom feeds cannot be imported: the import switches on the `version` attribute of an RSS root element only.
- No feed validator runs in CI. Validity of generated feeds is **Unverified** beyond the tests above.

## Sitemaps (sitemaps.org 0.9)

### What it is

The Sitemaps protocol 0.9 (sitemaps.org, supported by the major search engines) lists a site's URLs in XML.

### How Exponential complies

The `bcgooglesitemaps` extension (required as `~1.1.6.6`, version 1.1.6.6) generates sitemaps by cronjob
(`classes/runnable/cronjobs/generate.php`, `generatemultilingual.php`) with the namespace
`http://www.sitemaps.org/schemas/sitemap/0.9` and the elements `loc`, `lastmod`, `changefreq` and `priority`, and
serves them through its `sitemap` module.

### Status

**Complies.**

### Known gaps

- The 50,000 URL / 50 MB limit per file and sitemap index files were not checked for this page. **Unverified.**

## SMTP (RFC 5321) and mail

### What it is

The Simple Mail Transfer Protocol, IETF RFC 5321 (October 2008). STARTTLS for SMTP is RFC 3207. RFC 8058 (January
2017) defines one-click unsubscribe with `List-Unsubscribe-Post`.

### How Exponential complies

- `settings/site.ini [MailSettings] Transport` chooses `sendmail` (default), `smtp` or `file`
  (`TransportAlias[smtp]=eZSMTPTransport`). SMTP settings: `TransportServer`, `TransportPort=25`,
  `TransportConnectionType` (empty, `ssl`, `tls` and older values), `TransportUser`, `TransportPassword`.
- `eZSMTPTransport` (`lib/ezutils/classes/ezsmtptransport.php`) sends through `ezcMailSmtpTransport` from
  `zetacomponents/mail ~1.10.1`. In that library `tls` means STARTTLS: it requires the server to announce `STARTTLS`
  (`vendor/zetacomponents/mail/src/transports/smtp/smtp_transport.php`).
- RFC 8058: mail preferences add `List-Unsubscribe` and `List-Unsubscribe-Post` per recipient
  (`kernel/classes/mailpreferences/expmailgate.php`, `expmailtoken.php`); see
  [Mail preferences compliance](../specifications/6.0/mail-preferences-compliance.md).
- Tests: `tests/tests/lib/ezutils/eZMailTest.php`, `eZMailTransportSendTest.php`, `eZMailEzcTest.php`,
  `tests/tests/kernel/classes/mailpreferences/MailPreferencesComplianceTest.php`.

### Status

**Complies** (SMTP and RFC 8058).

### Known gaps

- The comment in `site.ini` still offers `sslv2` and `sslv3` as connection types, which are obsolete and insecure;
  whether current PHP accepts them at all is **Unverified**.
- The `site.ini` comment cites RFC 2821, which RFC 5321 replaced.
- DKIM signing is not done by Exponential; it is left to the mail server.

## LDAP (RFC 4511)

### What it is

The Lightweight Directory Access Protocol version 3, IETF RFC 4511 (June 2006); filter strings are RFC 4515.
LDAPv2 was declared historic in RFC 3494 (2003).

### How Exponential complies

- Login handler `eZLDAPUser` (`kernel/classes/datatypes/ezuser/ezldapuser.php`), switched on with
  `settings/site.ini [UserSettings] LoginHandler[]=LDAP` (commented out by default). Settings in
  `settings/ldap.ini [LDAPSettings]`: `LDAPServer`, `LDAPPort=389`, `LDAPBaseDn`, `LDAPBindUser`,
  `LDAPLoginAttribute=uid`, `LDAPVersion`, `LDAPFollowReferrals`.
- It uses PHP's `ldap` extension and sets `LDAP_OPT_PROTOCOL_VERSION` from `LDAPVersion`.
- The login is escaped before it goes into a filter (`eZLDAPUser::ldap_escape()`: `*`, `(`, `)`, `\` and NUL as in
  RFC 4515; a separate set for DNs).
- Tests: `tests/tests/kernel/datatypes/ezuser/eZLDAPUserTest.php` (database group; skipped when the `ldap`
  extension is not loaded).

### Status

**Partly complies.**

### Known gaps

- The default is `LDAPVersion=2`; set it to 3 for any current directory server.
- No StartTLS: `ldap_start_tls()` is not called. Encryption depends on an `ldaps://` server address, which is
  **Unverified** with the separate `LDAPPort` setting.
- `LDAPDebugTrace=enabled` logs logins and passwords (warning in `ldap.ini`); keep it off in production.
- The test points at a fixed remote LDAP server, not a CI service; whether it runs in CI is **Unverified**.

## Webhooks

### What it is

There is no formal webhook standard. Common practice is an HTTPS POST of JSON, signed with an HMAC so the receiver can
check origin and freshness.

### How Exponential complies

The audit log can deliver its records to a webhook (`kernel/classes/audit/sinks/expauditwebhooksink.php`,
`settings/audit.ini [AuditSink_webhook]`, off until `URL` is set):

- Batches of records as JSON, `{"v":1,"installation":...,"events":[...]}`.
- Headers `X-Exponential-Timestamp`, `X-Exponential-Batch` and `X-Exponential-Signature: sha256=<hex>`, the
  HMAC-SHA-256 of `timestamp + "." + body` with `SigningSecret`; receivers reject timestamps more than 300 seconds
  away.
- Delivery through a spool with retries (`Retries`, `RetryBackoff`); at least once, de-duplicated by event id.
- Tests: `tests/tests/kernel/classes/audit/expAuditSinksTest.php`. Documentation: [audit](../bc/6.0/audit.md).

### Status

**Complies** with its own documented contract.

### Known gaps

- Only the audit log sends webhooks; content events (publish, remove) have no generic outgoing webhook.
- No incoming webhook endpoint.

## Model Context Protocol (MCP)

The Exponential 6 kernel repository ships no MCP server. The extension point survey (Setup > RAD tools,
`settings/rad.ini`) can count MCP tool registries that an extension declares, and Velocity publishes
`/.well-known/mcp.json` for its own handlers
([Velocity discovery, federation and deploy](../features/6.0/velocity-discovery-federation-and-deploy.md)); neither
was checked against the MCP specification for this page. **Not applicable** to the kernel.

## Related pages

- [REST API v1 and v2](../guides/rest-api-v1-and-v2.md), [Personal API keys](../guides/api-keys.md),
  [Remote services and apps](../guides/remote-services-and-apps.md)
- [OAuth authorize policy (upgrade note)](../bc/6.0/oauth-authorize-policy.md)
- [RSS feeds](../guides/rss-feeds.md), [OPML exports](../bc/6.0/opml.md)
- [Two-factor login](../guides/two-factor-login.md)
- [Audit](../bc/6.0/audit.md), [Mail preferences compliance](../specifications/6.0/mail-preferences-compliance.md)
- [Documentation and publishing standards](documentation-and-publishing.md),
  [Databases and SQL](databases-and-sql.md)
