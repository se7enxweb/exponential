---
title: The 62 language limit
audience: government, admin, project-manager
category: public-sector
order: 290
summary: An Exponential installation holds at most 62 content languages on 64-bit PHP (30 on 32-bit PHP). Why, what counts and what does not, how to free a place, and what it means for public bodies with many languages or many sites in one installation.
keywords: language limit, 62 languages, maxCount, language mask, 64-bit PHP, content languages, many languages, multisite, capacity planning
type: help
---
# The 62 language limit

This page is for administrators and project managers who plan an installation with many languages, for example a
national portal or many sites sharing one installation. The facts come from
[Languages in Exponential, section 11](../../../languages.md#11-the-limit-of-62-content-languages).

## Contents

1. [The limit](#the-limit)
2. [What counts](#what-counts)
3. [Freeing a place](#freeing-a-place)
4. [Planning](#planning)

## The limit

At most **62 content languages on 64-bit PHP** (30 on 32-bit PHP; `eZContentLanguage::maxCount()`). Each content
language is one bit of an integer, the language mask that objects, versions and classes carry; bit 0 is the "always
available" flag and the sign bit is not used. **Setup > Languages** shows how many languages are in use and how many
more can be added; the add form refuses a language beyond the limit and says how to free a place.

The project does not plan to raise it: it would change the database schema and every query that tests a mask.

## What counts

| Counts | Does not count |
|---|---|
| content languages added in Setup > Languages | the locale files in `share/locale/` (268 ship) |
| a language added and never used | interface translations |
| the test language Lorem Ipsum while it is added | a locale chosen only as a siteaccess `Locale` for formats |

## Freeing a place

Remove a content language that no object and no class has a translation in (see "Removing a translation safely");
its bit becomes free and the next language added gets the lowest free bit. Remove the test language when your test
is done.

## Planning

| Situation | Consideration |
|---|---|
| A country with a few official languages | far below the limit |
| Many sites in one installation, each with its own languages | count the union of all languages; regional variants (`fre-FR`, `fre-CA`, `fre-BE`) each take a bit |
| A portal publishing in very many languages | consider whether regional variants are needed, or separate installations |

Prefer one content language per language unless a variant really differs in content; formats of a country can come
from the siteaccess's `Locale` without a separate content language.

## Related pages

- [Languages in Exponential](../../../languages.md#11-the-limit-of-62-content-languages)
- [Content languages](../../../guides/content-languages.md)
- [Adding a content language](adding-a-content-language.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
