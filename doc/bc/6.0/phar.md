# Engine archive (phar) support

Exponential 6.0 can load its own engine — `kernel/`, `lib/` and `autoload/` —
from a single phar archive instead of from a thousand separate files on disk.

This document is the whole feature: what it is, what it is not, how to use it,
what had to change in the kernel to make it possible, and what it measured.

---

## What it is

One command builds an archive:

```bash
bin/php/console exp:phar build      # write dist/engine.phar
bin/php/console exp:phar info       # what exists, and whether it is in use
bin/php/console exp:phar clean      # remove it
```

One environment variable switches the runtime onto it:

```bash
EXP_ENGINE_PHAR=/path/to/install/dist/engine.phar
```

Set it in the environment of whatever serves the site. Unset it and the
installation is exactly what it was.

## What it is not

**It is not a replacement for the installation.** Nothing leaves the disk. The
same files stay where they are, and `design/`, `extension/`, `settings/`,
`share/`, `bin/` and `var/` are untouched and still read from disk. Only the
autoloader behaves differently.

**It is not a performance feature.** Measured, it makes no difference. See
*Measurements* below. If it is being proposed as an optimisation, the numbers
there are the answer.

**It is not source protection.** A phar is an archive. Anyone who can read it
can extract it.

**It is not yet a single executable.** Combining the archive with a static PHP
binary — `spc micro:combine`, which would remove the need for PHP on the
target — is not implemented. See *Not implemented*.

---

## Using it

### Build

```bash
bin/php/console exp:phar build
```

Building requires `phar.readonly=0`. That is an ini setting, and the console
dispatches each command to a fresh PHP, so a flag typed on the console line
never reaches the command. The command therefore re-executes itself once with
the setting rather than asking anyone to know that. Nothing needs to be passed.

The archive is written to `dist/engine.phar`. `dist/` is gitignored; nothing
in it is source.

Options:

| Option | Meaning |
|---|---|
| `--output=PATH` | write the archive somewhere other than `dist/engine.phar` |
| `--json` | print the result as JSON, for scripting |

### Inspect

```bash
bin/php/console exp:phar info
```

Reports the archive's path, size, build time and version, whether the runtime
is currently using it, and the working tree's own version for comparison.

The same facts appear in the admin under **Setup → System information**, in a
block named *Engine*:

- **Loaded from** — individual files on disk, or the archive
- **Installation root** — the absolute path the kernel resolves against
- **Archive** — path, file count, size, build time
- **Archive version** — read from inside the archive
- **Matches working tree** — whether the archive was built from what is on
  disk now
- **Phar stream wrapper** — registered or not, and `phar.readonly`
- **Opcode cache** — loaded, enabled, scripts cached

That block exists because when an edit to a kernel file appears to do nothing,
or a stack trace names a `phar://` path, this is the first thing worth reading.

### Switch the runtime onto it

    [ServerSettings]
    EnginePhar=enabled

in `settings/velocity.ini`, or an override of it. The value may be:

| | |
|---|---|
| `disabled` | the files on disk. The default. |
| `enabled` | `dist/engine.phar`, wherever `expPhar` puts it. |
| *a path* | that archive, relative to the installation root or absolute. |

The service exports `EXP_ENGINE_PHAR` into the server's environment at launch,
and the workers inherit it when they fork. It has to be the environment rather
than a command line argument, because `autoload.php` reads it before it has
parsed anything -- which is necessarily before any argument could be consulted.

An archive that is not there stops the server from starting rather than falling
back to the files on disk. A silent fall back is indistinguishable from success
and the difference only surfaces much later.

`exp:velocity status` reports which engine is running, for the same reason:
from outside, an archive that failed to load and an ordinary run look identical.

**Clear the caches when switching, in either direction.** Pages rendered under
one engine are cached and will be served under the other.

### Revert

    EnginePhar=disabled

then clear the caches and restart. Unsetting the variable or deleting the
archive works too. There is no other state.

### It could not work at all before 0.0.4.8

The compatibility layer in the application server rewrites `header()`,
`setcookie()` and their kin as each file is included, because under the CLI SAPI
those functions do nothing on their own. It did that by wrapping the `file://`
scheme, and code included through `phar://` never passed through it.

So the engine ran from the archive and every one of those calls went nowhere.
The site rendered the right pages and sent almost nothing with them:

    Content-Type: text/html; charset=UTF-8
    Content-Length: 76800
    Connection: close

No `Cache-Control`, so nothing could be cached and every request was rendered --
75ms became 1.2-1.7s. No `Set-Cookie`, so nobody could sign in: the login
answered 302 to the right place and set no session. Nothing was logged, because
nothing failed.

`qbix-webserver` 0.0.4.8 wraps `phar://` as well. If the archive is in use and
headers or sign-in misbehave, check that version first.

---

## Versioning

The archive is stamped with a version composed of the release, the short
commit, and `-dirty` when the working tree does not match that commit:

```
6.0.15stable-aae2c57d72-dirty
```

A build from a dirty tree is allowed — refusing would make iterating painful —
so the marker is what stops the artifact claiming to be a commit it is not.
The stamp is readable from inside the archive at `ENGINE_VERSION`, through
`exp:phar info`, and in the admin.

---

## How it resolves classes

The build writes a `MANIFEST.php` into the archive: a map whose keys are the
paths exactly as the kernel class map holds them. The autoloader reads it
once, then every lookup is a single `isset()`.

Asking the archive per class would mean a stat per class, which is the cost
this is meant to avoid rather than add.

**A class the archive does not carry is still loaded from disk.** An archive
that has gone stale therefore degrades to the old behaviour for that class
instead of failing. This is deliberate: a kernel file added after the archive
was built must not break the installation.

---

## What had to change in the kernel

Kernel code derives paths from its own file's location. `__DIR__` inside an
archive names a path *inside the archive*, so that code stops finding things
that were never in it.

### `autoload.php`

Publishes `EXP_ROOT_DIR`, the absolute installation root, because it is the
one file guaranteed to be on disk. Resolves `EXP_ENGINE_PHAR` from the
environment. Resolves kernel and library classes from the archive when there
is one.

It also keeps the phar stream wrapper registered when the runtime is itself
read through it — unregistering it in that case unloads the running program.
See `doc/bc/6.0/` notes on the wrapper, and the comment in the file.

### `lib/ezutils/classes/ezini.php`

Five places computed the installation root as three levels up from that file.
All five now call `eZINI::installationRoot()`, which returns `EXP_ROOT_DIR`
when defined and falls back to the old computation when not.

Two of them mattered, and **the one that mattered most was not the obvious
one**:

- `eZINI::exists()` — settings appear absent. Loud.
- `eZINI::findInputFiles()` — **quiet and much worse.** The base `.ini` drops
  out of the input list while the override, which resolves against the working
  directory, stays. Configuration comes back half-loaded: `VarDir` reads
  correctly, `CacheDir` returns `false`, and the failure surfaces somewhere
  else entirely as `Cannot use bool as array` in `global_functions.php`.

If a future change to the archive's contents produces symptoms like that,
this is the pattern to look for.

### Not changed

Four other files use the same self-relative pattern —
`kernel/setup/expcronjobrunner.php`, `kernel/setup/expextensionwizard.php`,
`kernel/setup/expkerneloverridewizard.php` and `lib/ezdb/classes/expmongodb.php`.
None is on the request path. If any of them is ever packaged and exercised
from an archive, it will need the same treatment.

---

## The phar stream wrapper

The bootstrap unregisters the phar stream wrapper, and that protection is
real: the wrapper lets any path-taking function reach inside an archive, and a
file that is a valid image and a valid phar at the same time is trivial to
produce. This application accepts image uploads by design.

The 2018 metadata-deserialization vector the original comment named is closed
on PHP 8. The wrapper still matters for the reason above.

Consequences for this feature:

- When `EXP_ENGINE_PHAR` is set, the wrapper stays registered, because the
  runtime is being read through it.
- When it is not set, the wrapper is unregistered — including on the command
  line. `expPhar` therefore restores it around its own archive work and puts
  it back exactly as it found it. `Phar` extends `RecursiveDirectoryIterator`
  and cannot even be constructed without the wrapper.

---

## Measurements

Measured on this installation, `/site/recipes` and the front page, four
workers each side, against the persistent-worker server.

**The first method was wrong and the mistake is worth recording.** Timing one
server and then the other gave `+3.9%`, `+7.8%`, `-19.8%`, `+7.8%` — a spread
far wider than anything being measured. The page costs over half a second and
the machine does other work, so whatever drifts during a run lands entirely on
whichever side is being timed.

Interleaved — one request to each server in turn, so drift hits both equally —
five runs gave:

```
+2.4%   -2.3%   -3.8%   -0.1%   +1.8%      mean about -0.4%
```

**There is no measurable difference.**

That is consistent with what was already known about this kernel: the cost is
PHP generated at runtime — compiled templates and ini caches, included on
every request — not the cost of finding class files. Packaging does not touch
that.

---

## Speed: what was measured, and what to stop trying

The archive was built because a client asked for it and because a single
stamped artifact is easier to deploy than a thousand files. It was also
hoped to be faster. It is not.

Everything below was measured on this installation, on a content page, four
workers per server, with requests round-robined between the servers being
compared so that anything drifting during a run hits them all equally.

| Change | Result |
|---|---|
| **Opcode cache enabled for the server process** | **+18 to +26%** |
| Engine loaded from the archive | no measurable difference (mean −0.4%) |
| Cheaper tests in the server's file stream wrapper | +9.7%, then +2.9%, then +0.5% — noise |
| Opcode cache without timestamp checks | within noise of the cache alone |
| Opcode cache with a 32M interned string buffer | within noise |
| Tracing JIT | within noise; worst of five on one run, best on the next |

**Only the opcode cache pays.** It was off because
`/etc/php.d/99-no-opcache-cli.ini` disables it for every command-line PHP on
the machine, and the persistent-worker server runs under that interpreter. It
is now requested per process in `settings/velocity.ini` under `[PHPSettings]`;
the system-wide file is deliberately untouched.

**Measure by round-robin, never one server after the other.** Timing one and
then the other reported +3.9%, +7.8%, −19.8% and +7.8% for a change that is
actually worth about −0.4%. The page costs half a second and the machine does
other work.

**The remaining time is the application, not the server.** A trivial script
through the same server answers in about 30ms against roughly 450ms for a
content page. The next place to look is the database and template rendering.

---

## Not implemented

Deliberately left undone:

- **Combining with a static PHP binary.** The part that would make this a
  single-file handover with no PHP on the target.
- **Engine and site version mismatch enforcement.** The archive is stamped and
  the admin shows whether it matches, but nothing refuses to start on a
  mismatch. Drop a newer archive beside an older install and the failure looks
  like a bug in the site.
- **Consistency checking of the archive.** `share/filelist.md5` checks the
  on-disk copies, which are still present, so it keeps working. It does not
  verify the archive's contents.

---

## Files

| Path | Role |
|---|---|
| `kernel/classes/expphar.php` | the service: build, info, clean, version, wrapper handling |
| `bin/php/phar.php` | the command, discovered by the console as `exp:phar` |
| `autoload.php` | publishes the root, resolves the archive, loads classes from it |
| `lib/ezutils/classes/ezini.php` | asks for the root instead of computing it |
| `kernel/setup/info.php` | gathers the Engine block |
| `design/admin/templates/setup/info.tpl` | renders it |
| `dist/engine.phar` | the artifact (gitignored) |
