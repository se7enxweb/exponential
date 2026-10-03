# Velocity worker pool and compatibility layer

*Reference. Applies to: Exponential Velocity 0.0.4.42. History: [August 2026](../../history/velocity/2026-08.md) (octane), [7 to 21 September](../../history/velocity/2026-09a.md) (compat layer), [23 September](../../history/velocity/2026-09c.md) (memory fixes), [25 to 30 September](../../history/velocity/2026-09e.md) (zygote, non-root workers).*

## Model

A parent process loads the application once, optionally warms it, then forks **workers**. Each worker answers one request after another. After each request the worker is returned to the state it had when it was forked, so one request never sees another's variables. The parent accepts every connection and reads every request; static files, response-cache hits and the server's own pages are answered there, and only a request that runs PHP goes to a worker: the first idle one, checked to be alive before use. When every worker is busy the request waits in the parent (it is never refused for lack of a worker; the limit is `maxConnections`, beyond which the server answers `503`). A request that cannot be handed to a worker goes back to the front of the queue up to three times, then the client gets `502`.

Modes: **persistent** (default); **`forkPerRequest`** (one request per worker, then a fresh fork: the isolation of a fresh process at the cost of a fork per request); **php-cgi** carve-out for scripts that need a classic SAPI.

## Pool size

`--workers=N` sets it. Without it the server sizes the pool from the machine and from what a worker costs: a worker is assumed to hold at least what the parent holds after loading the application (never less than 8 MB); the count is what fits in the memory left after 1 GB is set aside, at most 8 per core and 64 in all, never fewer than 4. The count is then checked against file descriptor and process limits; on a terminal the server asks before starting a larger pool, and **it never prompts when nobody can answer** (a service manager starting it). Anything beyond the automatic ceiling is a deliberate `--workers`.

### What a worker costs (measured 2026-09-26, PSS from `/proc/<pid>/smaps_rollup`, 12-core Linux, PHP 8.5)

| | Private per worker | PSS per worker | Workers per GB |
|---|---|---|---|
| The server alone, one-line PHP page | 1.3 to 1.9 MB | 1.4 to 1.9 MB | about 530 |
| A full CMS (Exponential, about 600 classes) | about 10 MB | about 11 MB | about 90 |

Shared code and data loaded before the fork: about 20 MB for the server alone, 47 MB for the CMS, paid once. Memory is always judged as PSS or private, never RSS (RSS counts every shared copy-on-write page once per worker and overstates a pool many times). On an Exponential install a fixed pool of 590 held 7,094 MB; the dynamic pool held 626 MB. With the default event loop (`stream_select`, 1,024 descriptors) the pool has a ceiling just below 1,000 workers: 900 workers served 20,000 requests without failure, 1,000 reset every connection.

## Static and dynamic pools

Static (default): every worker is forked at start and stays. Dynamic (`spareWorkers` above 0): only the spare workers are forked at start; when all are busy one more is forked up to the pool size; a worker beyond the spare count that has been idle for `idleWorkerTimeout` seconds is retired, longest idle first. A busy worker is never retired; the pool never falls below the spare count.

```json
{ "Q": { "webserver": { "spareWorkers": 8, "idleWorkerTimeout": 60 } } }
```

A worker's death never costs a request that could be served: a `GET`, `HEAD` or `OPTIONS` it had not started answering runs once more on another worker; a `POST` is never run twice and gets a `502`. An idle worker that exits is noticed within two seconds, replaced and reaped (no zombies).

## Settings (all under `Q.webserver`)

| Setting | Default | Meaning |
|---|---|---|
| `spareWorkers` | `0` | Workers kept when idle; above 0 makes the pool dynamic |
| `idleWorkerTimeout` | `60` | Seconds a worker beyond the spare count may stay idle |
| `maxRequests` | `1000` | Requests a worker serves before it is replaced; `0` no limit |
| `requestTimeout` | `30` | Seconds a request may run before the client gets `504` and the worker is replaced; the request is not run again; `0` no limit |
| `workerMemoryCeiling` | `256` MB, or 3/4 of `memory_limit` if lower | Heap size past which a worker is replaced; `0` turns it off |
| `forkPerRequest` | `false` | One request per worker |
| `zygote` | `true` | Fork workers started after the pool from a zygote (below) |
| `warmup` | none | A script run once in the parent before forking |
| `keepGlobals` | `[]` | Globals a worker keeps between requests (`--keep-globals=A,B`) |
| `maxConnections` | `1024` | Connections open at once; beyond it `503` |
| `user`, `group` | owner and group of the document root | Who workers run as when the server is root |
| `allowRootWorkers` | `false` | Permit `user` root (`--allow-root-workers`) |
| `warmupAsUser` | `true` | Run the warm-up with the worker user's effective ids |
| `writable` | `[]` | More directories handed to the worker user at start |
| `reusePort` | `false` | `SO_REUSEPORT` on the HTTP and HTTPS listeners so several servers share a port |
| `eventLoop` | `"auto"` | `auto` (Revolt if installed, else `select`), `iopoll` (opt-in), `revolt`, `select`; env `QBIX_EVENT_LOOP` overrides |
| `debug` | `false` | Whether an uncaught error's message, file, line and trace are shown in the response |
| `Q.compat.statTtl` | `0` | Seconds (at most 10) a worker keeps file facts across requests |

From Exponential, the pool is set in `settings/velocity.ini` (`[ServerSettings]`: `Workers`, `SpareWorkers`, `Instances`, `Zygote`); see [Velocity engines](../../bc/6.0/velocity-engines.md).

## When a worker is replaced

After the request it has, a worker is replaced by a fresh fork when it has served `maxRequests` requests; when its heap is above `workerMemoryCeiling`; when its output buffers were left unbalanced; or when the application called `Q_WebServer_Pool::retireAfterResponse($reason)` (for code that can run only once per process, for example one that defines constants from the request). The reason is logged, for example `worker 12345 replaced after 812 requests: heap 402 MB over the 384 MB ceiling`. A worker still on one request after `requestTimeout` seconds is killed.

The control panel's Workers tab (API `POST /Q/api/workers/resize` with `{"workers": N}`) changes the size while the pool runs and can recycle one worker or all of them without a reload.

**Reload and stop**: `qbixconsole server:reload` (`qbixctl graceful`) re-executes the server: listeners are closed first, open connections get up to five seconds, workers three. The new server forks a new pool from the new code and configuration. `qbixctl restart` brings a server back with the options it was started with (release 0.0.4.40; a record is written next to the pid file as `<pid file>.json`).

## The zygote

`fork()` hands a new worker every descriptor the server holds, including every visitor's connection; a worker cannot close a TLS connection without writing a close_notify onto the shared socket. A dynamic pool forks exactly when connections are open, and measured: after a 20-second burst of 355 rendered pages up to 50 connections stayed in `CLOSE-WAIT` held by workers alone.

With `zygote` on, one extra process is forked at the end of startup, before the first connection is accepted. It closes what a worker closes and waits. Every later worker is forked by the zygote: the server hands the worker's socket end over a Unix control socket (`SCM_RIGHTS`, deadline five seconds for the whole hand-off), the zygote forks, and answers with the pid. The zygote never held a client connection, so its workers inherit none. The pool checks liveness by signal `0` and `/proc` (a zombie does not count). Requirements: PHP `sockets`, `pcntl`, `posix`. A start-up self-test hands a socket to itself and uses the zygote only when it arrives intact (the worker's connection is passed as a stream resource, which is intact on PHP 8.1 and later, so PHP 8.2 to 8.5 all run it). If a hand-off fails, the log says `zygote: the zygote did not fork a worker; forking workers from the server from now on` and no request fails.

Check it:

```bash
P=<server pid>
for c in $(ps -o pid= --ppid $P); do n=$(ps -o pid= --ppid $c | wc -l); [ $n -gt 0 ] && echo "zygote $c: $n workers"; done
ss -Htanp '( sport = :443 )' | grep -v LISTEN | grep -v "pid=$P,"      # connections held by anyone but the server
```

History: 0.0.4.29 turned it on by default without the check, so on PHP 8.2 and 8.3 each new worker answered one request and the rest were `502`; 0.0.4.30 ran without it there; 0.0.4.32 runs it on 8.2 to 8.5. If you run 0.0.4.29 on PHP 8.2 or 8.3 and cannot upgrade, set `Q.webserver.zygote` to `false`.

## The user workers run as

A server started as root keeps root only in the server process (ports, certificates, reloads). The zygote, every worker, every fork-per-request child and every scheduled task give it up right after the fork and before application code runs (`setgid`, `initgroups`, `setuid`, then a check that root cannot be regained; a process whose switch fails is ended). Who: first match wins.

| Where | User | Group |
|---|---|---|
| command line | `--user=NAME` | `--group=NAME` |
| configuration | `Q.webserver.user` | `Q.webserver.group` |
| environment, then the `envvars` file of the configuration tree | `QBIX_RUN_USER` (Velocity reads `VC_RUN_USER` first) | `QBIX_RUN_GROUP` (`VC_RUN_GROUP`) |
| default | owner of the document root | group of the document root |

A user or group that does not exist stops the start. `root` is refused unless allowed. With nothing configured and a root-owned document root, workers stay root as before and the start-up says so. The directories the server writes for workers (`Q.web.cache.dir`, `Q.web.appCache.dir`, `Q.webserver.precompress.dir`, `Q.webserver.writable`) are handed to that user at start. The worker user must be able to read the engine's source tree. The start-up prints `Workers as: <user>:<group> (...)`.

## Reset between requests

| State | How |
|---|---|
| Static class properties | Snapshot at startup, restored through cached reflection handles (0.28 ms for 424 classes, 0.91 ms for 586) |
| `$_GET`, `$_POST`, `$_COOKIE`, `$_FILES`, `$_SERVER`, `$_REQUEST` | Re-populated from the request |
| `$_SESSION` | `session_write_close()`, then emptied |
| Output buffers | One capture buffer per process, emptied each request; buffers a script leaves open are part of its response |
| Custom globals | Snapshot and restore (skipping superglobals and `keepGlobals`) |
| Shutdown functions, error and exception handlers, autoloaders, `ini_set`, `putenv` | Shimmed: collected or tracked per request and restored to the boot state |
| Response headers | Cleared |
| Cyclic garbage | Collected between requests |

Measured total: 0.55 ms median for the one-line page and 4.6 ms for the CMS (about 1.5 percent of a 300 ms render). What persists by design: OPcache, loaded classes, APCu, interned strings, autoloader maps. What needs care (the application's job): database connection transaction state, file handles, stream contexts, signal handlers, cURL handles, and registries in globals filled once with `include_once` (name them in `keepGlobals`).

### Why workers stay flat (fixed in 0.0.4.27)

Before 0.0.4.27 every request left an output buffer behind (about 2 MB a request on an Exponential install: one worker at 1.1 GB after 600 requests), every request left its error handler on PHP's internal handler stack (about 1 MB a request of debug log), the file wrapper leaked a registration on every filesystem call (up to 14,000 a request), cyclic garbage was not collected (about 47 KB a request), and workers ending deleted the server's pid file. All are fixed; a per-request health check (output stack back to the one empty capture buffer, heap under the ceiling) replaces a worker that fails it and logs why. Together a worker that grew 2 MB a request now grows about 2 KB.

## The compatibility layer

Source transformation (stream wrapper plus `token_get_all()`) rewrites calls to 44 functions that behave differently under a long-lived CLI process: `header`, `setcookie`, `setrawcookie`, `http_response_code`, `headers_sent`, `headers_list`, `header_remove`, the `session_*` family, `move_uploaded_file`, `is_uploaded_file`, `ini_get`, `ini_set`, `set_time_limit`, `getallheaders`, `apache_request_headers`, `phpinfo`, `register_shutdown_function`, the error and exception handler functions, `spl_autoload_register` and `unregister`, `putenv`, the file stat functions (`file_exists`, `is_dir`, `is_file`, `filemtime`, `filesize`, `clearstatcache`) and the process functions (`exec`, `system`, `passthru`, `shell_exec`, `proc_close`, `pclose`). `exit` and `die` are rewritten so they end the request, not the worker (also after a keyword or operator: `$db or die(...)`, `else exit;`).

Behaviour guaranteed by the layer (each fixed in a named release):

- A PHP file read with `file_get_contents()`, `md5_file()` or `fopen()` returns the file's own bytes; only `include` and `require` are transformed (0.0.4.33; it fixed Exponential's file-consistency check listing about 330 kernel files as modified).
- Files rewritten after start are picked up without a restart (the wrapper invalidates a script's compile when the mtime moves; included files are read once and kept, at most 4 MB per process, while a real stat in the same request still matches).
- `compress.zlib://`, `gzopen()` and `copy()` into or out of a `.gz` work through the file layer, as do stream options (0.0.4.38).
- Multipart fields with nested names reach `$_POST` as PHP builds them through `parse_str()`; `$_FILES` has PHP's layout (0.0.4.37).
- `session.save_path` is read in PHP's `[N;[MODE;]]/path` form and sessions are created with its mode (0.0.4.31); each session value is decoded from its own bytes without a warning (0.0.4.42).
- `REQUEST_TIME` and `REQUEST_TIME_FLOAT` are set per request (0.0.4.42).
- `Q.compat.statTtl` keeps what a worker knows about files (whether a path exists, mtime, size) across requests for up to that many seconds (like `opcache.revalidate_freq`); measured 13 percent less CPU per rendered page at one second. The worker's own writes, `clearstatcache()` and a program it runs are seen at once either way.

### Warm-up and preload

`Q.webserver.warmup` names a script the pool runs once in the parent after the source transform is installed and before it forks (typically it renders a representative page); every worker inherits the warmed arena copy-on-write (about 21 MB private per warm worker against about 209 MB unwarmed on a heavy app). Do not use `Q.webserver.preload` for the application: it is required before the transform exists, so workers would inherit a real `exit` and a `header()` that does nothing (the server warns at startup). Whatever the warm-up leaves becomes every worker's baseline, so a warm-up script resets user-class statics to their defaults and clears request globals, keeping only configuration and type registries. If a warm-up throws halfway, `Q_WebServer_WarmupGuard` restores the parent's state before the snapshot (0.0.4.36), so workers never begin every request as the failed render's request. Verify a warm-up by behaviour: several distinct URLs must return distinct content, each with a resolving stylesheet.

## Limits

- Event loop ceiling below about 1,000 workers (use `reusePort` or `Instances` instead).
- A POST is never retried.
- No fork on Windows.
- Per-request kernel caches keyed on request time need engine 0.0.4.42 or later (`REQUEST_TIME_FLOAT` per request).
