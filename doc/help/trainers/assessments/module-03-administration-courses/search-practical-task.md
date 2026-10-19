---
title: "Lesson 3.4: Search, practical task"
audience: trainer, admin, developer
category: training
order: 34
summary: A 45-minute practical task for the end of the Search course, find items with scope, advanced search and the content finder, switch on delayed indexing on a training installation, prove that new content waits for cron, index it by hand, rebuild the index and switch the setting back, scored with a five-criterion rubric. 70 minutes with scoring.
keywords: assessments and practical exercises, search, practical task, rubric, search scope, advanced search, content finder, delayed indexing, ezpending_actions, runcronjobs, indexcontent, updatesearchindex
type: help
---
# Lesson 3.4: Search, practical task

Search problems are nearly always "it is not found yet". In this task the learner first searches like an editor, then
reproduces the most common indexing fault on purpose, fixes it, and leaves the installation as they found it.

> **Note**
> This is the trainer's own assessment, not an official certification. Learners change settings on their own training
> installation only; never on a live site.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 3: Assessing the administration courses](README.md) |
| Duration | 70 minutes (5 briefing, 45 task, 20 scoring) |
| Level | Intermediate |
| Audience | Learners at the end of the Search course |
| Prerequisites | [Lesson 3.3](search-written-test.md); administrator access and a shell on the learner's own training installation |

## Learning objectives

Learners who pass this task have shown that they can:

- find items with the search scope, the advanced search and the content finder;
- switch on delayed indexing and explain its effect;
- index waiting content with the default cron part;
- rebuild the index from the shell;
- restore the original setting and check it.

## Trainer's notes

### How to run it

1. **Before**: one training installation per learner with a site package that has content (articles in several
   folders). Learners need a shell in the installation root.
2. **Briefing**: hand out the steps and the rubric; 45 minutes. Remind learners to note every command they run.
3. **Scoring**: read the learner's notes and check the final setting with
   `./console exp:ini get site.ini/SearchSettings/DelayedIndexing --allow-root-user`.

### Common questions

**The new article is found at once, even with delayed indexing on.** The INI cache was not cleared after the change;
that is evidence for the rubric, not a broken lab.

**Can the cron part be run from Setup > Cronjobs instead?** Yes, the part that holds `indexcontent.php`.

### Pitfalls

- **Settings left changed.** A learner who forgets step 10 leaves delayed indexing on; the next course meets a
  confusing installation. It is scored.

## Hands-on exercise

The task, as handed to learners. Use your own training installation; write down every command.

1. In the administration, search for a word that appears in several articles. Then narrow the same search with
   **Search scope** to **Current location** below one folder. Note both result counts.
2. Open **Advanced search**. Search for the same word with **Class** set to *Article* and **Published** set to the last
   year. Note the count.
3. Note the node number of one article from its page. Open the dashboard, then open that article again by typing its
   number in the **Content finder**.
4. Switch on delayed indexing in `settings/override/site.ini.append.php`:

   ```ini
   [SearchSettings]
   DelayedIndexing=enabled
   ```

   and clear the INI cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ```

5. Create and publish an article titled `Indexing test <your name>` with a word nobody else uses, for example
   `zebracrossing`.
6. Search for that word. Note the result.
7. Run the default cron part by hand:

   ```bash
   php runcronjobs.php -s <your public siteaccess> --allow-root-user
   ```

   (or run it from **Setup > Cronjobs**). Search again and note the result.
8. Rebuild the whole index:

   ```bash
   php bin/php/updatesearchindex.php --clean --allow-root-user
   ```

   Search for the word once more.
9. Write two sentences: why step 6 found nothing, and what a host must make sure of on a site with delayed indexing.
10. Remove the two lines from step 4 (or set `DelayedIndexing=disabled`), clear the INI cache again and check the value
    with `./console exp:ini get site.ini/SearchSettings/DelayedIndexing --allow-root-user`.

## Expected result

- Notes with three result counts that make sense (scoped and filtered counts no larger than the first).
- Step 6 found nothing; step 7 and step 8 found the new article.
- Two correct sentences: publishing only queued the object; cron must run the default part.
- The final check prints `disabled`.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Searching in the administration | Nothing found | One of scope, advanced, finder | Two of them | All three, counts noted | The notes |
| Delayed indexing switched on | Not done | Setting written, cache not cleared | On, effect not observed | On, step 6 shows nothing found | The notes, the settings file |
| Indexing by cron | Not done | Wrong part or siteaccess | Ran, result not checked | Ran, article found | The notes, the command |
| Rebuilding the index | Not done | Ran without `--clean` and without explanation | Ran, result not checked | Ran with `--clean`, article found | The notes |
| Leaving it clean (must) | Setting still `enabled` | Changed, cache not cleared | Disabled, not checked | Disabled, cache cleared, checked | `exp:ini get` output |

**Pass**: 10 of 15 points or more and **Leaving it clean** at 2 or 3.

## Quiz

Debrief questions to ask after scoring.

1. In which table did your article wait between step 5 and step 7?
2. When is delayed indexing worth switching on for real?
3. On a large site, when would you run step 8, and as which user?
4. What did step 1 teach you about the scope of a search?

<details>
<summary>Answers</summary>

1. `ezpending_actions` ([10.8](../../../../install/10-after-installing.md#search)).
2. When publishing large objects is slow; it makes publishing faster, at the price of depending on cron
   ([Search backends](../../../hosting/basics/search-backends.md#delayed-indexing-and-cron)).
3. In a quiet hour, as the site's user ([Search backends](../../../hosting/basics/search-backends.md#rebuilding-the-index)).
4. A scope below the current location finds only what is below it
   ([Searching](../../../searching.md#narrow-the-search)).

</details>

## Further reading

- [Search backends](../../../hosting/basics/search-backends.md): indexing, rebuilding and Solr.
- [Cronjobs](../../../../guides/cronjobs.md): running a part from the page or the shell.

## Related pages

- [Lesson 3.3: Search, written test](search-written-test.md)
- [Module 3: Assessing the administration courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
