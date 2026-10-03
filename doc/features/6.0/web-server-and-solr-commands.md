# Server control commands: exp:webserver, exp:frankenphp and exp:solr

Three console commands added on 25 and 26 September 2026 control the servers an
Exponential installation runs beside PHP: the web server (any of the three
Velocity engines) and an optional Solr search server. They share one controller
with [Velocity](velocity-persistent-worker-server.md), so every verb behaves the
same whichever name you type.

Run them as `./console exp:<name> <verb>`. As `root` add `--allow-root-user`.
`./console exp:<name> --help --allow-root-user` prints the full help from which
this page is taken.

## exp:webserver: one command for whichever engine you run

`exp:webserver` is the engine-agnostic front end. Use it when you do not care
whether the engine is PHP's built-in server (development), FrankenPHP
(production) or the bundled Qbix server.

| Verb | What it does |
|---|---|
| `status` (default) | What each engine is doing. |
| `start` | Configure on first use, then run the engine. |
| `stop`, `restart` | Ask it to stop and wait; stop then start. |
| `graceful` | Re-exec without dropping the listening socket. |
| `kill` | Stop without asking, for a wedged worker. |
| `configure` | Resolve and save the engine, port, document root and HTTPS port into `settings/override/velocity.ini.append.php` without starting anything; `--dry-run` shows the plan only. |
| `command` | Print the command line it would run, and exit. |
| `config` | Read and write the settings it runs on. |
| `cache` | Cache clear: re-render every cached page on its next request. |
| `layout` | The configuration tree and every file the server uses. |
| `ctl` | The engine's own control with this installation's tree. |
| `install` | Put the engine's binary in place. |

`--engine=<name>` and `--all` reach the other engines for start, stop, restart,
graceful, kill and status. Each engine has its own port, pid file and logs, so
they can run side by side.

**Configure once, then run directly.** The first `start` with no saved engine
works out the effective engine, port, document root and HTTPS port and writes
them to the installation's override; every later start runs straight from it. To
change that without editing the INI file:

```bash
./console exp:webserver configure --engine=frankenphp --port=8089 --dry-run --allow-root-user   # plan only
./console exp:webserver configure --engine=frankenphp --port=8089 --allow-root-user             # persist, start nothing
./console exp:webserver start --reconfigure --engine=php --port=8087 --allow-root-user          # persist, then run
```

`configure` rewrites only those keys and leaves certificates and PHP ini options
untouched.

## exp:frankenphp: the same command pinned to FrankenPHP

`./console exp:frankenphp <verb>` is `exp:velocity <verb> --engine=frankenphp`.
Its intended alias `exp:fp` is declared in the command's docblock but is not resolved by the console on this tree (checked 2 October 2026: "Command exp:fp was not found"); type `exp:frankenphp`. Verbs: `status`, `start`, `stop`, `restart`, `graceful`, `kill`,
`command`, `config`, `cache`, `ctl` (`caddyfile`, `validate`, `adapt`, `version`,
`list-modules`) and `install` (download the pinned FrankenPHP release and verify
its SHA-256; `--force`, `--from=<file>`, `--check`, `--trust-github-digest`).

```bash
./console exp:frankenphp install --check --allow-root-user   # is the pinned binary in place?
./console exp:frankenphp status --allow-root-user
./console exp:frankenphp ctl validate --allow-root-user       # validate the generated Caddyfile (prints "Valid configuration")
```

Address and ports come from `[FrankenPHPSettings]` in `settings/velocity.ini`
(`Port=8089` for plain HTTP, `HTTPSPort=8444` for TLS, `HTTPS=enabled`). HTTPS
is on by default with a self-signed certificate unless `[HTTPSSettings]` names
one (`Certificate` and `Key`, both or neither); `--https` and `--no-https` force
it for a single start. If the certificate cannot be made (no `openssl`
extension, an unwritable directory) the default starts plain HTTP and says why;
`--https` or a named certificate that does not exist refuses the start.

Two fixes of the same days matter if you ran FrankenPHP earlier:

- **Signing in works.** FrankenPHP passes every `php_ini` line of the generated
  Caddyfile to PHP as INI text, where a semicolon starts a comment, so
  `session.save_path "0;0660;<dir>"` arrived as `0`, no session file was found
  and every visitor was anonymous. Values PHP's INI parser would cut are now
  quoted.
- **The built-in PHP engine starts beside HTTPS on another engine.** It speaks
  plain HTTP only but counted the shared `HTTPSPort` another engine serves (for
  example Qbix on 8080) as its own and refused an otherwise free start.

The reasons and the Caddyfile that is generated are in
[FrankenPHP and PHP's built-in server](../../bc/6.0/frankenphp.md).

## exp:solr: an optional search server

Solr is optional. Exponential searches with its built-in index
(`php bin/php/updatesearchindex.php`) until Solr is installed and switched on;
until then every verb says so and `start` exits non-zero, so nothing pretends a
search server is running. (A docblock alias `exp:search` exists but is not resolved by the console on this tree; use `exp:solr`.)

| Verb | What it does |
|---|---|
| `status` | Whether Solr is configured and, if so, whether it answers. |
| `start`, `stop`, `restart` | Run Solr's own start and stop for a local instance (`BinaryPath`); with a remote `Url` they report that it is managed elsewhere. |
| `ping` | Ask Solr whether it is alive. |

`settings/solr.ini` (override in `settings/override/solr.ini.append.php`),
block `[SolrSettings]`, scope: the installation:

| Key | Default | Meaning |
|---|---|---|
| `Enabled` | `false` | Solr takes over only when `true`. |
| `Url` | empty | A running Solr to talk to; with it set Solr is treated as remote and `start`/`stop` do nothing. |
| `BinaryPath` | empty | The Solr control binary, for a local instance (used when `Url` is empty). |
| `Host`, `Port` | `127.0.0.1`, `8983` | Where Solr listens. |
| `DataDir` | empty | Solr's data directory. |
| `RunDir`, `LogDir` | `var/vc/solr/run`, `var/vc/solr/log` | Pid and log files, kept in the Velocity layout. |
| `StartTimeout` | `60` | Seconds `start` waits for Solr to answer. |

```bash
./console exp:solr status --allow-root-user   # says how to enable it until [SolrSettings] Enabled=true
```

## Related pages

- [Velocity: running Exponential in a persistent-worker web server](velocity-persistent-worker-server.md)
- [FrankenPHP and PHP's built-in server](../../bc/6.0/frankenphp.md)
- [Velocity engines](../../bc/6.0/velocity-engines.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
