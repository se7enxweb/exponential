---
title: Second and third official languages
audience: government, project-manager, admin, editor
category: public-sector
order: 120
summary: A setup guide for public bodies with two or three official languages: one siteaccess and address per language with equal standing, fallback policy, translator roles and approval, a switcher, interface languages for staff, and the checks before launch.
keywords: official languages, bilingual, trilingual, second language, equal standing, language policy, translators, siteaccess per language, switcher, public service
type: help
---
# Second and third official languages

This page is for managers and administrators of public bodies that must serve the public in two or three official
languages. It brings the other pages of this section together into one setup. The underlying guide is
[Building a multilingual site](../../creators/website-creators/multilingual-sites.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Decisions first](#decisions-first)
2. [The setup](#the-setup)
3. [People and process](#people-and-process)
4. [Before launch](#before-launch)

## Decisions first

| Decision | Options |
|---|---|
| Equal standing in addresses | a prefix for every language (`/en/`, `/fr/`) or a host per language; avoid one language at `/` and the others below it |
| What the start page does | redirect by browser language, or a neutral page offering all languages |
| Fallback | full parity (hide untranslated) or fallback to another official language with a notice |
| Which content must be in all languages | per section, written down |
| Main language of new content | per team, or the drafting language |
| Interface language of staff | each person's choice |

## The setup

1. **Add the content languages** in **Setup > Languages** (see "Adding a content language").
2. **One siteaccess per language** with `Locale`, `SiteLanguageList[]` and `SiteName` (see "A siteaccess per
   language").
3. **Addresses** by path or host (see "Language addresses: path or host").
4. **Start page** redirect or neutral page (see "Redirecting to the visitor's language").
5. **Switcher** on every page (see "The language switcher").
6. **Fixed texts** of the design translated in `.ts` files; menus and footers kept as content where possible.
7. **Always available** for media and users (see "Always available content").
8. **Search** per language (see "Multilingual search").

## People and process

- Translator roles limited by **Language** (see "Translator roles and permissions").
- An approval step per language if translations are reviewed (see "Translation workflow and approval"); the
  *Approve* event can be limited to **Affected languages**.
- A translation backlog: the language's page in **Setup > Languages** lists objects with a translation in it; compare
  counts between languages to see what is missing.
- Style and terminology: a glossary per language (see "Glossaries of terms").

## Before launch

- [ ] Every language address answers with the right `lang` on `<html>`.
- [ ] The switcher leads to the same page in the other language.
- [ ] Pages that must exist in all languages do.
- [ ] Forms, error pages, search results and mails exist in each language.
- [ ] The privacy notice and accessibility statement exist in each language.

## Related pages

- [Languages at a glance](languages-at-a-glance.md)
- [Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md)
- [Content languages](../../../guides/content-languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
