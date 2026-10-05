# Redirect query, ezjscore errors and image alias guards (6.0.15)

## Module redirects keep the request query only on the own host

Since the fix of pull request 105, `ezpKernelWeb::redirect()` appends the query string of the current request to the
redirect target through `ezpKernelWeb::appendRequestQuery()`.

| Redirect target | Query of the request |
|---|---|
| relative (`/content/view/full/2`) | kept |
| absolute on the own host (`https://alpha.se7enx.com/a`, also with a port such as `:8080`) | kept |
| another host, also another host-matched siteaccess (`https://edit.alpha.se7enx.com/...`) | dropped |
| protocol-relative (`//host/a`) or another scheme (`ftp:`, `mailto:`) | dropped unless it names the own host |

The own host is the host of the request (`eZSys::hostname()`, which honours the first `X-Forwarded-Host` behind a proxy,
as everywhere else in the kernel) plus the host of `SiteSettings` `SiteURL`. Hosts compare case-insensitively, without
port and trailing dot, and internationalised names by their punycode form. A target with a query gets the request query
after an `&`; a `#fragment` stays last.

Why: a payment window URL is sealed over its exact query, so a tracking parameter (`_gl`, `gclid`) of the shop request
appended to it invalidates the seal, and a query must not travel to a foreign host.

What changed for existing code: a redirect to another host used to receive the request query, now it does not. A module
that needs a parameter on a foreign target puts it into the target it hands to `redirectTo()`. A redirect between two
hosts of the same installation (for example the public and the admin siteaccess host) no longer carries the query; add
the parameters to the target, or list the host as own host by redirecting through a relative URL.

## ezjscore/call

A PHP `Error` (`TypeError`, a call on null) in a server function is logged with class, message, file and line and
answered as `error_text` "Internal error in the server function"; the response stays valid JSON or XML. An
`Exception` still answers its own message. The remote services (`expServiceBase::invoke()`) catch their errors
themselves and keep answering `{ "ok": false, "error": { "code", "message" } }` inside `content`.

## Image aliases

`eZImageAliasHandler::imageAlias()` returns null for an attribute whose original has no file, without asking the image
manager. The lazy write-back of generated aliases compares the stored row with the in-memory XML first
(`eZImageAliasHandler::storedXMLSupersedesDOMTree()`: serial number and directory) and skips the write when the row was
published or replaced meanwhile; the aliases regenerate from the fresh XML on the next render. A skipped write-back
logs an error line naming the attribute.
