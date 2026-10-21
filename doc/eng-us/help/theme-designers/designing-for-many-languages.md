---
title: "Designing for many languages"
audience: theme-designer
category: theme-design
order: 24
summary: The design decisions behind a multilingual Exponential site - one siteaccess per language, text expansion, scripts and fonts, right to left, dates and numbers, the language switcher, missing translations and images with text - with links to the tutorial and right-to-left pages.
keywords: theme designer, multilingual, languages, translation, text expansion, scripts, fonts, right to left, rtl, language switcher, locale, dates, internationalization
type: help
---
# Designing for many languages

This page is for theme designers whose site, or theme, will be used in more than one language. Exponential stores a
translation per object and usually serves each language through its own siteaccess, with its own locale. The
technical steps are in [Tutorial: internationalize a theme](../designers/theme-design/tutorial-internationalize-a-theme.md),
[Translating template strings](../designers/templates/translating-template-strings.md) and, for Arabic, Hebrew and other
right-to-left scripts, [Right-to-left designs](../designers/theme-design/right-to-left-designs.md). This page lists the
decisions a designer makes before those steps.

## Contents

1. [How languages reach the page](#how-languages-reach-the-page)
2. [Text expansion](#text-expansion)
3. [Scripts and fonts](#scripts-and-fonts)
4. [Right to left](#right-to-left)
5. [Dates, numbers and names](#dates-numbers-and-names)
6. [The language switcher](#the-language-switcher)
7. [Missing translations](#missing-translations)
8. [Images and icons with meaning](#images-and-icons-with-meaning)
9. [A language checklist](#a-language-checklist)

## How languages reach the page

| Text | Comes from | Translated by |
|---|---|---|
| Content (titles, body) | the object's translation in that language | editors |
| Interface text in templates ("Read more", "Search") | translation files of the design, through `i18n` | translators |
| Dates and numbers | the siteaccess's locale | automatic |

Your design must hold all three at their longest. Background on content translations:
[Translations and languages](../cms-developers/translations-and-languages.md).

## Text expansion

Translated interface text is often longer than English; German and Finnish labels are long, and some languages
need more height. Short labels expand the most, in relative terms. Therefore:

- buttons, tabs and menu items grow with their text; no fixed widths;
- avoid designs that only work when a label fits on one line;
- test with the longest language of the site, or with English text padded by about a third where there is no
  translation yet.

## Scripts and fonts

- Check that the chosen fonts cover every script of the site. A font without Greek or Cyrillic glyphs falls back to a
  system font for those characters, mid-sentence.
- For scripts such as Chinese, Japanese, Korean, Arabic or Devanagari, plan a separate font stack, chosen per
  language with `:lang()` selectors. The administration does this with separate tokens for international and CJK
  stacks ([Fonts in a design](../designers/theme-design/fonts-in-a-design.md#system-fonts)).
- Some scripts need more line height or a larger size to read as well ([Type scale](type-scale.md#other-scripts-and-languages)).
- Italic and uppercase do not exist in every script; do not carry meaning with them alone.

## Right to left

A right-to-left language mirrors the layout: navigation, icons with direction (arrows, "next"), the order of
columns. A design built with logical CSS properties needs very little extra work
([Logical CSS properties](../designers/theme-design/logical-css-properties.md)). Draw the key screens mirrored at
least once, and mark icons that must not mirror (a play button, a logo, a checkmark).
[Tutorial: a right-to-left theme](../designers/theme-design/tutorial-right-to-left-theme.md) and
[Checklist: right to left](../designers/theme-design/checklist-right-to-left.md) cover the rest.

## Dates, numbers and names

Dates, times and numbers are formatted by the locale of the siteaccess
([Dates, numbers and l10n](../designers/templates/dates-numbers-and-l10n.md)). Design date areas for the longest
format ("Thursday, 24 September 2026" is far longer than "24/09/2026", and some languages write it longer still). Names of people and places
vary in length and order; do not design a byline that needs first and last name in one fixed order.

## The language switcher

- Each language named in itself ("Deutsch", "English", "Français"), not in the current language and not as a flag:
  flags stand for countries, not languages.
- The current language marked; the others link to the same page in that language when it exists.
- Shipped designs build it with the `language_switcher` template operator
  ([Designing headers and footers](designing-headers-and-footers.md#switches-language-and-color-scheme)).

## Missing translations

Not every object exists in every language. Depending on the settings, a missing translation is either not shown at
all, or shown in another language of the siteaccess's language list. Ask the developer which applies, then design for
it: a list may be shorter in one language, and a page may show content in a language other than the frame. The page
should then mark the content's language (`lang` attribute) so screen readers pronounce it correctly.

## Images and icons with meaning

- Avoid text inside images: it cannot be translated without new images. Put text in HTML over or under the image.
- Gestures, colors and symbols differ in meaning between cultures; prefer plain icons with labels.

## A language checklist

- [ ] Every language and script of the site listed, with its font coverage checked.
- [ ] Key screens in the longest language.
- [ ] Mirrored screens for right-to-left languages.
- [ ] Date and number areas sized for the longest format.
- [ ] Language switcher with each language in itself.
- [ ] No text baked into images.

## Related pages

- [Tutorial: internationalize a theme](../designers/theme-design/tutorial-internationalize-a-theme.md)
- [The i18n operator](../designers/templates/i18n-operator.md)
- [Languages](../languages.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
