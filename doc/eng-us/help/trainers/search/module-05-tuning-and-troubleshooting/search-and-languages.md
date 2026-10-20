---
title: "Lesson 5.2: Search and languages"
audience: trainer, admin
category: training
order: 52
summary: Learners explain how a translated object is indexed and which translations a siteaccess's search lists, test a search in two languages on their training installation, and name ezfind's language settings. 25 minutes.
keywords: search course, languages, translations, multilingual search, SiteLanguageList, always available, SearchMainLanguageOnly, MultiCore, LanguagesCoresMap, siteaccess
type: help
---
# Lesson 5.2: Search and languages

On a multilingual site the same item exists in several languages, and visitors of each siteaccess expect results in
their language. This lesson shows learners how the built-in engine handles translations, what decides the language
of a result, and which settings ezfind adds for languages.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 5: Tuning and troubleshooting](README.md) |
| Duration | 25 minutes (10 presenting, 15 exercise) |
| Level | Intermediate |
| Audience | Site administrators of multilingual sites |
| Prerequisites | [Lesson 5.1](tuning-the-search-settings.md); a training installation with at least two content languages |

## Learning objectives

After this lesson, learners can:

- explain that the built-in engine indexes the words of every translation of an object;
- explain that a siteaccess lists only objects available in its languages (`[RegionalSettings] SiteLanguageList[]`
  and the *always available* flag), under their name in the first language that exists;
- test a search for a word that exists only in one translation, on two siteaccesses;
- name ezfind's `[LanguageSearch]` settings `SearchMainLanguageOnly`, `MultiCore` and `LanguagesCoresMap[]`.

## Trainer's notes

### How to present it

Source: [Languages](../../../languages.md), [Content languages](../../../../guides/content-languages.md), the built-in
engine (`kernel/search/plugins/ezsearchengine/`) and the extension's `ezfind.ini [LanguageSearch]`.

1. **Indexing** (3 minutes). When an object is published, the built-in engine indexes each searchable attribute in all
   of its translations. A word from the German text and a word from the English text both point to the same object.
2. **Listing** (4 minutes). A search runs in a siteaccess. The results are filtered by that siteaccess's languages,
   the same filter content lists use: an object is listed when it exists in one of the languages of
   `SiteLanguageList[]` or is marked always available, and its name is taken from the first of those languages it
   has. So a German word can find an object on the English siteaccess when that object also exists in English; it
   is then shown in English.
3. **ezfind** (3 minutes). With Solr, `SearchMainLanguageOnly=enabled` (shipped) returns only results in the first
   language of `SiteLanguageList[]`; `disabled` uses the fallback order and the always-available flag.
   `MultiCore=enabled` with `LanguagesCoresMap[<locale>]=<core>` indexes each language in its own Solr core for
   language-specific analysis; unmapped languages are then not indexed.

### Common questions

**Why does a visitor on the English site get a result for a German word?** The object has a German translation that
contains it and also exists in English (or is always available), so the English siteaccess lists it. This is how the
built-in engine works; test it on your own content.

**Can the built-in engine search one language only?** It has no setting for that. Language-specific search is one of
the reasons to consider ezfind with `SearchMainLanguageOnly` or several cores.

**Do accents matter?** No: the search transformation removes diacritics and case in every language (lesson 1.2).

### Pitfalls

- **A training site with one language.** The exercise needs two. Add a second content language before the class, or
  run the exercise as a demonstration.
- **Testing in the admin only.** The admin siteaccess usually shows every language. Test on the public siteaccesses.

## Hands-on exercise

Use your own training installation with two content languages and a public siteaccess for each (for example an
English and a German one; see **Setup > Languages** and **Setup > System information**, section **Siteaccesses**).

1. Print the languages of each public siteaccess:

   ```bash
   php bin/php/console exp:ini get site.ini/RegionalSettings/SiteLanguageList siteaccess:<first siteaccess>
   php bin/php/console exp:ini get site.ini/RegionalSettings/SiteLanguageList siteaccess:<second siteaccess>
   ```

2. Create and publish an article in the first language with the word `blueberry` in its title.
3. Translate it into the second language and put the word `heidelbeere` (or another word that exists only there) in
   the translated title. Publish.
4. On the first public siteaccess, as a visitor, search for `blueberry`, then for `heidelbeere`.
5. On the second public siteaccess, search for both words.
6. Note in which language the result's name is shown each time.
7. Create a second article in the second language only, with the word `preiselbeere`. Search for it on both public
   siteaccesses.

## Expected result

- Steps 4 and 5 find the translated article for both words on both siteaccesses, shown in the siteaccess's language.
- Step 7 finds the second article only on the siteaccess whose languages include the second language (unless the
  object is always available).
- The trainer asks one learner to explain step 7 with the words *SiteLanguageList* and *always available*.

## Quiz

1. An object exists in English and German. Which translations' words are in the built-in index?
2. Which setting decides which languages a siteaccess shows?
   A. `[SearchSettings] SearchLanguage` B. `[RegionalSettings] SiteLanguageList[]` C. `[SolrBase] Shards[]` D. none
3. An object exists only in German and is not always available. Is it found on a siteaccess whose languages are only
   English?
4. What does ezfind's `SearchMainLanguageOnly=enabled` do?
5. With `MultiCore=enabled`, what happens to a language that is not in `LanguagesCoresMap[]`?

<details>
<summary>Answers</summary>

1. The words of all its translations.
2. **B.**
3. No; the siteaccess lists only objects available in its languages.
4. It returns only results in the first language of `SiteLanguageList[]` (the extension's `ezfind.ini`).
5. It is not indexed ("Unmapped languages won't be indexed").

</details>

## Further reading

- [Languages](../../../languages.md): languages for editors and administrators.
- [Content languages](../../../../guides/content-languages.md): adding and managing content languages.
- [Translations and languages](../../../../features/6.0/translations-and-languages.md): the feature reference.

## Related pages

- [Module 5: Tuning and troubleshooting](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
