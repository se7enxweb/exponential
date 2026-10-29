---
title: "Lesson 5.4: Runbooks for a site that is down or slow"
audience: trainer, hosting, admin
category: training
order: 54
summary: Learners work through the site-down and slow-site runbooks on a training installation with prepared faults, tell the error pages apart, measure before tuning and write an incident note. 45 minutes.
keywords: hosting and operations, runbook, site down, slow site, incident, error page, ERR reference, database cannot be reached, maintenance, vendor missing, exp:benchmark, baseline, ANALYZE, incident note
type: help
---
# Lesson 5.4: Runbooks for a site that is down or slow

On-call staff do not need to know everything; they need a short order of checks that finds the cause fast. The
hosting help has runbooks for exactly that. This lesson rehearses the two most common ones, "site down" and "slow
site", on training installations with faults the trainer has prepared.

> **Note**
> Prepare the faults on learners' own training installations only. Never break a shared or customer site for practice.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 5: Monitoring and logs](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | System administrators and on-call support staff |
| Prerequisites | [Lesson 5.3](request-and-security-logs.md); Modules 1 to 4 |

## Learning objectives

After this lesson, learners can:

- follow the first five minutes of the site-down runbook: what the visitor sees, the newest errors, the machine;
- tell the error pages apart and name the cause of each;
- decide whether nothing answers at all, and check the engine or the web server and pool;
- measure a slow site before changing anything, and follow the checks in order;
- write an incident note with start, detection, cause, fix, end and prevention.

## Trainer's notes

### How to present it

Source: [Runbook: site down](../../../hosting/runbooks/site-down.md),
[Runbook: slow site](../../../hosting/runbooks/slow-site.md) and
[Chapter 12, Pages and errors at run time](../../../../install/12-troubleshooting.md#129-pages-and-errors-at-run-time).

1. **Site down: first five minutes** (4 minutes). Note exactly what the site shows with the status (`curl -sI`), try
   the administration, read the newest errors (`exp:logs --tail=var/log/error.log`), check disk, memory and load.
   A full disk causes most of the symptoms.
2. **Which page?** (4 minutes). Show the table: "We'll be right back" (503) is the database; "Down for maintenance"
   (503) is maintenance mode; "missing the software libraries" (503) is `vendor/`; "Something went wrong" (500) with
   `ERR-...` is an uncaught error; a blank page or a 500 with nothing in `error.log` means PHP failed before
   Exponential could log. Errors that started right after a deploy are almost always caused by that change.
3. **Slow site** (5 minutes). A slow site is a number, not a feeling: `curl` timing and `exp:benchmark`, compared
   with a saved baseline. Then: is it everyone or one page? Checks in order: the machine, the caches, the engine,
   OPcache, the database, traffic, the static cache. A benchmark is load; keep it small on a struggling site.
4. **After the incident** (2 minutes). Clear what the fix needs, the response cache after any restart; verify front
   page, administration, sign-in and publish; write the note; tell the customer.

### Common questions

**Where is the bug reported if Exponential is at fault?** The runbook links
[When something goes wrong](../../../when-something-goes-wrong.md).

**Only editors find the site slow. Why?** Signed-in users bypass the anonymous caches; check the database and
OPcache.

**The site is slow only at certain times.** Look for a cron job, a backup, an import or a crawler in the request logs
and cron times.

**Velocity runs but does not answer.** `exp:velocity restart`; if a worker is wedged, `exp:velocity kill`, then
`start`.

### Pitfalls

- **Fixing before reading.** Learners jump to restarting. Insist on step 1 of the runbook first; the visitor's page
  names the cause most of the time.
- **Prepared faults that persist.** Write down every fault you prepare and undo each one at the end of the lesson.
- **Benchmarks on a shared machine.** Keep `--requests` small so learners do not slow each other down.

## Hands-on exercise

Work in pairs. The trainer has prepared one fault on each pair's training installation, for example maintenance
mode left on, the database file or credentials made unreachable, `vendor/` renamed, or the content view cache switched
off in an override. Each pair diagnoses its own installation.

1. Request the front page with headers and note status and title:
   `curl -s http://127.0.0.1:8088/ | grep -i -m1 '<title>'` and `curl -sI http://127.0.0.1:8088/ | head -5`.
2. Request the administration login page the same way.
3. Read the newest errors: `php bin/php/console exp:logs --tail=var/log/error.log --lines=30`.
4. Check the machine: `df -h .`, `free -m`, `uptime`.
5. Check the engine: `php bin/php/console exp:velocity status`.
6. Check maintenance mode: `php bin/php/maintenance.php status`.
7. Match what you saw to a row of the table *Which page does the visitor see?* in the site-down runbook and name the
   cause.
8. Fix it with the runbook's action, then clear the response cache: `php bin/php/console exp:velocity cache clear --apply`.
9. Measure the site: `php bin/php/console exp:benchmark --requests=20`. Note median and p90.
10. Verify: front page, administration sign-in, and a test publish in your course area.
11. Write a five-line incident note: start, detection, cause, fix, end and what prevents a repeat.

## Expected result

- Each pair names the prepared fault from the visitor's page and the logs before changing anything.
- After step 8 the front page and administration answer `200`.
- Step 9 prints median, p90 and p99 for the pages it requests.
- Each pair has an incident note with all five items.
- The trainer checks by asking each pair which row of the table matched and which command proved it.

## Quiz

1. A visitor sees "We'll be right back" with status 503. What is the most likely cause?
   A. maintenance mode B. the database cannot be reached C. a template error D. a full cache
2. Where do you look when a page is blank and `error.log` has nothing new?
3. What do you do first when a customer says the site is slow?
4. Errors started right after a deploy. Where is the cause most likely?
5. Why is the response cache cleared after a restart and not before?

<details>
<summary>Answers</summary>

1. **B.** The page for a database that cannot be reached; "Down for maintenance" is maintenance mode
   ([Runbook: site down](../../../hosting/runbooks/site-down.md#which-page-does-the-visitor-see)).
2. The web server's or PHP-FPM's error log: PHP failed before Exponential could log (syntax error, missing extension,
   memory exhausted).
3. Measure: `curl` timing and `exp:benchmark`, compared with a baseline if one exists.
4. In that change; under Velocity a changed class or setting needs a restart or `exp:velocity deploy`.
5. Cleared before, pages requested in between are rendered by the old code and cached again.

</details>

## Further reading

- [Runbook: site down](../../../hosting/runbooks/site-down.md)
- [Runbook: slow site](../../../hosting/runbooks/slow-site.md)
- [Velocity will not start](../../../hosting/runbooks/velocity-will-not-start.md)
- [Chapter 12: Troubleshooting](../../../../install/12-troubleshooting.md)

## Related pages

- [Module 5: Monitoring and logs](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
