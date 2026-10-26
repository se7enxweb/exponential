---
title: "Story: languages and right to left"
audience: press
category: press-media
order: 64
summary: A feature story about languages in Exponential - 268 locale files, 40 of them right to left, 71 administration translations, all of them complete, an administration that turns around for Hebrew and Arabic - with printable facts, a counting difference to watch, and the claims to avoid.
keywords: story, languages, locales, translations, right to left, RTL, Hebrew, Arabic, multilingual, internationalization, press
type: help
---
# Story: languages and right to left

This page is for journalists who write for language communities, international audiences or regional media.
Language figures are easy to inflate. This page explains the three kinds of language support, gives the documented
counts with their date, and shows where two sources count differently.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [Where the sources differ](#where-the-sources-differ)
5. [What not to claim](#what-not-to-claim)
6. [Angles and headline ideas](#angles-and-headline-ideas)
7. [Who to ask](#who-to-ask)
8. [Related pages](#related-pages)

## The story in one paragraph

Exponential separates the language of the **content** from the language of the **administration**. "268 locale files
(261 languages and countries, [a test language] and 6 variations, in 32 scripts, 40 of them written right
to left) can be chosen as content languages and siteaccess locales; 71 administration translations exist, all of them
complete" ([Languages in Exponential](../../languages.md)). When an editor chooses a right-to-left interface language,
"the administration turns around": the page says `dir="rtl"`, the side columns change places and text starts at the
right ([Right to left](../../features/6.0/right-to-left.md)). Most of this arrived in October 2026, in the 6.0.15
development line, which is not released.

## What is new, and when

| What | When | Source |
|---|---|---|
| Complete English and German catalogs, a translation string for almost every visible text | 27 and 28 September 2026 | [Translations and languages](../../features/6.0/translations-and-languages.md) |
| 89 new locales and one page for every language | 15 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#15-october-2026-89-new-locales-and-one-page-for-every-language) |
| Right-to-left languages, Hebrew, Latin, Klingon and 81 more locales | 16 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#16-october-2026-right-to-left-languages-hebrew-latin-klingon-and-81-more-locales) |
| The administration in Arabic, machine-assisted and to be reviewed by a native speaker | 18 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#18-october-2026-the-administration-in-arabic-and-hebrew-for-the-newest-strings) |

## Facts you can print

| Fact | Source |
|---|---|
| 268 locale files, 40 written right to left, in 32 scripts (figures of 15 October 2026). | [Languages in Exponential](../../languages.md) |
| 71 administration translations, all complete (26 October 2026), listed in [Languages in Exponential, section 4](../../languages.md#4-every-administration-translation). All but `eng-US` and `ger-DE` were machine-assisted, at least in part. | [Languages in Exponential, Support levels](../../languages.md#2-support-levels) |
| Each translation of an item is published on its own; badges show what exists and what is missing. | [Languages](../languages.md) |
| A country without its own translation falls back to a related one, for example every German-speaking country to German. | [Languages in Exponential, Support levels](../../languages.md#2-support-levels) |
| A left-to-right page stays "the same markup byte for byte" after the right-to-left work. | [Right to left](../../features/6.0/right-to-left.md) |
| No PHP extension is needed at run time for the languages. | [Languages in Exponential, section 10](../../languages.md#10-no-php-extension-at-run-time) |
| An installation can hold at most 62 content languages on 64-bit PHP. | [Languages in Exponential, section 11](../../languages.md#11-the-limit-of-62-content-languages) |
| Several complete translations were machine-assisted; a native review is invited. | [Languages in Exponential, section 8](../../languages.md#8-adding-or-completing-an-administration-translation) |

## Where the sources differ

The README counts "219 locales, 170 of them new" ([README, Recent improvements](https://github.com/se7enxweb/exponential/blob/main/README.md#recent-improvements-to-exponential)),
while [Languages in Exponential](../../languages.md) counts 268 locale files on 15 October 2026. Locales were added in
October in several steps, so use the dated figure from the languages page and give its date.

## What not to claim

- "Exponential is available in 268 languages." Say "268 locale files as content languages; 71 administration
  translations, all complete".
- "Every translation is perfect" or "professionally translated". Several were machine-assisted.
- "Unlimited content languages." The documented limit is 62 per installation on 64-bit PHP.
- "Klingon support" as a serious feature: the README itself says "(for fun)".

## Angles and headline ideas

- Regional angle: is your language complete, partial or content only? Check section 4 of the languages page.
- Community angle: native speakers are invited to review machine-assisted translations.

```text
"An admin that reads right to left: Exponential adds Hebrew and Arabic"
"268 locales, 71 complete translations: counting languages honestly"
"Native speakers wanted: reviewing a CMS translated with machine help"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| The language work and its numbers | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| Helping with a translation | the community | <https://share.exponential.earth/forums>, <https://t.me/exponentialcms> |

## Related pages

- [Talking points: languages and right to left](../advocates/talking-points-languages-and-right-to-left.md)
- [Story: multisite](story-multisite.md)
- [Story: accessibility](story-accessibility.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
