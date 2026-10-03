# uwebserver

*Applies to: Exponential Velocity 0.0.4.42 and later. History: [August 2026](../../history/velocity/2026-08.md) (first C benchmark server), [25 to 30 September](../../history/velocity/2026-09e.md) (the rebuild).*

## What it is

`uwebserver` is a small web server written in C that ships with the Velocity engine. It is **not** the application server (that is `sbin/qbixserver.php` and the phar, which run PHP). It answers HTTP/1.1 and HTTPS from one event loop per process with no PHP at all. It exists for two jobs: a baseline to measure the PHP server against, and a plain static file server for tests and small jobs.

**Nothing in a site starts it.** The engine, its packages, the container image, the release workflows and the Exponential installation never run it, so upgrading changes nothing for a site: only people and tests start it.

## Why use it

A static file server you can start in one line, that is safe by default: it listens on `127.0.0.1:8000` only, refuses to start as root without `--user`, never starts on an option it does not understand, and has had four security reviews, a fuzzed request parser and sanitiser-clean tests (release 0.0.4.42).

## Build and run

It needs a C compiler and OpenSSL 3 (`libssl-dev` or `openssl-devel`):

```bash
make -C native/uwebserver                         # builds sbin/uwebserver (hardened, -Werror)
sbin/uwebserver --root=./web                      # http://127.0.0.1:8000/
sbin/uwebserver --help                            # every option, grouped
man docs/uwebserver.1                             # the manual page
make -C native/uwebserver install PREFIX=/usr/local
make -C native/uwebserver sanitize                # AddressSanitizer + UndefinedBehaviorSanitizer build for tests
make -C native/uwebserver fuzz                    # the request-parsing fuzzer
```

`sbin/uwebserver` as committed is built with GCC 11.5 on EL 9, x86-64, linked against the system's OpenSSL 3; on another system, build it again as above. `bin/uwebserver` is a forwarder at the former path that executes `sbin/uwebserver`.

## The command line

GNU style: `--name=VALUE`, `--name VALUE`, the one-dash spellings (`-root=/srv/www`), bundled short options (`-p8081`), `--no-` forms, and `--` to end options. The whole command line is read first; an unknown option, a missing or malformed value, contradictory options or a stray word is reported on standard error with `Try 'uwebserver --help' for more information.` and exit status 2, and nothing starts. `--help` exits 0. `--check` (`--test-config`) checks everything a start needs and serves nothing; `--print-config` shows the effective configuration; `--config=FILE` (or `UWEBSERVER_CONFIG`) reads the same names from a file.

### Listening

| Option | Default | |
|---|---|---|
| `-l`, `--listen=[ADDR:]PORT` | none | HTTP there; repeatable |
| `-b`, `--bind=ADDR` | `127.0.0.1` | Address of `--port` and `--tls-port` |
| `-p`, `--port=PORT` | `8000` | HTTP on ADDR:PORT |
| `--tls-listen=[ADDR:]PORT` | none | HTTPS there; needs `--cert` and `--key` |
| `--tls-port=PORT` | `8443` | HTTPS on ADDR:PORT |
| `--reuse-port` | off | Let other servers share the ports (`SO_REUSEPORT`); without it a second start fails with "Address already in use" |
| `--backlog=N` | `511` | Connections waiting to be accepted |

`ADDR` is a numeric IPv4 address, an IPv6 address in brackets, `*` for every IPv4 address, or `localhost`. Host names are never resolved. Every interface is one explicit `--listen='*:PORT'` away.

### Content (`--root=DIR`)

| Option | Default | |
|---|---|---|
| `-r`, `--root=DIR` | none | The document root |
| `--index=NAMES` | `index.html` | Files that answer for a directory |
| `--directory-listing` | off | List a directory with no index file (otherwise `403`) |
| `--hidden-files` | off | Serve names starting with a dot (otherwise `404`) |
| `--symlinks=inside\|never` | `inside` | Follow links that stay inside the root, or none |
| `--mime-types=FILE` | none | More types in `mime.types` format |
| `--cache-control=VALUE` | none | `Cache-Control` of files |
| `--etag` | on | `ETag`, `If-None-Match`, `If-Range` |
| `--gzip-static` | off | Serve `FILE.gz` to clients that accept gzip |
| `-H`, `--header='NAME: VALUE'` | none | Added to every response; repeatable |

GET and HEAD, `Last-Modified` and `ETag` with conditional requests (`304`), one byte range (`206`, or `416`), a redirect to a directory's slash, `X-Content-Type-Options: nosniff` on every file and error. A path cannot contain `..`, an encoded `/` or NUL (`400`); dot names are `404`; files are opened with `openat2(2)` and `RESOLVE_BENEATH`, so the kernel refuses a path, link or race that leads out of the root (links inside the tree must be relative). Without `--root`, it gives the built-in answers of the benchmark.

### Limits, TLS, logging, privileges

| Option | Default | |
|---|---|---|
| `-w`, `--workers=N` | `1` (at most 256) | Worker processes; one that ends is replaced |
| `--max-connections=N` | `1024` | Per worker |
| `--max-requests=N` | `1000` | Then `Connection: close` (0: no limit) |
| `--max-header-size=SIZE` | `8k` | `431` |
| `--max-uri-length=SIZE` | `4k` | `414` |
| `--max-body-size=SIZE` | `1m` | `413` |
| `--header-timeout=SECONDS` | `10` | The whole head must arrive |
| `--read-timeout`, `--write-timeout` | `30`, `30` | Without progress |
| `--keepalive-timeout=SECONDS` | `5` | Idle between requests |
| `--tls-handshake-timeout=SECONDS` | `10` | A TLS handshake |
| `--cert`, `--key`, `--chain` | none | PEM files; the key is refused when another user may change it |
| `--tls-min-version=1.2\|1.3` | `1.2` | Oldest version accepted |

TLS 1.2 offers only forward-secret AEAD ciphers; no session tickets, no compression, no renegotiation. Other options: `--access-log` (common, combined or json), `--error-log`, `--quiet`, `--verbose`, `--daemon`, `--pid-file` (locked), `--user`, `--group`, `--chroot`, `--allow-root`. **As root it serves only with `--user` (dropped after the ports are open) or `--allow-root`.**

## Upgrading from 0.0.4.41 or earlier

- It listens on `127.0.0.1:8000` by default, not on every interface at 8080. To keep the old address: `--listen='*:8080'`. `--port=N` alone now binds `127.0.0.1:N`.
- As root it needs `--user` or `--allow-root`.
- An unknown option starts nothing (exit 2). `--cert` without `--key` is an error, not plain HTTP.
- The `Server` header is `uwebserver` (no version); the start-up line goes to standard error. `/health` keeps its keys.
- Removed: the generated record types and the U runtime that were compiled in but never called, and the unused headers `u_runtime.h`, `u_merkle_cache.h`.

## Limits

- HTTP/1.1 only (ALPN `http/1.1`); no PHP.
- Linux with OpenSSL 3; `openat2` containment needs kernel 5.6 or later (before it, every component is opened with `O_NOFOLLOW` and no link is followed).
