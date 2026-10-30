# Speed of the three template engines: Twig, Smarty and Exponential templates

This page is for site builders, operators and developers who want to know, with numbers, how fast pages are when
their templates are Exponential templates (`.tpl`), Smarty templates (`.stpl`) or Twig templates (`.twig`). It
reports only what was measured, says under which conditions, explains what each number means and what it does not,
and shows how to repeat the measurements on your own installation. Nothing here is an estimate.

All measurements were taken on 29 October 2026 on a development installation: the media demo design (the Fit &
Healthy site) in three siteaccesses that show the same content, one with the original `.tpl` design, one with the
design converted to `.stpl`, and one with the design converted to `.twig` (517 templates each, converted with
`exp:template:convert`). The machine has 12 CPU cores and was shared with other work; the load average is given with
every run. Apache with PHP-FPM (PHP 8.5, port 443) and Exponential Velocity (port 8080) served the same installation
and the same database (SQLite).

## In short

- **Cached pages do not depend on the template engine.** A cached page is answered before any template runs. On
  Velocity the median server time of a cached page was 0.1 ms for all three engines; on Apache with PHP-FPM it was
  7.5 ms (`.tpl`) and 7.6 ms (`.twig`). What differs is the server, not the engine.
- **Uncached pages render within a narrow band.** With every response cache passed by, the median page time was 0.100
  to 0.115 s for `.twig`, 0.106 to 0.155 s for `.tpl` and 0.092 to 0.115 s for `.stpl` on Velocity; 0.070 to 0.092 s,
  0.090 to 0.109 s and 0.069 to 0.094 s on Apache (medians per page, 8 to 25 load).
- **Measured side by side in the same rounds**, an uncached `.twig` page took 0.83 to 0.98 times the time of the `.tpl`
  page and 0.90 to 1.19 times the time of the `.stpl` page.
- **With the debug output on** (every request renders the whole page and the debug report), `.twig` and `.stpl` pages
  took about the same time, 0.037 to 0.044 s median, and `.tpl` pages 0.059 to 0.110 s.
- **Most of an uncached request is not the template engine**: settings, translations, the database connection, the
  HTTP cache's rules and the debug report take more of the request than rendering the templates (profile below).

## What is measured, and the words used

| Term | Meaning |
|---|---|
| **cached** | the page is answered by Velocity's response cache or by the role-aware HTTP cache (`X-Exp-Cache: HIT`); no template runs |
| **uncached** | a unique query string on every request (`?bench=<n>`), so no response cache answers (`X-Exp-Cache: BYPASS (query string)`); the content view cache, cache blocks and compiled templates are used, as on most real cache misses |
| **debug render** | the client's address is in the debug list (`[DebugSettings] DebugByIP`, `DebugIPList[]`), so every response carries the debug report and bypasses the page caches (`X-Exp-Cache: BYPASS (debug report)`); this is what a developer sees, signed in, with the debug bar |
| **server time** | the page's time to first byte minus that of a static file fetched on the same connection just before, so the network round trip is taken out (it can be slightly negative when the static file was the slower one) |
| **total** | the client's whole request time, network included |
| **toolbar time** | the script time the debug bar shows ("Exp Debug ... s"): the time PHP spent on the request |
| **median, p95, p99** | half, 95 % and 99 % of the requests were at or below this time |
| **load** | the machine's load average (1 minute) at the start of the requests; 12 means all 12 cores busy |

## 1. Cached pages

**Conditions.** From a second server in the same network (round trip about 1 to 3 ms), whose address is not in the
debug list. 8 pages per engine (front page, three list pages, three articles, an author page), 200 requests per page
after 20 warm-up requests, three runs in a row: 600 timed requests per page, engine and server. Run on 29 October 2026
from 15:51 to 16:32, load 5.2 to 18.3 at the start of the blocks (other work was running on the machine). Data:
`var/tmp/bench-outside/20261029-155155.tsv` (one line per request) and `quiet-200x3.log` (the summary).

The author page is left out of the tables below: it answers with a redirect (302), which no cache keeps, so it measures
something else. The remaining 7 pages give 4,200 requests per engine and server.

**Velocity (port 8080), server time:**

| Engine | Median | p95 | p99 | Max | ≤ 13 ms | ≤ 20 ms | ≤ 33 ms |
|---|---|---|---|---|---|---|---|
| `.twig` | 0.1 ms | 3.0 ms | 20.4 ms | 805 ms | 98.4 % | 99.0 % | 99.6 % |
| `.stpl` | 0.1 ms | 3.2 ms | 25.7 ms | 88 ms | 98.0 % | 98.7 % | 99.5 % |
| `.tpl` | 0.1 ms | 2.8 ms | 17.2 ms | 1,112 ms | 98.6 % | 99.1 % | 99.7 % |

Total request time on Velocity, network included: median 3.0 ms (`.twig`), 2.2 ms (`.stpl`), 2.9 ms (`.tpl`).

Two `.twig` pages had every one of their 600 server times at or below 33 ms: the Fitness list (p99 3.9 ms, maximum
30.5 ms, 99.5 % at or below 13 ms) and the article "Why Track Your Workouts?" (p99 3.3 ms, maximum 17.7 ms, 99.8 % at
or below 13 ms).

**Apache with PHP-FPM (port 443), server time:**

| Engine | Median | p95 | p99 | Max | ≤ 13 ms | ≤ 20 ms | ≤ 33 ms |
|---|---|---|---|---|---|---|---|
| `.twig` | 7.6 ms | 22.5 ms | 50.6 ms | 2,065 ms | 88.5 % | 93.9 % | 96.9 % |
| `.tpl` | 7.5 ms | 18.9 ms | 45.6 ms | 140 ms | 90.9 % | 95.4 % | 98.0 % |

Total request time on Apache: median 10.8 ms (`.twig`) and 9.9 ms (`.tpl`).

The `.stpl` pages on Apache are not in the table: in two of the three runs the HTTP cache did not answer them
(`X-Exp-Cache: BYPASS (empty)` on 400 of 600 requests per page), so those requests were rendered, and their median
of 63 ms measures a render, not a cache. On Velocity all three engines were answered from cache throughout.

**What the numbers say:**

- The medians of the three engines are the same on each server: a cached request never reaches a template engine.
- Velocity answers a cached page in about a tenth of a millisecond of server time; most of the 2 to 3 ms total is the
  network. Apache with PHP-FPM needs about 7.5 ms before the HTTP cache answers, for every engine: PHP starts, reads
  the settings and the session, and only then reads the cache. That is the floor of Apache with PHP-FPM on this
  machine.
- The slow requests (p99, maximum) are rare and appear for all three engines alike; single maxima of 0.8 to 2 s are
  requests that waited for the busy machine, not slow templates. They are reported because they happened.

## 2. Uncached pages

**Conditions.** The same outside client, a unique query string on every request, 4 pages per engine, 100 requests per
page and server (2 × 50), on 29 October 2026 from 11:14 to 11:24, load 7.8 to 25.6. Data:
`var/tmp/bench-outside-uncached/20261029-111445.tsv` and its `.summary.txt`.

Median server time per page (the range is the fastest and slowest of the 4 pages):

| Server | `.twig` | `.tpl` | `.stpl` | p95, all engines |
|---|---|---|---|---|
| Velocity | 0.100 to 0.115 s | 0.106 to 0.155 s | 0.092 to 0.115 s | 0.17 to 0.26 s |
| Apache with PHP-FPM | 0.070 to 0.092 s | 0.090 to 0.109 s | 0.069 to 0.094 s | 0.11 to 0.25 s |

In this run Apache rendered faster than Velocity; under a load of 18 to 25 Velocity's workers compete with the other
work on the machine. Do not read a general "Apache is faster" from it: the benchmarking guide shows runs where it was
the other way round, and cached pages, which most visitors get, were served faster by Velocity above.

This run, and the side-by-side runs below, were made before the render-path change of section 3; the debug render
numbers of section 3 show the engines after it.

**Side by side in the same rounds.** Uncached times drift with the load of the machine, so a fairer comparison asks
for the same page from the three engines back to back in every round, in a rotating order, so a load spike hits all
three alike. 20 rounds per page, requests from the machine itself (its address is in the debug list, so each response
also carries the debug report), on 29 October 2026 at 12:04 (Velocity) and 12:05 (Apache), load 13 to 15. Data:
`var/tmp/bench-interleaved/20261029-120441.tsv` (Velocity) and `20261029-120506.tsv` (Apache).

| Server | Page | `.twig` median | `.tpl` median | `.stpl` median | `.twig` / `.tpl` | `.twig` / `.stpl` |
|---|---|---|---|---|---|---|
| Velocity | Fitness (a list) | 0.101 s | 0.114 s | 0.089 s | 0.88 | 1.13 |
| Velocity | an article | 0.113 s | 0.125 s | 0.095 s | 0.90 | 1.19 |
| Apache | Fitness | 0.127 s | 0.130 s | 0.120 s | 0.98 | 1.06 |
| Apache | an article | 0.150 s | 0.181 s | 0.167 s | 0.83 | 0.90 |

An uncached `.twig` page was 2 to 17 % faster than the `.tpl` page and between 10 % faster and 19 % slower than the
`.stpl` page. Differences of this size change from run to run: read them as "about the same as `.stpl`, a little
faster than `.tpl`", not as exact factors.

## 3. The debug render: what a developer sees

**Conditions.** Requests with the debug output on, from the machine itself, 100 requests per page and engine,
measured as the toolbar's script time and the total time.

| Run (29 October 2026) | Server | Load | `.tpl` toolbar median | `.stpl` toolbar median | `.twig` toolbar median | Data |
|---|---|---|---|---|---|---|
| 16:25 | Velocity | 4.5 to 12.9 | not measured | 38 to 40 ms | 37 to 39 ms | `var/tmp/debug-view/20261029-162548.tsv` |
| 16:40 | Apache | 4.9 to 16.7 | not measured | 42 to 44 ms | 42 to 43 ms | `var/tmp/debug-view/20261029-164016.tsv` |
| 17:29 | Velocity | 4.6 to 8.1 | 71 ms | 38 ms | 39 ms | `var/tmp/debug-view/20261029-172903.tsv` |
| 17:39 | Apache | 3.7 to 9.0 | 60 ms | 39 ms | 44 ms | `var/tmp/debug-view/20261029-173946.tsv` |
| 17:52 | Velocity | 4.1 to 16.6 | 110 ms | 37 ms | 38 ms | `var/tmp/debug-view/20261029-175207.tsv` |
| 18:03 | Apache | 4.2 to 9.5 | 63 ms | 34 ms | 38 ms | `var/tmp/debug-view/20261029-180311.tsv` |

The front page and the Fitness list were measured; the ranges cover both. The total request times of the runs from
17:29 on (`var/tmp/trpc/owner-view-c-172903.log`, `owner-view-d-175207.log`) have the same shape: medians of 0.037 to
0.044 s for `.twig` and `.stpl`, 0.059 to 0.110 s for `.tpl`.

**Before the render-path change.** Until the afternoon of 29 October, a compiled `.twig` looked up every bridged
operator and template function in the Twig environment on each call, and the first lookup of a request made Twig set
up all of its extensions. On the debug render path of a `.twig` page the script time was 82 to 113 ms, against 25 to
36 ms for the same page in Smarty; with direct calls from the compiled template it went to 25 to 36 ms, the same as
Smarty (measured when the change was made). Runs made before it, at 12:13 under a load of 17 to 22
(`var/tmp/debug-view/20261029-121332.tsv`), show toolbar medians of 80 to 96 ms for `.twig`, 78 to 81 ms for `.stpl`
and 148 to 172 ms for `.tpl`: under that load every engine was slower, so compare engines within a run, never across
runs made at different loads.

## 4. Why the numbers look like this

**Cached pages are engine-independent.** Velocity's response cache and the role-aware HTTP cache keep the whole
response and answer before the kernel loads a template. Which engine wrote the page once does not matter for the next
thousand requests. To make a site fast, make sure its pages are cached first; the engine comes later.

**Why `.tpl` is slower in a full render.** The converted `.stpl` and `.twig` designs keep the whole page in one cache
block, keyed by node, address, page content and the visitor's roles, with the visitor's own menu rendered fresh after
it ([Caching](twig-templates-caching.md), [Smarty templates: caching](smarty-templates-caching.md)). A rendered request
then reads one cache entry for the page frame. The original `.tpl` design reads several cache blocks and renders the
parts between them on every request. The same pattern written in a `.tpl` pagelayout would close most of that gap;
the engines' own rendering differs much less than the designs' caching.

**What Twig does to be fast.** Each `.twig` is compiled once to a PHP class; the compiled template calls the
template system's operators and functions as static methods, without looking them up; the logic, arithmetic and
control operators, `wash`, `implode`, `contains` and `concat` run directly in PHP; constant translations are done when
the template is compiled; one Twig environment is kept per process, so Velocity's persistent workers build it once
([Performance](twig-templates-performance.md)).

**Where an uncached request spends its time.** A sampling profile of Velocity serving the `.twig` front page with
the debug output on (29 October 2026, 9,384 samples, `var/tmp/trpc/vprof-twig-1.txt`), as inclusive shares of the
request (shares overlap, a function's share includes what it calls):

| Part of the request | Share |
|---|---|
| translator setup (loading the translation files) | 13 % |
| the HTTP cache's rules for the response (`ezpHttpCacheListener::contract`, building its configuration) | 13 % |
| connecting to SQLite and applying its settings | 11 % |
| the debug report | 11 % |
| loading template classes (autoload) | 5 % |
| all compiled templates together, the `.twig` and the `.tpl` they include | 24 % |

Rendering is about a quarter of such a request. The rest is work every engine pays: it is the same for `.tpl`,
`.stpl` and `.twig`.

## 5. In progress (not shipped)

These are being worked on after the measurements above and are **not** part of any release yet. They are listed so
the numbers are read in context, not as a promise:

- Memos kept by Velocity's workers between requests for values that do not change from one request to the next (the
  translator setup, the HTTP cache's rule configuration).
- Keeping the SQLite connection of a Velocity worker open between requests instead of connecting and applying its
  settings on every request.
- Serving visitors whose address is in the debug list from the page caches too, where today the debug report makes
  every such response bypass them.

## 6. Repeating the measurements

Use `exp:benchmark` ([Benchmarking](benchmarking.md)); compare engines in the same run, with the same counts and the
same caches warm. The examples assume three siteaccesses with the same content, matched by path: the `.tpl` design at
`/`, the `.stpl` design at `/<stpl siteaccess>/` and the `.twig` design at `/<twig siteaccess>/`.

**Cached pages**, from a client whose address is not in the debug list (else every response bypasses the caches),
Apache and Velocity back to back:

```bash
./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 \
    --url=/fitness --url=/<stpl siteaccess>/fitness --url=/<twig siteaccess>/fitness \
    --runs=200 --warmup=20 --save=var/benchmark/engines-cached.json --allow-root-user
```

Check the `cache` column: `HIT:200` means every request was answered from cache. A row with `BYPASS` measures
something else.

**Uncached pages**: the same with `--cold` (a query string no cache has seen on every request).

**The debug render**: run the same command from a client in the debug list. The `cache` column then says
`BYPASS:<n>`, and the times are those of a full render with the debug report. On the development installation, from
the machine itself, at load 6.1 on 29 October 2026 at 19:15 (Apache, 40 requests each, concurrency 2):

```bash
./console exp:benchmark --url=/fitness --url=/smarty/fitness --url=/twig/fitness --runs=40 --warmup=5 \
    --concurrency=2 --allow-root-user
```

```
  target            url               n   min  median   p90   p99   max    sd  req/s  err  status  bytes  cache
  ----------------  ---------------  --  ----  ------  ----  ----  ----  ----  -----  ---  ------  -----  ---------
  www.example.com  /fitness         40  57.4    73.6  92.2   107   108  11.6   25.2    0  200:40  12.2k  BYPASS:40
  www.example.com  /smarty/fitness  40  43.5    60.6  81.4  95.4  97.1  13.4   31.9    0  200:40  11.9k  BYPASS:40
  www.example.com  /twig/fitness    40  39.6    53.6  60.9  63.7  63.8  6.01   36.2    0  200:40  11.9k  BYPASS:40
```

**Where the time goes in one page**: set `[TwigSettings] Profile=enabled` with the debug output on; each page writes
a "twig profile" notice ([Debugging Twig templates](twig-templates-debugging.md#4-the-twig-profile)).

**Without a server**: the performance guard renders the same templates as `.tpl` and `.twig` in alternating rounds
and fails when Twig is more than twice as slow:

```bash
php vendor/bin/phpunit --group performance tests/tests/lib/twig/expTwigPerformanceGuardTest.php
```

**Rules for a fair comparison**: write down the load average with every run and repeat a run made under a load above
the number of cores; compare medians, not single requests; compare engines within one run (interleave them if you
can); keep the debug state the same for all engines; say whether the page was cached; never compare a cached page of
one engine with a rendered page of another.

## Related

- [Twig templates: performance (Speed)](twig-templates-performance.md)
- [Smarty templates: performance (Speed)](smarty-templates-performance.md)
- [The three template engines compared](template-engines.md)
- [Benchmarking](benchmarking.md)
- [Twig templates: caching](twig-templates-caching.md)
