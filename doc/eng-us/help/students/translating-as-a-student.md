---
title: Translating as a student
audience: student
category: learning
order: 43
summary: How language students and native speakers contribute translations to Exponential - where the administration's translations live, how to pick a language and check its completeness, the translation workflow, quality rules, right-to-left languages, translating the help under the GFDL, and why translation is a good first contribution.
keywords: translation, translate, translation.ts, share/translations, locale, language, interface language, right to left, GFDL, documentation translation, contribution, student
type: help
---
# Translating as a student

If you speak a language other than English well, translation is one of the most useful things you can contribute,
and one of the easiest to start with. "Translations reach every user of that language, often people who would never
read an English manual" ([Contributing translations](../supporters/contributing-translations.md)).

## Contents

1. [Where translations live](#where-translations-live)
2. [Pick a language and a piece](#pick-a-language-and-a-piece)
3. [The workflow](#the-workflow)
4. [Quality rules](#quality-rules)
5. [Right-to-left languages](#right-to-left-languages)
6. [Translating the help](#translating-the-help)
7. [What you learn](#what-you-learn)

## Where translations live

From [Contributing translations](../supporters/contributing-translations.md#where-translations-live):

- The administration's messages are in `share/translations/<locale>/translation.ts`, one file per language, for
  example `ger-DE`, `fre-FR`, `esl-ES`, `ukr-UA`, `heb-IL`.
- `doc/eng-us/languages.md` lists the locales, the administration translations and how complete each one is
  ([Languages](../../languages.md)).
- Extensions carry their own translations in their own `translations/` folders.

Content languages (the language of articles) are something else: those are translated by editors in the
administration ([project 5](project-05-a-multilingual-site.md)).

## Pick a language and a piece

1. Find your language and its completeness in `doc/eng-us/languages.md`.
2. Pick a small piece you can finish: the screens you use most, for example the dashboard or the edit form.
3. Check on your study site how the screen looks in your language: the **Interface** box in the right column switches
   the administration's language for you and shows how complete each translation is
   ([Languages](../languages.md#the-language-of-the-administration)).

## The workflow

1. Fork the repository and open the language's `translation.ts`.
2. Translate messages that are untranslated or marked unfinished. Keep placeholders such as `%1` exactly as they are.
3. Test: switch the administration to your language and look at the screens you translated.
4. Commit with a subject such as `Updated: German translation of the help dashboard` and open a pull request.

Source: [Contributing translations](../supporters/contributing-translations.md#how-to-translate). The files can be
edited with Qt Linguist or an XML editor ([Mentoring a first contribution](../advocates/mentoring-a-first-contribution.md#path-b-translation)).
`php bin/php/ezchecktranslation.php <locale>` shows how complete the interface translation of a locale is
([Content languages](../../guides/content-languages.md#10-commands-for-the-shell)).

## Quality rules

- Use the terms the rest of the translation already uses; consistency matters more than elegance.
- Ask a second native speaker to review before you submit. A study group is ideal for this.
- Do not machine-translate without review.
- Product names stay as they are: Exponential, 7x, the Exponential Foundation.

Source: [Contributing translations](../supporters/contributing-translations.md#quality).

## Right-to-left languages

The administration supports `dir="rtl"` for right-to-left languages such as Arabic and Hebrew. When you translate one,
check the layout of the screens, not only the words ([Contributing translations](../supporters/contributing-translations.md#right-to-left-languages)).

## Translating the help

Help pages are free documentation under the GFDL 1.3 or later, so you may translate them
([Free documentation](../free-documentation.md#translate-it)). Agree with the maintainers in an issue how translated
pages are organized before you translate many pages. How readers find help in other languages:
[Reading the help in other languages](../reading-help-in-other-languages.md).

## What you learn

- The administration in depth: you will read every message of the screens you translate.
- Software localization: placeholders, plural forms, context, consistency.
- Git and review, on a contribution that is easy to check.

A merged translation is a contribution you can show in your portfolio ([Building a portfolio](building-a-portfolio.md)).

## Related pages

- [Contributing translations](../supporters/contributing-translations.md)
- [Helping with translations](../advocates/helping-with-translations.md)
- [Contributing as a student](contributing-as-a-student.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
