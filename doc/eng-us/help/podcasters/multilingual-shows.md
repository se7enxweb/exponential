---
title: Multilingual shows
audience: podcaster
category: podcasting-streaming
order: 38
summary: How to run a show in several languages on an Exponential site - translated episodes or separate shows per language, translatable media fields, one podcast feed per language with its Language field and siteaccess, and the checks that each feed carries the right language.
keywords: multilingual, languages, translation, translated episodes, dubbing, language version, siteaccess, feed language, Language field, translatable attribute, language switcher
type: help
---
# Multilingual shows

This page is for producers who publish a show in more than one language, or several shows for different language
audiences on one site. Exponential stores translations of every item and serves each language through its own
siteaccess ([Content languages](../../guides/content-languages.md)). How that maps to a show depends on whether the
episodes are really the same in each language.

## Contents

1. [Two models](#two-models)
2. [Translated episodes](#translated-episodes)
3. [Separate shows per language](#separate-shows-per-language)
4. [One feed per language](#one-feed-per-language)
5. [Checks](#checks)

## Two models

| Model | When | In Exponential |
|---|---|---|
| **Translated episodes** | the same episode exists in several languages (dubbed, or re-recorded from one script) | one item per episode, with a translation per language |
| **Separate shows** | each language has its own content and rhythm | one show folder per language, each with its own episodes |

Mixing is possible, but decide per show; a feed lists items, and each model gives a clean feed.

## Translated episodes

1. The site has the languages and a siteaccess for each
   ([Building a multilingual site](../creators/website-creators/multilingual-sites.md)).
2. In the episode class, the media field is **translatable**, so each translation carries its own audio file
   ([Building an episode class](building-an-episode-class.md#choices-that-matter-later)). Title, notes and transcript
   are translatable too.
3. Publish the episode in its main language, then translate it: choose the language next to **Edit**, upload that
   language's audio and translate the notes ([Languages](../languages.md#translate-an-item)).

The translations of one item share its remote id, so in each language's feed the episode has the same `guid`. Apps
treat feeds as separate shows, so that does no harm, but keep it in mind if you merge feeds later.

If the audio is the same in every language (music, a language-neutral recording), make the media field not
translatable and translate only the text.

## Separate shows per language

Create one show folder per language, each with its own episodes in that language, and treat each as its own show:
its own feed, artwork, description and directory listing. This is simpler when the content differs, and each feed
holds only one language.

## One feed per language

Make one podcast export per language:

| Field | Set |
|---|---|
| **Name**, **Description**, **Summary** | in that language |
| **Language** (Apple Podcasts section) | that language's code, for example `de` or `fr-ca`; empty uses the siteaccess language |
| **Site URL** | the address of that language's site, so links and enclosures point to it (the RSS card's **Links point to** shows which siteaccess an empty Site URL uses) |
| **Access URL** | distinct per language, for example `example-show-de` |
| Source | the show folder (translated model) or that language's show folder (separate model) |

Which translation the feed shows depends on the siteaccess that serves it: each siteaccess has its own language list
(`SiteLanguageList`), and the feed cache is kept per siteaccess and feed (`kernel/private/classes/views/rss/feed.php`).
Open each feed through its own language's host.

Episodes not yet translated may be shown in another language where the siteaccess's fallback languages allow it
([Fallbacks and untranslated content](../creators/website-creators/multilingual-sites.md#fallbacks-and-untranslated-content)).
For a feed, publish the translation before relying on it.

## Checks

- [ ] Each feed's `language` element says the right language.
- [ ] Each item's title and description are in that language.
- [ ] Each enclosure is that language's audio file.
- [ ] Links in each feed point to that language's site.
- [ ] The episode page offers a language switcher to the other versions
      ([Language switcher and language URLs](../creators/website-creators/language-switcher-and-language-urls.md)).

## Related pages

- [Content languages](../../guides/content-languages.md)
- [Translation routine](../customers/day-to-day/translation-routine.md)
- [Building a multilingual site](../creators/website-creators/multilingual-sites.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
