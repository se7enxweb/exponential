---
title: Language of the page and its parts
audience: government, developer, editor, admin
category: public-sector
order: 270
summary: For public bodies with multilingual sites - how Exponential sets the language of administration and public pages, how translations and language siteaccesses give each page its own language, and how editors mark passages in another language (WCAG 3.1.1 and 3.1.2).
keywords: language, lang attribute, language of page, language of parts, translations, siteaccess, locale, multilingual, WCAG 3.1.1, 3.1.2, screen reader voice, content languages, minority languages
type: help
---
# Language of the page and its parts

This page is for developers who build the pagelayout, administrators who set up languages, and editors who write in
more than one language. Screen readers choose their voice and pronunciation from the `lang` attribute: a page in the
wrong language is read as nonsense. WCAG 2.2 asks for the language of the page (3.1.1, level A) and of passages in
another language (3.1.2, level AA) (<https://www.w3.org/TR/WCAG22/>).

## Contents

1. [In the administration](#in-the-administration)
2. [On the public site](#on-the-public-site)
3. [Passages in another language](#passages-in-another-language)
4. [Checking](#checking)
5. [Related pages](#related-pages)

## In the administration

Every admin4 pagelayout writes `<html lang="...">` from the siteaccess locale (`design/admin4/templates/pagelayout.tpl`;
[Accessibility standards](../../../standards/accessibility.md#wcag-22)). Users who switch the interface language get the
matching `lang` and, for right-to-left languages, `dir="rtl"`.

## On the public site

- Each language of a site is usually its own siteaccess with its own locale, and each content object has a
  translation per language ([Content languages](../../../guides/content-languages.md),
  [Multilingual sites](../../creators/website-creators/multilingual-sites.md)).
- Your pagelayout writes `<html lang>` from the siteaccess, for example from `$site.http_equiv.Content-language`
  ([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)).
- When a translation is missing and the site shows the content in another language (a fallback configured by the
  site's language list), that content is in a different language than the page: mark it, or show a notice and link to
  the version that exists.
- Language switchers name each language in that language ("Deutsch", "Français") and carry `lang` on each entry
  ([Language switcher and language URLs](../../creators/website-creators/language-switcher-and-language-urls.md)).

## Passages in another language

- A whole page in another language belongs in that translation, not in the default one.
- Short passages (a quotation, a title of a foreign law) need `lang` on the element. Whether editors can set it
  depends on the editor engine and the custom tags your site offers; developers can include
  `parts/exp_content_direction.tpl` in templates that show content from another language, which writes `lang` and,
  for right-to-left languages, `dir` ([Right-to-left languages](../../../features/6.0/right-to-left.md#content)).
- Proper names and technical terms do not need marking.

## Checking

- Inspect `<html lang>` on one page per siteaccess.
- With a screen reader, open a page in each language and listen: the voice must fit.
- Check fallback content and language switchers.

## Related pages

- [Content languages](../../../guides/content-languages.md)
- [Languages](../../languages.md)
- [Right-to-left accessibility](right-to-left-accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
