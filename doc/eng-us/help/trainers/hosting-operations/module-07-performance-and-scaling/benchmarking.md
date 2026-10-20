---
title: "Lesson 7.1: Benchmarking a site"
audience: trainer, hosting, admin
category: training
order: 71
summary: Learners measure a site with exp:benchmark, read median, p90 and the cache column, compare warm and cold runs, and save a baseline that catches a regression after a deploy. 45 minutes.
keywords: hosting and operations, benchmark, exp:benchmark, median, p90, p99, cold, warm, baseline, save, threshold, min-delta, compare, regression, noise, plan limits
type: help
---
# Lesson 7.1: Benchmarking a site

"A slow site is a number, not a feeling." Hosts need numbers to compare two setups, to prove a deploy did not slow a
site down, and to set the limits of a plan. This lesson teaches `exp:benchmark`: what it measures and what not, how to
read its output, and how to keep a baseline.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 7: Performance and scaling](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate to advanced |
| Audience | System administrators and product managers |
| Prerequisites | [Module 2](../module-02-velocity/README.md) and [Lesson 4.1](../module-04-deploying-and-cron/deploying-a-change.md) |

## Learning objectives

After this lesson, learners can:

- say what `exp:benchmark` measures (time to the last byte, per page) and what it does not (browser work, maximum
  capacity);
- run it safely and read median, p90, p99, standard deviation, requests per second and the cache column;
- compare warm and cold (`--cold`) runs and explain why both are reported;
- save a baseline and compare a later run with it, reading the exit code;
- reduce noise and explain why figures from one site are not a property of the server.

## Trainer's notes

### How to present it

Source: [Benchmarking a hosted site](../../../hosting/performance/benchmarking-a-hosted-site.md),
[Benchmarking Exponential](../../../../guides/benchmarking.md) and [Benchmark](../../../../features/6.0/benchmark.md).

1. **What it measures** (3 minutes). The time until the last byte of each answer, in HTTP mode. It does not answer how
   many visitors a machine can take; that needs a load generator against a copy of the site (`--tool=ab|wrk|oha` is
   the start of that).
2. **Safe by default** (2 minutes). `GET` only, no cookies unless asked; large runs or other hosts need `--force`;
   `--cold-clear` is the one option that clears something.
3. **Reading the output** (4 minutes). Median is the number to compare; p90 and p99 show the slow tail; a standard
   deviation as large as the median means two kinds of answer mixed; compare only rows with the same cache answer.
4. **Warm and cold** (3 minutes). Warm is what a returning visitor gets; `--cold` bypasses every response cache. Most
   visitors get the warm number, editors and searches the cold one. A single "which server is faster" number
   misleads.
5. **Baselines** (3 minutes). `--save` and `--baseline`; a metric regressed when it grew by more than `--threshold`
   percent (15) and more than `--min-delta` milliseconds (1). Exit code 0, 1 or 2.

### Common questions

**Can I benchmark a customer's production site?** Keep the defaults, or a small `--requests` on a struggling site; a
benchmark is load. Prefer a staging copy.

**Why do my two runs differ?** Noise: other load on the machine, a cache cleared between runs. Run back to back, on a
quiet machine, and use a higher `--threshold` on shared machines.

**How do I set plan limits from this?** Measure a representative site rendered at rising concurrency, raise workers
step by step, and tune to the point where p90 starts to climb (lesson 2.3).

### Pitfalls

- **Benchmarking while signed in** with a cookie: signed-in pages bypass the anonymous caches. Keep the default.
- **Fifteen learners benchmarking one shared server.** Each learner benchmarks their own installation; keep
  `--requests` small on shared hardware.
- **Comparing a warm row with a cold row.** Check the cache column first.

## Hands-on exercise

Use your own training installation, served by Velocity, as the site's user.

1. Run the default benchmark with fewer requests:

   ```bash
   php bin/php/console exp:benchmark --requests=30
   ```

2. Note, for the front page, the median, p90 and the cache column.
3. Run it rendered, past every response cache: `php bin/php/console exp:benchmark --cold --requests=30`.
4. Compare the front page's median warm and cold, and write one sentence on what each number means for visitors.
5. Save a baseline:

   ```bash
   mkdir -p var/benchmark
   php bin/php/console exp:benchmark --cold --requests=30 --save=var/benchmark/base.json
   ```

6. Clear the content view cache (`php bin/php/ezcache.php --clear-id=content`) to simulate a deploy's effect.
7. Compare with the baseline and print the exit code:

   ```bash
   php bin/php/console exp:benchmark --cold --requests=30 --baseline=var/benchmark/base.json --min-delta=5; echo "exit $?"
   ```

8. Warm the site (`php bin/php/warm.php`) and repeat step 7.
9. Record the exit codes of steps 7 and 8 and explain the difference.

## Expected result

- Step 1 prints a table per page with median, p90, p99, minimum, maximum, sd and the cache column, plus the
  environment.
- Step 3: the cold medians are higher than the warm ones.
- Step 5 writes `var/benchmark/base.json`.
- Step 7 may report a regression right after the clear (exit 1); after warming, step 8 usually exits 0.
- The trainer checks by asking for the warm and cold medians of the front page and the two exit codes.

## Quiz

1. What does `exp:benchmark` measure in HTTP mode?
   A. browser rendering B. time to the last byte of each answer C. maximum visitors D. database CPU
2. Which number do you compare between runs?
3. Why report both warm and cold runs?
4. When does a metric count as regressed against a baseline?
5. What does exit code 1 mean after a run with `--baseline`?

<details>
<summary>Answers</summary>

1. **B.** ([Benchmarking a hosted site](../../../hosting/performance/benchmarking-a-hosted-site.md#what-it-measures-and-what-it-does-not))
2. The median.
3. Most visitors get the warm number; editors, searches and cache misses get the cold one.
4. When it grew by more than `--threshold` percent (15) and by more than `--min-delta` milliseconds (1).
5. A metric regressed (0 is no regression, 2 a usage error or a refusal).

</details>

## Further reading

- [Benchmarking a hosted site](../../../hosting/performance/benchmarking-a-hosted-site.md)
- [Benchmarking Exponential](../../../../guides/benchmarking.md): every mode and workflow.
- [Cache clearing and warming](../../../hosting/performance/cache-clearing-and-warming.md)
- [Sizing memory, CPU and disk](../../../hosting/basics/sizing-memory-cpu-and-disk.md)

## Related pages

- [Module 7: Performance and scaling](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
