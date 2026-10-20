---
title: Right-to-left sites
audience: website-creator, admin
category: website-creation
order: 270
summary: Build a public site in Arabic, Hebrew, Persian or another right-to-left language - where the direction comes from, setting dir on the page and on content, CSS logical properties, and how to test.
keywords: right to left, RTL, Arabic, Hebrew, Persian, Urdu, dir, text direction, TextDirection, text_direction, is_right_to_left, logical properties, bidi, website creator
type: help
---
# Right-to-left sites

This page is for website creators who build a public site, or a language of one, in a right-to-left script. The
administration turns around by itself for right-to-left interface languages
([Right-to-left languages in the administration](../../../features/6.0/right-to-left.md)); a public design does what
its templates and stylesheets say, so this page shows what to add.

## Contents

1. [Where the direction comes from](#where-the-direction-comes-from)
2. [The page](#the-page)
3. [Content in a right-to-left language](#content-in-a-right-to-left-language)
4. [Stylesheets](#stylesheets)
5. [Testing](#testing)

## Where the direction comes from

The only source is the locale file: `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl`. 28 locale
files carry it; the list is in [Languages in Exponential](../../../languages.md). The kernel exposes it:

| Where | What |
|---|---|
| `$site.text_direction` | `rtl` or `ltr` for the current interface locale of the siteaccess |
| `$code\|locale().is_right_to_left` | for any locale code in a template |
| `eZLocale::textDirectionOf( 'heb-IL' )` | the same in PHP |

## The page

Print the direction on `<html>` in your pagelayout, as the administration pagelayouts do:

```text
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

The siteaccess's `Locale` decides `$site.text_direction`, so an Arabic siteaccess with `Locale=ara-SA` gets
`dir="rtl"` and an English one gets nothing.

## Content in a right-to-left language

A page may show content in another direction than the interface, for example an Arabic quote on an English page.
`design/standard/templates/parts/exp_content_direction.tpl` prints ` lang="..." dir="rtl"` for a right-to-left content
locale and nothing for a left-to-right one; include it on the element that wraps the content. The online editors switch
their direction for right-to-left texts, so editors write in the right direction.

## Stylesheets

| Instead of | Write |
|---|---|
| `margin-left`, `padding-right` | `margin-inline-start`, `padding-inline-end` |
| `left: 0`, `right: 0` | `inset-inline-start: 0`, `inset-inline-end: 0` |
| `text-align: left` | `text-align: start` |
| `float: left` | flex or grid layouts, which follow the direction |
| arrow icons pointing "forward" | flip them with `[dir="rtl"] .icon-forward { transform: scaleX(-1); }` |

With logical properties one stylesheet serves both directions; no `-rtl.css` copy to maintain.

## Testing

- [ ] Open the right-to-left siteaccess: `<html dir="rtl">` is present, columns and menus mirror.
- [ ] Mixed text (numbers, Latin words in Arabic text) reads correctly.
- [ ] Forms: labels, inputs and error messages align to the start side.
- [ ] Icons that point along the line point the right way.
- [ ] The left-to-right sites look exactly as before.
- [ ] Check at a narrow width too; menus that collapse often break first.

## Related pages

- [Building a multilingual site](multilingual-sites.md)
- [Accessibility for site builders](accessibility-for-site-builders.md)
- [Stylesheets and scripts for your site](stylesheets-and-scripts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
