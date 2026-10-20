---
title: Language and multilingual requirements
audience: government, project-manager, editor, admin
category: public-sector
order: 70
summary: Requirement wording for official and minority languages in a CMS tender - content translations, right-to-left scripts, administration languages, language addresses and encodings - with what Exponential documents.
keywords: languages, multilingual, translation, official languages, minority languages, right to left, RTL, Arabic, Hebrew, Unicode, UTF-8, locale, administration language, language switcher, tender
type: help
---
# Language and multilingual requirements

This page is for public bodies that publish in more than one language: official languages, minority languages, or
languages of the communities they serve. It gives requirement wording and names the Exponential pages that document
each capability, from [Internationalization standards](../../../../standards/internationalization.md) and
[Languages in Exponential](../../../../languages.md).

## Contents
1. [Questions to answer first](#questions-to-answer-first)
2. [Requirement wording](#requirement-wording)
3. [What Exponential documents](#what-exponential-documents)
4. [Known gaps to ask about](#known-gaps-to-ask-about)
5. [Pitfalls](#pitfalls)

## Questions to answer first

- Which languages must every page have, and which only some pages?
- Is any language written right to left (for example Arabic, Hebrew, Persian, Urdu)?
- Do staff need the **administration** itself in their language, or only the content?
- Who translates: staff, a translation agency, or a machine with human review?
- How do visitors choose a language: by address (`example.gov/fr/...`), by host (`fr.example.gov`), or by a switcher?

## Requirement wording

| Id | Requirement |
|---|---|
| LAN-01 | Every content item can exist in each of the languages <list>; each translation is published on its own. |
| LAN-02 | Editors see which translations of an item exist and which are missing. |
| LAN-03 | A translation can start from the text of another language. |
| LAN-04 | Right-to-left languages are displayed with the correct direction in the public site and in the editor. |
| LAN-05 | The language is reflected in the page address or host, and pages declare their language to browsers and assistive technology. |
| LAN-06 | The administration interface is available in <languages>; each user can choose it. |
| LAN-07 | All text is stored and delivered as Unicode (UTF-8), including characters outside the Basic Multilingual Plane. |
| LAN-08 | Dates, numbers and currencies follow the conventions of each language and country. |

## What Exponential documents

| Id | Evidence |
|---|---|
| LAN-01, LAN-02, LAN-03 | [Languages](../../../languages.md) (translation badges, "Translate based on"), [Content languages](../../../../guides/content-languages.md) |
| LAN-04 | [Right-to-left languages](../../../../features/6.0/right-to-left.md): `dir="rtl"` on content and administration; [Right-to-left sites](../../../creators/website-creators/right-to-left-sites.md). Unicode Bidirectional Algorithm: **Complies** (through the browser) |
| LAN-05 | [Siteaccess host and language matching](../../../../features/6.0/siteaccess-host-and-language-matching.md), [Language switcher and language URLs](../../../creators/website-creators/language-switcher-and-language-urls.md); BCP 47 tags sent to browsers: **Complies**; admin pagelayouts set `<html lang>` ([Accessibility standards](../../../../standards/accessibility.md)) |
| LAN-06 | [UI switch: interface languages](../../../../features/6.0/ui-switch-languages.md); [Languages in Exponential](../../../../languages.md) counts the shipped administration translations |
| LAN-07 | UTF-8 internal encoding **Complies**; 4-byte UTF-8 on MySQL/MariaDB **Partly complies** ([Rich text supplementary characters](../../../../features/6.0/rich-text-supplementary-characters-and-tex-source.md)) |
| LAN-08 | Locale files built from Unicode CLDR data, 268 locale files as of October 2026 ([Languages in Exponential](../../../../languages.md)) |

## Known gaps to ask about

From [Internationalization standards](../../../../standards/internationalization.md):

- **Plural forms** (CLDR plural rules): **Does not comply**. Languages with more than two plural forms read awkwardly
  for some counts in the interface.
- **MySQL and MariaDB connections** use three-byte `utf8`; prefer SQLite or PostgreSQL, or ask how the bidder
  handles characters such as emoji and rare scripts ([Databases](../../../../install/09-databases.md)).
- **Administration translations** are complete for some languages only; check the language you need in
  [Languages in Exponential](../../../../languages.md) before you make LAN-06 a "must".
- **64-bit PHP** is needed for more than 30 content languages ([Requirements](../../../../install/02-requirements.md)).

## Pitfalls

- Making "all pages in all languages" a must when your translation budget cannot deliver it.
- Forgetting the language of PDFs, newsletters and system e-mails.
- Treating machine translation as finished translation. If AI drafting is in scope, see the trainer course
  [Exponential Intelligence](../../../trainers/exponential-intelligence/README.md) for how drafts are reviewed.

## Related pages
- [Requirements for a public-sector CMS](README.md)
- [Tender template: multilingual clause](../tender-templates/multilingual-clause.md)
- [Multilingual sites](../../../creators/website-creators/multilingual-sites.md)
- [Translations and languages](../../../../features/6.0/translations-and-languages.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
