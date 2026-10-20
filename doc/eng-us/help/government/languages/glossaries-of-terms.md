---
title: Glossaries of terms
audience: government, editor, website-creator
category: public-sector
order: 240
summary: How to keep a public glossary and an internal terminology list in several languages with Exponential: a term class with a translation per language, a glossary page, linking terms from content, tags with synonyms for search, and keeping translators consistent.
keywords: glossary, terminology, terms, definitions, multilingual glossary, term class, eztags synonyms, translators, consistency, plain language
type: help
---
# Glossaries of terms

This page is for editors, translators and site builders. A glossary helps readers understand official terms and helps
translators use the same word every time. In Exponential a glossary is content: one object per term, translated into
each language like any page.

## Contents

1. [A term class](#a-term-class)
2. [The public glossary page](#the-public-glossary-page)
3. [Linking terms from content](#linking-terms-from-content)
4. [Synonyms and search](#synonyms-and-search)
5. [An internal terminology list for translators](#an-internal-terminology-list-for-translators)

## A term class

Create a class `term` in **Setup > Classes** ([Content classes for websites](../../creators/website-creators/content-classes-for-websites.md)):

| Attribute | Datatype | Translatable |
|---|---|---|
| Term | Text line | yes |
| Definition | Rich text | yes |
| Also known as | Text line | yes |
| Source of the definition (law, policy) | URL or Text line | no |
| Related terms | Object relation list | no |

Object name pattern `<term>`. Each translation of a term object is the term in that language, so the French site's
glossary shows French terms.

## The public glossary page

A folder "Glossary" with the terms as children, sorted by name. With many terms, one folder per letter, or a
template that groups by first letter. The site search finds terms when Term and Definition are **Searchable**.

## Linking terms from content

Link the first use of a term in a page to its glossary entry with the editor's link to an object; the link follows the
term if it moves, and points to the right language version on each language site.

## Synonyms and search

With `eztags`, a tag tree of terms can carry synonyms, so a tag found under another word leads to the same tag page
([Tagging with eztags](../../creators/website-creators/tagging-with-eztags.md)). With Solr, a synonyms file in the Solr
configuration makes the search treat words as equivalent; the shipped file holds only test mappings, so you write
your own ([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)).

## An internal terminology list for translators

Keep a second glossary in a section only staff can read, with: the term in each language, the forbidden variants,
notes for translators and the date agreed. Export it for translation agencies with `xrowextract` as CSV, one row per
translation ([xrowextract](../../../features/6.0/extensions/xrowextract.md)).

## Related pages

- [Plain language](plain-language.md)
- [Translation workflow and approval](translation-workflow-and-approval.md)
- [Relations and reuse](../../creators/website-creators/relations-and-reuse.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
