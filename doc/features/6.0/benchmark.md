# Benchmark: `exp:benchmark`

This page is for site owners and developers who want to know how fast their installation is, which server
answers it faster, and whether a change made it slower. `exp:benchmark` ships with Exponential 6.0.15. It needs
nothing but PHP with the curl extension: no `ab`, no `wrk`, no external service.

It does four things:

- **http** (the default): requests pages and times them, against one server or several back to back (A/B).
- **kernel**: times the parts of a page inside PHP, without HTTP: boot, template render cold and warm, INI load,
  content fetch, node list, database round trip, cache write and read, image alias lookup.
- **micro**: times pure-PHP hot paths of the kernel with neither a web server nor a database (template compile and
  render, INI parsing, autoload, `eZURI`, translations, datatype validation), normalised against a calibration
  loop so that runs on different machines compare. The repository's CI runs it on every push.
- **baseline**: saves a run as JSON and compares a later run with it, exiting with status 1 when a metric got worse
  by more than a threshold. That is what a CI job or a deploy script checks.

Quick start, from the Exponential root directory:

```bash
./console exp:benchmark --allow-root-user                 # the front page, three menu pages, the admin login
./console exp:benchmark kernel --allow-root-user          # the parts of a page, in-process
./console exp:benchmark micro --allow-root-user           # hot paths, no web server, no database
./console exp:bench --help                                 # exp:bench is the short name
```

`php bin/php/benchmark.php ...` is the same command without the console.

This page is the reference. The guide [Benchmarking Exponential](../../guides/benchmarking.md) teaches every use
step by step, with real sample output: reading the numbers, comparing runs fairly, workflows for code changes,
engines and deploys, the tour speed card, the CI check and troubleshooting.

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
2. the first pages of the menu, which are content pages: the children of the front page node
   (`[SiteSettings] IndexPage`) that have a URL alias, by priority and then node id (`--pages=3`, `--pages=0` for
   none);
3. the search page `/content/search?SearchText=<word>`, when anonymous visitors may search (the view needs
   `content/read`, or `content/search` is in `[RoleSettings] PolicyOmitList`). The word is the first word of the
   first menu page's name, or `--search=<words>`; `--no-search` leaves the page out;
4. the admin login page `/<admin siteaccess>/user/login`, when siteaccesses are matched by URI (`--no-admin` to
   leave it out).

The list and its order are the same in every run as long as the content is, so two runs measure the same pages in
the same order. For a list that never changes, name it in a file (`--urls-file`) and keep that file with the
baseline.

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
| `--requests=N` (or `--runs=N`), `--concurrency=N`, `--warmup=N`, `--rounds=N` | the load, per URL and server: measured requests, at the same time, unmeasured requests first (at least one per connection, so no measured request opens one), rounds the requests are split into |
| `--format=table`, `--format=json`, `--format=csv` | the output; the same as `--json` and `--csv` |
| `--auth=user:password` | HTTP basic auth, e.g. for a staging site; or set `EXP_BENCHMARK_AUTH` so the password stays out of the shell history. It is never saved. |
| `--header="Name: value"` (repeat), `--cookie="..."` | extra headers; a session cookie measures a signed-in page. Cookie and Authorization values are saved as `(hidden)`. |
| `--resolve=www.example.com:443:127.0.0.1` | reach a host at another address, as `curl --resolve` |
| `--timeout=30` | seconds per request |
| `--tool=ab`, `--tool=wrk`, `--tool=oha` | use an installed load generator instead of the built-in client. The table shows `(ab)` in the cache column, and only what that tool reports (wrk runs for `--duration=10` seconds rather than a number of requests). The header line names the tools that are installed. |

## Repeatable runs

A number means something only next to another one measured the same way. What keeps two runs comparable:

- **Fixed counts.** `--warmup=N` unmeasured requests, then `--runs=N` measured ones per page and server (kernel:
  `--repeat`, micro: `--repeat` and `--warmup`). Keep them the same from run to run; more runs make p99 steadier
  (with 100 runs p99 is nearly the maximum, with 1000 it means something).
- **Same pages, same order.** The default list is built the same way every time (see above); `--urls-file` pins it.
- **Every statistic.** Median, p90, p99, minimum, maximum and the standard deviation (`sd`, sample, n - 1) for every
  row, in the table, `--format=json` and `--format=csv`.
- **The environment is recorded.** `--format=json` and `--save` write an `environment` block next to the rows:

```json
"environment": {
    "date": "2026-10-06T02:30:26Z", "php": "8.5.11", "sapi": "cli", "engine": "php-cli",
    "opcache": false, "jit": false, "xdebug": false, "os": "Linux 5.14.0-687.5.3.el9_8.x86_64",
    "arch": "x86_64", "machine": "web.example.com", "cpus": 12, "load": 9.59,
    "exponential": "6.0.15stable", "git_commit": "3a5029891ec19884b64c8bf8e6bbeeb793b57c61", "ci": ""
}
```

  `opcache` and `engine` describe the PHP that ran the benchmark (for http mode that is the client; the servers
  measured are named per row by their `Server` header in the `server` field). The commit is read from `.git`
  without running git (a linked worktree and packed refs included), or from `GITHUB_SHA`. Comparing with a baseline
  made under another PHP minor version, OPcache, JIT or Xdebug setting prints a note naming the difference, because
  then the set-ups are compared, not the code.

### Noise, and how to reduce it

| Source | Effect | What to do |
|---|---|---|
| Other work on the machine (load average in the header line) | 10 to 30 % from one session to the next on a shared server; mostly in p90 and p99 | Measure when the machine is quiet; compare only runs made back to back; `--compare` alternates the servers within one run |
| CPU frequency scaling and turbo | the first seconds are faster or slower than the rest | warm-up runs; on a dedicated benchmark machine set the `performance` governor |
| Xdebug loaded | everything several times slower | never benchmark with it; the header says `Xdebug ON` |
| OPcache off (the CLI default) or a different JIT | kernel and micro numbers change by a factor | keep the setting of the baseline; the note on the comparison names a difference |
| Response cache, view cache, compiled templates | a hit is one or two orders of magnitude faster than a render | say which one you measure: warm (default), `--cold`, `--cold-clear` |
| Few runs | p99 of 100 runs is close to the maximum, a single outlier | `--runs=500` or more for tail latencies |
| Small numbers | a 0.2 ms difference is 50 % of 0.4 ms | the comparison's `--min-delta` noise floor (http, kernel); micro probes do enough work per iteration to take milliseconds |
| The network and TLS | handshakes and a remote client add their own time | measure from the server itself (loopback or its own name); keep-alive is on and the warm-up opens the connections |
| A different machine | everything | compare the http and kernel modes only on the machine of the baseline; the micro mode divides the machine out (see below) |

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

## Micro mode

```bash
./console exp:benchmark micro --allow-root-user
./console exp:benchmark --micro --repeat=50 --warmup=5 --allow-root-user
./console exp:benchmark micro --probe=template_compile --probe=ini_parse --allow-root-user
php bin/php/benchmark.php micro --format=json > micro.json      # without the console
```

No web server, no database, no response cache: only PHP and the kernel's code, so it runs the same on a laptop, a
server and a CI runner. Each probe does a fixed amount of work per iteration, runs `--warmup=3` iterations
unmeasured and `--repeat=30` measured.

| Probe | One iteration |
|---|---|
| `template_compile` | five shipped templates (`navigator/google.tpl`, both page layouts, `content/edit.tpl`, `node/view/full.tpl`) parsed and compiled to PHP, anew every time |
| `template_render` | the compiled `navigator/google.tpl` and a fixed template of loops, conditions, operators, escaping and a translation, rendered with fixed variables |
| `ini_parse` | seven files of `settings/` parsed with `eZINI::parseFile()`: no INI cache and no override directories, so every installation parses the same text |
| `autoload_map` | `autoload/ezp_kernel.php` read, as the first class lookup of a request does |
| `autoload_miss` | 2000 `class_exists()` of names nobody defines, through every registered autoloader |
| `uri_parse` | 500 `eZURI` parses of module URLs with ordered and named view parameters |
| `i18n_load` | `share/translations/ger-DE/translation.ts` (2.4 MB) parsed, no translation cache, no extension translations |
| `i18n_lookup` | 3000 lookups of its messages by context and source |
| `datatype_validate` | 360 validations of posted input by the integer, float, text line, email and date datatypes (valid and invalid values) and of integer class settings, against content class attributes built in memory |

The only writes are compiled templates and a fixture template in a directory of the cache directory
(`benchmark-micro/`), removed at the end.

```
  probe               n   ops   min  median   p90   p99   max    sd    ops/s  x calib  err  note
  template_compile   30     5  56.6    65.3  82.6  85.6  85.9  8.94     76.6    6.978    0  5 shipped templates, ...
  ini_parse          30     7  4.32    5.23  5.87  6.13  6.16  0.47     1337    0.559    0  7 files of settings/ ...
  i18n_lookup        30  3000  3.27    3.39  3.77  4.08  4.12  0.22   885768    0.362    0  300 messages of ger-DE, 10 times
```

- Times are milliseconds per iteration; `ops` is the work one iteration does, `ops/s` the operations per second at
  the median.
- **x calib** is the median divided by the median of the **calibration loop**: fixed pure-PHP work (integer
  arithmetic, string building, sorting, a regular expression, hashing, an associative array) timed before and after
  the probes in the same process. On a machine twice as fast both halve and the ratio stays; that ratio is what
  the CI check compares. The header line gives the calibration median and its drift between before and after; a
  drift of more than about 10 % means the machine's load changed during the run.

## Reading the results

```
  target                 url    n   min  median   p90   p99   max    sd  req/s  err  status  bytes  cache
  alpha.se7enx.com       /     40  7.51    10.7  18.0  29.7  30.1  5.31    307    0  200:40  16.6k  HIT:40
  alpha.se7enx.com:8080  /     40  1.16    2.55  3.44  4.17  4.35  0.69   1110    0  200:40  15.3k  HIT:40
```

- Times are milliseconds, measured by curl itself for each request (from the start of the request until the last
  byte), not by the loop around it.
- **median** is the typical request. **p90** and **p99** are what the slowest 10 % and 1 % wait at least; they show
  queueing, garbage collection, a cache that expired (p95 is in the JSON and CSV output). **sd** is the standard
  deviation: small next to the median means steady answers, as large as the median means the run was noisy or the
  page sometimes renders and sometimes comes from a cache. Percentiles are interpolated between the two nearest samples,
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

The repository runs the micro mode on every push and pull request that touches PHP, templates, settings or the
benchmark itself: the workflow `.github/workflows/performance.yml`, job "Micro benchmark (PHP 8.5)".

```bash
php bin/php/benchmark.php micro --repeat=30 --warmup=3 --baseline=tests/benchmark/baseline.json --all-checks
```

- **Normalised.** CI runners differ in speed from one run to the next. A micro run is compared with the baseline
  after dividing every probe's median by the run's own calibration median, so a slower runner is not a regression;
  a probe that got slower relative to the calibration loop is. The comparison prints the speed of this machine
  against the baseline's (`x0.86` = 14 % slower) and, per probe, the normalised medians and their change.
- **Lenient.** Only a probe more than 40 % slower after normalisation counts (`--threshold=40` is the micro
  default; http and kernel keep 15). A micro probe that fails, or fails more often than in the baseline, counts too.
- **Warning only.** The workflow is separate from PHPUnit and Quality and both its job and the benchmark step have
  `continue-on-error: true`: a regression shows as a warning annotation and in the job summary, the check stays
  green, and the main CI cannot be made red or flaky by it. The run is kept as the artifact `benchmark-micro`.
- **Making it required**, once several runs on `main` have stayed well inside the threshold: remove the two
  `continue-on-error: true` lines of `performance.yml` and add "Micro benchmark (PHP 8.5)" to the required status
  checks of the branch protection of `main`. Raise or lower `--threshold` in the same step if the runs show it is
  needed.

**Refreshing the baseline**, after a change that is meant to change the speed, after adding a probe, or when PHP is
upgraded. Best from a CI run of `main`, which measures where the check runs:

```bash
gh run list --workflow=performance.yml --branch=main --limit=5
gh run download <run id> -n benchmark-micro -D var/tmp/benchmark-micro
cp var/tmp/benchmark-micro/benchmark-micro.json tests/benchmark/baseline.json
```

or locally, on a quiet machine with PHP 8.5, OPcache off for the CLI (`php -d opcache.enable_cli=0`, the default)
and no Xdebug:

```bash
php bin/php/benchmark.php micro --save=tests/benchmark/baseline.json
```

Commit `tests/benchmark/baseline.json` on its own with a message that says why the speed changed.

The http and kernel modes work in CI the same way, without the normalisation:

```bash
./console exp:benchmark kernel --repeat=30 --baseline=ci/benchmark-kernel.json --threshold=20 --format=json \
    --allow-root-user > benchmark.json
```

- `--format=json` prints the whole run (settings, environment, rows, the comparison) on stdout; `--format=csv`
  prints the rows. Progress goes to stderr, so the output can be piped. `-q` silences the progress and keeps the
  result. On GitHub Actions every regression is also printed as a `::warning` annotation.
- Measure on the same machine as the baseline, with nothing else running, and keep the baseline in the repository
  of the project, not in `var/`. A baseline from another machine compares the machines.
- Kernel mode is steadier than http: no network, no web server, no response cache. HTTP mode on a CI runner that
  starts its own server (`php -S`, Velocity, a container) works with `--base=http://127.0.0.1:<port>`; loopback
  has no request cap.
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
| `kernel/classes/expbenchmarkmicro.php` | the micro probes and the calibration loop |
| `tests/benchmark/baseline.json` | the micro baseline the CI check compares with |
| `.github/workflows/performance.yml` | the CI check (warning only) |
| `tests/tests/kernel/classes/benchmark/` | the tests: statistics, normalisation, comparison, the environment block, formats and parsers without network; the HTTP client against a `php -S` server on 127.0.0.1 that the test starts and stops |

Run the tests: `php vendor/bin/phpunit tests/tests/kernel/classes/benchmark/`.

## Related pages

- [Benchmarking Exponential](../../guides/benchmarking.md): the guide, from the first run to the CI check.
- [Console](../../bc/6.0/console.md): every command.
- [Velocity engines](../../bc/6.0/velocity-engines.md): the servers you will want to compare.
- [HTTP caching](../../bc/6.0/http-caching.md) and [cache warming](../../bc/6.0/http2-and-cache-warming.md): what the
  warm numbers measure.
