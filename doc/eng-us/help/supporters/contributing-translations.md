---
title: Contributing translations
audience: supporter
category: supporting
order: 50
summary: How native speakers translate Exponential - where the translation catalogues live, which languages are complete, how to translate and test, review by a second speaker, right-to-left languages, and translating the documentation.
keywords: translation, translations, translate, language, locale, catalogue, translation.ts, i18n, localization, right to left, RTL, review
type: help
---
# Contributing translations

This page is for native speakers who want Exponential in their language. Translations reach every user of that
language, often people who would never read an English manual.

## Where translations live

- The administration's messages are in `share/translations/<locale>/translation.ts`, one file per language
  (for example `ger-DE`, `fre-FR`, `esl-ES`, `ukr-UA`, `heb-IL`).
- `doc/eng-us/languages.md` lists the locales, the administration translations and how complete each one is; the help page
  [Languages](../languages.md) explains languages for editors and administrators.
- Extensions carry their own translations in their own `translations/` directories.

## How to translate

1. Pick a language and check its completeness in `doc/eng-us/languages.md`.
2. Fork the repository and open the language's `translation.ts`.
3. Translate the messages that are untranslated or marked unfinished. Keep placeholders such as `%1` exactly as they
   are.
4. Test: switch the administration to your language and look at the screens you translated.
5. Commit with a subject such as `Updated: German translation of the help dashboard` and open a pull request
   ([Contributing code](contributing-code.md) has the workflow and the manifest rule).

## Quality

- Use the terms the rest of the translation already uses; consistency matters more than elegance.
- Ask a second native speaker to review before you submit.
- Do not machine-translate without review.
- Product names stay as they are: Exponential, 7x, the Exponential Foundation.

## Right-to-left languages

The administration supports `dir="rtl"` for right-to-left languages such as Arabic and Hebrew
([Languages](../languages.md)). When you translate one, check the layout of the screens, not only the words.

## Translating the documentation

Help pages are free documentation under the GFDL 1.3 or later, so you may translate them
([Free documentation](../free-documentation.md)). Agree with the maintainers in an issue how translated pages are
organized before you translate many pages.

## Related pages

- [Languages](../languages.md)
- [Contributing documentation](contributing-documentation.md)
- [Contributing code](contributing-code.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
