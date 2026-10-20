---
title: "Lesson 1.4: OPcache and APCu"
audience: trainer, hosting, admin
category: training
order: 14
summary: Learners configure OPcache for PHP-FPM, explain why Velocity ships different OPcache options, and read the opcode cache profile to see whether files are really cached. 35 minutes.
keywords: hosting and operations, OPcache, validate_timestamps, revalidate_freq, file_update_protection, JIT, APCu, opcode cache profile, Velocity, PHP-FPM
type: help
---
# Lesson 1.4: OPcache and APCu

The opcode cache is the largest single performance win for PHP, and one wrong setting breaks an Exponential site in
ways that are hard to diagnose. This lesson teaches the safe settings under PHP-FPM, why Velocity's differ, and how
to prove with the opcode cache profile that the cache works.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 1: Serving the site](README.md) |
| Duration | 35 minutes (15 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Lesson 1.3](php-fpm-pools-and-tuning.md); a training installation |

## Learning objectives

After this lesson, learners can:

- write the OPcache settings chapter 10 recommends for a PHP-FPM pool;
- explain why `opcache.validate_timestamps=0` must never be used on an Exponential site;
- explain why Velocity ships `revalidate_freq=0` and `file_update_protection=0`;
- switch the opcode cache profile on, read `included_not_cached`, and switch it off again.

## Trainer's notes

### How to present it

Source: [OPcache](../../../hosting/performance/opcache.md) and
[Velocity and the opcode cache](../../../../features/6.0/velocity-opcode-cache-and-profile.md).

1. **Why it matters** (2 minutes). The measurement recorded in `settings/velocity.ini`: a content page took
   526.6 ms without an opcode cache and 390.8 ms with OPcache. The tracing JIT was slower (416.7 ms) and is
   deliberately absent.
2. **Under PHP-FPM** (4 minutes). Show the settings block: `opcache.enable=1`, `memory_consumption=256`,
   `max_accelerated_files=20000`, `interned_strings_buffer=32`, `validate_timestamps=1`, `revalidate_freq=2`.
   Exponential rewrites PHP cache files (INI caches, compiled templates) under the same name; with timestamp checks
   off, a rewritten file is served in its old form until PHP restarts.
3. **Under Velocity** (5 minutes). OPcache compares a file's time with the request's start time, and a Velocity
   worker is one long request. With the PHP defaults a worker never checked a file again, and every file written after
   the worker started counted as too new and was compiled again on every include. Velocity therefore passes its own
   options (`[PHPSettings] IniOptions[]`). Nothing needs to change; a restart applies them.
4. **The profile** (4 minutes). `touch var/tmp/opcache_profile.on` makes every request append one JSON line to
   `var/tmp/opcache_profile.log`. Read `included_not_cached`: close to 0 on later requests is healthy.

### Common questions

**Should I turn on JIT?** Only after measuring the customer's site with and without it; on the project's workload
it was slower.

**Where do I reset OPcache?** **Setup**, **Cache management** (the page **Caches**) has **Reset OPcache** and **Empty APCu** for the server
process that answered the page; another server keeps its own cache.

**Does FrankenPHP read my `php.ini`?** No. It gets OPcache settings from `[FrankenPHPSettings] IniOptions[]` and
`[PHPSettings] IniOptions[]`.

**What is a good first suspect when something works under PHP-FPM but not under Velocity?** Any PHP setting that
compares against the request time; it behaves as frozen in a Velocity worker.

### Pitfalls

- **Leaving the profile on.** The log grows with every request. End the exercise by moving the `.on` file out of
  `var/tmp/`.
- **Reading the first request.** The first request of a process compiles everything; read the second and later lines.
- **Resetting the wrong process.** The admin buttons act on the process that answered; under Apache and Velocity side
  by side, each has its own cache.

## Hands-on exercise

Use your own training installation, served by Velocity or by Apache with PHP-FPM.

1. In the installation root, list Velocity's shipped options:

   ```bash
   grep -n "opcache\." settings/velocity.ini
   ```

2. Find `revalidate_freq` and `file_update_protection` in the output and note their values.
3. If your site runs under PHP-FPM, open the pool's `php.ini` and compare its OPcache lines with the PHP-FPM block of
   [OPcache, Under PHP-FPM](../../../hosting/performance/opcache.md#under-php-fpm).
4. Show the cache status of the command-line process:

   ```bash
   php bin/php/cache.php opcache status --allow-root-user
   ```

5. Switch the profile on: `touch var/tmp/opcache_profile.on`.
6. Request the front page three times with `curl -s -o /dev/null <your site address>`.
7. Read the last lines: `tail -n 3 var/tmp/opcache_profile.log`. Find `included_not_cached` in each line.
8. Switch the profile off by moving the marker out of `var/tmp/`, for example
   `mv var/tmp/opcache_profile.on var/opcache_profile.off`.
9. Request the page once more and check that the log did not grow.

## Expected result

- Step 2: `revalidate_freq` is `0` and `file_update_protection` is `0` in Velocity's shipped options.
- Step 7: the first line may show many files not cached; later lines show a value close to 0.
- Step 9: no new line in `var/tmp/opcache_profile.log`.
- The trainer checks by asking for the `included_not_cached` value of the third request.

## Quiz

1. Why must `opcache.validate_timestamps` stay on for an Exponential site?
2. What did the recorded measurement show for the tracing JIT?
   A. 25 % faster B. slower than OPcache alone C. the same D. it was not measured
3. Why does a Velocity worker need `revalidate_freq=0`?
4. Which value in the opcode cache profile shows whether files are compiled again on every include?
5. True or false: **Reset OPcache** in the administration resets the cache of every server process on the machine.

<details>
<summary>Answers</summary>

1. Exponential rewrites PHP cache files under the same name; without timestamp checks the old form is served until
   PHP restarts ([OPcache](../../../hosting/performance/opcache.md#under-php-fpm)).
2. **B.** 416.7 ms against 390.8 ms for OPcache alone.
3. A worker is one long request that started when the worker did; with the default it never checks a file again.
4. `included_not_cached`.
5. **False.** It acts on the process that answered the page; the other server keeps its own cache.

</details>

## Further reading

- [OPcache](../../../hosting/performance/opcache.md): every setting for every server.
- [Velocity and the opcode cache](../../../../features/6.0/velocity-opcode-cache-and-profile.md): the measurements and
  the profile.
- [Caching layers](../../../hosting/performance/caching-layers.md): how the opcode cache fits with the other caches.
- [Caches](../../../../guides/caches.md): the administration's cache page.

## Related pages

- [Module 1: Serving the site](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
