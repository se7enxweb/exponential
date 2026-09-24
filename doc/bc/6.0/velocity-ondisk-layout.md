# Velocity on-disk layout — Debian Apache style

How Exponential Velocity keeps its configuration on disk, laid out like Debian's
/etc/apache2, and how an existing installation is moved to it.

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
/var/log/vc/  /var/cache/vc/  /run/vc/   FHS counterparts (optional; see "Assets")
```

File contents are JSON objects (the engine's config format) under Apache's file names.
`envvars` is `export NAME=value` lines, as in Apache.

## Load order and precedence

Like apache2.conf's includes, later wins (deep merge):

1. engine defaults, then the app's `config/server.json`
2. `vc.conf`
3. `ports.conf`
4. `mods-enabled/*` (name order)
5. `conf-enabled/*` (name order)
6. the site file, `--config=/etc/vc/sites-enabled/<site>.conf`
7. command-line flags (`--port`, `--workers`, ...)

The engine only reads a tree when told to: `--conf-dir=DIR|auto`, `QBIX_CONF_DIR`/`VC_CONF_DIR`,
or a `--config` file inside a `sites-*` directory. It never picks one up because it exists,
so an unrelated server or the test suite is not affected by a machine's `/etc/vc`.

## Tools (a2ensite family)

`exp:velocity site|conf|mod enable|disable <name>` and `exp:velocity layout` (shows the
resolved paths and migration state); `exp:velocity layout migrate` runs the migration
explicitly. Enabling creates the relative symlink; disabling removes only the symlink.

## Who writes what

- `velocity.ini` (Exponential's settings) remains the source for what `exp:velocity`
  generates: `sites-available/<site>.conf` and the module files it manages. Generated files
  carry `"_generated": "exp:velocity"`. Like a dpkg conffile, a file whose marker was removed
  by an administrator is never overwritten again.
- Everything else in the tree (extra conf-available snippets, envvars additions) is the
  administrator's and is only ever read.

## Migration of an existing installation

Runs on `start`/`restart` (and `layout migrate`), idempotent and non-destructive:

1. Resolve the directory: `[LayoutSettings] ConfDir=auto` → `/etc/vc` if it exists or can be
   created; else `/etc/qbix` if that exists; else `<root>/var/vc/etc` (a user without root).
2. Create the tree if missing; write the generated files; create the enabled symlinks.
3. Verify that the merged tree equals the single generated config it replaces, key for key,
   before using it. If it does not, keep using the old single file and log why.
4. Keep the old path working: `var/tmp/velocity-server.json` stays written.
5. Record `/var/lib/vc/sites/<site>.json` (root, engine version, migrated-at, previous paths).
6. Log every action to the Velocity log.

## Assets

Every other on-disk asset (pid, logs, response cache, precompress, compat prewarm cache,
certificates, dashboard token, warm-up script, engine archive) is resolved in one place in
`expVelocity` and listed by `exp:velocity layout`. Their defaults are unchanged. The FHS
locations above are available via settings, not forced, because moving live caches and logs
is a separate decision.
