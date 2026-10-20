---
title: "Lesson 1.2: The built-in search engine"
audience: trainer, admin, developer
category: training
order: 12
summary: Learners explain how the built-in engine eZSearchEngine stores words in the database, how it matches words and phrases, and which shipped limits shape every search. 30 minutes.
keywords: search course, eZSearchEngine, built-in search, ezsearch_word, ezsearch_object_word_link, phrase, stop words, wildcard, SearchSettings, MaximumSearchLimit
type: help
---
# Lesson 1.2: The built-in search engine

Every installation starts with the built-in engine, `eZSearchEngine`. It needs no extra server: its index lives in
two tables of the site's own database. Knowing how it stores and matches words explains most of what learners will
see in the search results later in the course.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 1: How search works](README.md) |
| Duration | 30 minutes (15 presenting, 15 exercise) |
| Level | Intermediate |
| Audience | Site administrators and developers |
| Prerequisites | [Lesson 1.1](where-people-search.md); a terminal on the training installation |

## Learning objectives

After this lesson, learners can:

- describe the two index tables, `ezsearch_word` and `ezsearch_object_word_link`, in one sentence each;
- explain that words are normalized (lower case, accents removed) before they are indexed and searched;
- search for an exact phrase with double quotes and predict the difference to the same words without quotes;
- read the shipped `[SearchSettings]` limits with `exp:ini` and explain what each one protects.

## Trainer's notes

### How to present it

Source: `settings/site.ini [SearchSettings]`, [Site search](../../../creators/website-creators/site-search.md) and
[10.8 Search indexing and image handling](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling).

1. **Index in the database** (4 minutes). When an object is published, the engine takes the text of every attribute
   marked **Searchable** in its class, splits it into words and stores each word once in `ezsearch_word`. For every
   occurrence it adds a row to `ezsearch_object_word_link` (which object, which attribute, the position, the class and
   the section). A search looks the words up and joins the links back to the objects.
2. **Normalization** (3 minutes). Before indexing and before searching, text goes through the character
   transformation group `search` of `settings/transform.ini`: decomposition, normalization, removal of diacritics,
   lower case and a cleanup. So `Café`, `cafe` and `CAFE` are the same word to the engine.
3. **Words and phrases** (3 minutes). Several words find items that contain all of them. Words in double quotes,
   `"healthy breakfast"`, are a phrase: the words must stand next to each other in that order. Typographic quotes count
   as straight ones.
4. **Limits** (5 minutes). Show the shipped values with `exp:ini get` (exercise). Explain each:
   `MaximumSearchLimit=30` (most results per request), `MaximumSearchTextWords=16` and
   `MaximumSearchTextLength=1024` (longer texts are cut), `AllowEmptySearch=disabled`, `EnableWildcard=false` with
   `MinCharacterWildcard=3`, and the stop word rule `StopWordThresholdValue=100` with
   `StopWordThresholdPercent=60`: on a site with more than 100 objects, a word that appears in more than 60 percent of
   them is ignored.

### Common questions

**Why does the word "the" find nothing on a large site?** It is a stop word by the threshold rule: it appears in more
than 60 percent of the objects. On a small training site with fewer than 100 objects the rule does not apply yet.

**Can it find part of a word?** Only with wildcards (`EnableWildcard=true`), and at least `MinCharacterWildcard`
characters before the `*`. It is off by default because it can slow the site down.

**Does the index hold the whole text?** It holds words and their positions, not the text itself. The result list
shows the content from the objects.

**Does it work on MongoDB?** Yes; on a MongoDB installation run the cronjobs once after the install to build the
index ([Search backends](../../../hosting/basics/search-backends.md)).

### Pitfalls

- **Reading the INI file directly.** Learners open `settings/site.ini` and take its values as the truth. Overrides
  can change them. Teach `exp:ini where`, which shows every file and the value in effect.
- **Expecting relevance ranking.** The built-in engine has no relevance model like Solr's. Do not promise better
  ordering than it gives.

## Hands-on exercise

Use your own training installation and a terminal in its root directory. Add `--allow-root-user` only if you work as
`root`.

1. Print the search engine in effect:

   ```bash
   php bin/php/console exp:ini get site.ini/SearchSettings/SearchEngine
   ```

2. Show where it is set and whether anything overrides it:

   ```bash
   php bin/php/console exp:ini where site.ini/SearchSettings/SearchEngine
   ```

3. Print the limits one by one: `MaximumSearchLimit`, `MaximumSearchTextWords`, `AllowEmptySearch`,
   `EnableWildcard`, `StopWordThresholdValue` and `StopWordThresholdPercent` (same command as step 1, other names).
4. In the administration, create an **Article** below a folder of your own with the title `Morning café routine` and
   publish it.
5. Search for `cafe` (without the accent) with the **Search...** field. Then search for `CAFÉ`.
6. Search for `routine morning`, then for `"routine morning"` with double quotes, then for `"morning café"`.
7. Write down which searches found the article and why.

## Expected result

- Step 1 prints `eZSearchEngine` on a fresh installation.
- Step 2 lists `settings/site.ini` and any override file, with the value in effect.
- Step 5: both searches find the article, because accents and case are removed before matching.
- Step 6: `routine morning` finds it (both words present), `"routine morning"` does not (wrong order as a phrase),
  `"morning café"` does.
- The trainer checks by asking two learners to explain step 6.

## Quiz

1. In which two tables does the built-in engine keep its index?
2. What does `StopWordThresholdPercent=60` do on a site with 500 objects?
3. Which search finds an article titled "Fresh green salad"?
   A. `"salad green"` B. `"green salad"` C. `salad*` with wildcards off D. none of them
4. Why is `AllowEmptySearch` disabled by default?
5. Which command shows every file that sets a setting and the value in effect?

<details>
<summary>Answers</summary>

1. `ezsearch_word` (each word once) and `ezsearch_object_word_link` (each occurrence in an object).
2. A word that appears in more than 60 percent of the objects is ignored in searches (the threshold value 100 is
   exceeded).
3. **B.** A phrase must match the order; wildcards are off by default.
4. Empty searches take a lot of resources (`settings/site.ini` comment).
5. `php bin/php/console exp:ini where <file>/<block>/<setting>`
   ([Change settings from the command line](../../../../features/6.0/exp-ini-command.md)).

</details>

## Further reading

- [Site search](../../../creators/website-creators/site-search.md): the settings table for the built-in engine.
- [Change settings from the command line: exp:ini](../../../../features/6.0/exp-ini-command.md): reading and writing
  settings safely.
- [Search backends](../../../hosting/basics/search-backends.md): the built-in engine from a host's point of view.

## Related pages

- [Module 1: How search works](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
