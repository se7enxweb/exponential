---
title: "Lesson 2.3: Rebuilding the search index"
audience: trainer, admin, hosting
category: training
order: 23
summary: Learners decide when the whole search index must be rebuilt, run bin/php/updatesearchindex.php with and without --clean on their training installation, and read its output and audit entry. 30 minutes.
keywords: search course, reindex, rebuild search index, updatesearchindex.php, --clean, import, database move, audit, data.index.rebuild
type: help
---
# Lesson 2.3: Rebuilding the search index

Sometimes the index no longer matches the content: after an import that wrote straight into the database, after a
database move, or after a change to which attributes are searchable. Then the whole index is rebuilt with one script.
This lesson teaches when to run it, how, and how to do it without hurting a live site.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 2: Indexing](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators and hosting staff |
| Prerequisites | [Lesson 2.2](delayed-indexing-and-the-indexcontent-cronjob.md); a terminal in the training installation's root |

## Learning objectives

After this lesson, learners can:

- name four situations that call for a full rebuild;
- run `php bin/php/updatesearchindex.php` with `--clean` and explain what `--clean` removes first;
- read the script's output (*Number of objects to index*) and find the rebuild in the audit trail;
- plan a rebuild on a live site: as the site's user, in a quiet hour, with the right siteaccess.

## Trainer's notes

### How to present it

Source: [Search backends](../../../hosting/basics/search-backends.md#rebuilding-the-index) and
[10.8 Search indexing and image handling](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling).

1. **When** (3 minutes). After an import or a database move; when search results are wrong; after attributes were
   made searchable or not searchable (lesson 2.4); on MongoDB, once after the installation if the cronjobs have not
   built the index yet.
2. **How** (4 minutes). The script reads every published object, 50 at a time, removes its old entry and indexes it
   again, and commits after each batch. With `--clean` it first empties the whole index (*Cleaning up search data*),
   so search finds less and less of the site until the run is complete. Without `--clean` the old entries stay
   findable while each object is replaced.
3. **Where it is recorded** (3 minutes). At the end the script writes an audit entry `data.index.rebuild` with the
   number of objects and whether `--clean` was used. Show **Setup > Audit** if the training installation records
   audits.

### Common questions

**Should I always use --clean?** Use it when the index holds rubbish: objects that no longer exist, or words from
attributes that are no longer searchable. On a live site, a run without `--clean` keeps search working during the
rebuild.

**How long does it take?** It depends on the number of objects and their size. Run it once on the training site and
note the time; on a live site, test on a copy first.

**Does it reindex drafts?** No, only published objects.

**With ezfind?** Use the extension's own script; see Module 4.

### Pitfalls

- **Running as root on a server.** The script can create files that the web server cannot overwrite later. Run it as
  the site's user (`sudo -u <site user> php ...`); `--allow-root-user` is for training machines only.
- **The wrong siteaccess.** On an installation with several databases, `-s <siteaccess>` chooses which one is
  indexed. Make learners name it explicitly.
- **Stopping it halfway with --clean.** The index is then incomplete. Run it again to the end.

## Hands-on exercise

Use your own training installation and a terminal in its root. `<siteaccess>` is your public siteaccess. Add
`--allow-root-user` only if you work as root.

1. Count the published objects the script will see: run it without `--clean` and read the first line.

   ```bash
   php bin/php/updatesearchindex.php -s <siteaccess>
   ```

2. Note the number after *Number of objects to index* and how long the run takes (use `time` in front of the command
   if your shell has it).
3. Search in the administration for a word from a sample article. It is found.
4. Run the rebuild with `--clean`:

   ```bash
   php bin/php/updatesearchindex.php -s <siteaccess> --clean
   ```

5. While it runs (on a larger site you have a few seconds), search for the same word in the administration.
6. When the script has finished, search again.
7. Open **Setup > Audit** if your training installation records audits, and look for the rebuild entry.
8. Write the command you would put in a runbook for a live site, with the site's user and the siteaccess.

## Expected result

- Steps 1 and 4 print *Number of objects to index: N* and progress, and end without errors.
- Step 4 prints *Cleaning up search data* before indexing.
- Step 5 may find fewer results while the run is in progress; step 6 finds everything again.
- Step 7 shows an entry for the rebuild (action `data.index.rebuild`) when audits are recorded.
- The runbook line in step 8 runs as the site's user, names the siteaccess, and says when (a quiet hour).

## Quiz

1. Name three situations that call for a full rebuild.
2. What does `--clean` do?
   A. removes drafts B. empties the index before indexing C. clears all caches D. deletes trashed objects
3. Why can a rebuild without `--clean` be the better choice on a live site?
4. Which option chooses the siteaccess, and why does it matter?
5. Where is a rebuild recorded?

<details>
<summary>Answers</summary>

1. Any three of: after an import, after a database move, when results are wrong, after changing which attributes are
   searchable, once after a MongoDB installation.
2. **B.** "Remove all search data before beginning indexing" (the script's help).
3. Search keeps finding the old entries while each object is replaced; with `--clean` the site's search is
   incomplete until the end.
4. `-s <siteaccess>`; it selects the settings, and with them the database, that are indexed.
5. In the audit trail, as `data.index.rebuild`, with the number of objects and whether `--clean` was used.

</details>

## Further reading

- [Search backends](../../../hosting/basics/search-backends.md#rebuilding-the-index): rebuilding from a host's point
  of view.
- [Audit trail](../../../../features/6.0/audit-trail.md): what the audit records and where to read it.
- [Backups: what to back up](../../../hosting/operations/backups-what-to-back-up.md): the index is rebuilt rather than
  backed up on the built-in engine, since it lives in the database.

## Related pages

- [Module 2: Indexing](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
