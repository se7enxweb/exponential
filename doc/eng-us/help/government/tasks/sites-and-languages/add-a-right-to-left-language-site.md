---
title: Add a right-to-left language site
audience: government, admin, website-creator
category: public-sector
order: 110
summary: Task for administrators and site builders: add an Arabic, Hebrew, Persian or Urdu language site, with the direction printed in the pagelayout and logical CSS, and test it.
keywords: right to left, RTL, Arabic, Hebrew, Persian, Urdu, dir rtl, text_direction, language site, logical properties
type: help
---
# Add a right-to-left language site

This task is for administrators and site builders. Background:
[Right-to-left languages on public sites](../../languages/right-to-left-languages-on-public-sites.md).

## Before you start

- The locale has `TextDirection=rtl` (for example `ara-SA`, `heb-IL`, `fas-IR`, `urd-PK`).
- Your design's fonts cover the script.

## Steps

1. Add the content language and a siteaccess with `Locale=<the locale>` (see "Add a new content language" and "Give a
   language its own address").
2. In the pagelayout of your design, print the direction:

   ```text
   <html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
   ```

3. Replace left and right in the stylesheets with logical properties (`margin-inline-start`, `text-align: start`
   ...); mirror icons that point along the line.
4. Clear the template and INI caches.

## Check

- `<html dir="rtl">` on the new site; columns and menus mirror; forms align to the start side.
- Numbers and Latin words in the text read correctly.
- The left-to-right sites look as before.

## Problems

| Problem | Fix |
|---|---|
| No `dir` attribute | the pagelayout does not print it, or the siteaccess `Locale` is a left-to-right locale |
| Parts of the layout stay left to right | physical `left`/`right` CSS remains |

## Related pages

- [Right-to-left sites](../../../creators/website-creators/right-to-left-sites.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
