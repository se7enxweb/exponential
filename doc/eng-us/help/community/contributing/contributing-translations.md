---
title: Contributing translations as a community member
audience: community, supporter, editor, admin
category: community
order: 27
summary: How community members bring Exponential to their language - where the interface translations live, how complete each language is, translating help pages under the GNU FDL, reviewing machine-assisted translations, and working with other speakers of your language.
keywords: translation, translate, language, locale, translation.ts, share/translations, ger-de, right-to-left, documentation translation, review, native speaker
type: help
---
# Contributing translations as a community member

A translation reaches every user of a language, often people who would never read an English manual. It is one of
the contributions where a single person makes the largest difference.

## Contents

1. [What can be translated](#what-can-be-translated)
2. [Translating the interface](#translating-the-interface)
3. [Translating the help](#translating-the-help)
4. [Working as a language team](#working-as-a-language-team)
5. [Quality](#quality)

## What can be translated

| What | Where | Page |
|---|---|---|
| The administration's messages | `share/translations/<locale>/translation.ts`, one file per language | [Contributing translations (supporters)](../../supporters/contributing-translations.md) |
| An extension's messages | The extension's own `translations/` directory | same |
| The documentation and this help | `doc/<locale>/`, for example `doc/ger-de/` for German | [Reading help in other languages](../../reading-help-in-other-languages.md) |

Which locales exist and how complete each is: `doc/eng-us/languages.md` and the help page
[Languages](../../languages.md).

## Translating the interface

1. Check the completeness of your language.
2. Fork the repository and open the language's `translation.ts`.
3. Translate the untranslated or unfinished messages. Keep placeholders such as `%1` exactly as they are.
4. Switch the administration to your language and look at the screens you translated.
5. Commit with a prefixed message, such as `Updated: German translation of the help dashboard`, and open a pull
   request ([Contributing code](contributing-code.md)).

For right-to-left languages such as Arabic and Hebrew, check the layout as well as the words
([Right-to-left](../../../features/6.0/right-to-left.md)).

## Translating the help

The help is free documentation, so you may translate it ([Free documentation](../../free-documentation.md)). A
translated page lives under `doc/<locale>/` with the same relative path as the English page, or with a translated
file name listed in `doc/<locale>/.paths.json`; a page the reader's language does not have yet is shown in English
with a note (`settings/exphelp.ini`, `[DocLanguages]`). Agree with the maintainers in an issue before you translate
many pages.

## Working as a language team

- Find other speakers in the forums or chats and agree on terms before you start.
- Keep a short glossary of the terms your translation uses.
- Divide the work by screen or by help section, not by alphabet.
- Review each other's work before it is submitted.

## Quality

- Consistency matters more than elegance; reuse the terms already in the translation.
- Product names stay as they are: Exponential, 7x, the Exponential Foundation.
- Do not submit machine translations without review. The code of conduct asks you to check AI-assisted work and say
  when a substantial part was generated ([Reviewing a machine-assisted translation](../../advocates/reviewing-a-machine-assisted-translation.md)).

## Related pages

- [Contributing as a community member](README.md)
- [Helping with translations](../../advocates/helping-with-translations.md)
- [Translations and languages](../../../features/6.0/translations-and-languages.md)
- [Languages](../../languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
