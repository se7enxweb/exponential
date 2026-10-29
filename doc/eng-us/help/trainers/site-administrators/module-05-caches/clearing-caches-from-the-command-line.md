---
title: "Lesson 5.3: Clearing caches from the command line"
audience: trainer, admin
category: training
order: 53
summary: Learners list caches with their sizes, clear by id and by tag with ezcache.php and exp:cache, preview with --dry-run, read PASS and FAIL, and clear Velocity's response cache. 35 minutes.
keywords: site administration, ezcache.php, exp:cache, cache.php, clear-tag, clear-id, clear-all, dry-run, purge, list-tags, velocity cache clear, velocity restart, PASS, FAIL
type: help
---
# Lesson 5.3: Clearing caches from the command line

Deploy scripts, cron jobs and support staff without a browser session clear caches in a terminal. Two commands do
it: the long-standing `bin/php/ezcache.php`, and `exp:cache`, which shares its code with the Caches page and reports
`PASS` or `FAIL`. This lesson uses both, always looking before clearing.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 5: Caches](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators comfortable with a terminal |
| Prerequisites | [Lesson 5.2](clearing-caches-in-the-administration.md); a terminal in the training installation's root |

## Learning objectives

After this lesson, learners can:

- list the caches with their tags and sizes;
- clear a cache by id and by tag with `ezcache.php`;
- preview a clear with `exp:cache ... --dry-run` and read its `PASS` line;
- read the status of the caches in front of PHP;
- clear Velocity's response cache and say when Velocity needs a restart.

## Trainer's notes

### How to present it

Source: [Operating a site, parts 1 and 2](../../../../guides/operating-a-site.md),
[Caches, from a shell](../../../../guides/caches.md#5-from-a-shell) and
[Cache control from the console](../../../../bc/6.0/cache-console.md).

1. **Look first** (3 minutes). `php bin/php/cache.php list --sizes` lists every cache with its tag, state, directory
   and size; `php bin/php/cache.php status` gives one screen for the caches in front of PHP.
2. **ezcache.php** (3 minutes). `--clear-id=<id>` for one or several caches, `--clear-tag=<tag>` by tag,
   `--clear-all` for everything; `--list-tags` and `--list-ids` print the valid names. `--purge` removes entries
   physically instead of marking them expired.
3. **exp:cache** (2 minutes). The same work with `PASS`/`FAIL` on the last line and `--json` for monitoring;
   `--dry-run` shows what would be cleared, with sizes.
4. **Velocity** (2 minutes). `exp:velocity cache clear` clears the response cache;
   `exp:velocity restart` makes Velocity read changed settings. On a server, `exp:velocity deploy` does every step of
   a deployment in the right order ([Hosting and operations](../../hosting-operations/README.md), lesson 4.1).

### Common questions

**Do I need `--allow-root-user`?** Only when you run the command as the operating system root user; the scripts
refuse root otherwise. Better: run them as the user that owns the files.

**`--clear-id=ini` did not pick up my setting.** Use the tag: `--clear-tag=ini` also clears the global INI cache.

**Are command-line clears recorded?** Yes, one audit record per run, naming the operating system user. The Caches
page shows them in its last-cleared column.

### Pitfalls

- **Running as root and leaving root-owned files.** A clear as root can leave directories the web server cannot write.
  Show `ls -l var/` after a clear as root, if your lab permits.
- **`--clear-all` in a cron job.** It makes the site slow every time it runs. Clear the smallest thing, or nothing:
  publishing clears the affected views itself.
- **Wrong directory.** The commands must run in the installation root (the directory with `index.php`).

## Hands-on exercise

In a terminal, in the root of your training installation:

1. List the caches with their sizes:

   ```bash
   php bin/php/cache.php list --sizes
   ```

2. List the valid tags and ids:

   ```bash
   php bin/php/ezcache.php --list-tags
   php bin/php/ezcache.php --list-ids
   ```

3. Preview what clearing everything would touch:

   ```bash
   php bin/php/cache.php clear --all --dry-run
   ```

4. Clear only the INI caches by tag, and read the output:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   ```

5. Clear the template block and content caches by id:

   ```bash
   php bin/php/ezcache.php --clear-id=template-block,content
   ```

6. Clear by tag with `exp:cache` and read the last line:

   ```bash
   php bin/php/cache.php clear --tag=template
   ```

7. Read the status of the caches in front of PHP:

   ```bash
   php bin/php/cache.php status
   ```

8. Clear Velocity's response cache:

   ```bash
   php bin/php/console exp:velocity cache clear --apply
   ```

9. Open **Setup > Cache management** in the browser and read the last-cleared column: which clears were made from a
   shell, and by which operating system user?

## Expected result

- Step 3 lists the caches it would clear and changes nothing.
- Step 6 ends with a line starting `PASS`.
- Step 7 ends with `PASS  status read` and has one line each for the static cache, the HTTP cache, the query cache,
  Velocity and PHP.
- Step 9 shows the shell clears with the learner's operating system user.
- The trainer checks by asking a learner which command they would use after a template change.

## Quiz

1. Which option of `ezcache.php` clears every cache carrying one tag?
   A. `--clear-id` B. `--clear-tag` C. `--purge` D. `--list-tags`
2. What does `--dry-run` with `exp:cache` do?
3. Why prefer `--clear-tag=ini` to `--clear-id=ini` after a settings change?
4. Which command clears Velocity's response cache?
5. When does a script need `--allow-root-user`?

<details>
<summary>Answers</summary>

1. **B.**
2. It shows what would be cleared, with sizes, and changes nothing.
3. The tag also clears the global INI cache; the id alone may leave old settings in use
   ([Cache control from the console](../../../../bc/6.0/cache-console.md#in-short)).
4. `exp:velocity cache clear` (or `php bin/php/cache.php velocity clear`).
5. Only when it runs as the operating system root user.

</details>

## Further reading

- [Operating a site](../../../../guides/operating-a-site.md): the caches, part by part.
- [Cache control from the console](../../../../bc/6.0/cache-console.md): every `exp:cache` group and option.
- [Caches](../../../../guides/caches.md#5-from-a-shell): the shell commands next to the page.

## Related pages

- [Module 5: Caches](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
