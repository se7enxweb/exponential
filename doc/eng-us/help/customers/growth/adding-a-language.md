---
title: Adding a language
audience: customer, admin, businesses
category: customer-success
order: 6
summary: What a site owner plans and decides before adding a content language to an Exponential site - adding a language translates nothing and shows nothing by itself, what the siteaccess needs, translators and approvers, which content first, and a launch order.
keywords: adding a language, multilingual, content language, translation, siteaccess, SiteLanguageList, language switcher, translators, launch, site owner
type: help
---
# Adding a language

This page is for the owner who wants to offer the site in another language. In Exponential this has two technical
halves (a content language, and a siteaccess that shows it) and one large human half: the translation work. The
administrator's steps are in [Content languages](../../../guides/content-languages.md); the planning view is
[Multilingual planning](../../businesses/multilingual/multilingual-planning.md).

## Contents

1. [What adding a language does, and does not](#what-adding-a-language-does-and-does-not)
2. [Decisions before you start](#decisions-before-you-start)
3. [The steps, and who does them](#the-steps-and-who-does-them)
4. [What to translate first](#what-to-translate-first)
5. [Launch checks](#launch-checks)

## What adding a language does, and does not

From [Content languages](../../../guides/content-languages.md):

- **Setup > Languages > Add language** adds a content language; 132 locales are in the list on a standard
  installation, and a custom one can be made from a locale file.
- **Adding translates nothing and changes no siteaccess**: no visitor sees anything new yet.
- What a visitor sees is decided per siteaccess by `SiteLanguageList`: the content languages it shows, most preferred
  first. An item is shown in the first of these it has; items in none of them are hidden unless *always available*
  (or `ShowUntranslatedObjects` is enabled).
- A content language is not the interface language: buttons and menus come from the siteaccess's `Locale` and the
  translation files.
- An installation holds at most 62 content languages.

## Decisions before you start

| Question | Options | Read |
|---|---|---|
| How do visitors reach the language? | a path (`/de`), a host (`de.example.com`) or a domain per country | [Language switcher and language URLs](../../creators/website-creators/language-switcher-and-language-urls.md) |
| What do visitors see when a page is not translated? | hidden, or shown in the main language | [Content languages](../../../guides/content-languages.md) |
| Everything, or a subset? | a full site, or the main pages only | [Multilingual planning](../../businesses/multilingual/multilingual-planning.md) |
| Who translates and who approves? | staff, agency, freelancers, assisted by the writing assistant | [Translation routine](../day-to-day/translation-routine.md) |
| Right-to-left? | Arabic, Hebrew, Persian and others need the design to support it | [Right-to-left markets](../../businesses/multilingual/right-to-left-markets.md) |
| Currencies, dates and formats? | the locale decides dates and numbers; a store also needs prices | [Currencies, dates and formats](../../businesses/multilingual/currencies-dates-and-formats.md) |

## The steps, and who does them

| Step | Who |
|---|---|
| 1. Decide the questions above | owner |
| 2. Add the content language in **Setup > Languages** | administrator |
| 3. Create or adjust the siteaccess, its address and `SiteLanguageList`; add a language switcher to the design | partner |
| 4. Give translators accounts limited to the language; set up per-language approval if wanted | administrator ([Approvals and review](../day-to-day/approvals-and-review.md)) |
| 5. Translate the content (below) | translators |
| 6. Test on the new address, signed out | owner and a native speaker |
| 7. Make the address public; tell search engines (sitemap per language) | partner ([XML sitemaps](../../creators/website-creators/sitemaps.md)) |

The new siteaccess can be tested before it is public; ask your partner for a test host
([One installation, many sites](../../businesses/multisite/one-installation-many-sites.md)).

## What to translate first

1. The start page and the menu folders: visitors reach everything else through them
   ([Translation workflow](../../businesses/multilingual/translation-workflow.md)).
2. Contact, legal and privacy pages.
3. The pages visitors from that market use most (your search statistics and analytics help).
4. Forms and their confirmation texts; newsletter lists for that language.
5. Everything else, in the order of the [Content calendar](../day-to-day/content-calendar.md).

## Launch checks

- [ ] Every page in the menu has a translation, or is deliberately shown in the main language.
- [ ] The language switcher leads from each page to the same page in the other language.
- [ ] Dates and numbers are written the local way.
- [ ] Forms, error messages and mails are in the language.
- [ ] A native speaker read the main pages.
- [ ] The [Multilingual checklist](../../businesses/multilingual/multilingual-checklist.md) is ticked off.

## Related pages

- [Translation routine](../day-to-day/translation-routine.md)
- [Languages and locales](../../businesses/multilingual/languages-and-locales.md)
- [Multilingual sites](../../creators/website-creators/multilingual-sites.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
