---
title: Benchmarking a hosted site
audience: hosting, admin
category: hosting
order: 120
summary: How a host measures Exponential sites with exp:benchmark: safe defaults, cached against rendered pages, comparing servers back to back, deploy regression checks, noise on shared machines, and turning measurements into plan limits without inventing numbers.
keywords: benchmark, exp:benchmark, performance test, median, p90, p99, req/s, --cold, --compare, --baseline, --save, regression, noise, shared server, ab, wrk, oha, plan limits, capacity
type: help
---
# Benchmarking a hosted site

This page is for system administrators and product managers at hosting companies who must say how fast a customer
site is, compare two server setups, prove a deploy did not make things slower, or set the limits of a plan. It
answers: what `exp:benchmark` measures, how to use it safely on production machines, how to compare fairly, and how
to turn the numbers into decisions. The sources are [Benchmarking Exponential](../../../guides/benchmarking.md) and
[Benchmark: exp:benchmark](../../../features/6.0/benchmark.md).

## Contents

1. [What it measures and what it does not](#what-it-measures-and-what-it-does-not)
2. [Safe by default](#safe-by-default)
3. [The first run](#the-first-run)
4. [Cached and rendered](#cached-and-rendered)
5. [Comparing two setups](#comparing-two-setups)
6. [Checking a deploy](#checking-a-deploy)
7. [Noise on shared machines](#noise-on-shared-machines)
8. [From measurements to plan limits](#from-measurements-to-plan-limits)
9. [Checklist](#checklist)

## What it measures and what it does not

| It measures | It does not measure |
|---|---|
| the time until the last byte of each answer arrived, per request (HTTP mode) | what a browser does afterwards (images, layout, painting) |
| the response cache, view cache and rendering, depending on warm or `--cold` | the network between a visitor and the server, unless you run it from elsewhere |
| requests per second at the chosen concurrency | the most a server can carry: the default of 4 at a time is polite, not a load test |
| the kernel's own costs in-process (`kernel` mode), pure-PHP hot paths (`micro` mode) | signed-in pages, unless you pass a session cookie |

"It does not answer 'how many visitors can this machine take'; that needs a load generator against a copy of the
site." `--tool=ab|wrk|oha` hands the requests to an installed load generator and is "the start of that, not the end".

## Safe by default

- HTTP mode sends `GET` only, never posts, no cookies unless `--cookie`.
- Against a host other than this machine's loopback, more than 1000 requests per URL, more than 16 at a time, a
  warm-up above 100 or more than 20,000 requests in all needs `--force`.
- The default page list is sent only to hosts this installation serves; another host must be named with `--url`.
- `--cold-clear` is the one option that clears something (the content view cache, once); it is refused for servers
  the installation does not serve. Do not use it on a server other people are working on.
- Run as root, the command prints a warning and waits ten seconds; run it as the site's user where you can.

## The first run

From the installation root:

```bash
./console exp:benchmark --allow-root-user
```

It requests the front page, the first three menu pages, the search page (when visitors may search) and the admin login
page, 100 times each after 5 warm-up requests, 4 at a time, and prints median, p90, p99, minimum, maximum and standard
deviation per page, plus the environment (PHP, OPcache, Xdebug, CPUs, load, commit). For a list that never changes,
name the pages:

```bash
./console exp:benchmark --urls-file=benchmark-urls.txt --allow-root-user
```

| Number | Read it as |
|---|---|
| median | the typical request; the number to compare |
| p90, p99 | what the slowest 10 % and 1 % waited; with 100 runs p99 is nearly the maximum |
| sd | small next to the median means steady; as large as the median means two kinds of answer mixed, or stalls |
| req/s | bounded by the concurrency: 4 at a time at 10 ms each cannot exceed about 400 |
| cache | which cache answered (`HIT`, `BYPASS`, `-`); compare only rows with the same answer |

## Cached and rendered

| Mode | What answers | Option |
|---|---|---|
| warm (default) | what a returning visitor gets: Velocity's response cache or the HTTP cache, else the view cache | none |
| cold | the application, past every response cache (a unique query string per request) | `--cold` |
| cold, empty view cache | the first request of every page renders from scratch | `--cold-clear` |

Most visitors get the warm number; editors, searches and cache misses get the cold one. Report both.

## Comparing two setups

Run both servers on the same installation (Velocity on its own port beside Apache, for example) and measure them in
one run, back to back:

```bash
./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --allow-root-user
./console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080 --cold --runs=30 --allow-root-user
```

The guide's own example shows why both runs matter: cached, Velocity answered in 1.3 to 2.6 ms against Apache's 8.7
to 10.7 ms; rendered, Apache was faster in that run. "A single 'which server is faster' number misleads"
([Benchmarking, section 4.4](../../../guides/benchmarking.md#44-two-servers-back-to-back-apache-and-velocity)).
Three or more servers can be compared at once; each is compared with the first.

## Checking a deploy

```bash
./console exp:benchmark --cold --runs=50 --save=var/benchmark/pre-deploy.json --allow-root-user
./console exp:velocity deploy --allow-root-user
php bin/php/warm.php --allow-root-user
./console exp:benchmark --cold --runs=50 --baseline=var/benchmark/pre-deploy.json --min-delta=5 --allow-root-user
echo $?        # 0 no regression, 1 a metric regressed, 2 usage error or refused
```

A metric counts as regressed when it grew by more than `--threshold` percent (15) and by more than `--min-delta`
milliseconds (1). "A deploy script can use the exit code; on a shared machine give it `--threshold=25` or more and a
`--min-delta`, or it will cry wolf" ([Benchmarking, section 11.5](../../../guides/benchmarking.md#115-checking-a-deploy-did-not-regress)).

## Noise on shared machines

The guide repeated a saved run nine minutes later with no change at all and got 13 "regressions": the machine was
busier. Cache hits of 1 to 3 ms doubled from one minute to the next on a shared machine. What to do
([Benchmarking, section 10](../../../guides/benchmarking.md#10-noise-and-how-to-reduce-it)):

1. Same machine, same PHP, OPcache, JIT and Xdebug setting (the environment line says which).
2. Same command: the same pages, runs, warm-up, concurrency, warm or cold.
3. Nothing else running that you can stop; note the load in the header line.
4. Before and after close together; for two servers, one `--compare` run, never two separate ones.
5. The caches in the same state: the same `cache` column in both runs.
6. Run it twice. A difference that does not repeat is noise.
7. Judge the median first, p90 and p99 second, max never alone.

> **Tip**
> Never benchmark with Xdebug loaded ("everything several times slower"); the header says `Xdebug ON` if it is.

## From measurements to plan limits

The project publishes no capacity per server, and this help gives none. What the sources do give is a method:

| Decision | How to measure it |
|---|---|
| Velocity worker count for a plan | rendered pages at rising concurrency; tune to the point where p90 turns over ([Velocity workers and pools](../servers/velocity-workers-and-pools.md#finding-the-number-for-a-plan)) |
| Engine for a plan | `--compare` warm and `--cold`, plus a signed-in page with `--cookie` if editors matter |
| OPcache or worker settings | one setting per step, a cold run before and after, kept only when the gain repeats ([Benchmarking, section 11.3](../../../guides/benchmarking.md#113-tuning-opcache-and-velocity)) |
| Customers per machine | measure a representative customer site, then a copy of it under a load generator; leave headroom for cron jobs, cache warming and backups |

Figures quoted in the documentation (for example 8,941 requests a second in the README, or the instance numbers on
[Velocity workers and pools](../servers/velocity-workers-and-pools.md#instances-several-servers-on-one-port)) were
measured on particular machines and sites. The sources say so: they "describe this site's templates on this machine.
They are not a property of the server and should not be quoted as one." Do not put them in a plan or an offer.

## Checklist

- [ ] Benchmarks run as the site's user, against hosts this installation serves.
- [ ] Every report gives warm and cold numbers, and the environment line.
- [ ] Comparisons made in one `--compare` run, back to back.
- [ ] Deploy checks use `--baseline` with a `--min-delta` suited to a shared machine.
- [ ] Plan limits come from your own measurements, not from documentation figures.

## Related pages

- [Performance](README.md)
- [Velocity workers and pools](../servers/velocity-workers-and-pools.md)
- [Designing hosting plans](../business/designing-hosting-plans.md)
- [Benchmarking Exponential](../../../guides/benchmarking.md)
- [Benchmark: exp:benchmark](../../../features/6.0/benchmark.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
