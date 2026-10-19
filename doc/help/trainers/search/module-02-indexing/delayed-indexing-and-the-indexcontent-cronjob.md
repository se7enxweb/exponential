---
title: "Lesson 2.2: Delayed indexing and the indexcontent cronjob"
audience: trainer, admin
category: training
order: 22
summary: Learners switch on delayed indexing on their training installation, watch published content wait in the queue, and index it by running the global cronjob part, from the shell and from Setup > Cronjobs. 35 minutes.
keywords: search course, DelayedIndexing, classbased, DelayedIndexingClassList, indexcontent.php, ezpending_actions, index_object, index_moved_node, cronjob, runcronjobs
type: help
---
# Lesson 2.2: Delayed indexing and the indexcontent cronjob

Indexing a large object at publish takes time, and the editor waits for it. Delayed indexing moves that work to a
cronjob: publishing only puts the object in a queue, and the script `indexcontent.php` indexes it later. The price is
that search depends on cron. This lesson shows both sides.

> **Note**
> Change settings only on your own training installation. Every step of this exercise is undone at the end.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 2: Indexing](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators and hosting staff |
| Prerequisites | [Lesson 2.1](indexing-at-publish.md); a terminal in the training installation's root |

## Learning objectives

After this lesson, learners can:

- explain the three values of `[SearchSettings] DelayedIndexing`: `disabled`, `enabled` and `classbased` with
  `DelayedIndexingClassList[]`;
- describe the queue table `ezpending_actions` and its two actions `index_object` and `index_moved_node`;
- run the global cronjob part, which contains `indexcontent.php`, from the shell and from **Setup > Cronjobs**;
- explain why a site with delayed indexing needs a working crontab.

## Trainer's notes

### How to present it

Source: [10.8 Search indexing and image handling](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling),
[Search backends](../../../hosting/basics/search-backends.md#delayed-indexing-and-cron) and
[Cronjobs](../../../../guides/cronjobs.md).

1. **The setting** (3 minutes). `site.ini [SearchSettings] DelayedIndexing` is `disabled` as shipped. `enabled`
   delays every class; `classbased` delays only the classes listed in `DelayedIndexingClassList[]` (class
   identifiers), for example only a large `file` or `article` class.
2. **The queue** (3 minutes). With delayed indexing, publishing writes a row to `ezpending_actions`: `index_object`
   for the object, or `index_moved_node` for a move when the engine keeps paths (ezfind). Each row has its time
   (`created`); the cronjob takes the oldest first, 50 at a time, and removes a row once it is indexed.
3. **The cronjob** (4 minutes). `indexcontent.php` is in `[CronjobSettings] Scripts[]`, the part that **Setup >
   Cronjobs** calls **Global**, which `runcronjobs.php` runs when it is given no part name. Show the page and the
   card of the global part. Stress the consequence: **without the crontab, new content never appears in search.**

### Common questions

**When should we use it?** When publishing large objects is slow because of indexing, for example long documents or
many attributes. The documentation names faster publishing as the reason.

**How long do editors wait to find their item?** Until the next run of the global part; that depends on the crontab
schedule of the site. Tell editors the schedule; the help says new items may take "a few minutes" on such sites.

**Can I index one queued object by hand?** Run the global part, or only the script:
`php runcronjobs.php -s <siteaccess> --script=indexcontent.php`.

### Pitfalls

- **Forgetting the INI cache.** After changing the setting, clear the INI cache, or the old value stays in effect.
- **Running cron as root.** Files created by root can break the site later. Run as the site's user; the
  `--allow-root-user` flag is only for training machines where everybody works as root.
- **Leaving it on.** The exercise ends by switching it off again, so the next lessons behave as shipped.

## Hands-on exercise

Use your own training installation and a terminal in its root. `<siteaccess>` is the name of your public siteaccess
(see **Setup > System information**, section **Siteaccesses**). Add `--allow-root-user` only when you work as root.

1. Switch on delayed indexing for the whole installation:

   ```bash
   php bin/php/console exp:ini set site.ini/SearchSettings/DelayedIndexing enabled global
   ```

2. Clear the INI cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   ```

3. In the administration, create and publish an **Article** titled `Queued plum`, with the word `plum` in the
   required fields.
4. Search for `plum` with the **Search...** field.
5. Check that `indexcontent.php` is in the global part (the first lines of the list), then run only that script:

   ```bash
   php runcronjobs.php --list
   php runcronjobs.php -s <siteaccess> --script=indexcontent.php
   ```

6. Search for `plum` again.
7. Publish a second article titled `Queued apricot`. This time open **Setup > Cronjobs**, choose your site, find the
   **Global** part and press **Run** for it. Watch **Output**.
8. Search for `apricot`.
9. Switch delayed indexing off again and clear the INI cache:

   ```bash
   php bin/php/console exp:ini set site.ini/SearchSettings/DelayedIndexing disabled global
   php bin/php/ezcache.php --clear-tag=ini
   ```

10. Publish a third article with the word `quince` and search for it at once.

## Expected result

- Step 4 finds nothing: the object waits in `ezpending_actions`.
- Step 5 lists `indexcontent.php` in the global part, and the script run indexes the queued object; step 6 finds it.
- Step 7 shows the run in **Output**; step 8 finds the second article.
- Step 10 finds the article at once: indexing at publish is back.
- The trainer checks `exp:ini get site.ini/SearchSettings/DelayedIndexing` on two machines: both print `disabled`.

## Quiz

1. Which value of `DelayedIndexing` delays only some classes, and which setting lists them?
2. In which table does a delayed object wait?
   A. `ezsearch_word` B. `ezpending_actions` C. `ezcontentobject_version` D. `ezcronjob_queue`
3. Which cronjob script indexes queued objects, and in which part is it shipped?
4. A site with delayed indexing reports that new articles are never found. What do you check first?
5. Which action does a move queue for a search engine that keeps paths?

<details>
<summary>Answers</summary>

1. `classbased`, with the class identifiers in `DelayedIndexingClassList[]`.
2. **B.** ([10.8](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling))
3. `indexcontent.php`, in `[CronjobSettings] Scripts[]`, the global (default) part.
4. Whether the crontab runs the global part for that site (**Setup > Cronjobs** shows status and last run).
5. `index_moved_node` (the object and every object below its main node are reindexed).

</details>

## Further reading

- [Cronjobs: running, scheduling and following them](../../../../guides/cronjobs.md): the page, the crontab and the
  shell.
- [Search backends](../../../hosting/basics/search-backends.md#delayed-indexing-and-cron): delayed indexing from a
  host's point of view.
- [Cron jobs](../../../hosting/operations/cron-jobs.md): scheduling cron on a server.

## Related pages

- [Module 2: Indexing](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
