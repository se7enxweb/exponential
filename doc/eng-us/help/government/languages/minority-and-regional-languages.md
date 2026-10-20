---
title: Minority and regional languages
audience: government, project-manager, admin, editor
category: public-sector
order: 130
summary: How a public body offers content in minority, regional or indigenous languages with Exponential: which locales ship, what to do when one is missing, partial translation with fallbacks, interface texts that stay in another language, and working with speakers.
keywords: minority languages, regional languages, indigenous languages, Welsh, Basque, Irish, Scottish Gaelic, Sami, Maori, Frisian, locale, partial translation, community translators
type: help
---
# Minority and regional languages

This page is for public bodies that publish in minority, regional or indigenous languages, often next to one or more
majority languages. Exponential treats every language the same: each one with a locale file can be a content
language. The question is usually less the software than the translation capacity.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Is the locale there?](#is-the-locale-there)
2. [Partial translation with a fallback](#partial-translation-with-a-fallback)
3. [Interface texts](#interface-texts)
4. [Fonts and characters](#fonts-and-characters)
5. [Working with speakers](#working-with-speakers)

## Is the locale there?

`share/locale/` ships 268 locale files, among them many regional and minority languages, for example Welsh
(`cym-GB`), Basque (`eus-ES`), Galician (`glg-ES`), Irish (`gle-IE`), Scottish Gaelic (`gla-GB`), Breton (`bre-FR`),
Occitan (`oci-FR`), Western Frisian (`fry-NL`), Northern Sami (`sme-NO`), Romansh (`roh-CH`), Luxembourgish
(`ltz-LU`), Maori (`mri-NZ`), Hawaiian (`haw-US`) and Greenlandic (`kal-GL`). The complete list with native names is
in [Languages in Exponential, section 3](../../../languages.md#3-every-locale); languages still missing are in
[section 6](../../../languages.md#6-common-languages-still-missing). A missing locale can be added as a file (see
"Custom locales for unlisted languages").

## Partial translation with a fallback

Most bodies publish a part of their content in a minority language. A siteaccess that lists the minority language
first and the main language second shows translated pages in the minority language and the rest in the main
language:

```ini
[RegionalSettings]
SiteLanguageList[]
SiteLanguageList[]=cym-GB
SiteLanguageList[]=eng-GB
```

Mark fallback pages for visitors (see "Fallback rules for untranslated content"), and decide which sections are
translated first: contact, services most used, rights and complaints.

## Interface texts

Most minority languages have no administration translation; the interface stays in another language, while content
can be written in the minority language and dates follow its locale ([Languages in Exponential, section 2](../../../languages.md#2-support-levels)).
The fixed texts of your public design are your own `.ts` translations, which a translator can provide for the few
strings a public site uses.

## Fonts and characters

Check that the design's fonts contain every letter of the language (for example accented and special letters).
Characters above U+FFFF need `utf8mb4` on MySQL and MariaDB ([Internationalization standards](../../../standards/internationalization.md#4-byte-characters-on-mysql-and-mariadb)).

## Working with speakers

- Give community or contracted translators accounts with a role limited to their language (see "Translator roles
  and permissions") and an approval step if needed.
- Keep a glossary of agreed terms (see "Glossaries of terms").
- Ask a speaker to review the locale file's day and month names and formats before launch.
- Contribute reviewed locale files and translations back to the project (see "Contributing back as a public body" in
  the sovereignty section).

## Related pages

- [Languages in Exponential](../../../languages.md)
- [Custom locales for unlisted languages](custom-locales-for-unlisted-languages.md)
- [Content languages](../../../guides/content-languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
