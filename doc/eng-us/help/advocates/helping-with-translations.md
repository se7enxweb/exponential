---
title: Helping with translations
audience: advocate
category: advocacy
order: 100
summary: How advocates help bring Exponential's administration and help to more languages - check a language's level, translate missing strings with Qt Linguist or an XML editor, keep placeholders, measure, review in the running administration, send a pull request, translate help pages - and how to recruit translators.
keywords: translations, translators, languages, translation.ts, Qt Linguist, ezlupdate, ezchecktranslation, placeholders, review, pull request, help pages, recruiting, localization
type: help
---
# Helping with translations

This page is for advocates who speak a language besides English, and for those who recruit translators. Translation
is one of the most valuable contributions: "Translations of the strings added since 6.0 (about 10,000) are the most
useful work for every language" that is not yet complete
([Languages in Exponential, section 8](../../languages.md#8-adding-or-completing-an-administration-translation)).

## Contents

1. [Check your language's level](#check-your-languages-level)
2. [What you can translate](#what-you-can-translate)
3. [Translating the administration, step by step](#translating-the-administration-step-by-step)
4. [Rules that keep translations working](#rules-that-keep-translations-working)
5. [Sending your work](#sending-your-work)
6. [Recruiting translators](#recruiting-translators)

## Check your language's level

| Level | Meaning |
|---|---|
| Full | The administration is complete (ten languages; several machine-assisted) |
| Partial | An older translation covers part of today's strings |
| Content only | No administration translation yet |

Your language's row is in [section 4, Every administration translation](../../languages.md#4-every-administration-translation);
the levels are in [section 2](../../languages.md#2-support-levels).

- **Complete but machine-assisted** (Spanish, French, Ukrainian, Hebrew, Latin, Klingon): a review is "the quickest
  win" ([Reviewing a machine-assisted translation](reviewing-a-machine-assisted-translation.md)).
- **Partial**: translate the missing strings.
- **Content only**: start a new translation.

## What you can translate

| What | Where |
|---|---|
| The administration interface | `share/translations/<code>/translation.ts` |
| Extensions' interfaces | Each extension's own translation files |
| Help pages | `doc/eng-us/help/...` as new files or translated copies, under the GNU FDL ([Free documentation](../free-documentation.md#translate-it)) |

## Translating the administration, step by step

From [section 8](../../languages.md#8-adding-or-completing-an-administration-translation):

1. **New strings**: the maintainers refresh the untranslated file with `ezlupdate`; for your language,
   `bin/linux/ezlupdate <code>` adds new strings and marks vanished ones obsolete.
2. **New language**: create `share/translations/<code>/`, copy the untranslated `translation.ts` into it, set the
   `language` attribute of `<TS>` to the code.
3. **Translate** with Qt Linguist or any XML editor. A message counts when its `<translation>` has text and no
   `type="unfinished"`.
4. **Measure**: `php bin/php/ezchecktranslation.php <code> --allow-root-user` prints the finished share.
5. **Review** in the running administration with your locale, after clearing the translation cache.

## Rules that keep translations working

- Keep placeholders (`%1`, `%name`), HTML entities and keyboard hints exactly as in the source.
- Keep leading and trailing spaces and line breaks.
- Do not leave the English text as the "translation" unless it is right (a product name); the quality test checks it.
- Use the product names correctly ([Naming rules quick card](naming-rules-quick-card.md)).
- For right-to-left languages, test the screens with `dir="rtl"` ([Right to left](../../features/6.0/right-to-left.md)).

The quality rules come from the translation quality test described in section 8.

## Sending your work

Send it as a pull request ([Develop with Git](../develop-with-git.md#contribute-a-change-with-a-pull-request)) with a
commit message such as `Updated: 300 more German strings of the administration`. Small, regular pull requests are easier
than one huge one.

## Recruiting translators

- Show the language's level and say exactly what is needed.
- Propose a sprint: a few hours together ([Proposing a contribution sprint](proposing-a-contribution-sprint.md)).
- Pair a translator with a reviewer.
- Use the template on [Social post templates](social-post-templates.md#community-calls).
- Thank translators publicly, with consent ([Recognition ideas](recognition-ideas.md)).

## Related pages

- [Reviewing a machine-assisted translation](reviewing-a-machine-assisted-translation.md)
- [Talking points: languages and right to left](talking-points-languages-and-right-to-left.md)
- [Languages in Exponential](../../languages.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
