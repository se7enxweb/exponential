---
title: Multilingual non-profit sites
audience: non-profit, government, editor, website-creator
category: non-profits
order: 40
summary: How a non-profit serves people in several languages on Exponential with a small team - deciding which content to translate first, content languages and a siteaccess per language, fallbacks for untranslated pages, the language switcher, volunteer translators limited by language, translation drafts from Exponential Intelligence reviewed by a person, right-to-left scripts, the 62-language limit and plain language.
keywords: multilingual, languages, translation, content languages, siteaccess, SiteLanguageList, fallback, language switcher, translators, volunteer translators, Exponential Intelligence, right-to-left, 62 languages, plain language, community languages
type: help
---
# Multilingual non-profit sites

This page is for communications staff and the website creator. Many non-profits serve people whose first language is
not the main language of the country: refugees, migrant communities, speakers of minority or indigenous languages,
international supporters. Exponential has "Advanced multi-lingual support" (`README.md`, "Main Exponential
features"); the work is mostly in deciding what to translate and who keeps it current.

## Contents

1. [Decide what to translate first](#decide-what-to-translate-first)
2. [How languages work in Exponential](#how-languages-work-in-exponential)
3. [Untranslated pages](#untranslated-pages)
4. [The language switcher](#the-language-switcher)
5. [Translators, often volunteers](#translators-often-volunteers)
6. [Translation drafts with Exponential Intelligence](#translation-drafts-with-exponential-intelligence)
7. [Right-to-left and other scripts](#right-to-left-and-other-scripts)
8. [Limits](#limits)
9. [Checklist](#checklist)

## Decide what to translate first

"Adding a language translates nothing and shows nothing by itself"
([Adding a language](../../customers/growth/adding-a-language.md)). Translate in this order:

| Priority | Content | Why |
|---|---|---|
| 1 | How to get help: services, contact, opening times, emergency information | The people you serve need it most |
| 2 | Forms: the help request or volunteer form, with labels and messages | Otherwise they cannot act |
| 3 | Who you are, privacy notice | Trust and legal basics |
| 4 | Donate page | If you fundraise in that community |
| 5 | News and events | As capacity allows |

A short translated section that is current is better than a full translation that is two years old.

## How languages work in Exponential

- Each object can have a translation per content language.
- A siteaccess per language decides which languages a visitor sees, in order (`SiteLanguageList`).
- The administration interface has its own languages, separate from content languages.

Sources: [Multilingual sites](../../creators/website-creators/multilingual-sites.md),
[Content languages and interface languages](../../government/languages/content-languages-and-interface-languages.md),
[A siteaccess per language](../../government/languages/a-siteaccess-per-language.md).

## Untranslated pages

When a page is not translated yet, the `SiteLanguageList` order, `ShowUntranslatedObjects` and "always available"
decide what the visitor sees ([Fallback rules for untranslated content](../../government/languages/fallback-rules-for-untranslated-content.md)).
For a non-profit the usual choice: show the main-language page rather than nothing, with a note in the visitor's
language that the page is not yet translated.

## The language switcher

Put a switcher on every page, labeled with each language's own name ("Español", not "Spanish"). The shipped designs do
not print `hreflang` links; your website creator adds them if wanted
([The language switcher](../../government/languages/the-language-switcher.md)).

## Translators, often volunteers

- Give each translator a role limited to their language: the policies to edit and translate can be limited by
  Language ([Translator roles and permissions](../../government/languages/translator-roles-and-permissions.md)).
- Use an approval step for translations of sensitive pages (services, legal) by a second speaker.
- Keep a short glossary of your terms in each language ([Glossaries of terms](../../government/languages/glossaries-of-terms.md)).
- A routine keeps translations in step: [Translation routine](../../customers/day-to-day/translation-routine.md).

## Translation drafts with Exponential Intelligence

Exponential Intelligence can produce translation drafts that a person reviews; a translation sync keeps translations
in step with changed source texts; nothing is published by the assistant
([Assisted translation with Exponential Intelligence](../../businesses/multilingual/assisted-translation-with-exponential-intelligence.md)).
It needs a provider, which may cost money and receives the text; decide that first
([Exponential Intelligence](../../customers/growth/exponential-intelligence.md)). For information people rely on for
their safety or rights, always have a fluent speaker check the draft.

## Right-to-left and other scripts

Arabic, Hebrew, Persian, Urdu and other right-to-left languages are supported on public sites and in the
administration ([Right-to-left sites](../../creators/website-creators/right-to-left-sites.md),
[Right-to-left languages on public sites](../../government/languages/right-to-left-languages-on-public-sites.md)).
If a locale you need is not shipped, a custom locale can be added
([Custom locales for unlisted languages](../../government/languages/custom-locales-for-unlisted-languages.md),
[Minority and regional languages](../../government/languages/minority-and-regional-languages.md)).

## Limits

- At most 62 content languages per installation on 64-bit PHP ([The 62-language limit](../../government/languages/the-62-language-limit.md)).
  Few non-profits come close.
- Every language multiplies the editing work. Budget translation time ([Budget lines](../grants/budget-lines-for-hosting-and-maintenance.md)).

Write clearly in every language; plain language helps everyone reading in a second language
([Plain language](../../government/languages/plain-language.md)).

## Checklist

- [ ] Languages chosen with the communities you serve.
- [ ] Priority content translated first; forms included.
- [ ] Fallback behavior decided and tested.
- [ ] Language switcher on every page.
- [ ] Translators with roles limited by language; sensitive pages checked by a second speaker.
- [ ] Machine drafts always reviewed by a person.
- [ ] Multilingual test done ([Testing a multilingual site](../../government/languages/testing-a-multilingual-site.md)).

## Related pages

- [Communications for non-profits](README.md)
- [Multilingual businesses](../../businesses/multilingual/README.md)
- [Languages at a glance](../../government/languages/languages-at-a-glance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
