---
title: "Lesson 4.3: Indexing with Solr"
audience: trainer, admin, hosting
category: training
order: 43
summary: Learners build the Solr index with the extension's updatesearchindexsolr.php, explain commits, the optimize and subtree cronjob parts, and why moves and visibility changes reach Solr, and keep the index in step with the content. 30 minutes.
keywords: search course, ezfind, Solr, updatesearchindexsolr.php, --clean, --clean-all, --conc, commit, CommitWithin, OptimizeOnCommit, ezfoptimizeindex, ezfindexsubtree, index_moved_node
type: help
---
# Lesson 4.3: Indexing with Solr

With ezfind, the index lives in Solr instead of the database. Publishing still indexes, delayed indexing still works,
but there are new pieces: a reindex script of its own, commits that make changes visible, cronjob parts for optimizing
and for subtrees, and operations such as moves that now need index work. This lesson builds the index and explains
the pieces.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 4: Solr and ezfind](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Advanced |
| Audience | Site administrators and hosting staff |
| Prerequisites | [Lesson 4.2](exp-solr-and-the-solr-settings.md); ezfind active and Solr answering |

## Learning objectives

After this lesson, learners can:

- build the Solr index with `extension/ezfind/bin/php/updatesearchindexsolr.php` and choose between `--clean` and
  `--clean-all`;
- explain what a commit is and name the `[IndexOptions]` settings that control commits and optimizing;
- name the cronjob parts the extension adds (`ezfoptimizeindex`, `ezfindexsubtree`, and its script in `frequent`);
- explain why moves, new locations, hiding and revealing reach Solr while the built-in engine needs nothing.

## Trainer's notes

### How to present it

Source: [10.8 Search indexing and image handling](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling),
[Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md#indexing) and the
extension's `ezfind.ini [IndexOptions]` and `cronjob.ini.append.php`.

1. **The reindex script** (3 minutes). The extension's notes reindex with
   `php extension/ezfind/bin/php/updatesearchindexsolr.php -s <admin siteaccess>`. Its options: `--clean` removes
   the search data of the current installation first; `--clean-all` removes the data of all installations in that
   Solr (dangerous on a shared Solr); `--conc` sets the number of parallel processes (2 when not given);
   `--commit-within` sets the commit delay in seconds.
2. **Commits** (3 minutes). Solr shows new documents only after a commit. `[IndexOptions]` controls it:
   `DisableDirectCommits=false` (commit at once), `CommitWithin` (milliseconds, `0` ignores it), `EnableSoftCommits`,
   `OptimizeOnCommit=disabled` (optimize by cron instead). The extension also ships
   `bin/php/ezsolrcommit.php` and `bin/php/ezsolroptimize.php`.
3. **Paths in the index** (4 minutes). Solr keeps paths, so with delayed indexing a move queues `index_moved_node` and
   `index_object`; adding a location or a swap queues `index_object`; hiding and revealing are indexed at once and
   queue the extension's own subtree action. The extension adds `ezfindexsubtree.php` to the `frequent` part and a
   part of its own, and `ezfoptimizeindex.php` in the part `ezfoptimizeindex`.

### Common questions

**Is the database index still used?** No. With `SearchEngine=ezsolr` searches go to Solr. The built-in tables are
left as they are.

**Which script do I use after an import?** With ezfind, the extension's `updatesearchindexsolr.php`; the hosting guide
says to reindex with the extension's own tools.

**How often should we optimize?** The extension's settings suggest a cronjob "typically every day or every week" and
note that not optimizing at all has a rather low impact.

### Pitfalls

- **`--clean-all` on a shared Solr.** It removes every installation's data. Teach `--clean` and say `--clean-all`
  only exists.
- **No cron for the frequent part.** Visibility and subtree changes then wait. Check the crontab as in
  [Lesson 2.2](../module-02-indexing/delayed-indexing-and-the-indexcontent-cronjob.md).
- **Running the kernel script by habit.** It works through the configured engine, but the extension's script is the
  documented one for Solr and offers parallel processes.

## Hands-on exercise

Use your training installation from lessons 4.1 and 4.2, with Solr running. `<admin siteaccess>` is your admin
siteaccess. Add `--allow-root-user` only if you work as root.

1. Search in the administration for a word from the sample content. Note the result (the index is still empty).
2. Build the Solr index:

   ```bash
   php extension/ezfind/bin/php/updatesearchindexsolr.php -s <admin siteaccess> --clean
   ```

3. Read the output: the number of processes and the progress.
4. Search for the same word again.
5. List the cronjob parts and find the extension's scripts:

   ```bash
   php runcronjobs.php --list
   ```

6. Print the commit settings:

   ```bash
   php bin/php/console exp:ini get ezfind.ini/IndexOptions/DisableDirectCommits
   php bin/php/console exp:ini get ezfind.ini/IndexOptions/OptimizeOnCommit
   ```

7. Publish a new article with the word `persimmon`, search for it, then hide it and search the public site as a
   visitor.
8. Run the optimize part once: `php runcronjobs.php -s <admin siteaccess> ezfoptimizeindex`.

## Expected result

- Step 1 finds nothing or almost nothing; step 2 ends without errors and step 4 finds the content.
- Step 3 names the number of processes (2 unless `--conc` was given).
- Step 5 lists `ezfindexsubtree.php` in `frequent` and in its own part, and `ezfoptimizeindex.php` in
  `ezfoptimizeindex`.
- Step 6 prints `false` and `disabled`.
- Step 7 finds the new article at once; after hiding, the visitor does not find it.
- The trainer checks step 4 on every machine.

## Quiz

1. Which option of `updatesearchindexsolr.php` removes only the current installation's data?
   A. `--clean` B. `--clean-all` C. `--conc` D. `--commit-within`
2. What makes a newly indexed document visible in Solr's results?
3. Why does a move queue work for Solr but not for the built-in engine?
4. Which cronjob script optimizes the Solr index, and in which part?
5. Name two settings of `[IndexOptions]` and what they do.

<details>
<summary>Answers</summary>

1. **A.** `--clean-all` removes the data of all installations.
2. A commit (direct, within `CommitWithin`, a soft commit, or a commit by cron).
3. Solr keeps paths in its index; the built-in engine's index holds none
   ([10.8](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling)).
4. `ezfoptimizeindex.php`, in the part `ezfoptimizeindex`.
5. Any two of: `OptimizeOnCommit`, `DisableDirectCommits`, `CommitWithin`, `DisableDeleteCommits`,
   `EnableSolrAttributeStorage`, `EnableSoftCommits`, with their meaning from the extension's `ezfind.ini`.

</details>

## Further reading

- [10.8 Search indexing and image handling](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling):
  what each operation queues for an engine with paths.
- [Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md#indexing): indexing
  for website creators.
- [Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md): parts added by extensions.

## Related pages

- [Module 4: Solr and ezfind](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
