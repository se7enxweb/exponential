---
title: "Project 6: search that finds the right things"
audience: student
category: learning
order: 30
summary: Study project 6 - learn how the built-in search engine indexes your content, plan which attributes of your classes are searchable, change one flag, rebuild the index with updatesearchindex.php, compare search results before and after, and hand in a search test plan with evidence, graded with a rubric.
keywords: study project, search, eZSearchEngine, searchable, search index, updatesearchindex, --clean, advanced search, class attribute, Solr, ezfind, learner
type: help
---
# Project 6: search that finds the right things

Visitors judge a site by whether its search finds what they look for. In this project you learn how the built-in
search engine indexes content, decide which fields should be found, change one, rebuild the index and prove the
difference.

## Contents

1. [At a glance](#at-a-glance)
2. [Goal](#goal)
3. [Before you start](#before-you-start)
4. [Tasks](#tasks)
5. [Hand in](#hand-in)
6. [Rubric](#rubric)
7. [Stretch goals](#stretch-goals)

## At a glance

| | |
|---|---|
| Level | Intermediate |
| Time | About 2 hours |
| Paths | Administrator, developer |
| Prerequisites | [project 1](project-01-a-content-class-of-your-own.md); a terminal; a study site that only you use, because classes are shared by everyone on an installation |

## Goal

A search test plan of ten queries with expected results, one changed **Searchable** flag, a rebuilt index, and a
before and after comparison that matches your expectations.

## Before you start

- Read [Searching](../searching.md) for the editor's view.
- The installation's search engine is set in `settings/site.ini [SearchSettings] SearchEngine`; the shipped value is
  `eZSearchEngine`, the built-in engine. It needs no extra server: its index lives in two tables of the site's own
  database ([Lesson 1.2: The built-in search engine](../trainers/search/module-01-how-search-works/the-built-in-search-engine.md)).
- The engine indexes only attributes marked **Searchable** in their class; a changed flag applies to new indexing, so
  existing objects keep their old entries until they are republished or the index is rebuilt
  ([Lesson 2.4: Choosing searchable attributes](../trainers/search/module-02-indexing/choosing-searchable-attributes.md)).

Check the engine on your site:

```bash
php bin/php/console exp:ini where site/SearchSettings/SearchEngine site
```

## Tasks

1. **Write the test plan.** Ten queries for your club site: words from titles, from summaries, from rich text, a
   phrase in double quotes, a word with an accent, and a word that should **not** be found (for example from an
   internal field). For each, write the items you expect.
2. **Run it before.** Use the quick search and the advanced search (class and class attribute). Record the results.
3. **Read the flags.** In **Setup > Classes**, open your class from project 1. Note which attributes say *Is
   searchable*.
4. **Change one flag.** Add an internal attribute such as "Notes for organizers" (Text block) if you have none, fill it
   in one event with a unique word, and make it **not** searchable. Press **OK** at the end, not leaving the class in
   edit.
5. **Rebuild the index.** As the trainers' lesson shows:

   ```bash
   php bin/php/updatesearchindex.php --clean -s <siteaccess> --allow-root-user
   ```

   `--clean` first empties the whole index, so search finds less and less until the run is complete; without it, the
   old entries stay findable while each object is replaced
   ([Lesson 2.3: Rebuilding the search index](../trainers/search/module-02-indexing/rebuilding-the-search-index.md)).
   Leave out `--allow-root-user` when you are not `root`.
6. **Run it after.** Repeat the ten queries and record the results.
7. **Explain the differences.** For each query whose result changed or did not match your expectation, write why.

## Hand in

- The test plan with expected and actual results, before and after.
- The output lines of the rebuild (the number of objects to index).
- A screenshot of the class with the changed flag.
- A short report: which attributes should be searchable on your site and why.

## Rubric

Score the four common criteria of [Grading your own work](grading-your-own-work.md), and these checks (7):

1. Ten queries with expected results written before testing.
2. The engine in use was shown with `exp:ini where`.
3. One flag was changed on a class the learner owns, on their own installation.
4. The index was rebuilt, and the report says what `--clean` does.
5. The hidden word is no longer found after the rebuild.
6. The report explains a phrase search and an accent search.
7. Every unexpected result has an explanation or an open question.

## Stretch goals

- Read about delayed indexing and the `indexcontent` cronjob
  ([Lesson 2.2](../trainers/search/module-02-indexing/delayed-indexing-and-the-indexcontent-cronjob.md)).
- Compare the built-in engine with Solr and the `ezfind` extension at a glance
  ([Lesson: Solr and ezfind at a glance](../trainers/search/module-01-how-search-works/solr-and-ezfind-at-a-glance.md)).
  Solr is a separate search server; it is not needed for this project.
- Search the help itself and compare how it ranks pages ([Searching](../searching.md#the-help-pages)).

## Related pages

- [Study projects: overview](study-projects-overview.md)
- [Searching](../searching.md)
- The trainers' [Search](../trainers/search/README.md) course
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
