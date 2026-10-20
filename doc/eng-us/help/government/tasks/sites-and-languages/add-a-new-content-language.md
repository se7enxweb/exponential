---
title: Add a new content language
audience: government, admin
category: public-sector
order: 30
summary: Task for administrators: add a content language in Setup > Languages, then make it visible through a siteaccess, and give translators access.
keywords: add language, content language, Setup Languages, SiteLanguageList, new language, translators
type: help
---
# Add a new content language

This task is for administrators. Background: [Adding a content language](../../languages/adding-a-content-language.md)
and [Content languages](../../../../guides/content-languages.md#4-adding-a-language).

## Before you start

- The policy `content/translations` (administrators have it).
- The locale code, for example `cym-GB` for Welsh; check it exists in [Languages in Exponential](../../../../languages.md#3-every-locale).

## Steps

1. **Setup > Languages**, press **Add language**.
2. Type part of the name or code into **Find a language**, choose the entry, press **Add language**.
3. The overview confirms the language was added; its card says it is not used and in no `SiteLanguageList`.
4. Make it visible: add the locale to the `SiteLanguageList[]` of a siteaccess, or create a siteaccess for it (see
   "Give a language its own address").
5. Clear caches: `php bin/php/ezcache.php --clear-tag=ini,content`, and `./console exp:velocity cache clear` with
   Velocity.
6. Give translators a role limited to the language (see "Limit a translator to one language").

## Check

The language's card shows **Shown first by** or the siteaccess chip; translating a page into it shows the page on that
siteaccess.

## Problems

| Problem | Fix |
|---|---|
| *There is no locale ... in share/locale* | a custom language needs a locale file first (see "Add a custom locale for a minority language") |
| The editor does not offer the language | add the **Language** limitation to the editor's policies |
| The add form refuses: limit reached | all 62 places are taken; remove an unused language |

## Related pages

- [A siteaccess per language](../../languages/a-siteaccess-per-language.md)
- [The 62 language limit](../../languages/the-62-language-limit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
