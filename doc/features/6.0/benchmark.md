# Benchmark: `exp:benchmark`

This page is for site owners and developers who want to know how fast their installation is, which server
answers it faster, and whether a change made it slower. `exp:benchmark` ships with Exponential 6.0.15. It needs
nothing but PHP with the curl extension: no `ab`, no `wrk`, no external service.

It does three things:

- **http** (the default): requests pages and times them, against one server or several back to back (A/B).
- **kernel**: times the parts of a page inside PHP, without HTTP: boot, template render cold and warm, INI load,
  content fetch, node list, database round trip, cache write and read, image alias lookup.
- **baseline**: saves a run as JSON and compares a later run with it, exiting with status 1 when a metric got worse
  by more than a threshold. That is what a CI job or a deploy script checks.

Quick start, from the Exponential root directory:

```bash
./console exp:benchmark --allow-root-user                 # the front page, three menu pages, the admin login
./console exp:benchmark kernel --allow-root-user          # the parts of a page, in-process
./console exp:bench --help                                 # exp:bench is the short name
```

`php bin/php/benchmark.php ...` is the same command without the console.

## Safe by default

A benchmark is load. These rules keep a first try from hurting a site:

| Rule | Why |
|---|---|
| 100 requests per URL, 4 at a time, 5 warm-up requests | Polite enough for a shared server, enough for a median. |
| Read-only: `GET` only, no cookies unless you pass `--cookie`, nothing posted | Measuring never changes content. |
| Kernel mode only fetches existing nodes and objects | The only writes are cache entries: its own probe file (removed again) and any template cache-blocks a full view fills, as a visitor's request would. |
| Against a host other than this machine's loopback, more than 1000 requests per URL, a concurrency above 16, a warm-up above 100, or more than 20 000 requests in all needs `--force` | A typo should not become a load test of production. |
| A host the site does not serve (none of its siteaccesses' `SiteURL` or host matching settings name it) is refused unless its URLs are named with `--url` or `--urls-file` | You can not point the default page list at somebody else's server by accident. |
| TLS certificates are verified; `--insecure` turns that off for a self-signed server | |

`--cold-clear` is the one option that writes: it clears this installation's content view cache before the run. It
is refused for a server the installation does not serve. Do not use it on a server other people are working on;
`--cold` measures past the caches without clearing anything.

## HTTP mode

### What it requests

Without `--url`, the list is made from the site itself:

1. the front page `/`;
2. the first pages of the menu: the children of the front page node (`[SiteSettings] IndexPage`) that have a URL
   alias, by priority (`--pages=3`, `--pages=0` for none);
3. the admin login page `/<admin siteaccess>/user/login`, when siteaccesses are matched by URI (`--no-admin` to
   leave it out).

The server is `https://` plus the `SiteURL` of the siteaccess (`-s`/`--siteaccess` chooses another one), or
`--base=<url>`.

Name your own pages instead:

```bash
./console exp:benchmark --url=/ --url=/news --url=/news/some-article --allow-root-user
./console exp:benchmark --urls-file=benchmark-urls.txt --allow-root-user   # one URL or path per line, # comments
./console exp:benchmark --url=https://staging.example.com/ --allow-root-user  # an absolute URL names its server
```

### Warm and cold

| | What answers | Option |
|---|---|---|
| **warm** (default) | whatever answers a returning visitor: the response cache (Velocity, the HTTP cache), else the view cache | |
| **cold** | the application: every request carries a query string no cache has seen (`?_bench=<unique>`), which every response cache of Exponential passes by (`X-Exp-Cache: BYPASS (query string)`) | `--cold` |

`--cold` still uses the view cache and the compiled templates, as most real misses do. To measure a render with an
empty view cache, `--cold-clear` clears it once before the run and skips the warm-up; then the first request of
every page is the expensive one, and it shows in `max` and `p99`.

The `cache` column counts the `X-Exp-Cache` (or `X-Cache`) header of the answers: `HIT:100` means every request was
answered from the response cache, `BYPASS:50` that the cache was passed by, `-` that the server sent no such header.

### Comparing servers (A/B)

```bash
./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --allow-root-user
```

Every page is requested from every server, and the second table puts them side by side:

```
  A/B, measured back to back (B/A below 1 means B is faster for the median, above 1 more req/s):
  url       A                 B                      A median  B median    B/A  A req/s  B req/s    B/A
  /         www.example.com   www.example.com:8080       9.86      2.08  x0.21      368     1327  x3.61
```

How it keeps the comparison fair:

- **Back to back.** The requests of a page are split into rounds (`--rounds=2`). In each round every server gets
  its share, one after the other, and the order is reversed every other round. When the machine gets busier halfway
  through, both servers feel it; neither is measured only in the quiet minute.
- **Same client, same connections.** One pool of connections per server for the whole run, opened during the
  warm-up (which is at least one request per connection), so no measured request pays a TLS handshake. With
  `--no-keepalive` every request opens a new connection, which is what to use to measure the handshake.
- **Same request.** Same headers, same `Accept-Encoding` (`gzip` by default, `--encoding=br`, `--encoding=none`),
  same HTTP version (what curl negotiates, or `--http=1.1` / `--http=2`).

Judge an A/B only by runs made back to back. On a shared machine the background load moves results by 10 to 30 %
from one session to the next; a number from this morning compared with one from tonight says more about the
machine than about the servers.

### More options

| Option | Effect |
|---|---|
| `--requests=N`, `--concurrency=N`, `--warmup=N`, `--rounds=N` | the load, per URL and server |
| `--auth=user:password` | HTTP basic auth, e.g. for a staging site; or set `EXP_BENCHMARK_AUTH` so the password stays out of the shell history. It is never saved. |
| `--header="Name: value"` (repeat), `--cookie="..."` | extra headers; a session cookie measures a signed-in page. Cookie and Authorization values are saved as `(hidden)`. |
| `--resolve=www.example.com:443:127.0.0.1` | reach a host at another address, as `curl --resolve` |
| `--timeout=30` | seconds per request |
| `--tool=ab`, `--tool=wrk`, `--tool=oha` | use an installed load generator instead of the built-in client. The table shows `(ab)` in the cache column, and only what that tool reports (wrk runs for `--duration=10` seconds rather than a number of requests). The header line names the tools that are installed. |

## Kernel mode

```bash
./console exp:benchmark kernel --allow-root-user
./console exp:benchmark kernel --repeat=50 --node=144 --allow-root-user
./console exp:benchmark kernel --probe=render_warm --probe=db_query --allow-root-user
```

| Probe | What is timed |
|---|---|
| `boot` | a new PHP process from its start until the database is connected: autoload, settings, siteaccess, extensions |
| `process` | the same child process from start to exit, as the parent saw it (it includes `render_cold`) |
| `render_cold` | the full view of a node, rendered once in that fresh process: empty in-process caches, compiled templates as they are on disk |
| `render_warm` | the same full view in the benchmark's own process, after one untimed render |
| `ini_load` | `content.ini` read again from the INI cache |
| `content_fetch` | one content object and its attributes from the database |
| `node_list` | up to 20 children of the front page node |
| `db_query` | one indexed `SELECT` on the node table |
| `cache_write`, `cache_read` | a 16 KB entry written and read through the cluster file handler |
| `image_alias` | an image attribute fetched and one of its existing aliases looked up (never one that would have to be scaled) |

`boot`, `process` and `render_cold` start `--boot-repeat=5` processes; every other probe runs `--repeat=20` times.
Without `--node`, the render probes use the front page, unless its full view is nearly empty (a front page built from
layout blocks renders them in the page layout, not in the full view); then the menu page with the largest full view.
The note column names what was used. `mem peak` is the memory the probe needed above what was in use before it (on
PHP 8.0 and 8.1, which cannot reset the peak, it is the process's peak so far).

Kernel numbers are what a request costs before the web server, the response cache and the network add or remove
anything. Use them to see what a change in the code costs, and HTTP numbers to see what a visitor gets.

## Reading the results

```
  target            url        n   min  median   p95   p99   max  req/s  err  status   bytes  cache
  alpha.example     /        100  6.91    9.86  14.3  16.2  16.4    368    0  200:100  16.5k  HIT:100
```

- Times are milliseconds, measured by curl itself for each request (from the start of the request until the last
  byte), not by the loop around it.
- **median** is the typical request. **p95** and **p99** are what the slowest 5 % and 1 % wait at least; they show
  queueing, garbage collection, a cache that expired. Percentiles are interpolated between the two nearest samples,
  the method of numpy, R type 7 and Excel `PERCENTILE.INC`. With 100 requests, p99 is close to the maximum.
- **req/s** is successful requests divided by the time the batches took. It depends on `--concurrency`: four at a
  time and 10 ms each cannot exceed about 400.
- **err** counts transport errors and responses of 400 and above; the lines under the table name them. A refused
  connection has no time and is left out of the timings, so a broken server never looks fast.
- **bytes** is the mean size of a response as transferred (compressed when the server compressed it).

What usually matters most is the difference between a cache hit and a render, which is one or two orders of
magnitude. Measured on the demonstration server on 5 October 2026 (12 cores, shared with other work, load about 8),
100 requests per page, concurrency 4:

| | Apache + PHP-FPM, median | Velocity (:8080), median |
|---|---|---|
| warm, cached pages (4 pages) | 9.4 to 11.4 ms | 1.3 to 3.3 ms |
| cold (`--cold`), the same pages rendered | 70 to 173 ms | 78 to 216 ms |
| admin login page (never cached) | 45 ms | 39 to 41 ms |

So the response cache decides most of what a visitor waits for, and a single "which server is faster" number
misleads: the order of the two servers was, for most pages, the opposite for cached and for rendered pages.

## Saving a run and catching regressions

```bash
mkdir -p var/benchmark
./console exp:benchmark --save=var/benchmark/baseline.json --allow-root-user
# ... deploy a change ...
./console exp:benchmark --baseline=var/benchmark/baseline.json --allow-root-user
echo $?        # 0 no regression, 1 a metric regressed, 2 usage error or refused
```

Rows are matched by server and path (kernel rows by probe name). A metric counts as regressed when:

| Metric | Regressed when |
|---|---|
| median, p95 | it grew by more than `--threshold` percent (default 15) **and** by more than `--min-delta` milliseconds (default 1) |
| req/s (HTTP rows) | it fell by more than `--threshold` percent while the median grew by more than `--min-delta` |
| error rate | it rose by more than one percentage point |

The noise floor is there because small numbers jump: a database round trip of 0.2 ms that takes 0.3 ms once is 50 %
slower and means nothing. A page missing from the new run is listed, not counted. `--all-checks` shows every
comparison, not only the regressions.

Two saved runs can be compared without measuring anything, and a saved run printed again:

```bash
./console exp:benchmark compare var/benchmark/baseline.json var/benchmark/today.json --allow-root-user
./console exp:benchmark show var/benchmark/today.json --allow-root-user
```

### In CI

```bash
./console exp:benchmark kernel --repeat=30 --baseline=ci/benchmark-kernel.json --threshold=20 --json \
    --allow-root-user > benchmark.json
```

- `--json` prints the whole run (settings, rows, the comparison) on stdout; `--csv` prints the rows. Progress goes
  to stderr, so the output can be piped. `-q` silences the progress and keeps the result.
- Measure on the same machine as the baseline, with nothing else running, and keep the baseline in the repository
  of the project, not in `var/`. A baseline from another machine compares the machines.
- Kernel mode is the steadier check: no network, no web server, no response cache. HTTP mode on a CI runner that
  starts its own server (`php -S`, Velocity, a container) works the same way with `--base=http://127.0.0.1:<port>`;
  loopback has no request cap.
- Cache hits of 1 to 3 ms move by more than 15 % from run to run on a busy machine. For those, raise `--threshold`
  or `--min-delta`, or check rendered pages with `--cold`, whose numbers are larger and steadier.

The saved format has a version (`"format": 1`); a baseline of another version is refused rather than compared
wrongly.

## Files

| File | |
|---|---|
| `bin/php/benchmark.php` | the command (`exp:benchmark`, `exp:bench`) |
| `kernel/private/classes/commands/benchmark.php` | options, safety rules, output |
| `kernel/classes/expbenchmark.php` | statistics, formats, regression check (no I/O) |
| `kernel/classes/expbenchmarkhttp.php` | the HTTP client (curl_multi) and the parsers of ab, wrk and oha |
| `kernel/classes/expbenchmarkkernel.php` | the kernel probes |
| `tests/tests/kernel/classes/benchmark/` | the tests: statistics, comparison, formats and parsers without network; the HTTP client against a `php -S` server on 127.0.0.1 that the test starts and stops |

Run the tests: `php vendor/bin/phpunit tests/tests/kernel/classes/benchmark/`.

## Related pages

- [Console](../../bc/6.0/console.md): every command.
- [Velocity engines](../../bc/6.0/velocity-engines.md): the servers you will want to compare.
- [HTTP caching](../../bc/6.0/http-caching.md) and [cache warming](../../bc/6.0/http2-and-cache-warming.md): what the
  warm numbers measure.
