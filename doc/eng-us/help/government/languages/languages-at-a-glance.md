---
title: Languages at a glance
audience: government, project-manager, admin, editor
category: public-sector
order: 20
summary: What Exponential offers a public body that serves people in several languages: content languages, a siteaccess and address per language, fallbacks, translator roles, right-to-left scripts, interface translations, and the known gaps, in one overview.
keywords: languages, multilingual, official languages, translation, content languages, interface language, right to left, locales, siteaccess, fallback, overview
type: help
---
# Languages at a glance

This page is for managers and administrators who plan a multilingual public website or administration with
Exponential. It lists what the product does, with the documentation behind each point, and the gaps to plan for. The
other pages of this section explain each topic for public bodies.

## Contents

1. [What Exponential provides](#what-exponential-provides)
2. [What the numbers are](#what-the-numbers-are)
3. [Known gaps](#known-gaps)
4. [Where to start](#where-to-start)

## What Exponential provides

| Need | Feature | Documentation |
|---|---|---|
| Content in several languages | every object holds one translation per content language | [Content languages](../../../guides/content-languages.md) |
| One site per language | one siteaccess per language with its `SiteLanguageList[]` | [Building a multilingual site](../../creators/website-creators/multilingual-sites.md) |
| An address per language | a path (`/de/`), a host, or both | [Language switchers and addresses per language](../../creators/website-creators/language-switcher-and-language-urls.md) |
| The start page in the visitor's language | `DefaultHostUriMatchMapItems[]` redirect by browser language | [Siteaccess by host prefix, and the redirect to the language](../../../features/6.0/siteaccess-host-and-language-matching.md) |
| A language switcher | `TranslationSA[]` and the `language_switcher` operator | same page |
| What visitors see when a translation is missing | the order of `SiteLanguageList[]`, "always available" | [Content languages, section 6](../../../guides/content-languages.md#6-main-language-and-always-available) |
| Translators limited to their language | policies limited by **Language** | [Roles and policies](../../../guides/roles-and-policies.md) |
| Right-to-left scripts | direction from the locale file; the administration mirrors itself | [Right-to-left languages](../../../features/6.0/right-to-left.md) |
| The administration in staff's language | interface translations and a per-user choice | [Languages (for editors)](../../languages.md) |
| Dates, numbers, currency per language | locale files | [Languages in Exponential](../../../languages.md) |

## What the numbers are

- **Locales**: 359 locale files ship in `share/locale/`; each one can become a content language
  ([Languages in Exponential](../../../languages.md)).
- **Right to left**: 43 of them carry `TextDirection=rtl` (counted in `share/locale/`; the list is in section 9 of
  [Languages in Exponential](../../../languages.md#9-right-to-left-languages)).
- **Content languages per installation**: at most 62 on 64-bit PHP (see "The 62 language limit").
- **Administration translations**: the directories of `share/translations/`, with very different completeness; the
  current table is in [Languages in Exponential, section 4](../../../languages.md#4-every-administration-translation).

## Known gaps

From [Internationalization standards](../../../standards/internationalization.md#known-gaps):

- A stock MySQL or MariaDB connection uses 3-byte `utf8`; some characters (many historic scripts, emoji) need
  `utf8mb4` configured by the administrator.
- No plural forms in the interface translation system.
- One time zone per installation or siteaccess, none per user.
- Some extension stylesheets are not yet right-to-left aware.
- `hreflang` links between language versions are not printed by the shipped designs; your pagelayout adds them.
- No translation memory or machine translation in the kernel; the optional `expai` assistant offers a Translate
  action on fields ([expai](../../../features/6.0/extensions/expai.md)).

## Where to start

Read "Content languages and interface languages" first, then "Second and third official languages" for the typical
public sector setup, and the languages checklist at the end of this section.

## Related pages

- [Content languages](../../../guides/content-languages.md)
- [Languages in Exponential](../../../languages.md)
- [Internationalization standards](../../../standards/internationalization.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
