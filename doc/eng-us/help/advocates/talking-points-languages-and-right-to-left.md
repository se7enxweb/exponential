---
title: "Talking points: languages and right to left"
audience: advocate
category: advocacy
order: 18
summary: Sourced talking points about Exponential's languages (locale files, content languages, administration translations and their support levels, fallbacks, right-to-left scripts) and how advocates can turn a language community into contributors.
keywords: talking points, languages, multilingual, translations, locales, content languages, administration translation, right to left, RTL, Hebrew, Arabic, fallback, support levels, translators
type: help
---
# Talking points: languages and right to left

This page is for advocates who talk to multilingual organizations and to language communities. Languages are one of
Exponential's strongest stories, and one where precision matters: a "supported" language can mean very different
things.

## Contents

1. [Three kinds of language support](#three-kinds-of-language-support)
2. [The points](#the-points)
3. [Show it](#show-it)
4. [Do not say](#do-not-say)
5. [Turning a language community into contributors](#turning-a-language-community-into-contributors)

## Three kinds of language support

| Level | What works | Source |
|---|---|---|
| Full: interface and content | The administration speaks the language completely; content, dates, numbers and currency follow the locale | [Languages in Exponential, Support levels](../../languages.md#2-support-levels) |
| Partial interface, full content | An older translation covers part of today's strings; the rest shows in English | same |
| Content only | No administration translation; content can be written in the language and formatted for it | same |

Always ask "for editors' screens or for content?" before answering "is my language supported?"

## The points

| Say | Source |
|---|---|
| "359 locale files can be used as content languages." | [Languages in Exponential](../../languages.md), [Languages and accessibility](../who-is-the-exponential-foundation.md#languages-and-accessibility) |
| "43 of them are right to left." | [Languages and accessibility](../who-is-the-exponential-foundation.md#languages-and-accessibility) |
| "administration translations exist (the current totals are at the top of [Languages in Exponential](../../languages.md))." | same |
| "Each translation of an item is published on its own, and badges show what exists and what is missing." | [Languages](../languages.md) |
| "Each editor chooses the language of their own administration." | [Languages](../languages.md) |
| "A country without its own translation falls back to a related one, for example every German-speaking country to German." | [Languages in Exponential, Support levels](../../languages.md#2-support-levels) |
| "In a right-to-left language the administration turns around, and content gets `dir=\"rtl\"`." | [Right to left](../../features/6.0/right-to-left.md) |
| "No PHP extension is needed at run time for the languages." | [Languages in Exponential, section 10](../../languages.md#10-no-php-extension-at-run-time) |

## Show it

On an installation you control:

1. Open an item; point at the filled and dashed language badges.
2. Click a dashed badge, **Translate based on** the existing language, publish.
3. In the right column, **Interface**, choose Hebrew or Arabic, **Apply**: the administration turns around.
4. Switch back.

Timed version: [Demo scripts, Part 3: languages and right to left](../business/demo-scripts.md#part-3-languages-and-right-to-left-3-minutes).

## Do not say

- "Exponential is available in 359 languages." Say "359 locale files as content languages; administration translations (the current totals are at the top of [Languages in Exponential](../../languages.md))".
- "All translations are perfect." Several complete translations were machine-assisted; a review is "the quickest
  win" ([Languages in Exponential, section 8](../../languages.md#8-adding-or-completing-an-administration-translation)).
- "You can use as many content languages as you like." There is a documented limit of 62 content languages per
  installation on 64-bit PHP ([section 11](../../languages.md#11-the-limit-of-62-content-languages)).

## Turning a language community into contributors

Language communities are natural advocates. When you meet one:

1. Show them their language's level ([section 4](../../languages.md#4-every-administration-translation)).
2. If it is complete but machine-assisted, invite reviewers
   ([Reviewing a machine-assisted translation](reviewing-a-machine-assisted-translation.md)).
3. If it is partial or missing, invite translators ([Helping with translations](helping-with-translations.md)).
4. Propose a translation sprint ([Proposing a contribution sprint](proposing-a-contribution-sprint.md)).
5. Translate a help page too: "A translated page helps every reader of that language"
   ([Free documentation](../free-documentation.md#help-others)).

## Related pages

- [Helping with translations](helping-with-translations.md)
- [Languages in Exponential](../../languages.md)
- [Multisite and multilingual planning](../leadership/multisite-and-multilingual-planning.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
