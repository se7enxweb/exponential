# Velocity: running Exponential in a persistent-worker web server

This page is for administrators of an Exponential installation who want to run it under Velocity, and it is the
entry point to everything Velocity in Exponential. Exponential was built for the shape of mod_php and PHP-FPM: one
PHP process per request, torn down afterwards. **Velocity** (`./console exp:velocity`) runs the same installation
under a modern application server that boots once and keeps a pool of workers alive. The result is a site that
answers cached pages in well under a millisecond of CPU and renders uncached pages with a warm database connection,
warm file caches and a warm opcode cache.

Below: what arrived between 22 and 30 September 2026, how to start it, which settings matter first, and where the
detailed guides are.

## Why you would use it

| You want | Velocity gives you |
|---|---|
| A fast site without installing Apache or Nginx | A bundled server (the Qbix engine), FrankenPHP or PHP's built-in server, all started with the same commands. |
| HTTPS from the first start | A self-signed certificate is made for you; certificates you own are used when named. |
| HTTP/2 | Built into the bundled server (`[ServerSettings] HTTP2=enabled`). |
| Whole pages answered before the kernel starts | The role-aware HTTP cache and the server's response cache. |
| Fewer database queries per page | The SQL query cache (`settings/querycache.ini`). |
| One command to deploy PHP changes | `./console exp:velocity deploy`. |

Velocity is the recommended engine for every stage (development, alpha, beta,
demo, stable, production). FrankenPHP is the production-ready alternative, and
PHP's built-in server is for development only. The shipped default in
`settings/velocity.ini` is `Engine=php`, because it needs nothing installed;
change it to `qbix` for any real site.

## Start, check, stop

```bash
./console exp:velocity start             # default engine, shows status straight away
./console exp:velocity status            # every engine: role, state, URL, stop command
./console exp:velocity status --all      # complete block for each engine, stopped ones too
./console exp:velocity restart
./console exp:velocity graceful          # reload workers one at a time, port keeps answering
./console exp:velocity stop
./console exp:velocity --help
```

Useful switches (all documented by `--help`):

| Switch | Effect |
|---|---|
| `--engine=<php\|qbix\|frankenphp>` | Drive one engine for this command. |
| `--all` | Drive every engine. |
| `--https` / `--no-https` | HTTPS for one start (FrankenPHP serves HTTPS by default with a self-signed certificate). |
| `--json` | Machine-readable output where the verb supports it. |

GNU and BSD spellings work (`--keep-global V`, `-keep-global=V`, `-json`,
`--no-json`); everything after `--` is passed to the engine untouched.

`status` lists every address to open as a clickable URL, the HTTPS address
beside each plain one, and the backend pages (administration login, Setup >
System information, Setup > Caches). Under each engine it shows the process,
the version, the configuration, the log paths, and whether APCu is on and for
which caches.

### Engines side by side

Each engine has its own port, pid file and logs, so all three can run at once
for tests.

| Engine | Default port | HTTPS port | Section in `velocity.ini` |
|---|---|---|---|
| php (built-in server) | 8087 | none | `[PHPServerSettings]` |
| qbix (bundled server) | 8088 | `[ServerSettings] HTTPSPort` (8080) | `[ServerSettings]` |
| frankenphp | 8089 | 8444 | `[FrankenPHPSettings]` |

Everything Velocity writes for a server lives together under
`var/vc/<engine>/`: `etc/`, `lib/`, `log/` and `run/` for the Qbix engine;
`bin/`, `caddy/`, `tls/`, `log/` and `run/` for FrankenPHP. See
[FrankenPHP and PHP's built-in server](../../bc/6.0/frankenphp.md) and
[Velocity engines](../../bc/6.0/velocity-engines.md).

## Settings to know first

All keys below are in `settings/velocity.ini`. Scope is the whole installation
(Velocity reads the file when it starts; `exp:velocity` drops a stale cached copy
before it builds the server configuration, so a change is read at the next
`start`, `restart` or `graceful`).

| Block | Key | Default | What it does |
|---|---|---|---|
| `[ServerSettings]` | `Engine` | `php` | Engine the commands drive when no `--engine` is given. |
| `[ServerSettings]` | `Workers` | `4` | Worker processes of the bundled server. |
| `[ServerSettings]` | `Instances` | `1` | Number of servers sharing the same ports. Each answers about 3,000 to 3,500 cached pages a second on one core, so `Instances=N` lets cached pages use N cores. |
| `[ServerSettings]` | `ForkPerRequest` | `disabled` | `enabled` forks a fresh worker for every request. Since 30 September workers are kept between requests, which cut signed-in admin pages from 256 to 284 ms to 73 to 80 ms in the measurement that led to the change. |
| `[ServerSettings]` | `User`, `Group`, `AllowRootWorkers` | empty, empty, `disabled` | Workers give up root after they start and run as this user (the site's user when left empty), so files they write in `var/` belong to the site, not root. |
| `[ServerSettings]` | `StatTtl` | `0` | Seconds a worker may remember file stat results across requests. `1` measured 13 percent less CPU per rendered page. |
| `[ServerSettings]` | `Zygote` | empty | Whether workers started after the pool are forked from the zygote (holds no visitor connection) or from the server. Empty keeps the engine default. |
| `[ServerSettings]` | `EnginePhar` | `disabled` | Run the engine out of `dist/engine.phar`. See [Engine archive](../../bc/6.0/phar.md). It does not make pages faster. |
| `[ServerSettings]` | `StaticMaxAge` | `31536000` | Seconds a browser may keep a static file without asking again. |
| `[ServerSettings]` | `HTTP2` | `enabled` | HTTP/2 for HTTPS connections. |
| `[ServerSettings]` | `ResponseHeaders[]` | four security headers | Headers added to static files, error pages, redirects and 304 answers. |
| `[ServerSettings]` | `ResponseHeadersOnScripts` | `enabled` | Same headers on answers of `index.php`, `index_rest.php` and `index_treemenu.php`, only where the script did not send them. |
| `[HTTPSSettings]` | `HSTSMaxAge`, `HSTSIncludeSubDomains`, `HSTSPreload` | `300`, `disabled`, `disabled` | `Strict-Transport-Security` on every HTTPS answer, never on plain HTTP. `0` writes nothing. |
| `[CacheSettings]` | `Enabled` | `enabled` | The server's own response cache. `disabled` really switches it off (since 28 September; before, a disabled value fell back to the server default). |
| `[PHPSettings]` | `IniOptions[]` | opcache and APCu options | PHP options handed to the server process, one per line, as after `-d`. Shipped: `opcache.enable_cli=1`, `opcache.memory_consumption=256`, `opcache.max_accelerated_files=20000`, `opcache.interned_strings_buffer=32`, `opcache.revalidate_freq=0`, `opcache.file_update_protection=0`, `apc.shm_size=256M` (read the full list and the reasons in `settings/velocity.ini`). |

Two of those options were found by measuring, and matter if you tune your own
server. **`opcache.file_update_protection=0`**: a persistent worker's request
starts when the worker starts, so every file written afterwards (INI caches,
compiled templates, override caches after a deploy or a cache clear) counted as
"too new" for the worker's whole life and was compiled again on every include;
the opcode cache served 2.7 percent of includes where PHP-FPM serves 97 percent,
and a rendered page cost about four times PHP-FPM's CPU. **`apc.shm_size=256M`**:
the command-line default of 32 MB made the response cache and the SQL query cache
evict each other (measured on 27 September: 51 percent query-cache hit rate and
727 ms CPU for a rendered front page with 32 MB, 303 ms with 256 MB). The memory
is reserved, not used, until it fills. Check what your server runs with:
`grep -n 'IniOptions' settings/velocity.ini`.

The only change measured to make the site meaningfully faster in the first
round was an opcode cache for the server process (18 to 26 percent), which is why
`IniOptions[]` ships `opcache.enable_cli=1` and `apc.shm_size=256M`. Three other
ideas were measured and recorded as not worth repeating: loading the engine
from an archive, replacing regular expressions in the server's stream wrapper
with string functions, and the tracing JIT. When you measure, alternate requests
between the servers you compare instead of timing them one after the other.

Change and read settings with the command instead of editing by hand:

```bash
./console exp:velocity config list ServerSettings
./console exp:velocity config get ServerSettings Workers
./console exp:velocity config set ServerSettings Workers 8
./console exp:velocity config unset ServerSettings Workers
./console exp:velocity config paths      # which of the three look-alike files is the real one
```

## The caches Velocity can use

Velocity works best with the caches stacked in front of the renderer.

1. **Browser cache** for anonymous visitors: `StaticMaxAge` and the page
   headers (see [HTTP caching for anonymous visitors](../../bc/6.0/http-caching.md)).
2. **Server response cache** (`[CacheSettings]`): whole anonymous pages, with
   `StaleWhileRevalidate=60` so a page that expires is rendered by one request
   while the others receive the stored copy, `NotFoundSeconds=60` so a missing
   page is remembered, and `MinifyHtml`.
3. **Role-aware HTTP cache** (`settings/httpcache.ini`, off by default):
   rendered pages kept per permission context, answered before the kernel
   starts, for signed-in visitors too. See [HTTP cache](../../bc/6.0/httpcache.md).
4. **SQL query cache** (`settings/querycache.ini`, `Mode=off` by default): see
   [SQL query cache](../../bc/6.0/sql-query-cache.md).
5. **Cache warmer** (`exp:warm`): walks the published content as an anonymous
   visitor so nobody pays for the first render after an expiry.

APCu is the shared memory the HTTP cache and the SQL query cache use. The
bundled server runs under command-line PHP, where APCu is off unless asked for,
so Velocity asks for it whenever any of the three caches needs it (before 28
September switching the response cache off silently took APCu from the other
two). `status` says which caches get APCu and why.

Clear every cache after a template or stylesheet change:

```bash
./console exp:velocity cache clear
```

For the Setup > Caches buttons from the command line see
[Cache console](../../bc/6.0/cache-console.md).

## Deploy a PHP change in one command

A PHP change needs the autoloads, the INI and template caches, the engine archive, a PHP-FPM reload, a Velocity restart and then the caches that hold rendered output, in that order. One command does all of it and prints PASS, FAIL or SKIP for each step:

```bash
./console exp:velocity deploy --dry-run     # what it would do, nothing else
./console exp:velocity deploy               # the usual case
./console exp:velocity deploy --kernel      # a kernel class was added or renamed
```

The step table, the reasons for the order, every option and an example run are in [Velocity engines: deploying a PHP change](../../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy).

## Other commands

| Command | Purpose |
|---|---|
| `./console exp:velocity ssl show` / `ssl renew` | Show the certificates HTTPS uses (`--json` for monitoring); make a new self-signed one, which a running server swaps in without a restart. |
| `./console exp:velocity ext check` | Report which PHP extensions of the standard set are missing, with the install command for this OS. |
| `./console exp:velocity layout` / `layout migrate` | The Debian Apache-style configuration tree. See [Velocity on-disk layout](../../bc/6.0/velocity-ondisk-layout.md). |
| `./console exp:velocity site\|conf\|mod enable\|disable <name>` | Enable and disable like `a2ensite`. |
| `./console exp:webserver` | Engine-agnostic front end: configures engine, port and document root on first start. |
| `./console exp:frankenphp` | Wrapper pinned to the FrankenPHP engine. |
| `./console exp:phar build\|info\|clean` | The engine archive. |
| `./console exp:vc` | Intended short alias of `exp:velocity`. The console reads an `@alias <name>` tag from the docblock of `bin/php/<script>.php`; since the command code moved into `kernel/private/classes/commands/` the tags (`vc`, `fp` for `exp:frankenphp`, `search` for `exp:solr`) are there and `bin/php/velocity.php` has none, so on this tree `./console exp:vc` answers "Command exp:vc was not found" (checked 2 October 2026). Use `exp:velocity`. Check again with `./console exp:vc --help`. |

The Qbix engine's programs moved to `sbin/` and `bin/` in engine release
0.0.4.41; `exp:velocity` finds either layout. The engine package was renamed
from `se7enxweb/qbix-webserver` to `se7enxweb/exponential-velocity`; an older
checkout under the former directory name is still found.

## What had to change in the application

A persistent worker keeps what a request leaves behind. The same release
therefore fixed, among others: function statics that carried one siteaccess's
asset paths into the next request (now class statics the worker resets), event
listeners registered twice, SQL profile counters that never reset, REST
requests that died with "Cannot redeclare", the warm-up leaving the public
site's language, hidden-node setting and template locations in every worker,
and the phar stream wrapper staying registered for public requests (an
upload-to-execution path under command-line PHP; closed). The system
information page now names the engine, its release, its caches and APCu, and
every log line carries the request's siteaccess and full address.

## Limits and cautions

- The response cache stores only anonymous pages. A page with a form token is
  marked `private` so a shared cache never stores it.
- A class added to an INI file while workers run is not known to workers started
  before it existed. After such a change run `exp:velocity deploy`.
- Do not mix the old and new engine paths in one start.

## Related pages

- Guides: [Velocity engines](../../bc/6.0/velocity-engines.md), [HTTP/2 and cache warming](../../bc/6.0/http2-and-cache-warming.md), [response cache and navigation cache](../../bc/6.0/response-cache-and-navigation.md), [HTTP cache](../../bc/6.0/httpcache.md), [SQL query cache](../../bc/6.0/sql-query-cache.md)
- Features: [Velocity web server](velocity-web-server.md), [Velocity response cache](velocity-response-cache.md), [opcode cache and profile](velocity-opcode-cache-and-profile.md), [server control commands](web-server-and-solr-commands.md), [maintenance mode](maintenance-mode.md) (Velocity pauses its caches while it is on)
- Specifications: [Velocity worker pool](../../specifications/6.0/velocity-worker-pool.md), [Velocity engine settings](../../specifications/6.0/velocity-engine-settings.md), [security defaults of September 2026](../../specifications/6.0/security-defaults-2026-09.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- Changelogs: [6.0.15](../../changelogs/6.0/6.0.15.md), [Exponential Velocity engine](../../changelogs/extensions/exponential-velocity.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
