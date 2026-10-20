# Specification: security defaults of September 2026

Between 22 and 30 September 2026 a set of defaults changed so that a **default installation** is safe without
extra work. This page lists each default, the setting that controls it, what changed for an existing
installation and how to check it. Read it before you upgrade an installation with its own `.htaccess`, a caching
proxy, or scripts that read the session cookie; the [upgrade checklist](#upgrade-checklist) at the end is the
short version. Input handling of individual datatypes is in
[Datatype and input hardening](datatype-input-hardening.md).

## Settings at a glance

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/site.ini` | `HTTPHeaderSettings` | `SecurityHeaders[<header>]` | see [section 1](#1-security-headers-on-every-page) | installation or siteaccess |
| `settings/site.ini` | `Session` | `CookieSecure` | `auto` | installation or siteaccess |
| `settings/site.ini` | `Session` | `CookieHttponly` | `true` | installation or siteaccess |
| `settings/site.ini` | `Session` | `CookieSameSite` | `Lax` | installation or siteaccess |
| `settings/httpcache.ini` | `HttpCacheSettings` | `TagHeader` | `disabled` (replaces `ProxyHeaders`) | installation |
| `settings/velocity.ini` | `ServerSettings` | `ResponseHeaders[]`, `ResponseHeadersOnScripts` | `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy` with the values of [section 1](#1-security-headers-on-every-page); `enabled` | Velocity |
| `settings/velocity.ini` | `HTTPSSettings` | `HSTSMaxAge`, `HSTSIncludeSubDomains`, `HSTSPreload` | `300` (`0` writes nothing), `disabled`, `disabled` | Velocity |
| `settings/velocity.ini` | `CacheSettings` | `SkipCookies[]` | empty (derive the list) | Velocity |
| `settings/velocity.ini` | `ServerSettings` | `User`, `Group`, `AllowRootWorkers` | `AllowRootWorkers=disabled` | Velocity |

The three `[Session]` keys are commented out in the shipped `settings/site.ini`; the defaults above apply while
they are unset.

## 1. Security headers on every page

Before 27 September a default installation answered every page, the
administration included, with no `X-Content-Type-Options`, no framing
restriction, no `Referrer-Policy` and no `Permissions-Policy`, on Apache and on
Velocity alike. The administration could be framed by any site (clickjacking on
the publish and delete buttons) and uploaded files could be sniffed into another
type by the browser.

`settings/site.ini` `[HTTPHeaderSettings]`:

| Key | Default |
|---|---|
| `SecurityHeaders[X-Content-Type-Options]` | `nosniff` |
| `SecurityHeaders[X-Frame-Options]` | `SAMEORIGIN` |
| `SecurityHeaders[Content-Security-Policy]` | `frame-ancestors 'self'` |
| `SecurityHeaders[Referrer-Policy]` | `strict-origin-when-cross-origin` |
| `SecurityHeaders[Permissions-Policy]` | `camera=(), microphone=(), payment=(), usb=()` |
| `SecurityHeaders[Strict-Transport-Security]` | empty (off; only ever sent over HTTPS) |

- An empty value drops a header. `HeaderList` overrides per path still win.
  Names and values that could split the response are ignored.
- To allow a trusted editor host to frame the admin, override the policy:
  `SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self' https://edit.example.com`.
- Turn HSTS on only when the whole site is HTTPS-only:
  `SecurityHeaders[Strict-Transport-Security]=max-age=31536000`.
- **Pages answered from the HTTP cache carry the same headers.** The cache stores
  the configured headers with each page; the key carries the scheme so a header
  meant for HTTPS never reaches an HTTP answer.
- **Velocity** sends the same headers on static files, its own error pages,
  redirects and 304 answers (`[ServerSettings] ResponseHeaders[]`) and on the
  answers of the three front controllers (`ResponseHeadersOnScripts=enabled`,
  only where the script sent none), plus `Strict-Transport-Security` on every
  HTTPS answer (`[HTTPSSettings] HSTSMaxAge=300`, `HSTSIncludeSubDomains`,
  `HSTSPreload`; `HSTSMaxAge=0` writes nothing). Needs the engine release that
  reads `Q.webserver.headers`, `headersOnScripts` and `hsts`; an older one ignores
  them.

Check:

```bash
curl -sI https://example.com/ | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security'
```

## 2. The session cookie

The session cookie is the login, yet it went out without `HttpOnly`, `SameSite` or
`Secure` unless the site had set them, which a default installation never did. A
script injected into any page could read the admin session; a forged cross-site
form carried it along; over HTTPS it was still offered to plain HTTP.

`settings/site.ini` `[Session]`:

| Key | Default | Meaning |
|---|---|---|
| `CookieSecure` | `auto` | `auto` marks the cookie `Secure` whenever the request itself came over HTTPS; `true` and `false` still force it. |
| `CookieHttponly` | `true` | Scripts cannot read the cookie. |
| `CookieSameSite` | `Lax` | `Lax`, `Strict`, `None` (honoured only together with `Secure`) or empty to send no attribute. |

`setCookieParams()` uses the array form of `session_set_cookie_params()`;
Velocity's session layer already honours it, so both servers send the same
cookie. Explicit settings still win. If an integration needs cross-site cookies,
set `CookieSameSite=None` and `CookieSecure=true`.

## 3. Login and account recovery

| Behaviour | Before | Now |
|---|---|---|
| Failed login timing | Unknown user name returned at once; a known one ran the password hash (about a quarter second with the default hash), so three requests per name told which accounts exist. | When no account matches, `eZUser` computes a hash of the submitted password with the configured hash type, costing the same as the check it stands in for. |
| Forgot password | "There is no registered user with that email address" (also on the admin login form) allowed testing addresses one by one. | A valid but unknown address gets the same page as a known one ("a mail has been sent if an account is registered"); only input that is not an address at all is refused. Templates wash every address they print. |
| Forgot-password link key (`ezmbpaex` 6.0.3) | | Carries a key that cannot be guessed. |
| Non-string password or hash | Cast to string; `true` could match a stored `true`. | Never authenticates. |
| First administrator password of a kickstart | Guessable default, or the site data's own password when no e-mail was given. | Random 20 characters, printed once and written to `var/log/initial-admin-password`. See [Installing in one command](../../features/6.0/install-in-one-command.md). |

## 4. Files the web server serves

`.htaccess_root` and `.htaccess_root_static` (and Velocity, which uses the same
list for every engine) changed so that Apache and Velocity serve rule for rule the
same files:

- A `.php`, `.phtml` or `.phar` file below the document root answers 404, so only
  the front controllers at the root run. Before, any `.php` in the package store
  (56 in the demo packages), in an extension or under `var/` was executed when its
  path was asked for.
- Only the preview images (png, jpg, gif, webp) of imported packages are served
  from `var/storage/packages`. Before, package XML, SQL, templates and settings
  were readable. SVG is left out on purpose because it can carry script.
- A dot file or dot directory (`.git`, `.env`, `.htaccess`) answers 404 wherever it
  lies; `.well-known` stays reachable.
- The site's own references are served: the service worker `/index.js` (and `/sw.js`, its former name, kept for browsers registered before the rename of 30 September), the `vendor`
  and `media` folders of an extension's design (the layout editor's scripts and
  fonts) and the image files among the stored originals (so SVG images are
  shown). Other originals stay behind `content/download`.
- Fonts of an extension's design are no longer refused by the shipped rules.
- Velocity serves from `var/` only image files below `storage/original/image`
  (png, jpeg, gif, webp, svg) and only the previews from the package store.
  Before, every file below `var/*/storage/original/image` and the whole package
  store were handed out.

Upgrade note: if you maintain your own copy of `.htaccess`, merge these rules.
The shipped example is `doc/examples/` and the root files `.htaccess_root` and
`.htaccess_root_static`.

## 5. Caching and personal pages

- A page that carries a form token (`csrf-token` meta tag, hidden field in every
  POST form) is sent `Cache-Control: private, no-cache, must-revalidate`. Before,
  `no-cache` only asked for revalidation, and a shared cache that did not know the
  session cookie could store the signed-in page with its token and hand it to
  every anonymous visitor.
- Velocity tells its response cache which cookies mean "personal": the session
  cookie as the handler names it (`session.name` for the `default` handler,
  `[Session] SessionNamePrefix` as a prefix for `custom`) plus `is_logged_in`. An
  explicit list replaces the derived one: `SkipCookies[]` in `velocity.ini`
  `[CacheSettings]` (the former `[ServerSettings] CacheSkipCookies` still works;
  a value in `[CacheSettings]` wins; default empty = derive the list).
  Before, an installation on the default handler (`PHPSESSID`) got a skip list of
  `eZSESSID` and never matched its own cookie, so with the response cache on, a
  signed-in request could be answered from the cache.
- **Purge tags are no longer public.** Every response of a cached siteaccess used
  to carry the page's purge tags twice (`xkey` and `Surrogate-Key`), naming its
  nodes, objects and layouts. `httpcache.ini [HttpCacheSettings] TagHeader`
  replaces `ProxyHeaders`; the default `disabled` sends none. Set it to the one
  header your purging proxy reads (`xkey` for Varnish, `Surrogate-Key` for Fastly,
  `Cache-Tag` for Cloudflare). `ProxyHeaders=enabled` in an existing override
  still means `xkey`. A name that is not an HTTP token or is a framing header is
  refused. See [HTTP cache](../../bc/6.0/httpcache.md).

## 6. Other fixes with a security effect

| Fix | Detail |
|---|---|
| Phar stream wrapper | The bootstrap unregisters the `phar` stream wrapper, guarded by `PHP_SAPI !== 'cli'`. A persistent-worker server runs public traffic under the CLI SAPI, so the protection was missing exactly where it is needed (measured: still registered on the worker server, unregistered under FPM). The guard now asks whether the request is a web request. |
| Package view and viewfile | A package description is escaped; a package file opened directly gets a sandboxing Content-Security-Policy. |
| Package datatype | Cannot read a package outside the repository or write an INI file outside a configured siteaccess. |
| INI writer | Values and names cannot inject lines or write outside the settings directory. |
| Mail headers, download file names | See [Datatype and input hardening](datatype-input-hardening.md). |
| REST | An unauthenticated request answers 401 with `WWW-Authenticate` on every server (the missing-credentials case used to state `HTTP/1.1 500`); the OAuth `Authorization` header is read case-insensitively with a `HTTP_AUTHORIZATION` fallback; an unknown token answers 401 on SQLite sites and on persistent workers. |
| Node sorting | `content/action` requires edit permission and a known sort field and order. |
| Form token refusals | A 403 page with no token in the log. See [form expired page](../../features/6.0/form-expired-page.md). |
| Velocity workers | `[ServerSettings] User`, `Group`, `AllowRootWorkers=disabled`: workers drop root after they start, so files they write belong to the site user. |
| Anonymous forms | No longer subscribe the shared anonymous account. |
| Outgoing identity | The static cache generator, link checker, RSS import, exchange rate and package downloads, outgoing mail and the REST realm identify themselves as Exponential. A web server rule that matches the former static cache user agent must be changed. |

## Upgrade checklist

1. Compare your `.htaccess` with `.htaccess_root` and merge the new rules.
2. Look at your pages' response headers (command above) and decide whether you
   need to relax `Content-Security-Policy` for a trusted editor host.
3. If a proxy in front of the site purged by `xkey` or `Surrogate-Key`, set
   `[HttpCacheSettings] TagHeader` in `settings/override/httpcache.ini.append.php`.
4. If a script of yours depended on a non-HttpOnly or cross-site session cookie,
   set `[Session] CookieHttponly` or `CookieSameSite` explicitly.
5. Change the static cache user agent in any web server rule that matched the old
   name.
6. Clear caches after the changes: `php bin/php/ezcache.php --clear-all --allow-root-user`.

## Related pages

- Specifications: [Datatype and input hardening](datatype-input-hardening.md), [Security hardening of August 2026](security-hardening-2026-08.md) (the earlier patch set), [Security hardening 6.0.13](security-hardening-6.0.13.md), [Velocity HTTP/2 and security](velocity-http2-and-security.md)
- Upgrade notes: [Hardening guide](../../bc/6.0/hardening.md), [HTTP cache](../../bc/6.0/httpcache.md), [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md), [RAD tools — security](../../bc/6.0/rad-security.md)
- Features: [Velocity](../../features/6.0/velocity-persistent-worker-server.md), [Form expired page](../../features/6.0/form-expired-page.md), [Installing in one command](../../features/6.0/install-in-one-command.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md), chronicles [16 to 30 September 2026](../../history/2026/2026-09b.md) and [February 2026](../../history/2026/2026-02.md)
