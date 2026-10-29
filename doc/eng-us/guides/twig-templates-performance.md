# Twig templates: performance (Speed)

This page is for site builders and developers who need to know how fast `.twig` pages are, why, and how to measure
their own. It describes what the Twig engine does to be fast, gives the measurements taken on a development
installation on 2026-10-29 with their conditions, and shows how to repeat them.

## What makes .twig fast

- **Compiled once.** Twig compiles a `.twig` to a PHP class, kept in `<cache directory>/twig/compile-secure`. The class
  name holds the file's modification time, so Velocity's persistent workers compile a changed file once and never
  check the cache again for a file that did not change.
- **One environment per process.** Velocity builds the Twig environment once per worker and registers every operator
  and template function as a Twig filter and function when it starts. PHP-FPM, which starts each request afresh,
  resolves only the names a template uses (building all of them, about 1,700, took 10-25 ms per request).
- **Operators in PHP.** The logic, arithmetic and control operators, `wash`, `implode`, `contains` and `concat` run
  directly in PHP, as static methods the compiled template calls without a lookup, with a fast path for plain
  numbers and texts. The other operators run through the template system's own code.
- **Translations when compiling.** A constant `i18n` text is translated when the template is compiled; the compiled
  class is kept per siteaccess and locale. On the media front page this took 37.5 ms of operator time off every
  uncached request.
- **Attribute reads as array lookups.** Under the sandbox (the default) an attribute read `a.b` is compiled as an
  array lookup on the template system's values, without Twig's general attribute resolution; the sandbox's check
  that an object may be printed is left out for the bridge's own values.
- **One cache block for the whole page** with the visitor's menu rendered fresh after it
  ([Caching](twig-templates-caching.md)).

## Cached pages

A cached page is answered by Velocity's response cache or by the role-aware HTTP cache before any template runs, so
the template engine makes no difference to it. Measured on a development installation from another server in the
same network (round trip about 3 ms), 8 pages of the converted media design, 200 requests per page, three runs
(3,600 timed requests per engine and server), on 2026-10-29. The load average of the measured server was 9 to 80 on 12
cores during the runs (other work was running), the client's 40 to 80. "Server" is the page's time to first byte
minus that of a static file fetched on the same connection just before, so the network round trip is taken out.

**Velocity (:8080):**

| Engine | Server median | Server p95 | Server p99 | Total median | Total p95 | Requests ≤ 33 ms | ≤ 20 ms | ≤ 13 ms |
|---|---|---|---|---|---|---|---|---|
| `.twig` | 0.1 ms | 23 ms | 74 ms | 6.1 ms | 56 ms | 96.7 % | 94.5 % | 93.3 % |
| `.stpl` | 0.1 ms | 27 ms | 67 ms | 6.0 ms | 55 ms | 96.1 % | 93.8 % | 92.2 % |
| `.tpl` | 0.1 ms | 28 ms | 76 ms | 5.4 ms | 56 ms | 96.2 % | 93.9 % | 92.4 % |

**Apache with PHP-FPM (:443):**

| Engine | Server median | Server p95 | Server p99 | Total median | Total p95 | Requests ≤ 33 ms | ≤ 20 ms | ≤ 13 ms |
|---|---|---|---|---|---|---|---|---|
| `.twig` | 11.8 ms | 92 ms | 203 ms | 23.7 ms | 177 ms | 74.7 % | 66.6 % | 53.8 % |
| `.stpl` | 11.9 ms | 98 ms | 227 ms | 23.3 ms | 179 ms | 76.0 % | 66.8 % | 53.6 % |
| `.tpl` | 11.6 ms | 107 ms | 242 ms | 23.3 ms | 211 ms | 75.4 % | 66.7 % | 54.4 % |

What the numbers say:

- On Velocity a cached page is served in about the time of the network round trip (median server time near zero,
  median total 5-6 ms with the round trip): the median is far below 13 ms for all three engines.
- The slow requests (p95, p99, maximum) are the same for all three engines and come from the load of the machine
  during the runs, not from a template engine; a run in a quiet window (no other jobs) is needed to show the
  maximum per request.
- With PHP-FPM a cached page costs about 12 ms of PHP before the HTTP cache answers: the request starts PHP, reads the
  settings and the session, then the cache. That is the floor of Apache with PHP-FPM, for every engine.

## Uncached pages

With a unique query string per request (the response caches do not answer; the content view cache does), measured
from the same client after the cached runs, 2 × 50 requests per page, load 8 to 25:

| Server | `.twig` median | `.tpl` median | `.stpl` median | p95 (all) |
|---|---|---|---|---|
| Velocity | 0.100-0.115 s | 0.106-0.155 s | 0.092-0.115 s | 0.17-0.26 s |
| Apache with PHP-FPM | 0.070-0.092 s | 0.090-0.109 s | 0.069-0.094 s | 0.11-0.25 s |

Measured on the same machine, requests of the three engines interleaved (each round asks for the same page from
each engine, in a rotating order), 20 rounds, load 13 to 15, after the latest changes:

| Server | Page | `.twig` / `.tpl` | `.twig` / `.stpl` |
|---|---|---|---|
| Velocity | Fitness (a list) | 0.88 | 1.13 |
| Velocity | an article | 0.90 | 1.19 |
| Apache | Fitness | 0.98 | 1.06 |
| Apache | an article | 0.83 | 0.90 |

An uncached `.twig` page is as fast as the `.tpl` page or faster, and within about 20 % of `.stpl`.

## The performance guard

`tests/tests/lib/twig/expTwigPerformanceGuardTest.php` (PHPUnit, `@group performance`, no server and no database)
renders three templates (a list with operators, conditions and an include, a logic template, a section with a
while loop) as `.tpl` and as the `.twig` the converter writes, 15 rounds of 20 renders each, the order of the two
alternating from round to round, and requires the median of the rounds' `.twig`/`.tpl` ratios to be at most 2.0.
Typical results on a loaded development machine: 1.0 to 1.7. A change that makes Twig noticeably slower fails it.

```bash
php vendor/bin/phpunit --group performance tests/tests/lib/twig/expTwigPerformanceGuardTest.php
```

## Measuring your own pages

- **Cached pages:** measure from a client whose address is not in the debug list (`[DebugSettings] DebugByIP`, `DebugIPList[]`), or every
  response carries the debug report and bypasses the page caches (`X-Exp-Cache: BYPASS (debug report)`). Fetch a
  static file and the page on the same connection and subtract the static file's time to first byte to take the
  network out:

  ```bash
  curl -s -o /dev/null -o /dev/null -o /dev/null -w '%{time_starttransfer} %{http_code}\n' \
       https://www.example.com/favicon.ico https://www.example.com/favicon.ico https://www.example.com/twig/fitness
  ```

  The second and third lines are the static file and the page on an open connection.
- **Uncached pages:** add a unique query string (`?bench=<n>`) to each request.
- **Profile a page:** with `[TwigSettings] Profile=enabled` and the debug output on, each page writes a "twig
  profile" notice: time in operators (by name), template functions, includes and cache blocks (hit or miss).
- Record the load average with every run, repeat runs at different times, and compare engines in the same run.

## Related

- [Caching](twig-templates-caching.md)
- [Smarty templates: performance](smarty-templates-performance.md)
- [Benchmarking](benchmarking.md)
