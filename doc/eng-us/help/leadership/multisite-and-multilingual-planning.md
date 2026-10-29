---
title: Planning multisite and multilingual projects
audience: project-manager, admin
category: leadership
order: 70
summary: Plan several sites and languages in one installation: siteaccesses, languages per site, translations, right to left, the 62-language limit, cross-site links and a planning sheet.
keywords: multisite, multi-site, multilingual, languages, translation, siteaccess, host, URL, SiteLanguageList, content languages, locales, interface translation, right to left, RTL, cross-site links, ezurlmultisite, per-site settings, project manager, administrator
type: help
---
# Planning multisite and multilingual projects

This page is for project managers and administrators who plan more than one site, more than one language, or both,
on one Exponential installation. It answers: what a siteaccess is and how visitors reach one, how languages are
shown per site, what the product supports for languages and right-to-left scripts, which limits to know before you
promise anything, and how to plan the work. The details live in the guides and feature pages linked from each
section; this page arranges them as decisions.

## Contents

1. [The building blocks](#the-building-blocks)
2. [Multisite: one installation, several sites](#multisite-one-installation-several-sites)
3. [How visitors reach a site](#how-visitors-reach-a-site)
4. [Multilingual: content languages per site](#multilingual-content-languages-per-site)
5. [What the product supports for languages](#what-the-product-supports-for-languages)
6. [Right-to-left languages](#right-to-left-languages)
7. [Limits to know before you promise](#limits-to-know-before-you-promise)
8. [Operations with many sites](#operations-with-many-sites)
9. [Roles for translators and site teams](#roles-for-translators-and-site-teams)
10. [The planning sheet](#the-planning-sheet)
11. [Checklists](#checklists)

## The building blocks

| Thing | What it is | Read |
|---|---|---|
| **Siteaccess** | "One way into the installation: a name, a set of settings in `settings/siteaccess/<name>/`, and a rule for which requests it answers" | [10.2 Siteaccesses and site addresses](../../install/10-after-installing.md#102-siteaccesses-and-site-addresses) |
| **Content language** | A language content can be written in (Setup > Languages) | [Content languages](../../guides/content-languages.md) |
| **Locale** | The file that names a language and country and holds date, number and currency formats and the text direction | [Languages in Exponential](../../languages.md) |
| **Administration translation** | The words of the interface for one language | same |
| **Siteaccess locale** | The locale a siteaccess formats with and the translation its interface uses | same |

Four things are easily mixed up: locale, administration translation, content language and siteaccess locale
([Languages, section 1](../../languages.md#1-locales-translations-content-languages-and-siteaccesses)). Use the words
exactly in your plans; it avoids most misunderstandings with the technical team.

## Multisite: one installation, several sites

Multisite support is one of the main features of the kernel listed in the README
([What Exponential is](what-exponential-is.md#what-the-kernel-does)). Several sites from one installation means each
site is one or more siteaccesses, each with "its own host, path, language and design"
([Who is 7x?](../who-is-7x.md#a-cms-a-cmf-and-a-shop-in-one)). The default site package of the installer,
`sevenx_multisite`, is itself a multisite installation
([4.3 Site packages](../../install/04-choosing-an-install-method.md#43-site-packages-what-is-installed)).

### Shapes of a multisite project

| Shape | How it is built | Consider |
|---|---|---|
| Several brands, one content tree | Each site shows its own subtree; a siteaccess per site | Links between the sites need `ezurlmultisite` ([Cross-site links](../../features/6.0/multisite-cross-site-links.md)) |
| One site, several languages | One public siteaccess per language, sharing one database | See [Multilingual](#multilingual-content-languages-per-site) |
| Several sites, each in its own extension | Per-site settings inside the extension, active only while its siteaccesses run | [Per-site settings inside extensions](../../features/6.0/multi-site-ini-overrides.md) |
| Separate administrations per site | Several administration siteaccesses | Roles limited by siteaccess and subtree |

Questions that decide the shape:

- [ ] Do the sites share content (an article on two sites), or only the installation?
- [ ] Does each site have its own design?
- [ ] Do the sites share editors, or does each have its own team?
- [ ] Do the sites share one database? Siteaccesses that share one are listed in `RelatedSiteAccessList[]`
      ([10.2](../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
- [ ] Will the sites be upgraded together? One installation means one kernel version for all of them.

> **Note**
> One installation is one kernel, one set of extensions and (usually) one database. That is the strength of multisite
> (one place to update and secure) and its constraint (every site moves with the others). Write that trade-off into
> the charter.

## How visitors reach a site

The installer chose one of three layouts, and they can be changed later
([10.2](../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)):

| Layout | Example | The sources say |
|---|---|---|
| URL | `/admin`, `/de` | "one host name for everything; simplest behind any web server" |
| Host | `admin.example.com` | "a host name per siteaccess; best for production and for HTTPS certificates per host" |
| Port | `:8081` | "one port per siteaccess; useful for local work" |

More options: a host map that matches by the start, end or part of a host (so a test host reaches the siteaccess of
the live host), and a redirect from `/` to the visitor's language, chosen by host and browser language, with one
address per language and no cookie
([Siteaccess by host prefix, and the redirect to the language](../../features/6.0/siteaccess-host-and-language-matching.md)).

Decide with your administrator and your SEO owner: one host per language, or one host with a path per language. The
product supports both; the choice is about certificates, analytics and how people share links.

## Multilingual: content languages per site

How it works ([Content languages](../../guides/content-languages.md)):

- **Adding a language translates nothing and changes no siteaccess.** Editors translate content in the editor; the
  administrator adds the language to `SiteLanguageList` of each siteaccess that should show it.
- **Each siteaccess shows an object in the first language of its `SiteLanguageList` that the object has.** Objects
  in none of them are hidden, unless they are "always available".
- **Every object has a main language** that cannot be removed while it is the main one.
- **Removing a translation deletes content for good**; removing a language is only possible when no object and no
  class uses it, so it never deletes content.
- **Translators can be limited to their language**: `content/edit`, `content/translate` and `content/create` can be
  limited by Language in the role editor.
- **Language switch links**: `TranslationSA[]` names the translation siteaccesses, and the `language_switcher`
  operator builds links between language versions of a page.

A typical setup, from [10.14 Multi-language sites](../../install/10-after-installing.md#1014-multi-language-sites):

```ini
# settings/siteaccess/site_de/site.ini.append.php
[RegionalSettings]
Locale=ger-DE
ContentObjectLocale=ger-DE
SiteLanguageList[]
SiteLanguageList[]=ger-DE
SiteLanguageList[]=eng-GB
ShowUntranslatedObjects=disabled
TextTranslation=enabled
```

> **Warning**
> "Do not change `ContentObjectLocale` of an existing site lightly: objects that do not exist in the new default
> language disappear from listings." And at install time, give every language the site package needs, or pages show
> without images or sub-items ([10.14](../../install/10-after-installing.md#1014-multi-language-sites)).

### Fallback decisions per site

For each public siteaccess decide:

| Decision | Options | Effect |
|---|---|---|
| Which languages, in which order | `SiteLanguageList[]` | The first language an object has is shown |
| Untranslated content | `ShowUntranslatedObjects=disabled` (shipped) or `enabled` | Hidden, or shown in its main language |
| Shared items (folders, users, media) | "Always available" per object, default per class | Shown in the main language where no translation exists |

## What the product supports for languages

From [Languages in Exponential](../../languages.md):

- **368 locale files** (361 languages and countries, the test language Lorem Ipsum and 6 variations),
  43 of them written right to left, can be chosen as content languages and siteaccess locales.
- **administration translations** (the current totals are at the top of [Languages in Exponential](../../languages.md)), listed in
  [Languages in Exponential](../../languages.md).
- **Support levels**:

| Level | Meaning |
|---|---|
| Full: interface and content | The administration speaks the language completely; content, dates, numbers and currency follow the locale |
| Partial interface, full content | An older administration translation covers 16 to 39% of today's strings; the rest shows in English |
| Content only | No administration translation; the interface stays English, content and formats follow the locale |

Plan accordingly: editors who need the administration in their own language need a "full" language, or a
translation project ([Languages, section 8](../../languages.md#8-adding-or-completing-an-administration-translation)).
A locale without its own translation falls back to another one of the same language where one exists
([Languages, section 13](../../languages.md#13-translation-fallbacks)).

## Right-to-left languages

- When the interface language is written right to left, the administration turns around: `dir="rtl"`, side columns
  swapped, text starting at the right ([Right to left](../../features/6.0/right-to-left.md)).
- Content written in a right-to-left content language is marked the same way where it is shown and edited, whatever
  the interface language is.
- The public site's design is your project's: templates and stylesheets of your designs must handle right to left.
  Put a right-to-left review into QA when a right-to-left language is in scope.
- Help pages downloaded as PDF print right-to-left text right to left; Arabic letters are drawn in their basic forms,
  without joining ([The help system](../../features/6.0/help-system.md)).

## Limits to know before you promise

| Limit | Value | Source |
|---|---|---|
| Content languages per installation | at most 62 on 64-bit PHP, 30 on 32-bit PHP | [Languages, section 11](../../languages.md#11-the-limit-of-62-content-languages) |
| A language added and never used | still takes its place until removed | same |
| 64-bit PHP | recommended "for support of more than 30 languages" | [Operating system support](../os-support.md) |
| Removing a language | only when no object and no class has a translation in it | [Content languages](../../guides/content-languages.md) |
| SQLite and several machines | SQLite needs one host; several web servers need a database server | [Database support](../database-support.md) |

The 62-language limit is deliberate: raising it would change the database schema for every installation, "to serve a
case no known site has reached" ([Languages, section 11](../../languages.md#11-the-limit-of-62-content-languages)).

## Operations with many sites

For administrators hosting several sites on one installation:

| Need | Feature | Read |
|---|---|---|
| Settings for all siteaccesses of one site, kept in that site's extension | Per-site override settings | [Per-site settings inside extensions](../../features/6.0/multi-site-ini-overrides.md) |
| Logs of each site in its own directory; cache of a site in memory; INI cache cleared per site | `UseGlobalLogDir`, `LogVarDir`, `CacheVarDir`, `INICacheDir` | [Logs, INI cache and expiry per site](../../features/6.0/multi-site-log-and-cache-paths.md) |
| Links from one site to content of another | `ezurlmultisite` | [Cross-site links](../../features/6.0/multisite-cross-site-links.md) |
| Warm the caches of the sites | Preload | [Preloading caches](../../guides/preloading-caches.md) |
| The installation's name shown in pages | Installation name | [Installation name in pages](../../features/6.0/installation-name-in-pages.md) |

## Roles for translators and site teams

| Team member | Policies to plan | Limitation |
|---|---|---|
| Translator for German | `content/edit` or `content/translate`, `content/create` | Language: German ([Content languages, section 7](../../guides/content-languages.md)) |
| Editor of site A only | `content/*` as agreed | Subtree of site A, or its section ([Roles and policies](../../guides/roles-and-policies.md)) |
| Editor who may sign in to one administration only | `user/login` | Siteaccess |
| Language owner | Translator rights plus approval | Language and workflow |

## The planning sheet

```text
Multisite and language plan for <project name>                  <date>

Sites
| Site        | Siteaccess(es)       | Reached by (URL/host/port) | Design        | Subtree/root      | Editors (group) |
|-------------|----------------------|----------------------------|---------------|-------------------|-----------------|
| <site A>    | <site_a_en, site_a_de> | <host or path>          | <design>      | <node or folder>  | <group>         |
| Administration | <admin>           | <host or path>             | admin4        |                   | <groups>        |

Languages
| Language (locale) | Content language? | Admin translation level | Right to left | Shown on siteaccesses | Translator (group) |
|-------------------|-------------------|-------------------------|---------------|-----------------------|--------------------|
| <eng-US>          | yes               | full                    | no            | <list>                | <group>            |
| <ger-DE>          | yes               | full                    | no            | <list>                | <group>            |

Per siteaccess: SiteLanguageList order, ShowUntranslatedObjects, always-available classes
Cross-site links needed: <yes/no>     Per-site logs/caches: <yes/no>
Content languages in use: <n> of at most 62
Open questions: <list>
```

## Checklists

**Before the build**

- [ ] Every site and every language is in the planning sheet with an owner.
- [ ] Access layout chosen (URL, host or port) and agreed with whoever runs DNS and certificates.
- [ ] Every language's administration translation level checked against what its editors need.
- [ ] Right-to-left designs in scope or explicitly out of scope.
- [ ] The language count is well below the limit, with room for growth.

**Before launch**

- [ ] Every siteaccess shows the expected language first and the expected fallback.
- [ ] Always-available is set on folders, users and media that every language needs.
- [ ] Language switch links tested in both directions.
- [ ] Cross-site links resolve to the right site (no 404).
- [ ] Translations complete for every published page of every language.
- [ ] Right-to-left pages reviewed by someone who reads the language.

## Related pages

- [Content languages](../../guides/content-languages.md)
- [Languages in Exponential](../../languages.md)
- [Languages for editors](../languages.md)
- [Right to left](../../features/6.0/right-to-left.md)
- [Translations and languages](../../features/6.0/translations-and-languages.md)
- [Chapter 10: After installing](../../install/10-after-installing.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
