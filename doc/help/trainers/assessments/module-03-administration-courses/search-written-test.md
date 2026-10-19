---
title: "Lesson 3.3: Search, written test"
audience: trainer, admin, developer
category: training
order: 33
summary: A twelve-question written test for the end of the Search course, on the quick and advanced search, the content finder, the built-in engine and its limits, delayed indexing and cron, rebuilding the index, Solr through eZ Find and search statistics, with an answer key that cites the documentation. 25 minutes for learners.
keywords: assessments and practical exercises, search, written test, quick search, advanced search, content finder, eZSearchEngine, delayed indexing, indexcontent, updatesearchindex, Solr, eZ Find, LogSearchStats
type: help
---
# Lesson 3.3: Search, written test

This test closes the Search course. It covers both sides: what editors and visitors do with search, and what
administrators run so that search keeps finding new content.

> **Note**
> This is the trainer's own assessment, not an official certification. Questions 1 to 5 suit editors; questions 6 to
> 12 suit administrators and developers.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 3: Assessing the administration courses](README.md) |
| Duration | 40 minutes (5 briefing, 25 test, 10 going through the answers) |
| Level | Intermediate |
| Audience | Learners at the end of the Search course |
| Prerequisites | The Search course; [Lesson 1.2](../module-01-designing-assessments/writing-written-tests.md) for the trainer |

## Learning objectives

Learners who pass this test have shown that they can:

- narrow a search in the administration and open an item by number or address;
- explain why an existing item is not found;
- explain where the built-in index lives and what delayed indexing changes;
- name the command that rebuilds the index and when to run it;
- describe what Solr through eZ Find adds and what it needs.

## Trainer's notes

### How to run it

1. Hand out the questions without the key; 25 minutes.
2. Go through the answers with [Searching](../../../searching.md) and
   [Search backends](../../../hosting/basics/search-backends.md) on the projector.

### Common questions

**We teach only the built-in search.** Drop questions 11 and 12, and change the pass mark accordingly.

**Is `LogSearchStats` really a privacy matter?** The statistics store every phrase a visitor typed, which can hold
personal data; that is why question 10 is in the test.

### Pitfalls

- **Wildcards.** Learners used to other systems expect `*` to work. On the built-in engine `EnableWildcard` is off as
  shipped.

## The test

1. Which key moves the focus to the administration's search field?
2. How do you search only below the place you are looking at?
3. Which advanced search field limits results to one content type, such as *Article*?
4. You know an item's address on the public site. Where do you type it to open the item in the administration?
5. An editor cannot find an item that certainly exists. Give three possible reasons.
6. Where does the built-in search engine keep its index?
7. What changes when `[SearchSettings] DelayedIndexing` is `enabled`?
8. On a site with delayed indexing, cron has stopped. What do editors notice?
9. After a database import, search results are wrong. Which command rebuilds the index, and what does `--clean` do?
10. Why is `LogSearchStats` off as shipped?
11. What does Solr through eZ Find need that the built-in search does not?
12. Which command tells you whether a configured Solr answers?

<details>
<summary>Answers</summary>

1. `/`, when the focus is not in another field ([Searching](../../../searching.md#the-quick-search)).
2. In **Search scope**, set **Where** to **Current location**
   ([Searching](../../../searching.md#narrow-the-search)).
3. **Class** ([Searching](../../../searching.md#the-advanced-search)).
4. In the **Content finder** below the tree, then **Find**
   ([Searching](../../../searching.md#open-an-item-by-its-number-or-address)).
5. Three of: the role hides it, it is in the trash, it is only a draft, the index has not caught up yet
   ([Searching](../../../searching.md#tips)).
6. In the site's database ([Search backends](../../../hosting/basics/search-backends.md#the-two-options)).
7. Publishing only queues the object in `ezpending_actions`; the default cron part's `indexcontent.php` indexes it
   later ([Search backends](../../../hosting/basics/search-backends.md#delayed-indexing-and-cron)).
8. New content never appears in search
   ([Search backends](../../../hosting/basics/search-backends.md#delayed-indexing-and-cron)).
9. `php bin/php/updatesearchindex.php --clean --allow-root-user`; `--clean` removes the old index first
   ([Search backends](../../../hosting/basics/search-backends.md#rebuilding-the-index)).
10. The statistics store every phrase a visitor typed, which can hold personal data
    ([Search backends](../../../hosting/basics/search-backends.md#the-built-in-search)).
11. A Solr server, local or remote, the `ezfind` extension, and `[SolrSettings]` in `solr.ini` switched on
    ([Search backends](../../../hosting/basics/search-backends.md#solr-through-ez-find)).
12. `php bin/php/console exp:solr status` (or `ping`)
    ([Search backends](../../../hosting/basics/search-backends.md#the-expsolr-command)).

</details>

## Scoring

- One point per question; question 5 needs three reasons for the point.
- Pass: 8 of 12 points or more, with question 8 correct.
- Editors only (questions 1 to 5): pass with 4 of 5.

## Further reading

- [Searching](../../../searching.md): the editor's side.
- [Search backends](../../../hosting/basics/search-backends.md): engines, indexing and Solr.
- [10.8 Search indexing and image handling](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling).

## Related pages

- [Lesson 3.4: Search, practical task](search-practical-task.md)
- [Module 3: Assessing the administration courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
