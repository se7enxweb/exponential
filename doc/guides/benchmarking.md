# Benchmarking Exponential: measuring, comparing and guarding speed

This guide teaches every way to measure the speed of an Exponential installation with `exp:benchmark`: pages over
HTTP through Apache or Velocity, cached and rendered; the parts of a page inside the kernel; pure-PHP hot paths with
no server and no database; saved runs, baselines and regressions; the speed card of the demonstration tour; and the
performance check the repository's CI runs on every push. It explains what each number means, how to compare two
runs or two machines without fooling yourself, and what to do when a number looks wrong.

It is written for operators who tune a site and for developers who want to know what a change costs. Read sections
1 to 3 first; after that every section stands on its own. Every command, option and default below was checked
against the code, and every output is real: it was printed by the commands on the demonstration server
(alpha.se7enx.com, 12 CPUs, shared with other work at a load of 5 to 11) on 5 October 2026, read-only, with long
lines shortened where marked.

[Guides](README.md) · Feature reference: [Benchmark: exp:benchmark](../features/6.0/benchmark.md) · In the
installation book: [10.12 Performance tuning](../install/10-after-installing.md#1012-performance-tuning)

## In short

- `./console exp:benchmark --allow-root-user` requests the front page, three menu pages, the search page and the admin
  login page, 100 times each after 5 warm-up requests, 4 at a time, and prints median, p90, p99, minimum, maximum and
  standard deviation per page.
- `--compare=https://site,https://site:8080` measures two servers back to back (Apache with PHP-FPM against
  Velocity); `--cold` measures rendered pages instead of cache hits.
- `exp:benchmark kernel` times the parts of a page in-process; `exp:benchmark micro` times pure-PHP hot paths with
  no web server and no database, normalised against a calibration loop so that runs on different machines compare.
- `--save=run.json` keeps a run, `--baseline=run.json` compares with it and exits 1 on a regression,
  `--format=json` prints everything, including the environment the run was made in.
- One run on a busy machine proves little: compare runs made back to back, with the same counts, the same pages and
  the same PHP set-up. Section 10 has a checklist.
- The repository's CI runs the micro mode against `tests/benchmark/baseline.json` as a warning-only check
  (section 13).

## Contents

- [1. What exp:benchmark measures, and what it does not](#1-what-expbenchmark-measures-and-what-it-does-not)
- [2. Before you start](#2-before-you-start)
- [3. The modes at a glance](#3-the-modes-at-a-glance)
- [4. HTTP mode: pages as a visitor gets them](#4-http-mode-pages-as-a-visitor-gets-them)
- [5. Kernel mode: the parts of a page](#5-kernel-mode-the-parts-of-a-page)
- [6. Micro mode: hot paths without a server or a database](#6-micro-mode-hot-paths-without-a-server-or-a-database)
- [7. Output: table, JSON and CSV](#7-output-table-json-and-csv)
- [8. Reading the numbers](#8-reading-the-numbers)
- [9. Comparing runs: baselines and regressions](#9-comparing-runs-baselines-and-regressions)
- [10. Noise, and how to reduce it](#10-noise-and-how-to-reduce-it)
- [11. Workflows, step by step](#11-workflows-step-by-step)
- [12. The tour speed card and "Measure again"](#12-the-tour-speed-card-and-measure-again)
- [13. The CI performance check](#13-the-ci-performance-check)
- [14. Troubleshooting](#14-troubleshooting)
- [15. Every option](#15-every-option)
- [References](#references)

## 1. What exp:benchmark measures, and what it does not

| It measures | It does not measure |
|---|---|
| The time from sending a request until the last byte of the answer arrived, per request, measured by curl itself (HTTP mode) | What a browser does afterwards: images, stylesheets, scripts, layout, painting. Use the browser's developer tools or Lighthouse for that. |
| The response cache, the view cache and the rendering, depending on what answers (warm or `--cold`) | The network between a visitor and the server: it measures from wherever it runs, usually the server itself |
| How many requests per second a server answers at the chosen concurrency | The most a server can carry: the default of 4 at a time is polite, not a load test |
| The kernel's own cost of boot, render, INI, database and cache operations, in-process (kernel mode) | Signed-in pages, unless you pass the session cookie (`--cookie`) |
| Pure-PHP code paths with fixed work, independent of content, database and server (micro mode) | Anything that writes: every mode is read-only apart from cache entries (see section 2) |

The command answers three questions well: *how long does a visitor wait for this page*, *which of two servers
answers it faster, measured the same minute*, and *did this change make anything slower*. It does not answer "how
many visitors can this machine take"; that needs a load generator against a copy of the site, and `--tool=wrk` or
`--tool=oha` (section 4.6) is the start of that, not the end.

## 2. Before you start

Run every command from the root of the installation (the directory with `console`, `index.php` and `settings/`).
`./console exp:benchmark` and `php bin/php/benchmark.php` are the same command; the short name `exp:bench` works too.
The examples add `--allow-root-user` because the demonstration server runs them as root; leave it out when you run as
the site user. As root, the command first prints `With great power comes great responsibility.` and waits ten
seconds.

What it needs: PHP with the curl extension. No `ab`, no `wrk`, no external service. HTTP and kernel mode need the
installation's database and settings; micro mode needs neither.

Exit codes, for scripts:

| Code | Meaning |
|---|---|
| 0 | the run finished; with `--baseline`, no regression |
| 1 | a metric regressed against `--baseline` (or, in `compare`, against the first file) |
| 2 | a usage error, a file that cannot be read, or a run refused by the safety rules |

The safety rules, so that a first try never hurts a site:

| Rule | Why |
|---|---|
| HTTP mode sends `GET` only, no cookies unless `--cookie`, never posts | measuring never changes content |
| Against a host other than this machine's loopback: more than 1000 requests per URL, more than 16 at a time, a warm-up above 100 or more than 20 000 requests in all needs `--force` | a typo should not become a load test of production |
| The default page list is only sent to hosts this installation serves (the `SiteURL` of its siteaccesses and the host matching settings); another host must be named with `--url` or `--urls-file` | you cannot point the defaults at somebody else's server by accident |
| TLS certificates are verified; `--insecure` turns that off for a self-signed server | |
| Kernel mode only fetches; its writes are its own cache probe file (removed again) and the template cache-blocks a full view fills, as a visitor would | |
| Micro mode writes compiled templates and one fixture template into `<cache directory>/benchmark-micro/`, and removes the directory at the end | |
| `--cold-clear` is the one option that clears something: the content view cache of this installation, once, before the run. It is refused for any server this installation does not serve. | do not use it on a server other people are working on |

## 3. The modes at a glance

| Mode | Command | Measures | Needs |
|---|---|---|---|
| http (default) | `exp:benchmark` | pages over HTTP, one server or several back to back | a running site |
| kernel | `exp:benchmark kernel` | boot, render cold and warm, INI, content fetch, node list, database, cache, image alias, in-process | the database |
| micro | `exp:benchmark micro` or `--micro` | template compile and render, INI parsing, autoload, `eZURI`, translations, datatype validation | PHP and the Composer packages only |
| compare | `exp:benchmark compare <baseline.json> <run.json>` | two saved runs against each other, measures nothing | two saved runs |
| show | `exp:benchmark show <run.json>` | prints a saved run as a table (or `--format=json/csv`) | a saved run |

The first word after the options is the mode; without one it is `http`, and `--micro` is the same as `micro`.

## 4. HTTP mode: pages as a visitor gets them

### 4.1 The page list

Without `--url`, the list is made from the site itself, in this order:

1. the front page `/`;
2. the first pages of the menu, which are content pages: the children of the front page node (`[SiteSettings]
   IndexPage`) that have a URL alias, ordered by priority and then node id. `--pages=3` is the default, `--pages=0`
   leaves them out;
3. the search page `/content/search?SearchText=<word>`, when anonymous visitors may search (the view requires
   `content/read`, or `content/search` is in `[RoleSettings] PolicyOmitList`). The word is the first word of three or
   more letters of the first menu page's name, `exponential` when there is none, or what `--search=<words>` says.
   `--no-search` leaves the page out;
4. the admin login page `/<admin siteaccess>/user/login`, when siteaccesses are matched by URI. `--no-admin` leaves
   it out.

The server is `https://` plus the `SiteURL` of the siteaccess (`-s <siteaccess>` picks another), or `--base=<url>`.

The list is the same in every run as long as the content is. For a list that never changes, name it:

```bash
./console exp:benchmark --url=/ --url=/news --url=/news/some-article --allow-root-user
./console exp:benchmark --urls-file=benchmark-urls.txt --allow-root-user     # one URL or path per line, # comments
./console exp:benchmark --url=https://staging.example.com/ --allow-root-user  # an absolute URL names its own server
```

Keep that file next to the baseline it belongs to.

### 4.2 Warm-up and measured runs

For every page and server, the command first sends `--warmup=5` requests that are not measured, then `--requests=100`
measured ones (`--runs` is the same option under a second name), `--concurrency=4` at a time. The warm-up is at least
one request per connection, so with `--concurrency=8` it is 8 even when `--warmup` says less: every connection is
open before the first measured request, and no measured request pays a TLS handshake.

The measured requests are split into `--rounds=2` rounds. With one server that changes nothing; with several
(section 4.4) each round gives every server its share back to back, and the order is reversed every other round.

For numbers that compare from one run to the next, keep `--runs`, `--warmup` and `--concurrency` the same. More runs
make the tail steadier: with 100 runs, p99 is nearly the maximum; with 1000 it starts to mean something.

### 4.3 Cached and rendered: warm, `--cold`, `--cold-clear`

| | What answers | Option |
|---|---|---|
| **warm** (default) | what a returning visitor gets: Velocity's response cache or the HTTP cache when the page is cached there, else the view cache | none |
| **cold** | the application: every request carries a query string no cache has seen (`?_bench=<unique>`), which every response cache of Exponential passes by (`X-Exp-Cache: BYPASS (query string)`) | `--cold` |
| **cold, empty view cache** | the first request of every page renders from scratch | `--cold-clear` (clears the content view cache once and skips the warm-up) |

`--cold` still uses the view cache and the compiled templates, as most real cache misses do. With `--cold-clear` the
expensive first request shows in `max` and `p99`, and the rest of the run is warm again.

The `cache` column counts the `X-Exp-Cache` (or `X-Cache`) answers: `HIT:40` means every request came from the
response cache, `BYPASS:20` that every request passed it by, `-` that the server sent no such header.

### 4.4 Two servers back to back: Apache and Velocity

```bash
./console exp:benchmark --compare=https://alpha.se7enx.com,https://alpha.se7enx.com:8080 --runs=40 --warmup=5 \
    --save=var/benchmark/ab.json --allow-root-user
```

On the demonstration server, Apache with PHP-FPM answers on 443 and Velocity on 8080, the same installation and the
same database. The output (the round lines left out):

```
exp:benchmark http  6 URL(s) x 2 server(s), 40 requests each, concurrency 4, warm-up 5, warm, tool curl  (installed: ab)
  saved: var/benchmark/ab.json
  http mode, web.my.se7enx.com, PHP 8.5.11, 2026-10-05T19:30:26-07:00
  php-cli, OPcache off, Xdebug off, 12 CPUs, load 9.59, commit 3a5029891e
  times in milliseconds; req/s counts successful requests; sd = standard deviation
  target                 url                                  n   min  median   p90   p99   max    sd  req/s  err  status  bytes  cache
  ---------------------  ----------------------------------  --  ----  ------  ----  ----  ----  ----  -----  ---  ------  -----  ------
  alpha.se7enx.com       /                                   40  7.51    10.7  18.0  29.7  30.1  5.31    307    0  200:40  16.6k  HIT:40
  alpha.se7enx.com:8080  /                                   40  1.16    2.55  3.44  4.17  4.35  0.69   1110    0  200:40  15.3k  HIT:40
  alpha.se7enx.com       /fitness                            40  6.53    8.65  10.9  13.2  13.4  1.59    415    0  200:40  11.9k  HIT:40
  alpha.se7enx.com:8080  /fitness                            40  0.85    1.38  1.80  2.62  2.85  0.39   2260    0  200:40  11.2k  HIT:40
  alpha.se7enx.com       /workout                            40  7.06    9.62  12.0  13.5  14.1  1.53    379    0  200:40  11.4k  HIT:40
  alpha.se7enx.com:8080  /workout                            40  0.63    1.27  2.12  2.40  2.45  0.45   2157    0  200:40  10.8k  HIT:40
  alpha.se7enx.com       /fit-healthy-site-info              40  7.48    10.5  15.7  16.5  16.5  2.66    333    0  200:40   9.7k  HIT:40
  alpha.se7enx.com:8080  /fit-healthy-site-info              40  0.72    1.84  2.71  3.05  3.13  0.61   1671    0  200:40   9.2k  HIT:40
  alpha.se7enx.com       /content/search?SearchText=fitness  40   285     334   396   407   409  32.8   11.3    0  200:40  11.1k  -
  alpha.se7enx.com:8080  /content/search?SearchText=fitness  40  1.01    2.06  3.73  4.16  4.17  0.98   1302    0  200:40  10.6k  HIT:40
  alpha.se7enx.com       /admin/user/login                   40  35.8    43.6  51.6  55.5  55.8  4.94   87.1    0  200:40   7.9k  -
  alpha.se7enx.com:8080  /admin/user/login                   40  34.2    39.9  49.6  92.6   101  12.3   87.9    0  200:40   7.9k  -

  A/B, measured back to back (B/A below 1 means B is faster for the median, above 1 more req/s):
  url                                 A                 B                      A median  B median    B/A  A req/s  B req/s      B/A
  /                                   alpha.se7enx.com  alpha.se7enx.com:8080      10.7      2.55  x0.24      307     1110    x3.61
  /fitness                            alpha.se7enx.com  alpha.se7enx.com:8080      8.65      1.38  x0.16      415     2260    x5.45
  /content/search?SearchText=fitness  alpha.se7enx.com  alpha.se7enx.com:8080       334      2.06  x0.01     11.3     1302  x114.90
  /admin/user/login                   alpha.se7enx.com  alpha.se7enx.com:8080      43.6      39.9  x0.91     87.1     87.9    x1.01
  (two rows of the A/B table left out)
```

What it says: for cached pages Velocity answers in 1.3 to 2.6 ms against Apache's 8.7 to 10.7 ms. The search page is
cached by Velocity's response cache (`HIT:40`) and rendered by Apache every time (`-`), hence the factor of 115: that
compares a cache with a render, not two engines. The admin login page is never cached; there the two are within
10 %.

The same pages rendered, past every response cache:

```bash
./console exp:benchmark --compare=https://alpha.se7enx.com,https://alpha.se7enx.com:8080 --url=/ --url=/fitness \
    --cold --runs=20 --warmup=2 --allow-root-user
```

```
  target                 url        n   min  median   p90   p99   max    sd  req/s  err  status  bytes  cache
  ---------------------  --------  --  ----  ------  ----  ----  ----  ----  -----  ---  ------  -----  ---------
  alpha.se7enx.com       /         20   139     158   193   206   208  19.4   20.3    0  200:20  16.9k  BYPASS:20
  alpha.se7enx.com:8080  /         20   196     258  2014  2046  2049   652   7.02    0  200:20  15.2k  BYPASS:20
  alpha.se7enx.com       /fitness  20  77.7    94.2   113   126   128  12.6   35.1    0  200:20  12.0k  BYPASS:20
  alpha.se7enx.com:8080  /fitness  20   103     131   149   157   158  16.3   25.8    0  200:20  11.2k  BYPASS:20
```

Here the order is the other way round: rendering, Apache was faster in this run. And the Velocity row of `/` has a
standard deviation (652 ms) larger than its median and a p90 of 2 seconds: a few requests waited about two seconds,
the rest took about 250 ms. That is the pattern of something occasional (a worker being started, a lock, a cache
being rebuilt), not of slow code; measure that page again before concluding anything, and look at the server's log
for the slow requests. Section 8 explains how to read such rows.

The lesson of both tables together: a single "which server is faster" number misleads. Measure cached and rendered,
and judge each page by its own row.

### 4.5 Requests, connections and headers

| Option | Default | Effect |
|---|---|---|
| `--concurrency=N` | 4 | requests at the same time, per server |
| `--no-keepalive` | keep-alive on | a new connection for every request, to measure the TLS handshake too |
| `--http=1.1`, `--http=2` | what curl negotiates | the HTTP version |
| `--encoding=gzip` | `gzip` | the `Accept-Encoding` sent; `--encoding=br`, or `none` for none |
| `--timeout=30` | 30 | seconds per request; a request that takes longer counts as an error |
| `--auth=user:password` | none, or the `EXP_BENCHMARK_AUTH` variable | HTTP basic auth, e.g. for a staging site; set the variable to keep the password out of the shell history. Never saved. |
| `--header="Name: value"` | none | an extra header; repeat for more. `Cookie` and `Authorization` values are saved as `(hidden)`. |
| `--cookie="..."` | none | a `Cookie` header, e.g. a session cookie to measure a signed-in page |
| `--resolve=host:port:address` | none | reach a host at another address, as `curl --resolve` |
| `--insecure` | verify | do not verify TLS certificates |

### 4.6 External load generators: `--tool=ab|wrk|oha`

When `ab`, `wrk` or `oha` is installed (the header line lists what it found: `(installed: ab)`), `--tool=` hands each
page to it instead of the built-in client and parses its summary into the same rows. Only what the tool reports is
filled in: the table shows `(ab)` where the cache column would be, `wrk` runs for `--duration=10` seconds rather than
a number of requests, and none of them alternates servers in rounds. Use the built-in client for comparisons and an
external tool when you want its own report or a higher load.

## 5. Kernel mode: the parts of a page

```bash
./console exp:benchmark kernel --allow-root-user
./console exp:benchmark kernel --repeat=10 --boot-repeat=3 --allow-root-user
./console exp:benchmark kernel --probe=render_warm --probe=db_query --node=144 --allow-root-user
```

| Probe | What one run is |
|---|---|
| `boot` | a new PHP process from its start until the database is connected: autoload, settings, siteaccess, extensions |
| `process` | the same child process from start to exit, as the parent saw it (includes `render_cold`) |
| `render_cold` | the full view of a node, rendered once in that fresh process |
| `render_warm` | the same full view in the benchmark's own process, after one untimed render |
| `ini_load` | `content.ini` read again from the INI cache |
| `content_fetch` | one content object and its attributes from the database |
| `node_list` | up to 20 children of the front page node |
| `db_query` | one indexed `SELECT` on the node table |
| `cache_write`, `cache_read` | a 16 KB entry written and read through the cluster file handler |
| `image_alias` | an image attribute fetched and one of its existing aliases looked up (never one that would have to be scaled) |

`boot`, `process` and `render_cold` start `--boot-repeat=5` processes; the other probes run `--repeat=20` times.
Without `--node`, the render probes use the front page, unless its full view is nearly empty (a front page built from
layout blocks renders them in the page layout); then the menu page with the largest full view. `--probe` (repeat it)
runs only the named probes. Output on the demonstration server:

```
  kernel mode, web.my.se7enx.com, PHP 8.5.11, 2026-10-05T19:38:55-07:00
  php-cli, OPcache off, Xdebug off, 12 CPUs, load 11.63, commit 38e10264bf
  probe           n   min  median   p90   p99   max    sd  ops/s  err  mem peak  note
  -------------  --  ----  ------  ----  ----  ----  ----  -----  ---  --------  ----------------------------------------------
  boot            3  66.7    74.2  75.1  75.3  75.3  4.66   13.9    0         -  new process until the database is connected
  process         3   455     531   542   545   545  48.6   1.96    0     16.0M  whole child process, start to exit
  render_cold     3   126     140   155   159   159  16.5   7.05    0     16.0M  node 26210, first full view in a fresh process
  render_warm    10  38.7    47.0  53.4  60.2  60.9  6.63   21.2    0    512.1k  node 26210, full view, 36.1 KB of HTML
  ini_load       10  0.29    0.31  0.38  0.67  0.70  0.12   2837    0     92.5k  content.ini from the INI cache
  content_fetch  10  0.05    0.06  0.16  0.92  1.01  0.30   6526    0     21.3k  object 25576 with its attributes
  node_list      10  0.23    0.26  0.59  2.62  2.84  0.81   1885    0         0  up to 20 children of node 89
  db_query       10  0.00    0.00  0.05  0.24  0.26  0.08  31798    0      2.9k  one indexed SELECT, sqlite
  cache_write    10  0.11    0.17  0.32  0.62  0.65  0.16   4387    0      1.2k  eZFSFileHandler
  cache_read     10  0.03    0.03  0.06  0.16  0.17  0.04  20965    0     29.3k  eZFSFileHandler
  image_alias    10  0.61    0.73  3.32  17.1  18.6  5.65    389    0    541.1k  attribute 172, alias small
```

Read it from the top: a fresh process costs 74 ms before it can do anything, the first render in it 140 ms, the same
render in a warm process 47 ms. That difference is what persistent workers (Velocity, PHP-FPM) and the opcode cache
save. `ops/s` is 1000 divided by the mean. `mem peak` is the memory the probe needed above what was in use before it
(on PHP 8.0 and 8.1, which cannot reset the peak, the process's peak so far). The note names the node, object, alias
and database used, so a later run can be checked against the same ones.

Kernel numbers are what a request costs before the web server, the response cache and the network add or remove
anything: the measure of what a code change costs. HTTP numbers are what a visitor gets.

## 6. Micro mode: hot paths without a server or a database

```bash
./console exp:benchmark micro --allow-root-user
./console exp:benchmark --micro --repeat=50 --warmup=5 --allow-root-user
./console exp:benchmark micro --probe=template_compile --probe=ini_parse --allow-root-user
php bin/php/benchmark.php micro --format=json > micro.json      # no console, no database
```

Micro mode times the kernel's pure-PHP work with fixed input: nothing depends on the content, the database, the web
server or the installation's override settings, so a laptop, a server and a CI runner all measure the same work. Each
probe does a fixed amount of work per iteration, runs `--warmup=3` iterations unmeasured and `--repeat=30` measured:

| Probe | One iteration (ops) | What it stands for |
|---|---|---|
| `template_compile` | five shipped templates (`navigator/google.tpl`, both page layouts, `content/edit.tpl`, `node/view/full.tpl`) parsed and compiled to PHP, anew every time (5) | the first request after a template change or a cache clear |
| `template_render` | the compiled `navigator/google.tpl` and a fixed template of loops, conditions, operators, escaping and a translation, rendered with fixed variables (2) | every page view that is not a cache hit |
| `ini_parse` | `site.ini`, `content.ini`, `template.ini`, `i18n.ini`, `image.ini`, `design.ini` and `menu.ini` of `settings/` parsed with `eZINI::parseFile()`, no INI cache, no override directories (7) | a request after the INI cache was cleared |
| `autoload_map` | `autoload/ezp_kernel.php` read (1) | the first class lookup of every request |
| `autoload_miss` | `class_exists()` of names nobody defines, through every registered autoloader (2000) | every "is this class there" check |
| `uri_parse` | ten module URLs with ordered and named view parameters parsed by `eZURI`, 50 times (500) | the start of every request |
| `i18n_load` | `share/translations/ger-DE/translation.ts` (2.4 MB) parsed, no translation cache, no extension translations (1) | a request after the translation cache was cleared |
| `i18n_lookup` | 300 messages of that file looked up by context and source, 10 times (3000) | every translated string |
| `datatype_validate` | 18 posted inputs validated by the integer, float, text line, email and date datatypes (valid and invalid values) and the integer's class settings, 20 times (360) | every save of an edit form |

The content class attributes the validation needs are built in memory; nothing asks the database.

Before the probes and again after them, a **calibration loop** is timed: fixed pure-PHP work (integer arithmetic,
string building, sorting 12 000 strings, a regular expression, hashing, an associative array). Every probe's median
divided by the calibration median is its `x calib` value. On a machine twice as fast both halve and the ratio stays;
that ratio is what section 13 compares.

```
exp:benchmark micro  30 measured and 3 warm-up iterations per probe, PHP 8.5.11, OPcache off
  micro mode, web.my.se7enx.com, PHP 8.5.11, 2026-10-05T19:33:47-07:00
  php-cli, OPcache off, Xdebug off, 12 CPUs, load 5.55, commit ec5699e4d1
  times in milliseconds per iteration; ops/s at the median; x calib = median / calibration loop median (9.359 ms, drift -7.8 %)
  probe               n   ops   min  median   p90   p99   max    sd    ops/s  x calib  err  note
  -----------------  --  ----  ----  ------  ----  ----  ----  ----  -------  -------  ---  ------------------------------
  template_compile   30     5  56.6    65.3  82.6  85.6  85.9  8.94     76.6    6.978    0  5 shipped templates, parsed ...
  template_render    30     2  4.47    6.24  6.93  7.48  7.52  0.68      320    0.667    0  2 templates rendered, compiled
  ini_parse          30     7  4.32    5.23  5.87  6.13  6.16  0.47     1337    0.559    0  7 files of settings/ (...)
  autoload_map       30     1  0.73    0.82  0.93  1.00  1.00  0.07     1217    0.088    0  autoload/ezp_kernel.php, 1322 classes
  autoload_miss      30  2000  0.84    0.96  1.09  1.16  1.18  0.09  2092688    0.102    0  2 registered autoloaders
  uri_parse          30   500  2.21    2.72  3.37  4.02  4.16  0.42   183599    0.291    0  10 module URLs, ...
  i18n_load          30     1   179     197   211   233   241  13.1     5.08   21.046    0  share/translations/ger-DE/...
  i18n_lookup        30  3000  3.27    3.39  3.77  4.08  4.12  0.22   885768    0.362    0  300 messages of ger-DE, 10 times
  datatype_validate  30   360  3.07    3.97  4.64  5.47  5.78  0.54    90725    0.424    0  18 inputs 20 times: ...
```

(This is the run committed as `tests/benchmark/baseline.json`; the notes are shortened.)

- The times are milliseconds per iteration; `ops` is the work one iteration does; `ops/s` is operations per second
  at the median (`ops × 1000 / median`).
- The header gives the calibration median and its **drift**, the change between the loop before and after the
  probes. More than about 10 % means the machine's load changed during the run; run it again.
- `template_render` says `compiled` in its note when template compilation is on, as in production; with
  `[TemplateSettings]` compilation off it says `interpreted`, and its numbers are not comparable with a compiled run.

Micro mode is for the question "did this change make the kernel's code slower", in CI and on a developer machine. It
says nothing about caches, the database or a server; for those use kernel and HTTP mode.

## 7. Output: table, JSON and CSV

`--format=table` (the default) prints the tables shown above. `--format=json` (or `--json`) prints the whole run as
one JSON document on stdout, `--format=csv` (or `--csv`) one line per row with a header line. With JSON or CSV the
progress lines go to stderr, so the output can be piped or redirected; `-q` silences the progress altogether.
`--save=<file>` writes the JSON document to a file in any format, creating the directory, and `show <file>` prints a
saved run again in any format.

```bash
./console exp:benchmark --runs=200 --warmup=10 --format=json --allow-root-user > var/benchmark/run.json
./console exp:benchmark show var/benchmark/run.json --format=csv -q --allow-root-user > run.csv
```

### 7.1 The JSON document

| Field | Content |
|---|---|
| `tool`, `format` | `exp:benchmark` and the format version, `1`. A baseline of another format version is refused rather than compared wrongly. |
| `mode` | `http`, `kernel` or `micro` |
| `started`, `machine`, `php`, `exponential` | when, where (host name), with which PHP and which Exponential |
| `settings` | the options the run was made with: counts, targets, cold, headers (secrets hidden), probes |
| `environment` | see 7.2 |
| `rows` | one per measured page and server, kernel probe or micro probe; see 7.3 |
| `pairs` | http with `--compare`: per page, each later server against the first (`base_median`, `other_median`, `median_ratio`, `base_rps`, `other_rps`, `rps_ratio`) |
| `tools` | http: the external load generators found, name => path |
| `calibration` | micro: the calibration loop's summary (`n`, `min`, `mean`, `median`, `p90`, `p95`, `p99`, `max`, `stddev`), `before_median`, `after_median`, `drift_pct` and the loop's `checksum` |
| `comparison` | with `--baseline`: the result of the comparison (section 9), with `baseline` (the file) and `baseline_started` |

### 7.2 The environment block

```json
"environment": {
    "date": "2026-10-06T02:33:47Z",
    "php": "8.5.11",
    "sapi": "cli",
    "engine": "php-cli",
    "opcache": false,
    "jit": false,
    "xdebug": false,
    "os": "Linux 5.14.0-687.5.3.el9_8.x86_64",
    "arch": "x86_64",
    "machine": "web.my.se7enx.com",
    "cpus": 12,
    "load": 5.55,
    "exponential": "6.0.15stable",
    "git_commit": "ec5699e4d13bb2b249a3fad20a53439f7f7e00bc",
    "ci": ""
}
```

- `date` is UTC. `engine` is `php-<sapi>`, with `(engine archive)` when the kernel ran from `engine.phar`.
- `opcache` is whether the opcode cache is active **in the PHP that ran the benchmark**, `jit` the `opcache.jit`
  mode when the JIT is on (else `false`), `xdebug` the `xdebug.mode` when Xdebug is loaded (else `false`). In HTTP
  mode these describe the client; the servers measured are named per row by their `Server` header (`server`).
- `load` is the one-minute load average when the run started.
- `git_commit` is read from `.git` without running git (a linked worktree and packed refs included), or taken from
  `GITHUB_SHA` in a GitHub workflow; empty when neither is there. `ci` is `github-actions`, `ci` (any `CI` variable)
  or empty.

The table output prints the same as one line: `php-cli, OPcache off, Xdebug off, 12 CPUs, load 9.59, commit
3a5029891e`. When Xdebug is loaded it says `Xdebug ON (...): times are not representative`.

### 7.3 The rows

Every row has `key` (what a later run is matched by: `<target> <name>`), `kind`, `target`, `name`, `tool`, `n` (the
samples), `min`, `mean`, `median`, `p90`, `p95`, `p99`, `max`, `stddev` (all milliseconds), `requests` (samples plus
failures), `errors`, `error_messages` and `rps`. Then, by kind:

| Kind | Further fields |
|---|---|
| `http` | `ttfb_median` (time to the first byte), `status` (code => count, `failed` for no answer), `bytes` (mean size as transferred), `cache` (`X-Exp-Cache` answer => count), `server` (the `Server` header most answers carried; empty when the server sends none, as Velocity does). `rps` is successful requests per second of wall time. |
| `kernel` | `mem_peak` (bytes), `note`. `rps` is 1000 / mean. |
| `micro` | `ops` (operations per iteration), `normalized` (median / calibration median), `note`. `rps` is ops per second at the median. |

A row saved before a field existed (a baseline from an older version has no `p90`) still loads; the missing value
prints as `-`.

## 8. Reading the numbers

| Number | What it tells you | Watch out |
|---|---|---|
| **median** | the typical request: half were faster, half slower | the number to compare; robust against a few outliers |
| **p90**, **p99** | what the slowest 10 % and 1 % waited at least: queueing, garbage collection, a cache that expired, a worker being started | with 100 runs p99 is nearly the max; with 20 it *is* the max |
| **min** | the best the server can do with nothing in the way | optimistic; good to see the floor |
| **max** | the worst single request | one event; never judge by it alone |
| **sd** | the standard deviation (sample, n - 1): how far requests scatter | small next to the median (10 % or less) = steady; as large as the median = two kinds of answer mixed (cache hit and render), or occasional stalls |
| **req/s** | successful requests per second at the chosen concurrency | bounded by the concurrency: 4 at a time at 10 ms each cannot exceed about 400 |
| **err**, **status** | transport errors and answers of 400 and above; the lines under the table name them | a refused connection has no time and is left out of the timings, so a broken server never looks fast |
| **cache** | which cache answered | compare only rows with the same cache answer |
| **x calib** (micro) | the median relative to the calibration loop | comparable across machines; the absolute times are not |

Percentiles are interpolated between the two nearest samples, the method of numpy's default, R type 7 and Excel's
`PERCENTILE.INC`. p95 is in the JSON and CSV output and is what the regression check uses besides the median.

Three patterns worth knowing:

- **Median low, p90 and p99 high, sd larger than the median** (the Velocity `/` row of the cold example in 4.4):
  most requests are fine and a few stall. Look for what happens occasionally: a worker started, a lock, a cache
  rebuilt, another process on the machine. Measure again before blaming code.
- **min close to median, sd small**: a steady answer. Changes of a few percent between runs are real only if they
  repeat.
- **A row with `HIT` next to a row with `-` or `BYPASS`**: the two measure different things (a cache and a render).
  The ratio between them is not the speed of the servers.

## 9. Comparing runs: baselines and regressions

### 9.1 Save, change, compare

```bash
mkdir -p var/benchmark
./console exp:benchmark --save=var/benchmark/before.json --allow-root-user
# ... make the change, deploy it ...
./console exp:benchmark --baseline=var/benchmark/before.json --save=var/benchmark/after.json --allow-root-user
echo $?        # 0 no regression, 1 a metric regressed, 2 usage error or refused
```

Rows are matched by `key` (server and path; kernel and micro rows by probe). For http and kernel rows a metric counts
as regressed when:

| Metric | Regressed when |
|---|---|
| median, p95 | it grew by more than `--threshold` percent (default 15) **and** by more than `--min-delta` milliseconds (default 1) |
| req/s (http rows) | it fell by more than `--threshold` percent while the median grew by more than `--min-delta` |
| error rate | it rose by more than one percentage point |

The noise floor is there because small numbers jump: 0.2 ms that becomes 0.3 ms is 50 % slower and means nothing. A
row missing from the new run is listed ("not measured this time"), not counted. `--all-checks` shows every
comparison, not only the regressions. Micro runs are compared differently, after normalisation (section 13.2), with
a default threshold of 40 %.

Two saved runs compare without measuring anything:

```bash
./console exp:benchmark compare var/benchmark/before.json var/benchmark/after.json --all-checks --allow-root-user
./console exp:benchmark compare var/benchmark/before.json var/benchmark/after.json --format=json --allow-root-user
```

`compare --format=json` prints only the comparison: `threshold`, `min_delta`, `regressions`, `checks` (each with
`key`, `metric`, `baseline`, `current`, `change_pct`, `regressed`), `missing`, `new` and
`environment_differences`; a micro comparison adds `method: "normalized"` and `calibration` (`baseline`, `current`,
`speed`).

### 9.2 A worked example: the same code, thirteen "regressions"

On the demonstration server, the A/B run of 4.4 was saved and repeated nine minutes later, with no change at all in
between:

```
  against var/benchmark/before.json: 13 regression(s), threshold 15 %, noise floor 1.0 ms
  row                                                   metric  baseline   now   change
  ----------------------------------------------------  ------  --------  ----  -------  ---------
  https://alpha.se7enx.com /fitness                     median      8.65  12.4   +43.6%  REGRESSED
  https://alpha.se7enx.com /fitness                     p95         12.5  18.6   +48.8%  REGRESSED
  https://alpha.se7enx.com /fitness                     rps          415   284   -31.6%  REGRESSED
  https://alpha.se7enx.com:8080 /fitness                p95         2.21  4.78  +116.3%  REGRESSED
  https://alpha.se7enx.com:8080 /workout                median      1.27  2.77  +117.8%  REGRESSED
  ...
  FAIL: slower than the baseline
```

Nothing got slower; the machine was busier (load 9 to 11 on 12 CPUs, other sites on it). Cache hits of 1 to 3 ms
double from one minute to the next on a shared machine, and 40 requests per page are too few to average that out.
What to do instead:

- compare **rendered** pages (`--cold`), whose numbers are larger and move less in percent;
- raise `--min-delta` (e.g. `--min-delta=5`) so that a few milliseconds on a cache hit never count;
- use more runs (`--runs=200`), and make before and after on a quiet machine, close together;
- for code changes, use kernel or micro mode, which have no network and no response cache in the way.

### 9.3 Comparing two machines

A baseline from another machine compares the machines. For http and kernel mode that is all you can get: run both
on the same day with the same options and read the difference as "machine plus set-up". For code, use micro mode,
which divides the machine's speed out (section 13.2), and look at the line `calibration loop: baseline ... ms, now
... ms (this machine x0.84 the speed of the baseline's)` for how the machines differ.

When the two runs were made with a different PHP minor version, OPcache, JIT or Xdebug setting, the comparison says
so, e.g. `note: the baseline was made with another set-up, OPcache: off -> on; the numbers compare the set-ups
too`.

## 10. Noise, and how to reduce it

| Source | Effect | What to do |
|---|---|---|
| Other work on the machine (the load in the header line) | 10 to 30 % from one session to the next on a shared server; mostly in p90 and p99; cache hits can double | measure when it is quiet; compare only runs made back to back; `--compare` alternates the servers within one run |
| CPU frequency scaling and turbo | the first seconds run at another speed than the rest | warm-up runs; on a dedicated benchmark machine the `performance` governor |
| Xdebug loaded | everything several times slower | never benchmark with it; the header says `Xdebug ON` |
| OPcache off (the command-line default) or another JIT mode | kernel and micro numbers change by a factor | keep the setting of the baseline; the comparison names a difference |
| Response cache, view cache, compiled templates | a hit is one or two orders of magnitude faster than a render | say what you measure: warm, `--cold`, `--cold-clear` |
| Too few runs | p99 of 100 runs is close to one outlier | `--runs=500` or more for tail latencies |
| Small numbers | 0.2 ms is 50 % of 0.4 ms | `--min-delta`; micro probes do enough work per iteration to take milliseconds |
| The network and TLS | handshakes and a distant client add their own time | measure from the server itself; keep-alive on; the warm-up opens the connections |
| Content that changed | another page list, other pages behind the same URL | `--urls-file`; note the commit and the date (the environment block does) |
| A different machine | everything | compare http and kernel only on the baseline's machine; micro divides the machine out |

### 10.1 Checklist for a fair comparison

1. Same machine (or micro mode), same PHP, OPcache, JIT and Xdebug setting: check the environment line of both runs.
2. Same command: the same pages (`--urls-file`), `--runs`, `--warmup`, `--concurrency`, warm or cold.
3. Nothing else running that you can stop; note the load in the header line of both runs.
4. Before and after made close together; for two servers, one `--compare` run, never two separate ones.
5. The caches in the same state: the same `cache` column in both runs.
6. Run it twice. A difference that does not repeat is noise.
7. Judge the median first, p90 and p99 second, max never alone.

## 11. Workflows, step by step

### 11.1 Before and after a code change

1. On the machine where you work, with the old code:
   `./console exp:benchmark micro --save=var/benchmark/micro-before.json --allow-root-user` and
   `./console exp:benchmark kernel --repeat=30 --save=var/benchmark/kernel-before.json --allow-root-user`.
2. Make the change (and deploy it, see 11.5).
3. `./console exp:benchmark micro --baseline=var/benchmark/micro-before.json --allow-root-user` and
   `./console exp:benchmark kernel --repeat=30 --baseline=var/benchmark/kernel-before.json --allow-root-user`.
4. A regression that repeats in a second run is real. Narrow it down with `--probe=<name>`.
5. Then the visitor's view: a cold HTTP run of the pages the change touches,
   `./console exp:benchmark --url=/the/page --cold --runs=50 --allow-root-user`, before and after.

### 11.2 Apache against Velocity

1. Both servers must serve the same installation (Velocity on its own port, e.g. 8080; see
   [Velocity engines](../bc/6.0/velocity-engines.md#running-them-side-by-side)).
2. Cached pages: `./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --allow-root-user`.
3. Rendered pages: the same with `--cold --runs=30`.
4. Read the A/B table per page, and only compare rows with the same `cache` answer (section 8).
5. Repeat at another time of day. Report both the cached and the rendered result; one number for "the faster server"
   misleads (section 4.4).

### 11.3 Tuning OPcache and Velocity

For PHP-FPM, OPcache is set in the pool's PHP settings; for Velocity in `settings/velocity.ini [PHPSettings]
IniOptions[]` (shipped with `opcache.enable_cli=1`, `revalidate_freq=0` and `file_update_protection=0`, and why, in
the file's comments), and the worker model in `[ServerSettings]` (`Workers`, `SpareWorkers`, `ForkPerRequest`,
`StatTtl`). To measure one setting:

1. Save a cold run of the pages that matter against the server you tune:
   `./console exp:benchmark --base=https://www.example.com:8080 --cold --runs=50 --save=var/benchmark/tune-before.json --allow-root-user`.
2. Change one setting, then restart that server so its workers start with it (`./console exp:velocity restart
   --allow-root-user` for Velocity, `systemctl reload <php-fpm unit>` for PHP-FPM; see
   [Deploying](deploying.md)).
3. `./console exp:benchmark --base=https://www.example.com:8080 --cold --runs=50 --baseline=var/benchmark/tune-before.json --all-checks --allow-root-user`.
4. Keep the setting only when the gain repeats in a second pair of runs. One setting per step.

The comments in `settings/velocity.ini` record measurements made exactly this way: the opcode cache alone took a
rendered content page from 527 ms to 391 ms (+26 % req/s), the tracing JIT made it slower again (417 ms), and
persistent workers (`ForkPerRequest=disabled`) took signed-in admin pages from 256-284 ms to 73-80 ms.

### 11.4 Choosing an engine

Velocity is the recommended engine for every stage; FrankenPHP and Apache with PHP-FPM are the alternatives
([Deploying](deploying.md#1-choose-how-the-site-is-served)). The three can run side by side on different ports, so
measure them on your own site before you decide:

1. Start the candidates on their own ports ([engines compared](../bc/6.0/velocity-engines.md)).
2. `./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080,https://www.example.com:8443 --allow-root-user`
   (three or more servers: each is compared with the first).
3. The same with `--cold`, and with a signed-in page (`--cookie=...`) if editors matter.
4. Weigh the cached numbers (what most visitors get), the rendered numbers (cache misses, editors) and the p99 (the
   worst experience), not one figure.

### 11.5 Checking a deploy did not regress

1. Before the deploy: `./console exp:benchmark --cold --runs=50 --save=var/benchmark/pre-deploy.json --allow-root-user`.
2. Deploy, e.g. `./console exp:velocity deploy --allow-root-user` ([Velocity engines](../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy)).
3. Warm the caches the deploy cleared (`php bin/php/warm.php --allow-root-user`), so you compare like with like.
4. `./console exp:benchmark --cold --runs=50 --baseline=var/benchmark/pre-deploy.json --min-delta=5 --allow-root-user;
   echo $?`. Exit 1 lists the regressed rows.
5. A deploy script can use the exit code; on a shared machine give it `--threshold=25` or more and a `--min-delta`, or
   it will cry wolf (section 9.2).

## 12. The tour speed card and "Measure again"

The demonstration server's guided tour shows a speed card after the Velocity stop: "The same pages, Apache and
Velocity". The administration dashboard of the same server shows the same numbers with a **Measure again** button.
Both belong to the demonstration site's own extension (`sevenx_alpha_settings`), which is not shipped with
Exponential; they are described here because they are the most visible use of `exp:benchmark`.

What they show: for each page of `showcase.ini [SpeedSettings] Pages[]` (on alpha: the front page, a landing page
built from layouts, the recipe list and the tour itself), the median time through Apache (`ApacheBase`) and through
Velocity (`VelocityBase`), once **cached** (a warm run, `BenchmarkRequests=20` requests per page and server) and once
**rendered** (a `--cold` run, `BenchmarkColdRequests=8`), and Apache's time divided by Velocity's. Under the table:
when it was measured, how long it took and how many requests each median is made of.

How it measures: **Measure again** posts to `showcase/measure` (it needs the `showcase/measure` policy), which runs
`./console exp:benchmark --url=<each page> --compare=<ApacheBase>,<VelocityBase> --requests=20 --concurrency=2
--format=json` (and the same with `--cold`), keeps the medians, p95, req/s, errors and cache answers in
`var/<site>/showcase/speed.json`, and returns to the dashboard. `showcase/speed` serves that file as JSON to the tour
and the dashboard; it reads the file and never measures, so visitors never cause requests. A measurement runs at most
once per `MinInterval` seconds (60) and only one at a time; a run that takes longer than two minutes is stopped. If
`exp:benchmark` fails, the card falls back to sequential curl requests (`Rounds=5` per page and server, server time =
time to the first byte minus the TLS handshake).

Its limits, which are the limits of any small benchmark:

- **Few requests.** 20 cached and 8 rendered requests per page: the median is fair, the p95 is close to the maximum.
- **One moment.** One measurement on a shared machine; the next one can differ by 30 % (section 9.2).
- **From the server itself.** No network between, so the times are the servers' own, not a visitor's.
- **Cached and rendered only.** No signed-in pages, no load.
- **A snapshot.** The card shows the last measurement until an administrator measures again.

For anything you want to decide on, run `exp:benchmark` yourself with more runs (section 11.2).

## 13. The CI performance check

### 13.1 What runs

The workflow `.github/workflows/performance.yml` (named "Performance", job "Micro benchmark (PHP 8.5)") runs on every
push and pull request to `main` that touches PHP files, templates, `settings/`, the German translation,
`tests/benchmark/` or the workflow itself, and by hand (workflow dispatch):

```bash
composer install --no-dev --prefer-dist --no-progress --no-interaction --ignore-platform-req=php --ignore-platform-req=ext-mongodb
php bin/php/benchmark.php micro --repeat=30 --warmup=3 \
    --baseline=tests/benchmark/baseline.json --all-checks \
    --save="$RUNNER_TEMP/benchmark-micro.json"
```

on PHP 8.5 with `opcache.enable_cli=0` and no Xdebug, as the baseline was measured. It writes the comparison and the
run into the job summary, prints a `::warning` annotation for every regressed probe, and keeps the run as the
artifact `benchmark-micro` for 30 days.

### 13.2 Calibration and normalisation

A CI runner is a shared virtual machine whose speed changes from one run to the next by more than any regression
worth catching. So the check does not compare milliseconds. In every run:

1. the calibration loop (section 6) is timed `--warmup` times unmeasured and `--repeat` times measured, then the
   probes run, then the loop is timed `--repeat` times again; the calibration median `C` is the median of all
   measured loop runs;
2. every probe's median `M` becomes `N = M / C`, the time of the probe in "calibration loops";
3. for every probe in both runs, the change is `(N_now / N_baseline - 1) × 100 %`.

A runner that is twice as slow at everything has `M` and `C` both doubled, so `N` and the change stay put. A change
that makes one probe slower while the loop stays the same raises `N`. The comparison prints the machine speed it
divided out:

```
  against tests/benchmark/baseline.json: 0 regression(s), threshold 40 % of the median relative to the calibration loop
  calibration loop: baseline 9.359 ms, now 11.121 ms (this machine x0.84 the speed of the baseline's)
  row                      metric        baseline     now  change
  -----------------------  ------------  --------  ------  ------  --
  micro template_compile   median/calib     6.978   5.641  -19.2%  ok
  micro template_render    median/calib     0.667   0.525  -21.3%  ok
  micro ini_parse          median/calib     0.559   0.620  +10.8%  ok
  micro autoload_map       median/calib     0.088   0.093   +5.4%  ok
  micro autoload_miss      median/calib     0.102   0.108   +6.2%  ok
  micro uri_parse          median/calib     0.291   0.251  -13.9%  ok
  micro i18n_load          median/calib    21.046  18.837  -10.5%  ok
  micro i18n_lookup        median/calib     0.362   0.304  -16.0%  ok
  micro datatype_validate  median/calib     0.424   0.282  -33.5%  ok
  PASS: no regression
```

The normalisation is not perfect: the loop is pure CPU work, while `ini_parse`, `i18n_load` and `template_compile`
also read files, and machines differ in their file systems and caches as much as in their CPUs. That is the reason
for the lenient threshold. Note the spread above, from -33 % to +11 %, between two runs of the same code on the same
machine.

### 13.3 The threshold

A probe regresses when its normalised median grew by more than 40 % (`expBenchmark::DEFAULT_MICRO_THRESHOLD`; pass
`--threshold=<percent>` for another), or when it failed more often than in the baseline. A probe in the baseline but
not in the run is listed, not counted; a new probe is listed until the baseline has it.

### 13.4 Why it is warning-only

A red check that turns out to be noise teaches everybody to ignore red checks. So the check cannot block anything
yet: the workflow is separate from PHPUnit and Quality, its job and its benchmark step have `continue-on-error:
true`, a regression shows as a yellow warning annotation and a warning in the job summary, and the workflow run stays
green. It has its own concurrency group, so a cancelled or slow run never holds up the others.

### 13.5 Making it required

Do it once several runs on `main` have stayed well inside the threshold (look at the `change` column of the job
summaries):

1. In `.github/workflows/performance.yml`, remove `continue-on-error: true` from the job `micro` and from the step
   "Micro benchmark against the baseline (warning only)", and rename the step.
2. If the runs show it is needed, set `--threshold=` in the same step.
3. In the repository's settings, branch protection of `main`: add "Micro benchmark (PHP 8.5)" to the required status
   checks.
4. Note in the workflow's header comment that it is blocking now.

### 13.6 Refreshing the baseline

When a change is meant to change the speed, when a probe was added or changed, or when PHP on the runners moves to
another minor version, refresh `tests/benchmark/baseline.json`. Best from a CI run of `main` after the change, because
that measures where the check runs:

```bash
gh run list --workflow=performance.yml --branch=main --limit=5
gh run download <run id> -n benchmark-micro -D var/tmp/benchmark-micro
cp var/tmp/benchmark-micro/benchmark-micro.json tests/benchmark/baseline.json
./console exp:benchmark show tests/benchmark/baseline.json --allow-root-user     # check it: 0 errors, every probe there
git add tests/benchmark/baseline.json
git commit -m "Updated: The micro benchmark baseline follows <the change that changed the speed>"
```

Or locally, on a quiet machine with PHP 8.5, OPcache off for the command line (the default) and no Xdebug:

```bash
php bin/php/benchmark.php micro --save=tests/benchmark/baseline.json
```

Commit the baseline on its own, with a message that says why the speed changed.

### 13.7 When it warns

1. Open the job summary: which probes, by how much, and the calibration speed line.
2. Is it one run? Re-run the job (Re-run jobs in the Actions tab). A warning that does not repeat is noise.
3. Does it repeat? Reproduce locally, before and after the change, with the probe alone:
   `./console exp:benchmark micro --probe=<probe> --repeat=50 --allow-root-user`, once on the commit before and once
   on yours, and compare the `x calib` values.
4. A real regression: fix it, or, when the cost is intended (a probe now does more work on purpose), refresh the
   baseline (13.6) and say so in the commit message.
5. Many probes slower at once with a large calibration speed difference: the runner type changed (another CPU
   family). Refresh the baseline from a CI run.

## 14. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| `REFUSED: <host> is not served by this installation` | the default page list goes only to this installation's own hosts | name the URLs with `--url` or `--urls-file`, or use `--base` with a host of a siteaccess |
| `REFUSED: --requests above 1000 against a host that is not loopback needs --force` (or concurrency, warm-up, total) | the safety caps of section 2 | lower the counts, measure via `127.0.0.1` with `--resolve`, or add `--force` if it is your server and you mean it |
| a row with `n` 0, `err` equal to the requests and `status` `failed:<count>` | the server is not reachable: wrong port, firewall, not running (a refused connection has no time, so it is not a sample) | `curl -I <url>`; the lines under the table name the curl error |
| `err` counts with `status` `401` | the site is behind HTTP basic auth | `--auth=user:password` or `EXP_BENCHMARK_AUTH` |
| `SSL certificate problem` in the error lines | a self-signed or expired certificate | fix the certificate, or `--insecure` for a test server |
| the search page is missing from the default list | anonymous visitors may not read content, or `--no-search` | check the anonymous role; or name the page with `--url` |
| the admin login page is missing | siteaccesses are matched by host, not by URI | `--url=https://admin.example.com/user/login` |
| cache hits differ by 50 to 100 % between two runs | load on the machine; small numbers | section 9.2: `--cold`, `--min-delta`, more runs, back to back |
| a row with sd larger than its median and a high p90 | occasional stalls among fast answers | measure again; look at the server's log for the slow requests (section 8) |
| `cache` shows `HIT` with `--cold` | a cache in front that keys on something else (a CDN, a proxy that ignores query strings) | measure the origin directly (`--resolve`), or bypass the proxy |
| `ERROR: Baseline not found or not readable` | wrong path, or the file is in a directory the user cannot read | check the path; `--save` creates the directory, `--baseline` does not |
| `ERROR: Saved with another format version than 1` | the baseline comes from an incompatible version of the command | make a new baseline |
| `ERROR: A micro run compares only with a micro run` | `--baseline` names an http or kernel run for a micro run | use the micro baseline |
| `ERROR: Both runs need a calibration loop ...` | a hand-made or truncated micro file | make the baseline with `micro --save` |
| `note: the baseline was made with another set-up, OPcache: off -> on` | the two runs differ in PHP, OPcache, JIT or Xdebug | make both runs the same way, or a new baseline |
| header says `Xdebug ON ... times are not representative` | Xdebug is loaded in the PHP that runs the command | run with a PHP without Xdebug, or `php -n` with the needed extensions |
| a micro probe shows `failed: ...` in its note and `err` = runs | the probe could not run (a file missing, a datatype not registered) | the note names the error; run from the installation root with the shipped `settings/` and `share/translations/` |
| `template_render` note says `interpreted` | template compilation is off in this siteaccess | compare only with runs made the same way; production has it on |
| kernel `boot` fails: `failed: could not start ...` | the PHP binary of the child process cannot be started | run with the PHP command-line binary of the installation |
| kernel `image_alias` row is empty: `no published image attribute found` | the site has no published image with an original | nothing to fix; the other probes still run |
| the command waits ten seconds before it starts | it runs as root | run as the site user, or accept the pause |
| the CI job shows a warning but the check is green | it is warning-only by design | section 13.7 |

## 15. Every option

Defaults in brackets. "http" options apply to the HTTP mode, "kernel" and "micro" to those modes; the rest to all.

| Option | Mode | Meaning |
|---|---|---|
| `--base=<url>` | http | the server [`https://` + `SiteURL` of the siteaccess] |
| `--compare=<a>,<b>[,...]` | http | two or more servers, measured back to back; each later one compared with the first |
| `--url=<url or path>` | http | a page to measure; repeat for more. An absolute URL names its server. |
| `--urls-file=<file>` | http | one URL or path per line, `#` starts a comment |
| `--pages=<n>` | http | menu pages in the default list [3] |
| `--no-search`, `--search=<words>` | http | leave out the search page; the words it searches for [first word of the first menu page] |
| `--no-admin` | http | leave out the admin login page |
| `--requests=<n>`, `--runs=<n>` | http | measured requests per page and server [100] |
| `--warmup=<n>` | http, micro | unmeasured requests per page and server, at least one per connection [5]; micro: unmeasured iterations per probe [3] |
| `--concurrency=<n>` | http | requests at the same time [4] |
| `--rounds=<n>` | http | rounds the requests are split into, server order alternating [2] |
| `--cold` | http | a unique query string on every request |
| `--cold-clear` | http | clear this installation's content view cache once before the run, no warm-up |
| `--insecure`, `--auth`, `--header`, `--cookie`, `--resolve`, `--http`, `--encoding`, `--timeout`, `--no-keepalive` | http | section 4.5 [verify, none, none, none, none, negotiated, gzip, 30, keep-alive] |
| `--tool=curl\|ab\|wrk\|oha` | http | the client [curl, built in] |
| `--duration=<s>` | http | seconds per page for wrk [10] |
| `--repeat=<n>` | kernel, micro | runs per probe [kernel 20, micro 30] |
| `--boot-repeat=<n>` | kernel | child processes for boot, process and render_cold [5] |
| `--node=<id>` | kernel | the node to render and fetch [front page, or the largest menu page] |
| `--probe=<name>` | kernel, micro | only these probes; repeat for more [all] |
| `--micro` | | the same as the mode `micro` |
| `--format=table\|json\|csv` | | the output [table]; `--json` and `--csv` are the same |
| `--save=<file>` | | write the run as JSON |
| `--baseline=<file>` | | compare with a saved run; exit 1 on a regression |
| `--threshold=<percent>` | | how much worse a metric may get [15; micro 40, after normalisation] |
| `--min-delta=<ms>` | | how many milliseconds a latency must grow by, too [1; not used by micro] |
| `--all-checks` | | show every comparison, not only the regressions |
| `--force` | http | allow more than the polite limits against a host that is not loopback |
| `-s <siteaccess>`, `-q`, `--allow-root-user`, `-d` | | the general options of every script: siteaccess, quiet (progress off, result kept), run as root, debug output |

## References

- [Benchmark: exp:benchmark](../features/6.0/benchmark.md): the feature reference, with the file list.
- [10.12 Performance tuning](../install/10-after-installing.md#1012-performance-tuning): the levers, in order of
  effect.
- [Deploying](deploying.md): Apache with PHP-FPM, Velocity, FrankenPHP; shipping a change.
- [Velocity engines](../bc/6.0/velocity-engines.md): the servers you will want to compare, deploying a PHP change,
  OPcache under Velocity.
- [FrankenPHP](../bc/6.0/frankenphp.md): the third engine.
- [HTTP caching](../bc/6.0/http-caching.md) and [cache warming](../bc/6.0/http2-and-cache-warming.md): what the warm
  numbers measure.
- [Quality checks](../features/6.0/quality-checks.md): the other checks the repository's CI runs.
- [Console](../bc/6.0/console.md): every command.
- Code: `kernel/private/classes/commands/benchmark.php` (options, safety rules, output),
  `kernel/classes/expbenchmark.php` (statistics, formats, comparisons, environment),
  `kernel/classes/expbenchmarkhttp.php` (the HTTP client), `kernel/classes/expbenchmarkkernel.php` (kernel probes),
  `kernel/classes/expbenchmarkmicro.php` (micro probes, calibration), `tests/benchmark/baseline.json`,
  `.github/workflows/performance.yml`, tests in `tests/tests/kernel/classes/benchmark/`.
- Percentiles: Hyndman and Fan, "Sample quantiles in statistical packages", *The American Statistician* 50 (1996),
  method 7.
