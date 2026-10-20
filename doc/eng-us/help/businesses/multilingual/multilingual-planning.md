---
title: Multilingual planning
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 10
summary: The decisions a business takes before publishing in several languages with Exponential - which languages and markets, addresses per language, fallbacks for untranslated content, who translates, and the limits to know.
keywords: multilingual, planning, languages, markets, SiteLanguageList, fallback, untranslated, translators, addresses per language, hreflang, limit of 62 languages, business
type: help
---
# Multilingual planning

This page is for owners, marketing leads and project managers who plan a website or store in more than one language.
It lists the decisions to take before the build, explains each in business terms, and points to the technical page for
the team that implements it. The longer planning guide for project managers is
[Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md).

## Contents

1. [Start from markets, not languages](#start-from-markets-not-languages)
2. [How Exponential shows languages](#how-exponential-shows-languages)
3. [Addresses per language](#addresses-per-language)
4. [What visitors see when a translation is missing](#what-visitors-see-when-a-translation-is-missing)
5. [Who translates, and who approves](#who-translates-and-who-approves)
6. [Limits to know before you promise](#limits-to-know-before-you-promise)
7. [The decision table](#the-decision-table)

## Start from markets, not languages

A language is a cost that recurs: every page you publish later needs a translation, every correction too. Before
adding one, write down for each market:

- [ ] Which customers or members it reaches, and what you expect from them.
- [ ] Which content they need: the whole site, the shop, or only key pages (products, contact, terms).
- [ ] Who writes and checks the language: staff, an agency, a partner, or a machine draft with a human review.
- [ ] Whether prices, taxes, shipping and legal texts differ in that market (see
      [Currencies, dates and formats](currencies-dates-and-formats.md) and the [Stores](../stores/README.md) section).

A smaller set of well-maintained languages usually serves customers better than many half-translated ones.

## How Exponential shows languages

From [Content languages](../../../guides/content-languages.md):

- **Adding a language translates nothing and changes no site address.** Editors translate content; an administrator
  adds the language to the language list (`SiteLanguageList`) of each siteaccess that should show it.
- **Each siteaccess shows an object in the first language of its list that the object has.**
- **Every object has a main language**, the one it was created in or chosen later.
- **Removing a translation deletes content for good**; removing a whole language is only possible when nothing uses
  it, so it never deletes content.

Most multilingual sites therefore have one public siteaccess per language, all sharing one database and one content
tree ([Building a multilingual site](../../creators/website-creators/multilingual-sites.md)).

## Addresses per language

| Shape | Example | Good for |
|---|---|---|
| Path per language | `example.com/de/` | One certificate, one host; simplest |
| Host per language | `example.de`, `de.example.com` | Country domains |
| Host and path | `example.com/de` on one host, other hosts elsewhere | Mixed setups |

From [Language switchers and addresses per language](../../creators/website-creators/language-switcher-and-language-urls.md#addresses-per-language).
The start page can also redirect once to the visitor's language, chosen by host and browser language, with one
address per language and no cookie
([Siteaccess by host prefix, and the redirect to the language](../../../features/6.0/siteaccess-host-and-language-matching.md)).

> **Note**
> Search engines understand `hreflang` links between language versions. Exponential does not print them by itself;
> the design builds them from the language switcher's result
> ([Telling search engines](../../creators/website-creators/language-switcher-and-language-urls.md#telling-search-engines)).
> Put this on the design team's task list.

## What visitors see when a translation is missing

Decide this per language siteaccess:

| You want | Setting (for the technical team) |
|---|---|
| Show English where German is missing | German first, then English in the language list |
| Hide what is not translated | Only German in the list, untranslated objects not shown |
| Show everything in its main language when nothing else fits | `ShowUntranslatedObjects=enabled` |
| A logo or a PDF appears in every language as it is | Mark the object "always available" |

Source: [Fallbacks and untranslated content](../../creators/website-creators/multilingual-sites.md#fallbacks-and-untranslated-content).
For a store, hiding untranslated products can hide things customers want to buy; showing them in another language
can confuse. Decide product by product category if necessary.

## Who translates, and who approves

- Translators can be limited to their language: the policies to edit, translate and create content can each be
  limited by **Language** in the role editor
  ([7. Translating content in the editor](../../../guides/content-languages.md#7-translating-content-in-the-editor)).
- An approval workflow can hold translations until a language owner approves them
  ([Translation workflow](translation-workflow.md)).
- A language model can prepare drafts that a person reviews
  ([Assisted translation with Exponential Intelligence](assisted-translation-with-exponential-intelligence.md)).

## Limits to know before you promise

| Limit | Value | Source |
|---|---|---|
| Content languages per installation | at most 62 on 64-bit PHP, 30 on 32-bit PHP | [Languages, section 11](../../../languages.md#11-the-limit-of-62-content-languages) |
| A language added and never used | still takes its place until removed | same |
| Administration in the editor's own language | complete for 19 translations; partial or English for the rest | [Languages, section 2](../../../languages.md#2-support-levels) |
| Changing the default content language of a site with content | can hide objects that do not exist in the new language | [10.14 Multi-language sites](../../../install/10-after-installing.md#1014-multi-language-sites) |

## The decision table

```text
Language plan for <organization>                               <date>

| Market | Language (locale) | Address | Fallback language | Untranslated: hide/show | Translator | Approver |
|--------|-------------------|---------|-------------------|-------------------------|------------|----------|
|        |                   |         |                   |                         |            |          |

Content in scope per language: <whole site / shop / key pages>
Right-to-left languages: <none / list>
Machine-assisted drafts: <yes, reviewed by ... / no>
Content languages in use: <n> of at most 62
```

## Related pages

- [Languages and locales](languages-and-locales.md)
- [Translation workflow](translation-workflow.md)
- [Multilingual checklist](multilingual-checklist.md)
- [Planning several brands](../multisite/multi-brand-planning.md)
- [Planning multisite and multilingual projects](../../leadership/multisite-and-multilingual-planning.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
