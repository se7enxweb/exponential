---
title: Performance measurement and benchmarks
audience: software-engineer, developer, admin
category: software-engineering
order: 360
summary: How to measure Exponential's performance before and after a change - exp:benchmark in its http, kernel, micro and baseline modes, the micro benchmark CI runs on every push, the debug bar's timing and SQL figures, profiling, fair A/B comparisons, and how to read the project's published numbers.
keywords: performance, benchmark, exp:benchmark, micro benchmark, baseline, regression, A/B, profiling, debug bar, SQL count, timing, CI performance, measurement, software engineer
type: help
---
# Performance measurement and benchmarks

This page is for engineers who need numbers: to compare servers, to prove a change made nothing slower, or to find
where a page spends its time. Exponential ships its own benchmark command, so no external load tool is needed. The
reference is [Benchmark: exp:benchmark](../../features/6.0/benchmark.md) and the step-by-step guide is
[Benchmarking Exponential](../../guides/benchmarking.md).

## Contents

1. [The four modes](#the-four-modes)
2. [Safe by default](#safe-by-default)
3. [Performance checks in CI](#performance-checks-in-ci)
4. [Finding where the time goes](#finding-where-the-time-goes)
5. [Comparing fairly](#comparing-fairly)
6. [Reading published numbers](#reading-published-numbers)

## The four modes

```bash
./console exp:benchmark --allow-root-user          # http: the front page, three menu pages, the admin login
./console exp:benchmark kernel --allow-root-user   # the parts of a page, in-process
./console exp:benchmark micro --allow-root-user    # hot paths, no web server, no database
./console exp:bench --help                          # exp:bench is the short name
```

| Mode | Measures | Use it for |
|---|---|---|
| `http` (default) | requests and their timings, against one server or several back to back | comparing engines, servers or configurations |
| `kernel` | boot, template render cold and warm, INI load, content fetch, database round trip, cache write and read | finding which part of a page got slower |
| `micro` | pure-PHP hot paths (template compile and render, INI parsing, autoload, translations), normalized against a calibration loop | comparing code changes across machines |
| `baseline` | saves a run as JSON and compares a later run with it, exit status 1 over a threshold | a CI job or a deploy script |

`php bin/php/benchmark.php ...` is the same command without the console.

## Safe by default

A benchmark is load. The defaults are polite (100 requests per URL, 4 at a time, `GET` only), and anything that looks
like a load test of somebody else's server (another host, high concurrency, many requests) needs `--force`. A host the
site does not serve is refused unless its URLs are named explicitly. `--cold-clear` is the one option that writes: it
clears the content view cache first; `--cold` measures past the caches without clearing anything.

## Performance checks in CI

The **Performance** workflow runs the micro benchmark (PHP 8.5) on a push or pull request that changes PHP, templates,
`settings/` or the benchmark itself, against `tests/benchmark/baseline.json`. A probe over the threshold is measured
once more and only the second run counts. It is a warning, not a blocking check. Reproduce it with:

```bash
php bin/php/benchmark.php micro --baseline=tests/benchmark/baseline.json --all-checks
```

with OPcache off for the command line ([Continuous integration](../../bc/6.0/ci.md)).

## Finding where the time goes

- **The debug bar** shows page time, SQL count and time, peak memory and templates used, with tabs for timing, SQL,
  templates and memory ([The Exp Debug bar](../../features/6.0/exp-debug-bar.md)). Turn it on for your own address only.
- **The kernel mode** of the benchmark splits a page into its parts.
- **Velocity** has an opcode cache report and a profile ([Velocity opcode cache and profile](../../features/6.0/velocity-opcode-cache-and-profile.md)).
- Profiling guidance for application code: [Profiling and performance](../creators/app-creators/profiling-and-performance.md).

## Comparing fairly

- Compare A and B back to back, on the same machine, with the same caches warm or cold.
- Use medians over many requests, not single requests or best cases.
- Change one thing at a time: the engine, a setting or the code.
- Note the PHP version, the engine, the database and which caches were on.
- Judge Velocity's memory by PSS, never RSS.

## Reading published numbers

The README documents figures for the Velocity stack (for example a median per cached page and requests per second on
a 12-core machine) and says the methodology and scripts ship with the project. Treat any published number as a
measurement on that hardware, with that page and those caches, and repeat it on yours before you plan with it.

## Related pages

- [Caching layers for engineers](caching-layers-for-engineers.md)
- [Benchmarking a hosted site](../hosting/performance/benchmarking-a-hosted-site.md)
- [HTTP/2 and cache warming](../../bc/6.0/http2-and-cache-warming.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
