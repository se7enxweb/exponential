---
title: Right-to-left languages on public sites
audience: government, website-creator, admin
category: public-sector
order: 150
summary: How a public body publishes in Arabic, Hebrew, Persian, Urdu or another right-to-left language with Exponential: where the direction comes from, what the public design must add (dir on the page, logical CSS), mixed-direction content, and testing.
keywords: right to left, RTL, Arabic, Hebrew, Persian, Urdu, Yiddish, dir, TextDirection, text_direction, logical properties, bidi, public site, design
type: help
---
# Right-to-left languages on public sites

This page is for site builders and administrators of public sites that serve communities reading right to left. The
administration of Exponential mirrors itself for right-to-left interface languages; a public design does what its
templates and stylesheets say, so the design has to add a few things
([Right-to-left sites](../../creators/website-creators/right-to-left-sites.md)).

## Contents

1. [Which languages](#which-languages)
2. [Where the direction comes from](#where-the-direction-comes-from)
3. [What the public design must do](#what-the-public-design-must-do)
4. [Mixed content](#mixed-content)
5. [Testing](#testing)

## Which languages

40 locale files in `share/locale/` carry `TextDirection=rtl`: Arabic for many countries, Hebrew, Yiddish, Persian and
Dari, Urdu, Pashto, Western Punjabi, Sindhi, Kashmiri, Uyghur, Central Kurdish (Sorani), Syriac, N'Ko and Dhivehi
([Languages in Exponential, section 9](../../../languages.md#9-right-to-left-languages)). Languages written in the Latin
script, such as Kurmanji Kurdish, are left to right.

## Where the direction comes from

The only source is the locale file. The kernel exposes it as `$site.text_direction` (`rtl` or `ltr`) for the
siteaccess's `Locale`, and `$code|locale().is_right_to_left` for any locale in a template.

## What the public design must do

1. Print the direction on `<html>` in the pagelayout:

   ```text
   <html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
   ```

2. Use CSS logical properties (`margin-inline-start`, `padding-inline-end`, `inset-inline-start`,
   `text-align: start`) instead of left and right, so one stylesheet serves both directions.
3. Mirror icons that point along the line (arrows, "next").
4. Check fonts cover the script.

## Mixed content

A page may show content in another direction than the page, such as an Arabic quote on an English page.
`design/standard/templates/parts/exp_content_direction.tpl` prints ` lang="..." dir="rtl"` for a right-to-left content
locale and nothing for a left-to-right one; include it on the element that wraps the content. The online editors
switch their direction for right-to-left texts, so editors write in the right direction
([Right-to-left languages in the administration](../../../features/6.0/right-to-left.md#content)).

## Testing

- [ ] `<html dir="rtl">` on the right-to-left siteaccess; columns and menus mirror.
- [ ] Numbers and Latin words inside the text read correctly.
- [ ] Forms: labels, inputs and errors align to the start side.
- [ ] Left-to-right siteaccesses look exactly as before.
- [ ] Checked at a narrow width and at 200% zoom.

## Related pages

- [Right-to-left sites](../../creators/website-creators/right-to-left-sites.md)
- [Right-to-left in the administration](right-to-left-in-the-administration.md)
- [Stylesheets and scripts](../../creators/website-creators/stylesheets-and-scripts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
