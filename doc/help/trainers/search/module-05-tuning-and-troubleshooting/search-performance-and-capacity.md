---
title: "Lesson 5.4: Search performance and capacity"
audience: trainer, admin, developer, hosting
category: training
order: 54
summary: Learners measure the speed of the search page with exp:benchmark, compare it before and after a settings change, and name the levers that keep search fast as a site grows, from limits and delayed indexing to Solr. 30 minutes.
keywords: search course, performance, capacity, exp:benchmark, --search, --cold, search page, wildcard cost, DelayedIndexing, MaximumSearchLimit, MaxBooleanClauses, Solr, baseline
type: help
---
# Lesson 5.4: Search performance and capacity

A search request is one of the most expensive pages a site serves: it cannot be served from the page cache the way an
article can, and every word adds work. This lesson teaches learners to measure the search page instead of guessing,
to compare honestly, and to know which levers keep search fast as content grows.

> **Note**
> Benchmark only your own training installation. Load tests against a site you do not run can look like an attack.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 5: Tuning and troubleshooting](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Advanced |
| Audience | Site administrators, developers, hosting staff |
| Prerequisites | [Lesson 5.1](tuning-the-search-settings.md); the training installation served (for example with `exp:velocity start`) |

## Learning objectives

After this lesson, learners can:

- measure the search page with `exp:benchmark` and read median and p90;
- compare two runs fairly: same counts, same pages, back to back, with a saved baseline;
- measure the effect of a search setting (wildcards) on the search page;
- name the levers for capacity: limits, delayed indexing, searchable attributes, and moving to Solr.

## Trainer's notes

### How to present it

Source: [Benchmarking Exponential](../../../../guides/benchmarking.md),
[Benchmarking a hosted site](../../../hosting/performance/benchmarking-a-hosted-site.md) and
`settings/site.ini [SearchSettings]`.

1. **The search page in the benchmark** (3 minutes). Without `--url`, `exp:benchmark` requests the front page, menu
   pages, the search page `/content/search?SearchText=<word>` (when anonymous visitors may search) and the admin login
   page, 100 times each after 5 warm-up requests, 4 at a time. `--search=<words>` chooses the words;
   `--url=/content/search?SearchText=<word>` measures only the search page.
2. **Comparing fairly** (3 minutes). One run on a busy machine proves little. Keep `--runs`, `--warmup` and
   `--concurrency` the same, run back to back, and keep runs with `--save=<file>`; `--baseline=<file>` compares and
   exits 1 on a regression.
3. **Levers** (4 minutes).
   - Limits: `MaximumSearchLimit`, `MaximumSearchTextWords`, `AllowEmptySearch=disabled` keep single requests
     bounded.
   - Wildcards cost: measure before switching them on (exercise).
   - Publishing speed: delayed indexing moves indexing to cron (Module 2).
   - Index size: only useful attributes searchable (lesson 2.4).
   - Scale: Solr is built for very large numbers of objects. With ezfind, `ezfind.ini [SearchHandler]
     MaxBooleanClauses` must match Solr's own limit; each word is one clause per query field, so the query builder
     keeps only as many words as the fields allow.

### Common questions

**Why is the search page slower than an article?** An article page can be served from the view cache; a search runs
the engine's query for each new text.

**Should search results be cached?** Not by this course. Measure first; the levers above are the documented ones.

**What numbers are good?** The documentation gives no target. Compare with your own baseline and with the other pages
of the same site.

### Pitfalls

- **Measuring once.** One run says little. Make learners save a baseline and repeat.
- **A shared machine.** Other learners' benchmarks distort yours. Run the exercise one row at a time, or with fewer
  requests (`--runs=30`).
- **Forgetting to undo the wildcard change.** Step 7 restores the setting.

## Hands-on exercise

Use your own training installation, served and reachable at its address. Add `--allow-root-user` only if you work as
root. Run the benchmark from the installation root.

1. Run the default benchmark once and find the search page in the table:

   ```bash
   php bin/php/console exp:benchmark --runs=30
   ```

2. Note the median and p90 of the search page and of the front page.
3. Measure only the search page and keep the run as a baseline:

   ```bash
   php bin/php/console exp:benchmark --url="/content/search?SearchText=salad" --runs=50 --save=var/search-baseline.json
   ```

4. Switch on wildcards for the public siteaccess, as in lesson 5.1, and clear the INI cache.
5. Measure a wildcard search against the baseline:

   ```bash
   php bin/php/console exp:benchmark --url="/content/search?SearchText=sal*" --runs=50
   ```

6. Compare the medians of steps 3 and 5. Write one sentence: is the difference larger than the spread between two
   runs of step 3? (Run step 3 once more without `--save` to see the spread.)
7. Switch wildcards off again and clear the INI cache.
8. Remove the baseline file you created in `var/` when you are done, or keep it for the next lesson's comparison.

## Expected result

- Step 1 prints a table with the search page among the measured pages (unless anonymous visitors may not search).
- Steps 3 and 5 print median, p90, p99, minimum, maximum and standard deviation for one page; step 3 writes the JSON
  file.
- Step 6 is a sentence based on numbers, not impressions; on a small training site the difference may be within the
  spread.
- After step 7 `exp:ini get` prints `false` for `EnableWildcard` on the public siteaccess.
- The trainer collects two or three sentences from step 6 and discusses why results differ between machines.

## Quiz

1. Which option sets the words of the search page in the default benchmark?
2. Name three conditions for a fair comparison between two runs.
3. Why can a search page not simply be served from the page cache like an article?
4. Which option compares a run with a saved one and fails on a regression?
   A. `--save` B. `--baseline` C. `--cold` D. `--compare`
5. Name three levers that keep search fast as a site grows.

<details>
<summary>Answers</summary>

1. `--search=<words>` ([Benchmarking Exponential](../../../../guides/benchmarking.md)).
2. Any three of: same `--runs`, `--warmup` and `--concurrency`; the same pages; back to back; the same PHP set-up; a
   quiet machine.
3. Each new search text runs the engine's query; the results depend on the words and the user.
4. **B.** It exits 1 on a regression.
5. Any three of: the search limits, wildcards off unless measured, delayed indexing, fewer searchable attributes,
   moving to Solr.

</details>

## Further reading

- [Benchmarking Exponential](../../../../guides/benchmarking.md): every mode, option and number of `exp:benchmark`.
- [Benchmarking a hosted site](../../../hosting/performance/benchmarking-a-hosted-site.md): benchmarking on a
  server.
- [Search backends](../../../hosting/basics/search-backends.md): what each engine costs to run.

## Related pages

- [Module 5: Tuning and troubleshooting](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
