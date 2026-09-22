# Running Exponential under the Qbix application server

Everything needed to run this installation on a persistent-worker PHP server,
why each change was necessary, and how to check the claims rather than trust
them.

Written for two readers: the operator who wants it running, and the developer
who wants to know what was wrong and whether it is fixed.

---

## Part 1 — Operating it

### The short version

```bash
./bin/php/console exp:velocity start   --allow-root-user
./bin/php/console exp:velocity status  --allow-root-user
./bin/php/console exp:velocity stop    --allow-root-user
```

That is the whole interface. The server runs from `vendor/se7enxweb/qbix-webserver`,
its settings come from `settings/velocity.ini`, and the command line is built
for you.

### The verbs

| Verb | What it does |
|---|---|
| `status` | what is running, on which ports (the default if you give no verb) |
| `start` | start it; refuses if it is already running |
| `stop` | ask it to stop, wait, and report if anything survived |
| `graceful` | re-exec without dropping the listening socket |
| `restart` | stop, then start |
| `kill` | stop without asking, for a worker that will not go |
| `command` | print the command line it would run, and exit |

Add `--json` for a caller that is not a person. It prints one object with
`ok`, `message` and `data`, and exits non-zero on failure.

Both forms work:

```bash
./bin/php/console exp:velocity restart --allow-root-user
php bin/php/velocity.php status --json --allow-root-user
```

The console discovers `bin/php/velocity.php` by itself — anything in
`bin/php/` becomes `exp:<name>` — so there is nothing to register.

### Settings

`settings/velocity.ini` ships the defaults. Override per installation in
`settings/override/velocity.ini.append.php`, which is never committed.

| Setting | Default | Notes |
|---|---|---|
| `ServerSettings/Host` | `127.0.0.1` | loopback by default, deliberately |
| `ServerSettings/Port` | `8088` | plain HTTP |
| `ServerSettings/HTTPSPort` | `8080` | only used when HTTPS is enabled |
| `ServerSettings/Workers` | `4` | explicit; see *Workers* below |
| `ServerSettings/PidFile` | `var/tmp/velocity.pid` | |
| `ServerSettings/LogFile` | `var/tmp/velocity.log` | the server's own output |
| `HTTPSSettings/Enabled` | `false` | |
| `HTTPSSettings/Certificate`, `Key` | — | absolute paths, outside the document root |
| `ApplicationSettings/KeepGlobals[]` | nine entries | see *The registries*, below |
| `ControlSettings/StopTimeout` | `15` | seconds before `stop` reports failure |

### Binding to anything other than loopback

The default is `127.0.0.1` because this server has **no HTTP authentication of
its own** and serves the whole document root. Exposing it means exposing
everything under the installation root to whoever can reach the port.

To browse it from elsewhere, prefer an SSH tunnel, which needs no firewall
change at all:

```bash
ssh -p 7822 -L 8080:127.0.0.1:8080 root@alpha.se7enx.com
# then http://localhost:8080/site/recipes
```

If it must be reachable directly, restrict it to one source address and make
the rule runtime-only so it disappears on reboot:

```bash
bash ai/bin/one/qbix_open_test_port.sh open <your.ip.address> 8080
bash ai/bin/one/qbix_open_test_port.sh close <your.ip.address> 8080
```

### Choosing a siteaccess

`MatchOrder` here is `uri;host`. A browser sends `Host: alpha.se7enx.com:8080`,
which matches no host rule, so the siteaccess comes from the URI prefix:

| | |
|---|---|
| front end | `/site/recipes` |
| admin | `/admin/content/dashboard` |
| other site accesses | `/bold`, `/bold_ger` |
| by host header instead | `curl -H 'Host: alpha.se7enx.com' http://127.0.0.1:8088/recipes` |

A bare `/` resolves to no siteaccess and will not work. This is a property of
the installation's `MatchOrder`, not of the server.

### TLS

The server speaks plain HTTP on `Port` and TLS on `HTTPSPort`. Point it at a
real certificate; a browser will refuse a self-signed one and report it as a
connection failure rather than a certificate problem, which is confusing.

This installation reuses the Let's Encrypt certificate Plesk already holds:

```bash
bash ai/bin/one/export_plesk_cert_for_qbix.sh alpha.se7enx.com
```

That writes `fullchain.pem` and `privkey.pem` into `/etc/qbix/certs`, mode
`600`, **outside the document root** — `var/` is web-readable on this host, so
a key there would be downloadable.

**Re-run it after every renewal.** Plesk writes a renewed certificate to a new
file under a new name; this copy goes stale and the server keeps serving the
old one until restarted.

Do not let the server obtain its own certificate here. Let's Encrypt's HTTP-01
challenge needs port 80 of the domain and Plesk's nginx owns it; two clients
competing produces failed renewals and spent rate limit for a certificate that
already exists.

### Workers

`Workers=4` is set explicitly in `velocity.ini`. Since **v0.0.2.3** the server
also chooses a sensible number on its own — it measures the parent's resident
size after preload rather than assuming, caps the per-core multiple, and will
not exceed 64 automatically. Before that release it chose 2400 on this machine
and never finished starting.

Raise it when requests queue; watch memory as you do. A worker here holds
about 8MB idle and grows as it serves.

### The server's own endpoints

Useful for telling "the server is broken" apart from "the application is
broken", because they do not touch Exponential at all:

| | |
|---|---|
| `/Q/health` | JSON: uptime, request count, status tallies |
| `/Q/dashboard` | live dashboard |
| `/Q/docs` | built-in documentation |

### When something is wrong

```bash
./bin/php/console exp:velocity status --allow-root-user
tail -50 var/tmp/velocity.log
grep -c 'Worker died' var/tmp/velocity.log     # should be 0
grep -c 'Fatal error' var/tmp/velocity.log     # should be 0
```

Symptoms worth recognising, all of which had causes that are now fixed and
would mean something new if they return:

| Symptom | What it used to mean |
|---|---|
| 502 with an eleven byte body | a worker died; body is literally `Worker died` |
| "Secure Connection Failed", connection reset | a redirect pointing at `http://` on a TLS port |
| `SSL_ERROR_BAD_MAC_READ` on one large asset | a file over 1MB served from a forked child over TLS |
| a page renders but its text is missing | the database connection fell back to latin1 |
| accepts connections, answers nothing | far too many workers, still forking |

`stop` leaving something behind is normal enough to have a verb: run `kill`.

---

## Part 2 — What had to change, and why

Exponential assumes the shape of mod_php and FPM: one process per request,
torn down afterwards, so nothing has to be reusable. A persistent-worker
server inverts that. Everything that was free — a fresh symbol table, fresh
globals, fresh statics, a fresh autoload stack — has to be earned.

Two categories of fault. Neither is anybody's mistake exactly; both are
assumptions that were true for twenty years.

### In Exponential

**Module views declare things.** eZ includes a module view on every request,
so a function or class declared in one is declared again on the second
include, which is fatal. 126 views across the kernel and bundled extensions
did this. All are now guarded with `function_exists` / `class_exists`.

Guarding alone is not enough, and this is the part that is easy to get wrong.
PHP binds an unconditional top-level declaration at compile time, before the
first line runs; a guarded one exists only once execution reaches it.
Twenty-six files called a function above its own declaration and twelve more
reached a top-level `return` first. So top-level declarations are also **moved
to the head of their file**. Declarations nested inside a condition or inside
another function are guarded where they stand, because moving those would
change their meaning.

**`eZSys::isSSLNow()` decided TLS by port number.** It compared the port to
`SSLPort` and consulted proxy headers, but never `$_SERVER['HTTPS']` — so eZ
could not recognise TLS on any port but 443. Every absolute URL came out
`http://`, including the redirect after a login or a publish, and following
that into a TLS listener resets the connection. Now it checks `HTTPS` first,
which changes nothing under Apache.

**`eZMySQLiDB::connect()` resolved a class inside an exception window.** It
called `eZMySQLCharset::mapTo()` inside a block where eZDebug turns any
diagnostic into an exception, so autoloading that class put a path probe in
the same window. A probe that missed raised, the raise became an exception,
`mysqli_set_charset` was recorded as failed, and the connection silently kept
the server default of **latin1**. Rows came back transliterated — a
non-breaking space arrived as `?` — which made XML attributes unparseable, so
rich text and meta descriptions vanished with no error anywhere. The name is
resolved before the window now, and the code asks the connection what charset
it actually has rather than trusting a return value.

**`expLayoutsResolver::nodeFromPath()` did not strip a siteaccess prefix.**
With `MatchOrder=uri;host` the request URI still carries the siteaccess name,
so `site/running` translated to nothing, the current node came back false, and
every dynamic collection returned an empty list while the page rendered
perfectly. Not a server problem at all: `https://alpha.se7enx.com/site/running`
was equally broken under Apache.

**The autoload generator swept `vendor/` into the kernel array.** 3002 vendor
entries had accumulated, duplicating a mapping Composer owns and keeps
current. There is now an `.autoloadignore`, and the generator skips blank
lines and comments in it — necessary because every entry becomes an anchored
expression, so one stray empty line would have matched every file and silently
excluded the whole tree.

### In the server

Fourteen fixes, each on its own branch off `origin/main`, all released:

| Branch | The fault |
|---|---|
| `fix/event-loop-survives-dead-connections` | `stream_select()` on a stream the client closed killed the parent and every worker |
| `fix/guard-closed-stream-handles` | `fclose()` on an already-closed handle did the same |
| `fix/no-fork-for-tls-connections` | a file over 1MB served from a forked child corrupted TLS records |
| `fix/headers-write-every-byte` | `Headers.php` discarded what `fwrite()` returned, truncating large responses |
| `fix/exit-ends-the-request-not-the-worker` | `exit`/`die` ended the worker; 99 files here call a helper whose last statement is `exit` |
| `fix/location-header-means-redirect` | a `Location` header returned 200, so no redirect was a redirect |
| `fix/file-wrapper-does-not-raise-for-missing-paths` | the wrapper raised for a missing path, which is what forced the latin1 connection |
| `fix/tell-the-worker-the-connection-is-tls` | the parent never told the worker the connection was TLS |
| `fix/keep-globals-setting` | an application can name globals it keeps between requests |
| `fix/raw-set-cookie-survives-the-shim` | follow-up to upstream #35: a raw `Set-Cookie` was dropped alongside `setcookie()` |
| `fix/size-the-worker-pool-from-measured-cost` | the automatic count chose a pool the machine could not run |
| `fix/do-not-prompt-when-nobody-can-answer` | the limits prompt blocked a start no person was watching |
| `fix/carry-binary-request-bodies-to-the-worker` | a request body that is not valid UTF-8 went to the worker as a zero-length frame |
| `fix/uploads-pass-the-check-that-accepts-them` | `is_uploaded_file()` refused every upload the pool had parsed |

**The last two are the file-upload story, and they are worth the space**
because between them they broke every upload in the installation while
producing no error anywhere. The parent hands a request to a worker as JSON;
`json_encode()` returns `false` for bytes that are not valid UTF-8, and
`strlen(false)` is `0`, so the length-prefixed frame went out as a length of
zero followed by nothing. The worker read an empty message and answered 500.
That is every multipart POST carrying bytes rather than text.

Fixing that revealed the second one underneath it. `is_uploaded_file()` and
`move_uploaded_file()` are replaced by the server, because the real ones
answer from a list that only the SAPI's own multipart parser fills and which
is always empty here. The replacements answer from a registry that
`Q_WebServer_Compat::parseMultipart()` writes to — and the pool worker uses
`Q_WebServer::parseMultipart()`, which did not. So the bytes arrived whole,
`$_FILES` was correct, and the kernel refused the file, because checking
`is_uploaded_file()` first is the documented way to accept an upload.

Neither failure looks like an error in a browser. The online editor's dialog
shows *"Upload is in progress, it may take a few seconds…"* and hides it when
its target iframe loads; a response that never arrives leaves that message on
screen for good. **A hang in a progress dialog is the shape this class of bug
takes, so treat one as a transport fault until proved otherwise.**

An upload that failed this way leaves a published object behind with an empty
storage directory and `width=""` in its image XML. Those objects are not
repaired by fixing the server — they never held any bytes — and they render
as missing previews forever. Find them with the `width=""` marker and
re-upload.

Three of these are one story: PHP 7 treated operating on a closed resource as
a warning, PHP 8 raises a `TypeError`, and `@` does not suppress an exception.
The event loop now also absorbs anything a callback throws, so a future one
costs a connection rather than the server.

**The `@` trap is worth remembering on its own.** The silence operator lowers
`error_reporting` for a call but a custom error handler is *still invoked*. An
application whose handler throws — eZ does, around `mysqli_set_charset` — gets
the exception anyway. `@stat()` did not fix the file wrapper; not statting a
missing path did.

### The registries

Three registries in this kernel are filled by a file reached with
`include_once` that registers itself as it loads. Clearing the global that
holds one empties it for the rest of that worker's life, because
`include_once` will not run the file again:

```
eZDataTypes                eZWorkflowTypes            eZNotificationEventTypes
eZDataTypeObjects          eZWorkflowTypeObjects      eZNotificationEventTypeObjects
eZDataTypeAllowedTypes     eZWorkflowAllowedTypes     eZNotificationEventTypeAllowedTypes
```

They are named in `ApplicationSettings/KeepGlobals[]`. The symptom of a
missing one is a lookup returning null and a fatal on the next line — the
notification registry is how publishing failed, with
`Call to a member function initializeEvent() on null`.

**Nothing else belongs in that list.** Request-scoped globals must be cleared,
and keeping one leaks a request into the next. A search of the kernel found
exactly three registries of this shape; the other thirty-odd globals are
request state.

---

## Part 2b — The engine archive, and what it is worth

A client asked for the engine to be compiled into a phar. It is built, it
works, and **it does not make the site faster**. Both halves of that are worth
recording, because the request will come again.

### What it is

`exp:phar` packages `kernel/`, `lib/` and `autoload/` — 1045 files, 18MB —
into `dist/engine.phar`.

```bash
bin/php/console exp:phar build      # write dist/engine.phar
bin/php/console exp:phar info       # what is there, and whether it is in use
bin/php/console exp:phar clean      # remove it
```

**Nothing leaves the disk.** The archive is an addition, not a replacement:
the same files stay where they are, and `design/`, `extension/`, `settings/`
and `var/` are untouched and still read from disk. The only thing that changes
is where the autoloader reads a class from. Set `EXP_ENGINE_PHAR` to the
archive's path in the server's environment and kernel and library classes come
from it; unset it and they come from disk. That is the whole switch, and it is
why this is safe to try — one variable reverts it, and deleting the file
reverts it too.

A class the archive does not carry is still loaded from disk, so an archive
that has gone stale degrades to the old behaviour for that class rather than
failing.

### What it cost to make work

Kernel code derives paths from its own file's location, and `__DIR__` inside
an archive names a path inside the archive. Five places in `eZINI` did this,
and the two that mattered were not the obvious one:

- `eZINI::exists()` — settings appear absent.
- `eZINI::findInputFiles()` — **the quiet one.** The base `.ini` drops out of
  the input list while the override, which resolves against the working
  directory, stays. Configuration comes back half-loaded: `VarDir` reads
  correctly, `CacheDir` returns `false`, and the failure surfaces somewhere
  else entirely as `Cannot use bool as array`.

All five now ask `eZINI::installationRoot()`, which returns `EXP_ROOT_DIR`
when it is defined and falls back to the old computation when it is not.
`autoload.php` publishes `EXP_ROOT_DIR`, because it is the one file guaranteed
to be on disk.

Four other files in `kernel/` and `lib/` use the same pattern —
`expcronjobrunner`, `expextensionwizard`, `expkerneloverridewizard` and
`expmongodb`. None is on the request path and none has been changed.

### What it is worth: nothing measurable

Measured on `/site/recipes` and the front page, against this installation,
four workers each side.

**The first attempt was wrong and worth describing.** Timing one server and
then the other gave +3.9%, +7.8%, −19.8% and +7.8% — a spread far larger than
anything being measured. This page costs over half a second and the machine
does other work, so whatever drifts during a run lands entirely on whichever
side is being timed.

Interleaved — one request to each server in turn, so drift hits both equally —
five runs gave **+2.4%, −2.3%, −3.8%, −0.1%, +1.8%**, a mean of about −0.4%.

**There is no measurable difference.** That is consistent with what was already
measured: the cost here is PHP generated at runtime — 118 compiled templates
and 58 ini caches, included on every request — not the cost of finding class
files. Packaging does not touch that, and the thing that would, OPcache,
cannot be enabled in the static binary because it segfaults before printing
its own version.

**So the archive is worth having for deployment and versioning, not for
speed.** It is one stamped file instead of a thousand, the stamp says which
commit it came from and marks a dirty tree, and it is one variable to switch.
If anyone proposes it as a performance measure, the numbers above are the
answer.

Reproduce with `ai/bin/one/measure_engine_phar_speed.sh <path> <requests>`,
which refuses to report a comparison when either side served nothing — the
first version happily compared two error pages and called it an 84% win.

### Still to do

Left undone deliberately, because neither is needed to answer the question
that was asked:

- **Combining with a static PHP binary.** `spc micro:combine` turns a phar
  into one executable with no PHP on the target. That is the part that would
  make this a single-file handover.
- **Combining with a static PHP binary** is still the missing half of a
  single-file handover, as above.
- **The consistency check.** `share/filelist.md5` still checks the on-disk
  copies, which are still there, so it keeps working. It does not verify the
  archive.


## Part 3 — Checking the claims

Nothing above needs to be taken on trust.

```bash
bash ai/bin/one/measure_qbix_repeat_requests.sh /site/recipes 20 alpha.se7enx.com https://alpha.se7enx.com:8080
bash ai/bin/one/check_qbix_cookie_mechanisms.sh https://alpha.se7enx.com:8080
bash ai/bin/one/check_module_views_with_curl_session.sh https://alpha.se7enx.com:8080/admin 4
bash ai/bin/one/test_qbix_admin_login_post.sh https://alpha.se7enx.com:8080/admin
python3 ai/bin/one/test_admin_publish_post.py https://alpha.se7enx.com:8080/admin 90
bash ai/bin/one/post_binary_upload_to_qbix_and_apache.sh
bash ai/bin/one/post_ezoe_image_embed_upload.sh 328 1
bash ai/bin/one/compare_binary_file_delivery_qbix_vs_apache.sh
bash ai/bin/one/compare_admin_edit_page_image_preview.sh 328 1
```

The four upload checks each run against Apache first and then against this
server. That ordering is the point: the probe reports from inside the
application, so a failure on its own says nothing about which side of the
boundary lost the file. Apache is the reference, and a difference between the
two is the finding.

The declaration work has its own verification, because `php -l` proves very
little here — every tooling bug found along the way produced files that
parsed:

```bash
php ai/bin/one/find_redeclarable_declarations.php $(cat var/tmp/module_views_all.txt)
php ai/bin/one/verify_guarded_declarations_reachable.php <files>
php ai/bin/one/verify_hoist_moved_nothing_but_position.php <before-dir> <files>
bash ai/bin/one/audit_module_view_baseline.sh var/tmp/module_views_paths.txt
```

The last two are the ones that matter: one proves each declaration's text and
the whole of the rest of the file are byte-identical to the original with only
position changed; the other proves every file was pristine before a run, and
says which of four sources it used to know.

### Measured on v0.0.2.4

| | |
|---|---|
| front page, 20 requests from cold | 20/20 at one response size |
| 16 admin views, 4 rounds, logged in | all pass, identical sizes |
| cookies — raw, `setcookie()`, both | 11/11 |
| a full publish | POST → `302 https://…` → 200, version incremented |
| assets byte-identical to disk | including 1.2MB and 1.9MB files |
| four site accesses | all 200 |
| fatals / worker deaths | 0 / 0 |
| Apache and FPM | unaffected throughout |

---

## Part 4 — For whoever works on this next

### Reproducing the tooling

The declaration guards were applied by tokenizer-driven scripts, not by
matching lines. Line matching was tried and was wrong three times: it cut a
declaration in half at a brace in column 0 inside it, it scanned past a
brace-less `if` and deleted a brace pair from unrelated code three files away,
and a `?>` inside a `//` comment in the tool itself ended PHP mode. All three
produced files that parsed.

```
ai/bin/one/hoist_declarations_tokenizer.php            guard and hoist; preserves CRLF
ai/bin/one/guard_declaration_in_place.php              guard a nested declaration where it stands
ai/bin/one/find_redeclarable_declarations.php          find what a second execution would redeclare
ai/bin/one/unguard_declarations_tokenizer.php          the exact reverse
```

### Rebuilding the server fixes

Every server fix is a named, idempotent transform, so a branch can be rebuilt
from scratch rather than depending on a working tree nobody else has:

```bash
python3 ai/bin/one/apply_qbix_fix.py --list
bash ai/bin/one/create_qbix_fix_branches.sh          # one branch per fix, off origin/main
bash ai/bin/one/merge_qbix_fixes_into_maintain.sh    # all of them into maintain
```

### Traps that cost real time here

- **`pkill -f qbixserver.php` kills the calling shell** when combined with
  other work in the same command. Issue it alone, then confirm with
  `ps -eo args | grep -c '[q]bixserver.php'`.
- **`cp` is aliased interactive** on this host and silently does nothing in a
  script. Use `cat source > dest`.
- **`git describe` returns the nearest reachable tag, not the highest
  version.** It answered `3.0.1` for a package already at `4.0.0.1`, and a
  release went out below one already published. Sort tags by version.
- **Fetch before computing a version.** Two repositories had moved on the
  remote since this checkout last looked.
- **A remote-tracking ref existing does not mean a branch was pushed.**
  Creating a branch from `origin/main` sets its upstream to `origin/main`, so
  `@{upstream}` resolves for a branch that has never been pushed. Ask the
  remote with `git ls-remote`.
- **Restart before believing a measurement.** FPM caches bytecode and the
  server pre-warms its transform cache; more than one "the fix did not work"
  here was a stale process.
- **An admin URL returning 200 is not proof of anything.** Unauthenticated
  requests return 200 carrying the login form. Check for content, or log in.
- **A published tag is a promise about a commit, so rewriting history under it
  is not free.** The messages on `maintain` were rewritten to drop a name the
  project does not use; `git filter-branch` was run *without*
  `--tag-name-filter`, so `v0.0.2.3` and `v0.0.2.4` still point at their
  original commits and every already-installed version resolves to exactly
  what it resolved to before. The price is that those two commits are no
  longer ancestors of `maintain`: the trees are identical, but `git describe`
  will not find them and only a tag listing shows them. The script is
  `ai/bin/one/rewrite_qbix_commit_messages_ez_publish_to_exponential.sh`, and
  it verifies both halves — no tag moved, no tree changed — before it reports
  success. The previous tip is kept as
  `backup/maintain-before-exponential-rename`, pushed, so the whole rewrite is
  one `git reset --hard` from undone.

### Still not done

- **The `ai/` tree and the root Markdown files are served publicly.**
  `https://alpha.se7enx.com/AGENTS.md` returns 32KB including credentials.
  Plesk's nginx serves them directly, so `.htaccess` cannot stop it. The fix
  and its verification are in
  `ai/doc/exposure-ai-tree-and-root-markdown-served-publicly.md`. **This is
  the one item here with a real security cost.**
- **The write path is only partly exercised.** Login, sessions, publish and
  the layouts editor are covered. File upload and anything going through the
  cluster file handler are not.
- **Three released extensions are not in `composer.json`** — `explayouts_ui`,
  `explayouts_ui_api`, `nxc_powercontent`. A version bump in them will not
  reach this installation through composer.
- **`.htaccess` reports as changed** in the file consistency check, on
  purpose. Nothing in this work touched it, and refreshing its hash would
  retire the only signal that it drifted.
- **Twelve fix branches are on `se7enxweb/qbix-webserver`, not proposed
  upstream.** Eleven are independent of the open pull requests;
  `fix/raw-set-cookie-survives-the-shim` is a follow-up to #35 and should be
  reviewed with it or squashed into it.

---

## Part 2c — Running the engine from the archive, which now actually works

Everything in Part 2b was written when the archive could be built and inspected
but not run. Two things have changed.

### There is a setting for it

    [ServerSettings]
    EnginePhar=enabled

in `settings/velocity.ini` or an override. `disabled` (the default) is the files
on disk; `enabled` is `dist/engine.phar`; anything else is read as a path.

The service exports `EXP_ENGINE_PHAR` into the server's environment at launch,
and the workers inherit it. It has to be the environment and not an argument,
because `autoload.php` reads it before it has parsed anything.

An archive that is not there stops the server from starting. A silent fall back
to the files on disk is indistinguishable from success, and the difference
surfaces much later. `exp:velocity status` reports the engine in use for the
same reason.

**Clear the caches when switching, either way.** Pages rendered under one engine
are cached and will be served under the other.

### Why it did not work, which is worth understanding

The compatibility layer in the server rewrites `header()`, `setcookie()` and
their kin as each file is included, because under the CLI SAPI those functions
do nothing on their own. It wrapped the `file://` scheme. Code included through
`phar://` never passed through it, so none of it was rewritten.

The engine ran, rendered the right pages, and sent almost nothing with them:

    Content-Type: text/html; charset=UTF-8
    Content-Length: 76800
    Connection: close

No `Cache-Control`, so nothing cached and every request rendered -- 75ms became
1.2-1.7s. No `Set-Cookie`, so nobody could sign in. **Nothing was logged,
because nothing failed.** The calls went nowhere.

Fixed in `qbix-webserver` 0.0.4.8, which wraps `phar://` as well. The first
attempt at that fix did nothing at all, because it tested `defined('EXP_ENGINE_PHAR')`
and the constant is defined by the application's own bootstrap, long after the
compatibility layer starts. It reads the environment now.

### What it costs, measured

Public pages are unaffected, because they are served from the response cache and
never reach the engine:

    /site            75-88 ms   disk and archive alike
    /site/fitness    72-99 ms

Admin pages, which are never cached, are slower from the archive:

    /admin/setup/info            143-168 ms disk    588-920 ms archive
    /admin/content/view/full/2   143-158 ms disk    244 ms+    archive

Every kernel `require` goes through the phar wrapper, and the opcode cache does
not hold those entries the way it holds plain files.

So the conclusion of Part 2b stands and is now better founded: the archive is
worth having for distribution and for knowing the engine is exactly what was
built. It is not worth having for speed, and on uncached pages it costs.

### The panel says what it means now

Setup > System information, under **Phar App Engine**, states on the first line
which engine is running and why. A stale archive says what differs and what to
run:

    Archive is current: no
      Why: it was built from commit aae2c57d and the working tree is now at
      a9ff7b25; the working tree has uncommitted changes, so a rebuild will
      capture whatever is on disk right now.
      To fix: php bin/php/phar.php build --allow-root-user, then restart the
      application server so it opens the new archive.
      The site is running from this archive, so what is on disk is not what is
      being served.

That last line changes with whether anything is actually running from the
archive. A stale archive nobody is using is a note; one serving the site is not.

---

## Part 2d — Hardening, and the tests that go with it

### Everything a peer could make the server hold was unbounded

Open streams, a header block being assembled, a body being received, bytes
queued to send. Bounded since `qbix-webserver` 0.0.4.9:

| limit | default | what it stops |
|---|---|---|
| `concurrentStreams` | 128 | streams open at once |
| `headerListSize` | 64 kB | CONTINUATION with no END_HEADERS |
| `bodySize` | 64 MB | a body held whole before the application sees it |
| `readBuffer` | 4 MB | frames announced and never completed |
| `writeBuffer` | 8 MB | a peer that requests much and stops reading |
| `resetStreams` | 256 | opening and cancelling, repeatedly |
| `reflexFrames` | 1000 | PING and SETTINGS, which oblige an answer |
| `idleSeconds` | 120 | sockets held open saying nothing |

Overridable under `Q.web.http2.limits`; a configured zero is ignored rather
than read as unbounded.

None of these need an authenticated user, a malformed frame or a bug — they are
ordinary protocol use taken to excess, and **no request completes, so nothing
reaches an access log while it happens.**

Two are worth singling out. The stream limit had been advertised in SETTINGS for
months and never counted, so a peer that believed us was the only thing keeping
the number down. And the write buffer arrived with a bug fix: making large
responses survive a full send buffer means what will not fit waits in memory,
which hands a peer that stops reading an unbounded allocation.

### The HPACK decoder read past its buffer

Found by fuzzing, not by reading — reads as far as 119,315,352 bytes past the
end. On PHP 8 that is a warning per byte at a frequency the peer chooses, and
with `display_errors` it lands in the response body.

Quieter and worse: `substr()` past the end returns what there is, so a block
declaring a 200-byte value and supplying 12 decoded those 12 **as the value**.
A lying block did not fail; it produced a header nobody sent. The connection
closes with `COMPRESSION_ERROR` now, because HPACK's dynamic table is shared
across the connection and there is no partial recovery.

### A unit suite that needs no server

The existing `tests/run.sh` starts a server, binds a port and makes real
requests — right for testing a server, wrong for finding out the frame codec is
broken. It ran none of the HTTP/2 tests at all.

```bash
php vendor/se7enxweb/qbix-webserver/tests/run-unit.php
```

No socket, no certificate, no fixture directory, nothing installed but PHP.
`run.sh` calls it first.

    ok  http2-cookies · http2-errorpage 12 · http2-flow-control
    ok  http2-full-socket · http2-hpack 22 · http2-limits 10
    ok  unit-cache-key 23 · unit-frame 32 · unit-hpack-malformed 20

    9 passed, 0 failed, 1.1s

`http2-limits` mounts each attack and asserts refusal; asserting that a constant
exists would have passed against the state it replaced.

### What has not been audited

`Panel.php` (4,433 lines), `WebSocket.php`, `Trust.php` and `Autohost.php` have
had no security pass. Memory behaviour across a long soak has not been measured.
The known HTTP/2 exhaustion classes are bounded and each has a test — which is a
different and much smaller claim than "no remaining defects".

---

## Part 2e — What to say to upstream, and what not to

Everything below is measured on this installation. The numbers are real; the
framing matters, because half of them are about this application on this machine
and half are about the server itself. Only the second half is an argument for a
merge.

### The part that travels: eight defects, each with a test

These are in the server, not in anything this site does. Any installation has
them.

| | what it did |
|---|---|
| HTTP/2 flow control | any response larger than one window stopped at that window |
| HTTP/2 socket writes | `fwrite()` returning 0 treated as fatal — the rest of a large body silently discarded |
| HTTP/2 `Set-Cookie` | dropped entirely, so **signing in over h2 was impossible** |
| HPACK bounds | read up to 119 MB past the buffer; a block lying about a length produced a header nobody sent |
| Compat / `phar://` | code in an archive skipped the source transform — no `Cache-Control`, no `Set-Cookie` |
| Compat / `?->` | nullsafe call to a rewritten name produced a **parse error** |
| `SERVER_NAME` | carried the port, against the CGI contract |
| Resource limits | eight unbounded: streams, header block, body, read and write buffers, resets, reflex frames, idle sockets |

Three of those are worth a maintainer's full attention:

**Signing in did not work over HTTP/2.** The login returned 302 to the right
place and set no session. It hid because a browser already holding a session
kept working and a session from HTTP/1.1 is equally good over h2 — only a fresh
sign-in on an h2 connection could see it. Every private window, and every
automated test.

**Two truncation bugs are invisible from the machine running the server.** Over
loopback the kernel send buffer never fills, and curl and Chromium negotiate
flow-control windows large enough to swallow a whole response. Both faults need
either a real network or a client with a small window. A test suite made of curl
cannot find them; Firefox found them in minutes.

**The HPACK bounds bug was found by fuzzing, not by reading.** The code looks
complete. 600 random header blocks found reads far past the end, and the quiet
half — `substr()` past the end returning what it has — meant a lying block
decoded to a plausible header rather than failing.

### The part that travels: the tests

| | at v0.0.4.5 | now |
|---|---|---|
| self-contained test files | 2 | 11 |
| assertions | 34 | **186** |
| run time | — | 3.3 s |
| needs a server, cert or fixtures | — | no |

`tests/run.sh` never ran the HTTP/2 tests at all. `tests/run-unit.php` does, and
runs first, because there is no sense binding a port to discover the frame codec
is broken. Four of the files are regression tests for the defects above, and
three of them **found** defects in shipped code — which is the argument that
makes a test PR worth reading.

`tests/bench-load.php` needs nothing installed either. Neither `wrk`, `h2load`,
`hey` nor `k6` was present here, and `ab` cannot speak HTTP/2.

### The part that does not travel: this site's numbers

Quote these as "what one application achieved", never as "what the server
does". They are a property of this workload, this content and this machine.

    front page, cached          70-90 ms
    admin page, never cached    143-168 ms
    peak throughput             307 req/s at concurrency 16
    page weight                 4.4 MB -> 1.9 MB
    CSS and JS                  760 kB -> 219 kB, 9 requests -> 3

The improvements behind those are mostly application-side — asset packing, cache
warming, a page that ran `git status` on every view — and belong in this
repository, not upstream.

### The worker curve, measured in waves

11 cores, 46 GB, 167.6 MB PSS per worker.

| workers | peak req/s | load avg | resident |
|---|---|---|---|
| 8 | 233.3 | — | 1.7 GB |
| **16** | **307.0** | 7.8 | 1.5 GB |
| 32 | 303.4 | 15.6 | 2.9 GB |
| 64 | 272.8 | 10.5 | 3.2 GB |

**Sixteen is the optimum and the curve turns down after it.** Thirty-two bought
nothing and doubled the load average; sixty-four was worse than sixteen. The
useful figure is not the peak but that it arrives at roughly 1.5x the core
count and declines thereafter.

Beyond that the ceiling is memory, not speed, and it is arithmetic: at 167.6 MB
per worker, 128 workers wants 21 GB and starts swapping, 512 wants 84 GB, and
2500 wants 409 GB on a machine with 46. Those configurations are not slow, they
are impossible, and no benchmark is needed to say so.

### How to pitch it

Lead with the sign-in bug and the two truncation bugs. They are unambiguous,
they affect every installation, each has a test that fails without the fix, and
each was found by a method the project was not using — a real browser, a real
network, and a fuzzer.

Do not lead with throughput. A maintainer reading "307 req/s" has no way to know
whether that is good, and the honest answer is that it says more about this
site's templates than about the server.

Every fix is on a `pr/*` branch cut from the previous release, carrying only its
own change and its own test, and passing on a bare checkout.

---

## Part 2f — The tail, and why 9ms is the wrong target for this server

Two questions arrived together: *"I need like 9ms load time to ship"* and
*"how can we maintain the lower page speed in ms times over many reloads?"*
They look like one question. They are not, and separating them is most of the
work.

### The part that is physics

A browser trace showed a 304 completing in 11–13 ms. The same response,
measured on the server, costs **0.6 ms**. Everything else is the round trip
between that browser and this machine.

That is worth stating plainly because it bounds what any server change can do:
**a request that is actually sent cannot come back faster than the network
allows.** From the vantage point those traces were taken, the floor is around
10 ms, so 9 ms is not reachable for a request that leaves the machine. It is
trivially reachable for one that does not — which is what `max-age` is for, and
which a forced reload (⌘R, F5) deliberately defeats. The traces show
`If-Modified-Since` on a document that was eight seconds old and fresh for
another 292: that is a reload asking anyway, not a cache failing.

So the number to quote upstream is not the browser's. It is the server's, and
the server's is 0.6 ms — on a machine whose peers will have different networks
and the same CPU.

### The part that was ours, and was real

Between a 0.6 ms hit and a 1382 ms render sat three defects that made the page
meet the render far more often than it should have.

**A reload was sent the whole page.** The cache never read `If-None-Match` or
`If-Modified-Since`. Static files had answered conditional requests since the
beginning; cached pages never did. A reload now sends 202 bytes of headers and
no body.

**The validator moved even when the page did not.** Entries carried only
`Last-Modified`, which recorded when the *entry was stored*. Each rebuild moved
it, so the next reload revalidated, failed, and pulled the document down again
— a page nobody had edited cost full price once per lifetime, forever. A strong
ETag derived from the body (before compression, since gzip is not byte-stable)
holds steady across rebuilds:

```
rebuild 1  etag="ae368…-gzip"  last-modified=20:48:29
rebuild 2  etag="ae368…-gzip"  last-modified=20:48:30
rebuild 3  etag="ae368…-gzip"  last-modified=20:48:31
```

**The expiry stopped the site for everyone at once.** An entry expires at a
moment, so every request in flight missed together and each rendered the page.
Eight simultaneous requests at that moment went from *8 rendering at ~1900 ms
each* to *one rendering and seven served at ~62 ms*.

That third one shipped broken first, and the way it broke is the useful part:
the helper deciding whether to serve stale was correct, and the live server
still stampeded 8 of 8, because `get()` consulted it and then deleted the entry
anyway — the request that claimed the re-render removed the copy the others
were about to be served. The unit test passed the whole time, because it tested
the helper instead of `get()`. Rewritten to drive the real path, it fails 6
cases against that bug.

### The part that turned out to be the ceiling

Hunting the p99 produced the largest finding of the day, and it is not in the
cache at all.

```
                    with TLS          without TLS
concurrency   8     p99  108.1 ms     p99  4.9 ms
concurrency  16     p99  207.8 ms     p99  6.8 ms
peak                1481 req/s        2961 req/s
```

The tail grows at about 13 ms × concurrency, which is a serialized cost, not
load. Over six fresh connections:

```
dns 7.99 ms    tcp 0.32 ms    tls 20.96 ms    server 1.46 ms
```

The accept path is fine. The handshake is 21 ms on a link whose round trip is
0.3 ms, so it is CPU and scheduling rather than round trips — and session
resumption is not working at all: `openssl s_client -reconnect` reports `New`
six times and `Reused` never. Every connection pays a full TLS 1.3 handshake
against an RSA-2048 key.

For a server meant to impress peers on smaller and larger machines alike, that
is the number that matters, and it is the next piece of work: make resumption
issue usable tickets, move to an ECDSA certificate, and get the handshake off
the event loop so sixteen do not serialize.

### And one that was a single line

The listen backlog was PHP's default of 32. A burst larger than that does not
queue and does not fail — the kernel drops the SYN and the client retransmits a
second later:

```
                  backlog 32              backlog 1024
concurrency  64    745 req/s  p99 1067 ms   2703 req/s  p99 19.5 ms
concurrency 128    603 req/s  p99 1317 ms   2773 req/s  p99 37.9 ms
```

Throughput did not degrade at 64, it collapsed to a quarter, and the p99 became
a round thousand milliseconds — the retransmit timer, while the server sat
idle. A server that is fast until exactly 32 concurrent connections and then
appears to hang is very hard to diagnose from outside, because nothing in it is
slow. Now `Q.webserver.backlog`, default 1024.

### What to claim, and what not

Claim the server-side numbers, because they are ours and they reproduce:
0.6 ms cached, 202 bytes on a reload, one render per expiry instead of N,
p99 at concurrency 128 down from 1317 ms to 37.9 ms.

Do not claim 9 ms page loads. The honest sentence is that the server answers in
well under a millisecond and the rest is the client's network — which is both
more defensible and more useful to someone deciding whether to run this.

---

## Part 2g — The stress test, and what the numbers are

Run at the end of the session, after everything below. HTTP/1.1 against the
cached front page, one generator sweeping concurrency:

```
conc       req/s    p50 ms    p90 ms    p99 ms    max ms   errors
1         1811.5       0.5       0.7       1.4      10.4        0
8         2555.4       1.2       2.1       3.8       8.6        0
32        3211.0       4.3       6.8       8.1      20.0        0
64        2956.4       9.5      15.8      18.4      22.0        0
128       3081.6      18.6      30.9      36.8      40.4        0
256       2712.8      43.4      66.9      74.2      76.4        0

0 errors across 12,000 requests
```

No collapse at 64, 128 or 256 — which is the backlog fix holding. Before it,
concurrency 64 fell to 745 req/s with a p99 of 1067 ms, because the kernel was
dropping SYNs and clients were waiting out a retransmit timer.

One generator is itself a bottleneck, so the ceiling needs several:

```
 2 generators =  64 in flight   5,048.9 req/s   0 errors
 4 generators = 128 in flight   6,203.8 req/s   0 errors
 6 generators = 192 in flight   7,903.3 req/s   0 errors
 8 generators = 256 in flight   8,641.5 req/s   0 errors
10 generators = 320 in flight   8,700.6 req/s   0 errors
12 generators = 384 in flight   8,941.7 req/s   0 errors
14 generators = 448 in flight   8,489.9 req/s   0 errors
```

**8,941 req/s at 384 in flight**, zero errors anywhere, and the knee is between
384 and 448. The generators share the twelve cores with the server they are
measuring, so this is a floor rather than a ceiling. 17 processes, 1,498 MB
resident throughout.

Against 2,961 req/s at the start of the session, on the same machine, serving
the same page.

### Middle-Out, and why it is switched off

Shared-dictionary compression of stored entries, named after the compression in
*Silicon Valley*. The technique is real — Brotli's built-in dictionary and the
late SDCH are the same idea — and it measures well on this installation's own
pages: 1,626,195 bytes as plain gzip against 1,290,140 with a 32 KB dictionary,
**20.7% smaller**, 273 of 273 round-tripping exactly.

Switched on, it compressed **1 of 272 entries**.

The cache stores bodies in wire form — already gzipped, deliberately, so a hit
needs no compression work — and a dictionary cannot shrink gzip output. The
20.7% is what it saves on the *plain* HTML, and capturing it would mean storing
uncompressed bodies and re-compressing on every hit: 20% of cache memory bought
with CPU on every request, on a machine with 24 GB free.

So it ships implemented, tested, documented and disabled. That is the honest
outcome and a better one than a feature that looks enabled and does nothing —
which is what it was for about ten minutes, until the entries were counted.

### What to publish

The browser figure a visitor sees is a property of that visitor. The same page,
the same server, the same instant: 0.36 ms of server work and 12.4 ms of
network for one observer, something else for the next. These reproduce on
anybody's hardware:

```
time to first byte, returning visitor    0.10 ms
server processing, cached page           0.36 ms p50
CPU per cached request                   0.54 ms   (from 3.80)
peak throughput                        8,941 req/s (from 2,961)
p99 at 256 concurrent                     74 ms
reload of a held page                    202 bytes (from 9.7 KB)
an expiry, 8 concurrent                1 render    (from 8)
a cached 404                              27 ms    (from 758)
document, decompressed                69,101 bytes (from 94,128)
```
