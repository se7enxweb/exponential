---
title: Web platform standards
audience: developer, admin
category: standards
order: 50
summary: Exponential against the web platform standards it speaks: HTTP semantics, caching and HTTP/2, TLS, HTML, CSS, ECMAScript, URLs, media types, cookies, robots and sitemaps, RSS and Atom feeds, and Open Graph.
keywords: web standards, HTTP, RFC 9110, RFC 9111, RFC 9112, RFC 9113, HTTP/2, TLS, RFC 8446, HTML, WHATWG, doctype, CSS, custom properties, prefers-color-scheme, dark mode, ECMAScript, JavaScript, jQuery 4, URL, RFC 3986, MIME, media types, cookies, RFC 6265, SameSite, robots.txt, RFC 9309, sitemaps, RSS 2.0, Atom, RFC 4287, Open Graph, Velocity
type: reference
---
# Web platform standards

This page is for developers and administrators who want to know which web platform standards Exponential 6.0.x
follows, and how far. For each standard it names the version, the code or setting that implements it, a status and
the known gaps. Where Exponential runs behind another web server (Apache, nginx, FrankenPHP), the protocol layer is
that server's; the statements about HTTP/2 and TLS below apply to Exponential Velocity, the web server that ships
with Exponential.

## Summary

| Standard | Version | Status | Evidence (short) |
|---|---|---|---|
| HTTP Semantics | RFC 9110 (2022) | **Partly complies** | status codes, `Content-Type` with charset, `Content-Language`, range requests for downloads; legacy defaults in headers |
| HTTP/1.1 message syntax | RFC 9112 (2022) | **Complies** (Velocity) | Velocity refuses ambiguous framing (`Content-Length` with `Transfer-Encoding`) |
| HTTP Caching | RFC 9111 (2022) | **Partly complies** | role-aware page cache, `Cache-Control: private` for personal answers, `Last-Modified`/304 for feeds; deprecated `Pragma`/`Expires` defaults |
| HTTP/2 | RFC 9113 (2022), HPACK RFC 7541 | **Complies** (Velocity) | ALPN negotiation, frame and HPACK checks; see the Velocity specification |
| TLS | TLS 1.2 (RFC 5246) / 1.3 (RFC 8446) | **Partly complies** | Velocity manages certificates (ACME, own files); minimum version for the PHP server **Unverified** |
| HTML | WHATWG HTML Living Standard | **Partly complies** | `<!DOCTYPE html>` in every core pagelayout; no HTML validation in CI |
| CSS | W3C CSS snapshot 2024 (custom properties, media queries level 5) | **Complies** (admin4) | tokens as custom properties, `prefers-color-scheme`, `forced-colors`, `prefers-reduced-motion`; no inline CSS |
| ECMAScript | ECMA-262 (ES5.1 style, runs on current engines) | **Complies** | admin scripts in plain ES5-style JavaScript with jQuery 4.0.0; syntax checked by Node.js in CI |
| URI / URL | RFC 3986, WHATWG URL Standard | **Partly complies** | URL aliases transformed to safe characters; IRI mode optional |
| Media types | RFC 6838, IANA registry | **Partly complies** | `settings/mime.ini`, content sniffing for images, `nosniff`; Atom served as `application/xml` |
| HTTP cookies | RFC 6265 (+ `SameSite` from RFC 6265bis draft) | **Complies** | `HttpOnly`, `Secure`, `SameSite=Lax` for the session cookie |
| Robots exclusion | RFC 9309 (2022) | **Complies** | shipped `robots.txt` at the web root |
| Sitemaps | sitemaps.org protocol 0.9 | **Complies** (with `bcgooglesitemaps`) | `xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"` |
| RSS / Atom | RSS 2.0, RSS 1.0 (RDF), Atom 1.0 (RFC 4287) | **Partly complies** | `kernel/classes/ezrssexport.php` writes all three; media type for Atom not `application/atom+xml` |
| Open Graph | The Open Graph protocol (ogp.me) | **Partly complies** | `xrowmetadata` prints `og:image` and its properties; `og:title`, `og:type`, `og:url` left to the design |

## HTTP semantics (RFC 9110) and message syntax (RFC 9112)

**What it is.** RFC 9110 "HTTP Semantics" and RFC 9112 "HTTP/1.1" (IETF, June 2022) define methods, status codes,
header fields and the HTTP/1.1 wire format.

**How Exponential complies.**

- Every page is answered by `kernel/private/classes/ezpkernelweb.php` with `Content-Type: text/html; charset=...`
  and `Content-Language` from the siteaccess, plus the security headers of `[HTTPHeaderSettings] SecurityHeaders[]`.
- File downloads (`lib/ezfile/classes/ezfile.php`) answer `Range` requests with `206 Partial Content`,
  `Content-Range` and `Accept-Ranges: bytes`.
- Feeds answer conditional requests with `304 Not Modified` (`kernel/private/classes/views/rss/feed.php`).
- The one-click unsubscribe answers `200 text/plain`, a broken link `400` (see [Privacy standards](privacy.md)).
- **Velocity** refuses a request with both `Content-Length` and `Transfer-Encoding`, or two different
  `Content-Length` values, with `400` (RFC 9112 section 6.1, request smuggling), limits head size and body size
  (`413`) ([Velocity HTTP/2 and security](../specifications/6.0/velocity-http2-and-security.md)).

**Status.** **Partly complies** (Exponential), **Complies** (Velocity framing).

**Known gaps.**

- The default page headers still carry an `Expires` date in 1997, `Pragma: no-cache` and `Last-Modified` set to the
  time of the request, inherited from the code base of eZ Publish 4; `Last-Modified` therefore says nothing about
  the content. Override per path with `[HTTPHeaderSettings] CustomHeader=enabled` and `HeaderList[]`.
- The download of a file deliberately sends an empty `Last-Modified` (resume compatibility).

## HTTP caching (RFC 9111)

**What it is.** RFC 9111 "HTTP Caching" (IETF, June 2022) defines how browsers and shared caches store and reuse
responses (`Cache-Control`, validators, `Vary`).

**How Exponential complies.**

- The kernel's own role-aware page cache (`settings/httpcache.ini`, `kernel/private/classes/httpcache/`) is shared by
  Apache, PHP-FPM and Velocity. An answer that depends on one user is marked `Cache-Control: private`, so no shared
  cache keeps it; the same rule holds when the session cookie is sent with every response
  (`[Session] CookieAlwaysAddToHttpResponse`) ([HTTP cache](../bc/6.0/httpcache.md)).
- Purge tags for a cache in front (Varnish `xkey`, `Surrogate-Key`, `Cache-Tag`) with `[HttpCacheSettings]
  TagHeader` (off by default).
- Per-path `Cache-Control`, `Pragma` and `Expires` with `[HTTPHeaderSettings] HeaderList[]`.
- Feeds use `Last-Modified` / `If-Modified-Since`.

**Status.** **Partly complies.**

**Known gaps.** Pages carry `Cache-Control: no-cache, must-revalidate` by default and no `ETag`, so browsers
revalidate every page without a validator to compare; `Pragma` is deprecated by RFC 9111 and still sent; the feed
check compares the `If-Modified-Since` string exactly instead of as a date.

## HTTP/2 (RFC 9113)

**What it is.** RFC 9113 "HTTP/2" (IETF, June 2022), with HPACK header compression (RFC 7541).

**How Exponential complies.** Velocity negotiates HTTP/2 by ALPN on its TLS listener and answers it from the same
worker pool as HTTP/1.1. HPACK is checked against the specification's test vectors; frames are validated before
dispatch (`PROTOCOL_ERROR`, `FRAME_SIZE_ERROR`); unknown frame types are discarded (RFC 9113 section 4.1); limits on
streams, header block size and decoded header size guard against HPACK amplification
([Velocity HTTP/2 and security](../specifications/6.0/velocity-http2-and-security.md)).

**Status.** **Complies** for Velocity. With Apache, nginx or FrankenPHP, HTTP/2 is that server's.

**Known gaps.** No HTTP/3 (RFC 9114). No interoperability test suite (such as h2spec) is part of the repository's CI.

## TLS

**What it is.** Transport Layer Security 1.3 (RFC 8446, 2018) and 1.2 (RFC 5246); HTTP Strict Transport Security
(RFC 6797).

**How Exponential complies.**

- Velocity serves HTTPS with its own certificate management: own files, archives, PKCS#12, or Let's Encrypt and other
  ACME CAs, verified before use and renewed in the background; a self-signed certificate stands in until a real one
  is usable ([Velocity HTTPS and certificates](../features/6.0/velocity-https-certificates.md)).
- HSTS: `settings/velocity.ini [HTTPSSettings] HSTSMaxAge=300` (with `HSTSIncludeSubDomains`, `HSTSPreload`), and
  `site.ini [HTTPHeaderSettings] SecurityHeaders[Strict-Transport-Security]` (empty by default) for every engine.
- The session cookie is `Secure` whenever the request came over HTTPS (`[Session] CookieSecure=auto`).
- Velocity's static file server `uwebserver` accepts TLS 1.2 or 1.3 (`--tls-min-version`, default `1.2`) with
  forward-secret AEAD ciphers only ([uwebserver](../features/6.0/velocity-uwebserver.md)).

**Status.** **Partly complies.** The minimum TLS version and cipher list of the PHP application server of Velocity
are not documented in this repository: **Unverified**. With Apache or nginx, TLS configuration is the operator's.

## HTML (WHATWG Living Standard)

**What it is.** The HTML Living Standard maintained by the WHATWG, the one HTML specification since 2019.

**How Exponential complies.** Every core page layout starts with `<!DOCTYPE html>`: `design/standard`, `design/base`,
`design/plain`, `design/mysite`, `design/admin`, `design/admin3`, `design/admin4`, `design/admin4l`, and the portal
designs of `extension/expservices`. Pages carry `lang` and, for right-to-left languages, `dir="rtl"` on `<html>`
([Right-to-left languages](../features/6.0/right-to-left.md)); landmarks and form semantics are tested
([Accessibility standards](accessibility.md)).

**Status.** **Partly complies.**

**Known gaps.** No HTML validator (such as the Nu HTML Checker) runs in CI; the e-mail layout
`design/standard/templates/mail/layout.tpl` still uses an XHTML doctype (acceptable for mail clients, but not the
Living Standard); site designs from packages were not checked.

## CSS

**What it is.** The CSS specifications of the W3C CSS Working Group, summarized in the yearly CSS Snapshot; here in
particular Custom Properties for Cascading Variables, Media Queries Level 5 (`prefers-color-scheme`,
`prefers-reduced-motion`, `forced-colors`) and Selectors Level 4 (`:focus-visible`, `:has()`).

**How Exponential complies.**

- admin4's design tokens are CSS custom properties (`--a4-*` in `design/admin4/stylesheets/components.css`), each
  color token with a dark value under `html[data-a4-theme="dark"]`.
- Dark mode follows the system: "system" mode reads `prefers-color-scheme` (`design/admin4/javascript/a4_head.js`),
  tested by `tests/tests/design/AdminThemeSwitchTest.php`.
- `@media (forced-colors: active)` and `@media (prefers-reduced-motion: reduce)` rules in
  `design/admin4/stylesheets/admin4.css`.
- No `<style>` blocks or `style=""` attributes in admin templates; all CSS in stylesheet files, bundled by the
  ezjscore packer. Enforced by `tests/tests/design/AdminStylingRulesTest.php` (hard-coded colors and radii in module
  stylesheets also fail) and required by an enforced CSP `style-src 'self'`
  ([admin4 styling](../bc/6.0/admin4-styling.md)).

**Status.** **Complies** for admin4 and admin4l. Other designs: **Unverified**.

## ECMAScript

**What it is.** ECMA-262, the ECMAScript Language Specification published yearly by Ecma International (TC39).

**How Exponential complies.**

- The administration's scripts are plain browser JavaScript without a build step. In `design/admin4/javascript/`,
  15 of 16 files use only ES5-style syntax (`var`, function expressions); one uses arrow functions and block-scoped
  declarations (ES2015). They run on every current browser engine.
- jQuery 4.0.0 with jQuery Migrate 4.0.2 and jQuery UI 1.14.2 are served locally
  (`extension/ezjscore/design/standard/javascript/`, `ezjscore.ini LocalScripts[jquery]=jquery-4.0.0.min.js`); YUI was
  removed ([jQuery 4 and YUI removal](../features/6.0/jquery4-and-yui-removal.md)).
- CI checks the syntax of every changed `.js` file with `node --check` on Node.js 22 (`.github/workflows/quality.yml`).
- Scripts run under a CSP without `'unsafe-inline'` in admin4 and admin4l (see [Security standards](security.md)).

**Status.** **Complies.**

**Known gaps.** No linter or ECMAScript target is configured; the language level is a convention, not a check.
The TinyMCE 3 editor and older extension scripts are legacy code.

## URLs (RFC 3986 and the WHATWG URL Standard)

**What it is.** RFC 3986 "Uniform Resource Identifier (URI): Generic Syntax" (IETF, 2005), RFC 3987 for IRIs, and the
WHATWG URL Standard that browsers implement.

**How Exponential complies.** Node URLs are URL aliases built from the content name through the transformation
group of `settings/site.ini [URLTranslator] TransformationGroup=urlalias` with `WordSeparator=dash`, which turns
names into plain path segments; `urlalias_iri` keeps Unicode letters (IRI) and `urlalias_compat` gives the oldest
behavior. Redirect targets from outside the code are checked by `eZRedirectManager`
([Safe redirects](../features/6.0/safe-redirects.md)).

**Status.** **Partly complies.** The alias rules are documented in [URLs and aliases](../guides/urls-and-aliases.md);
no test checks generated URLs against RFC 3986 as such: **Unverified** beyond that.

## Media types (RFC 6838)

**What it is.** RFC 6838 defines media type registration; the IANA registry lists the types.

**How Exponential complies.**

- `settings/mime.ini` maps file suffixes to types (for example `[svg] Types[]=image/svg+xml`, `[webp]
  Types[]=image/webp`, `[mp4] Types[]=video/mp4`).
- Uploaded images are typed by content with PHP's fileinfo and checked against `settings/image.ini
  [ImageUploadSettings] AllowedMIMETypes[]` (JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP, SVG).
- `X-Content-Type-Options: nosniff` on every page.
- Feeds: RSS as `application/rss+xml`, OPML as `text/x-opml`.

**Status.** **Partly complies.**

**Known gaps.** Atom feeds are sent as `application/xml` instead of `application/atom+xml` (RFC 4287 section 7);
`settings/mime.ini` has no entries for `avif`, `json` or `woff2`.

## HTTP cookies (RFC 6265)

**What it is.** RFC 6265 "HTTP State Management Mechanism" (IETF, 2011). The `SameSite` attribute comes from its
revision, draft RFC 6265bis, which all major browsers implement.

**How Exponential complies.** The session cookie is set by `lib/ezsession/classes/ezsession.php` from
`settings/site.ini [Session]`: `CookieHttponly` (default true), `CookieSecure` (default `auto`), `CookieSameSite`
(default `Lax`; `None` only together with `Secure`), `CookiePath`, `CookieDomain`, `CookieTimeout`. The
`is_logged_in` cookie uses the same options (`eZSession::siteCookieOptions()`). The cookies Exponential sets are listed
in [Privacy standards](privacy.md).

**Status.** **Complies.**

## Robots exclusion (RFC 9309) and sitemaps

**What it is.** RFC 9309 "Robots Exclusion Protocol" (IETF, 2022); the Sitemaps XML protocol 0.9 (sitemaps.org),
supported by the major search engines.

**How Exponential complies.** A new installation ships a `robots.txt` at the web root that keeps crawlers out of
pages without value and has commented `Sitemap:` lines to fill in ([robots.txt](../features/6.0/robots-txt.md)). The
`bcgooglesitemaps` extension (installed by `composer.json`, activated by the site) writes sitemap files with the
namespace `http://www.sitemaps.org/schemas/sitemap/0.9`, also per language (`classes/runnable/cronjobs/generate.php`,
`generatemultilingual.php` in the extension).

**Status.** **Complies** (sitemaps with `bcgooglesitemaps` active).

## RSS and Atom feeds

**What it is.** RSS 2.0 (RSS Advisory Board specification), RSS 1.0 (RDF Site Summary) and the Atom Syndication
Format 1.0 (RFC 4287, IETF, 2005).

**How Exponential complies.** RSS exports (**Setup > RSS**) are written by `kernel/classes/ezrssexport.php` as
RSS 1.0, RSS 2.0, Atom 1.0, Apple Podcasts (RSS 2.0 with the iTunes namespace) or OPML 2.0. The Apple Podcasts output carries an
`atom:link rel="self"`. Tests: `tests/tests/kernel/classes/eZRSSExportTest.php`,
`tests/tests/kernel/classes/security/eZRSSSecurityTest.php`
([RSS, podcasts and the feed list](../features/6.0/rss-podcast-and-feed-list.md), [OPML exports](../bc/6.0/opml.md)).

**Status.** **Partly complies** (formats written; Atom media type as noted above).

## Open Graph

**What it is.** The Open Graph protocol (ogp.me), started by Facebook and used by social networks and messengers to
build link previews.

**How Exponential complies.** The `xrowmetadata` extension, active by default (`settings/site.ini [ExtensionSettings]
ActiveExtensions[]=xrowmetadata`), prints `og:image` with `og:image:secure_url`, `og:image:type`, `og:image:width`,
`og:image:height` and `og:image:alt`, and `twitter:image`, from a per-object image or the class default; its sample
page head prints the `description` meta tag.

**Status.** **Partly complies.** `og:title`, `og:type` and `og:url`, which the protocol requires on every page, are
not printed by the extension's templates; a site design must add them.

## Related pages

- [Accessibility standards](accessibility.md), [Security standards](security.md), [Privacy standards](privacy.md)
- [Velocity HTTP/2 and security](../specifications/6.0/velocity-http2-and-security.md)
- [HTTP cache](../bc/6.0/httpcache.md)
- [admin4 styling](../bc/6.0/admin4-styling.md)
