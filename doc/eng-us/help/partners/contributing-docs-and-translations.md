---
title: Contributing documentation and translations
audience: partner
category: partnership
order: 650
summary: How partners contribute to the documentation and the translations of Exponential - editing help pages on GitHub, writing new pages with front matter, the GFDL, translating the administration, and why documentation work pays off for partners.
keywords: documentation, docs, help pages, translation, translations, i18n, GFDL, front matter, Edit on GitHub, languages, catalogues
type: help
---
# Contributing documentation and translations

This page is for partner staff who write or translate. Documentation and translations are among the most valuable
contributions: partners see the questions customers ask, and serve customers in many languages.

## Documentation

- The documentation lives in the repository in `doc/`, and the help shows it in the administration
  ([Free documentation](../free-documentation.md)).
- Every help page has an "Edit on GitHub" link: fix a mistake directly with a pull request.
- New help pages are Markdown files with front matter (title, audience, category, order, summary) under `doc/eng-us/help/`.
- The documentation is licensed under the GNU Free Documentation License 1.3 or later; your contribution will be too
  ([The license of this documentation](../../README-LICENSE.md)).

### What partners can document well

| Topic | Why partners |
|---|---|
| Real-world setups (hosting panels, containers, clouds) | You run them |
| Migration stories | You did them |
| Editor how-tos | You trained editors |
| Answers you give repeatedly | They belong in the help |

### Writing rules

- American English; product names right ([Naming and brand rules for partners](naming-and-brand-for-partners.md)).
- Every fact with its source; commands with expected output.
- No secrets, host names of private systems or customer data in examples.

## Translations

- The administration is translated into many languages; [Languages](../languages.md) explains the locales and
  `doc/eng-us/languages.md` lists the catalogues and their completeness.
- Translations live in `share/translations/<locale>/translation.ts`. Improve an existing language or complete a
  partial one with a pull request.
- Have a second native speaker review before you submit.

## Points

Tutorials (1,500+ words) and articles (500 to 1,500 words, FLOSS-licensed) earn 500 points each; "wiki article
documentation or documentation" is named as knowledge sharing (<https://exponential.earth/partner/requirements>).
Translations are not listed; ask the partner team, as "case-by-case" activities can be rewarded.

## Related pages

- [Contributing code back](contributing-code-back.md)
- [Earning points through community work](earning-points-through-community-work.md)
- [Contributing translations](../supporters/contributing-translations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
