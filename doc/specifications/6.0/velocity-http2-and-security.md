# Velocity HTTP/2, request handling and security

*Reference. Applies to: Exponential Velocity 0.0.4.42. History: [22 September](../../history/velocity/2026-09b.md) (HTTP/2), [23 September](../../history/velocity/2026-09c.md) and [24 September](../../history/velocity/2026-09d.md) (security fixes), [25 to 30 September](../../history/velocity/2026-09e.md) (headers, body limits).*

This page records what the server accepts, what it refuses and why, so that an operator can reason about it and anyone who maintains a fork can carry the checks across. Report problems of this kind privately to the maintainers rather than opening a public issue.

## HTTP/2

HTTP/2 is negotiated (ALPN) on the TLS listener and answered from the same worker pool as HTTP/1.1. It was built in five waves on 22 September 2026: HPACK (verified against the specification's own test vectors), frames and the connection state machine, negotiation on the real listener, a page instead of a blank window for URLs the server does not serve, and body compression with flow control. Every browser negotiates it; `curl --http1.1` shows what HTTP/1.1 clients see.

What it guarantees:

- **The same rules as HTTP/1.1.** The blocked-path list, the extension allow-list, the symlink containment and the server's own `/Q/` views are applied on both protocols. (Until 0.0.4.25, HTTP/2 served files that HTTP/1.1 refused, including a site configuration file and `/.git/config`, because the lists were consulted on one path only. The HTTP/2 route now sits behind every refusal of the HTTP/1.1 path, and `tests/unit-http2-route-order.php` asserts the order.)
- **Resource bounds.** Every resource a peer can make the server hold is bounded (0.0.4.9): concurrent streams, header block size, decoded header size (HPACK amplification), buffered DATA, flow-control windows. A header block that lies about its own lengths is refused. Streams cancelled across many reloads are not mistaken for a rapid-reset attack; every `GOAWAY` logs why it was sent.
- **Frames the RFC says to refuse are refused**, checked in `handle()` before the type switch (RFC 9113):

| Section | Frame | Answer |
|---|---|---|
| 4.2 | larger than the advertised `SETTINGS_MAX_FRAME_SIZE` | `FRAME_SIZE_ERROR` |
| 6.1 | `DATA` on stream 0 | `PROTOCOL_ERROR` |
| 6.2 | `HEADERS` on stream 0 | `PROTOCOL_ERROR` |
| 5.1.1 | an even stream id from a client | `PROTOCOL_ERROR` |
| 6.5 | `SETTINGS` length not a multiple of 6; `ACK` with a payload | `FRAME_SIZE_ERROR` |
| 6.7 | `PING` that is not 8 octets | `FRAME_SIZE_ERROR` |
| 6.9 | `WINDOW_UPDATE` of 0 | `PROTOCOL_ERROR` |
| 6.4 | `RST_STREAM` that is not 4 octets | `FRAME_SIZE_ERROR` |
| 6.8 | `GOAWAY` shorter than 8 octets | `FRAME_SIZE_ERROR` |

Unknown frame types are still accepted and discarded (RFC 9113 section 4.1: that is how the protocol is extended).

- **Same body limit as HTTP/1.1.** `post_max_size` applies: a `content-length` over the limit is answered `413` on its own stream before any DATA is kept; a body without one is refused on the DATA frame that takes it over; the stream is reset with `NO_ERROR` so the client stops sending; other streams carry on (0.0.4.38).
- **Same behaviour for scripts.** Cookies a script sets, `SERVER_NAME` without the port, `HTTPS` and `REQUEST_SCHEME`, the compressed body, the response cache (HTTP/2 consults it, so pages are not rendered from scratch) and the dashboard's counters all apply on HTTP/2.

Tests: `php tests/unit-http2-protocol-errors.php`, `tests/unit-http2-route-order.php`, `tests/http2-request-body-limit.php` (run from the engine tree).

## HTTP/1.1 request handling

| Rule | Behaviour | Since |
|---|---|---|
| Both `Content-Length` and `Transfer-Encoding`, or two different `Content-Length` values | `400` (RFC 9112 section 6.1: refusing is what prevents request smuggling) | 0.0.4.25 |
| Bare LF line endings | refused (they also used to hold a connection slot until the read timeout) | 0.0.4.25 |
| Framing read from the head only | a body that contains the text `Content-Length` (an uploaded log) cannot change framing | 0.0.4.38 |
| `Content-Length` of any size | bodies of 0 to 47 and 58 to 255 bytes were refused as "not a number" before 0.0.4.27; fixed | 0.0.4.27 |
| Body over `post_max_size` | `413` with `Connection: close`; the rest is read and discarded until the client stops (`Q.webserver.timeout.linger` 5 s of silence, `lingerTotal` 30 s in all) so the client can read the `413` instead of a reset | 0.0.4.38 |
| `post_max_size = 0` | no limit, as in PHP | 0.0.4.38 |
| Chunked bodies | parsed chunk by chunk as they arrive: `413` on the chunk that passes the limit, complete exactly where the last chunk and trailers end, `400` for a malformed size | 0.0.4.38 |
| Head of a request | `Q.webserver.timeout.read` seconds from its first byte; a body may take as long as it keeps arriving | 0.0.4.38 |
| Last response before `keepAlive.max`, and any `5xx` | `Connection: close` (before, one request in about a thousand failed) | 0.0.4.29 |
| `header('WWW-Authenticate: ...')` | answers `401` unless the call names a code | 0.0.4.35 |
| Large binary uploads under the limit | the worker frame limit follows `post_max_size` (an upload of about 7.5 MB used to give `502`) | 0.0.4.38 |

## Nothing outside the document root is served or run

A request resolves to a file; if that file, after every symbolic link is followed, does not lie under the document root, it is refused with `403`. This applies to static files and, more importantly, to PHP scripts: a link named `*.php` once turned the ability to create one file into the ability to run code from anywhere the server user could read (fixed in 0.0.4.25 at all four dispatch points; closing three of four was not enough, because with a worker pool the dispatch reaches `handlePhp()` by a different road).

`Q.webserver.followSymlinks` (default `false`) restores the older behaviour for installations that deliberately serve through links that leave the root. From Exponential, see [Velocity engines](../../bc/6.0/velocity-engines.md), "Symbolic links out of the document root".

Which scripts run and which files are served as they are is also configurable (opt-in): `Q.webserver.scripts` (scripts that may run by name, for example `["/index.php"]`), `Q.webserver.frontControllers` (path patterns to scripts, for example `{"^/api/": "index_rest.php"}`) and `Q.web.static.paths` (patterns a file must match to be sent as it is). Anything else goes to the front controller, as an application's `.htaccess` would send it, so a bundled tool or a command-line script is not run by being asked for, and protected uploads stay behind the application's download view. The response cache starts a new generation when the lists change. The `exponential` preset brings Exponential's own lists when the configuration names none.

## Headers on every response

A static file never reaches the application, so behind this server alone it would carry none of the security headers a front end sends. Three settings fix that (0.0.4.35; nothing changes without them):

```json
{ "Q": { "webserver": {
    "headers": {
        "X-Content-Type-Options": "nosniff",
        "X-Frame-Options": "SAMEORIGIN",
        "Referrer-Policy": "strict-origin-when-cross-origin",
        "Permissions-Policy": "camera=(), microphone=(), payment=(), usb=()"
    },
    "headersOnScripts": true,
    "hsts": { "maxAge": 300 }
} } }
```

| Setting | Default | Meaning |
|---|---|---|
| `Q.webserver.headers` | `{}` | Name to value, added to every response the server builds itself: static files over HTTP/1.1 and HTTP/2 (the in-memory copy included), `304`s, image variants, its own error pages and redirects |
| `Q.webserver.headersOnScripts` | `false` | Also add them to a script's response, each only where the script did not send that header (an application's own `X-Frame-Options: DENY` stays `DENY`) |
| `Q.webserver.hsts` | none | `Strict-Transport-Security` on every response over TLS (script responses included), never over plain HTTP; a number is the max-age, an object takes `maxAge`, `includeSubDomains`, `preload`, a string is sent as it is; a domain's own HSTS record wins. Mind the max-age: a browser that has seen it refuses plain HTTP to the host name, on every port, for that long |

A header already present, in any letter case, is never replaced or sent twice. Names that are not HTTP tokens, values containing CR, LF or NUL, and the framing headers (`Content-Length`, `Content-Type`, `Connection`, `Transfer-Encoding`, `Date`, ...) are ignored, so a typo cannot write a header of its own into every response. Every response goes through one serialiser, so a header value can never write headers of its own (0.0.4.25; before it, the guard existed in one of seven places that wrote headers).

## The admin surface

- The control panel, `/Q/api/` and the cluster join were open to anyone who could reach the port until 0.0.4.27; they now follow the access rule in [Control panel](../../features/6.0/velocity-control-panel.md).
- Log injection through a request line, dashboard injection through the Host header, response-cache personalisation and HPACK decoded-size amplification were closed in the same audit.
- Host names are validated strictly before they reach a certificate client, a resolver or a log (a name ending in a newline or a hyphen used to pass).
- Panel passwords are bcrypt, rule-checked and lockout-guarded; the credential store must pass an ownership rule or the panel is locked.
- The response cache never serves a personalised page: it skips requests with the session cookie names in `skip.cookies` by prefix (the match once required the name followed immediately by `=` and missed Exponential's session cookie, which carries a digest after the name; fixed in 0.0.4.26).
- Workers give up root ([Worker pool](velocity-worker-pool.md)).
- The shell's commands get none of the server's sockets or environment ([Q shell](../../features/6.0/velocity-q-shell.md)).
- Cache files default to a non-world-readable directory (0.0.4.15), with configurable `fileMode` and `dirMode`.

## Tests that guard this

`tests/unit-malformed-requests.php` (21 cases over a raw socket, because `curl` will not send most), `tests/unit-http2-protocol-errors.php`, `tests/unit-http2-route-order.php`, `tests/unit-response-headers.php` (each kind of response over HTTP/1.1, HTTP/1.1 with TLS and HTTP/2), `tests/unit-request-body-limit.php` (22 of 39 cases fail against v0.0.4.37), `tests/http2-request-body-limit.php`.

## Notes for anyone maintaining a fork

Two of the fixes above were found by running the server rather than reading it: the symlink execution path (three of four dispatch points can be closed while the fourth still runs the file) and the framing faults (`curl` refuses to send the requests that expose them). The containment check is one function and four call sites, and its absence is invisible until somebody creates a link. Carry it across.

## See also

- Features: [Velocity web server](../../features/6.0/velocity-web-server.md), [Control panel](../../features/6.0/velocity-control-panel.md), [HTTPS and certificates](../../features/6.0/velocity-https-certificates.md), [Static files and images](../../features/6.0/velocity-static-files-and-images.md), [uwebserver](../../features/6.0/velocity-uwebserver.md) (its own hardening).
- Specifications: [Worker pool](velocity-worker-pool.md), [Engine settings](velocity-engine-settings.md).
- Upgrade: [Velocity engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md) (response headers, upload limits), [Velocity engines](../../bc/6.0/velocity-engines.md).
- History: [22 September](../../history/velocity/2026-09b.md), [23 September](../../history/velocity/2026-09c.md), [24 September](../../history/velocity/2026-09d.md), [25 to 30 September](../../history/velocity/2026-09e.md); [changelog](../../changelogs/extensions/exponential-velocity.md).
