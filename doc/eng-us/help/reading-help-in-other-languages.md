---
title: Reading the help in your language
audience: editor, user, admin
category: languages
order: 20
summary: The help is shown in the language of the administration. Switch the interface language to read it in another one; a page that is not translated yet is shown in English, with a note.
keywords: help, language, translation, German, English, interface language, not translated, documentation language
type: help
---
# Reading the help in your language

This page is for everyone who reads the help of the administration and would rather read it in their own language.

## The help follows the language of the administration

The help is shown in the language the administration is shown in. You do not choose a language for the help
separately: switch the interface language and the help switches with it.

1. Open the interface language switch in the top bar of the administration (see [Languages](languages.md) if your
   administration does not show it).
2. Choose a language, for example German.
3. Open the help, or reload the help page you are on.

The page you are on stays open: every help page has the same address in every language, so you see the same page,
now in the language you chose.

## Switching from the help itself

The start page of the help and every help page show the language you are reading in, next to the search:
"Reading in: English / Deutsch". The language you read in is highlighted; choose another one to switch. This is the
same switch as the interface language switch in the top bar, so the whole administration changes to that language,
and you stay on the page you were reading.

Only the languages that have help pages are offered. Next to the switch you see how much of the help is translated,
for example "German: 120 of 7,265 pages". If your account may not change the interface language, the help shows
the language you are reading in without the switch.

## The English original

A translated page has a link to its English original ("Read the English original") above the text. The original
opens at the same address followed by `/(lang)/eng-us`; it is shown in English for that one visit, with a link back
to the page in your language. Your interface language does not change. (The older key `/(lang)/en-us` still works;
a `?lang=` in the address is not read.)

## A page that is not translated yet

The documentation is written in English first and translated page by page. When a page has no translation in your
language yet, the help shows the English page and says so above it, for example:

> This page is not translated into German yet. It is shown in English. Open the English original

Everything else works the same: the search, the navigation tree and the links. The changelogs are always in English;
they are not translated.

## Searching in your language

The search looks through the pages in your language, and through the English pages that are not translated yet. It
does not show the English original of a page that is translated, so every page appears once, in the best language
there is for you.

## Which languages there are

English is the original. German is being translated. Your administrator can add a language in the settings
(`exphelp.ini`, `[DocLanguages]`); developers and translators find how in
[Translated documentation](../bc/6.0/translated-documentation.md).

In the files of the documentation, each language is a directory named after the code Exponential uses for the
language, in small letters: `doc/eng-us/` is the English original (`eng-US`), `doc/ger-de/` the German translation
(`ger-DE`). A new language gets its directory the same way, for example `doc/fre-fr/` for French (`fre-FR`). You do
not need these names to read the help: every help address is the same in every language, and links and bookmarks
from before the directories were renamed still open the right page.

## Related

- [Languages](languages.md): content languages and the interface language
- [Searching](searching.md)
- [Getting started](getting-started.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](partners/partners-start-here.md) and [How to apply](partners/how-to-apply.md).
<!-- exp-partner-block:end -->
