# Velocity on-disk layout (Debian Apache style)

Read this page if you run Exponential Velocity with the Qbix engine and want to know where its configuration lives,
how to enable or disable a site, conf snippet or module, and what happens to an existing installation when it is
moved to the new layout. The layout mirrors Debian's `/etc/apache2`, so an administrator who knows Apache finds
everything in the expected place.

## In short

| | |
|---|---|
| What changed | Velocity's configuration lives in `/etc/vc` (an overlay on the engine's `/etc/qbix`), split into `vc.conf`, `ports.conf`, `envvars` and `*-available` / `*-enabled` directories. |
| Who is affected | Every installation that runs Velocity with the Qbix engine. FrankenPHP and plain PHP engines are not (see "Other engines"). |
| How to check | `./console exp:velocity layout` shows the resolved paths and the migration state. |
| How to fix | Nothing to do by hand: the migration runs on `start` and `restart`, is idempotent and keeps the old paths working. |

## The tree

```
/etc/vc/  Velocity's overlay on the engine's base tree /etc/qbix (loaded after it, so it wins)
          (registered by the engine's vc distribution: Q_WebServer_Distribution_Vc)
├── vc.conf                   base settings shared by every site       (apache2.conf)
├── ports.conf                listen ports                             (ports.conf)
├── envvars                   environment for the server process      (envvars)
├── conf-available/*.conf     shared snippets                          (conf-available)
├── conf-enabled/*.conf       -> ../conf-available/*.conf  symlinks    (conf-enabled)
├── mods-available/*.conf     engine modules: http2, cache, compat,    (mods-available)
│                             precompress, log, dashboard, brand, ...
├── mods-enabled/*.conf       -> ../mods-available/*.conf  symlinks    (mods-enabled)
├── sites-available/*.conf    one per installation (host)              (sites-available)
├── sites-enabled/*.conf      -> ../sites-available/*.conf symlinks    (sites-enabled)
└── designs/                  the server's own page designs

/var/lib/vc/sites/<site>.json   metadata / extended info per installation
/var/log/vc/  /var/cache/vc/  /run/vc/   FHS counterparts (optional; see "Other assets")
```

The files hold JSON objects (the engine's configuration format) under Apache's file names. `envvars` holds
`export NAME=value` lines, as in Apache.

## Enable and disable (the a2ensite family)

```bash
./console exp:velocity layout                      # resolved paths and migration state
./console exp:velocity site enable <name>          # also: site disable, conf enable|disable, mod enable|disable
./console exp:velocity layout migrate              # run the migration explicitly
```

Enabling creates the relative symlink in the `*-enabled` directory. Disabling removes only the symlink; the file in
`*-available` stays.

### Option spellings

`exp:velocity` accepts the same GNU and BSD spellings as the engine's own tools:

- `--keep-global=A,B`, `--keep-global A,B`, `-keep-global=A,B` and `-keep-global A,B` are the same.
- `-json` is `--json`; `--no-json` turns it off again.
- Everything after `--` is passed on untouched: `exp:velocity ctl server:status -- --json` hands `--json` to the
  engine's console instead of reading it here.
- Exponential's one-letter options (`-s admin`, `-v`, `-d`) and real options named `no-…` (`--no-colors`) are
  unchanged.

## Load order and precedence

As with the includes of `apache2.conf`, later wins (deep merge):

1. engine defaults, then the application's `config/server.json`
2. `vc.conf`
3. `ports.conf`
4. `mods-enabled/*` (name order)
5. `conf-enabled/*` (name order)
6. the site file, `--config=/etc/vc/sites-enabled/<site>.conf`
7. command-line flags (`--port`, `--workers`, ...)

The engine reads a tree only when told to: `--conf-dir=DIR|auto`, `QBIX_CONF_DIR` or `VC_CONF_DIR`, or a `--config`
file inside a `sites-*` directory. It never picks one up just because it exists, so an unrelated server or the test
suite is not affected by a machine's `/etc/vc`.

## Who writes what

- `velocity.ini` (Exponential's settings) stays the source for what `exp:velocity` generates:
  `sites-available/<site>.conf` and the module files it manages. Generated files carry
  `"_generated": "exp:velocity"`. As with a dpkg conffile, a file whose marker an administrator removed is never
  overwritten again.
- Everything else in the tree (extra `conf-available` snippets, additions to `envvars`) belongs to the administrator
  and is only ever read.

## Migration of an existing installation

The migration runs on `start` and `restart` (and on `layout migrate`). It is idempotent and non-destructive:

1. **Resolve the directory.** `[LayoutSettings] ConfDir=auto` means `/etc/vc` if it exists or can be created; else
   `/etc/qbix` if that exists; else `<root>/var/vc/qbix/etc` (for a user without root).
2. **Create the tree** if it is missing, write the generated files and create the enabled symlinks.
3. **Verify** that the merged tree equals the single generated configuration it replaces, key for key, before using
   it. If it does not, Velocity keeps using the old single file and logs why.
4. **Keep the old path working:** `var/tmp/velocity-server.json` is still written.
5. **Record** `/var/lib/vc/sites/<site>.json` (root, engine version, migrated-at, previous paths).
6. **Log** every action to the Velocity log.

### Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/velocity.ini` | `LayoutSettings` | `ConfDir` | `auto` | installation |

## Other assets

Every other file Velocity keeps on disk (pid, logs, response cache, precompressed files, compat prewarm cache,
certificates, dashboard token, warm-up script, engine archive) is resolved in one place in `expVelocity` and listed
by `exp:velocity layout`. Their defaults are unchanged. The FHS locations above can be chosen through settings but
are not forced, because moving live caches and logs is a separate decision.

## Other engines

The tree belongs to the Qbix engine.

- With `[ServerSettings] Engine=frankenphp` the whole configuration is one generated Caddyfile
  (`var/vc/frankenphp/run/Caddyfile`; add your own directives through `[FrankenPHPSettings] SiteInclude`). The binary
  lives in `var/vc/frankenphp/bin/` and Caddy's state in `var/vc/frankenphp/caddy/`.
- With `Engine=php` there is no configuration file, only `bin/php/velocity-router.php`.

On both, `layout` lists those files instead of the tree, and `layout migrate` and `site|conf|mod` refuse. See
[Velocity engines](velocity-engines.md).

## Related pages

- [Velocity engines](velocity-engines.md)
- [Velocity engine upgrade notes](velocity-engine-upgrade-notes.md)
- [Velocity packages, Docker images and binaries](../../features/6.0/velocity-packages-and-binaries.md)
- [Velocity chronicle: September 2026, 24 September](../../history/velocity/2026-09d.md)
