---
title: HTML language tags and accessibility
audience: government, website-creator, admin
category: public-sector
order: 270
summary: How the language of a page and of parts of a page reaches browsers and screen readers on an Exponential site (BCP 47 tags from the locale files, the lang attribute in the pagelayout, lang and dir on content in another language), the WCAG criteria involved, and what is and is not audited.
keywords: lang attribute, language of page, language of parts, BCP 47, ContentLanguage, WCAG 3.1.1, WCAG 3.1.2, screen reader, accessibility, hreflang, pagelayout
type: help
---
# HTML language tags and accessibility

This page is for site builders and administrators. Screen readers choose their pronunciation from the `lang` of the
page and of its parts; search engines and browsers use it too. [WCAG 2.2](https://www.w3.org/TR/WCAG22/) asks for the
language of the page (3.1.1) and of passages in another language (3.1.2).

## Contents

1. [Where the tag comes from](#where-the-tag-comes-from)
2. [The page language](#the-page-language)
3. [Parts in another language](#parts-in-another-language)
4. [What is and is not audited](#what-is-and-is-not-audited)
5. [Checks](#checks)

## Where the tag comes from

Page layouts print `eZLocale::httpLocaleCode()`, which is `[HTTP] ContentLanguage` of the siteaccess's locale file.
Since 6.0.15 every shipped locale names a valid BCP 47 tag (`fr-FR`, `nb-NO`, `he-IL`), replacing earlier non-standard
values such as `no-bokmaal` ([Translations and languages, HTML language tags](../../../features/6.0/translations-and-languages.md#html-language-tags)).
Internal locale codes (`fre-FR`) are not BCP 47; only the HTTP tag is
([Internationalization standards](../../../standards/internationalization.md#language-tags-bcp-47-and-iso-639)).

## The page language

In your public pagelayout:

```text
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

The administration's pagelayouts already do this ([Accessibility standards](../../../standards/accessibility.md)).

## Parts in another language

- **Content shown in another language than the page** (a fallback translation, a quote): wrap it in an element with
  its own `lang`; for right-to-left content `design/standard/templates/parts/exp_content_direction.tpl` prints
  ` lang="..." dir="rtl"`.
- **The language switcher**: each link carries the `lang` of its language (see "The language switcher").
- **Phrases inside rich text**: editors can mark a passage's language only if your rich text setup offers a way to
  do so; check the editor configuration of your site before promising it to editors.

## What is and is not audited

[Accessibility standards](../../../standards/accessibility.md) states the project's status: WCAG 2.2 level AA
**Partly complies** for the admin4 and admin4l administration; public site designs and the online editor are not
audited; EN 301 549 and Section 508 are **Unverified**; no VPAT or Accessibility Conformance Report exists. Your public
design's conformance is yours to test ([Testing accessibility](../../creators/website-creators/testing-accessibility.md)).

## Checks

- [ ] Every language siteaccess prints the right `lang` on `<html>`.
- [ ] Fallback content carries its own `lang`.
- [ ] Switcher links carry `lang`.
- [ ] A screen reader test of one page per language.
- [ ] `hreflang` links, if you add them, use the same tags.

## Related pages

- [Accessibility standards](../../../standards/accessibility.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- [Right-to-left languages on public sites](right-to-left-languages-on-public-sites.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
