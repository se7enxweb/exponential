# Twig templates: migrating a design

This guide is for site builders who move a whole design from `.tpl` (or `.stpl`) to `.twig`. It is a plan in five
steps: a siteaccess of its own, the conversion, the check, page-by-page comparison, and keeping the engines in step
until the switch. Each step uses commands that exist on every installation; the converter itself is described in
[The converter and porting a design](twig-templates-converter.md).

## 1. A siteaccess for the Twig design

Keep the `.tpl` siteaccess running and add a second one that uses the converted design, so both render the same
content side by side: copy the siteaccess's settings to a new one (`settings/siteaccess/<site>_twig/`), put the
converted design first in its `[DesignSettings] SiteDesign` / `AdditionalSiteDesignList[]`, and add it to
`[SiteAccessSettings] AvailableSiteAccessList[]` with a path or host match.

## 2. Convert

```bash
./console exp:template:convert:tpl-to-twig -s <site>_twig \
    --from=extension/<ext>/design/<design> --to=extension/<ext>/design/<design>_twig \
    --override-ini=settings/siteaccess/<site>_twig/override.ini.append.php
```

Every `.tpl` of the design becomes a `.twig` at the same place below `--to`, and the override rules of the siteaccess
are written for the new files. Keep the NOTE lines of the output: each names a construct the converter could not
write exactly. The media design of the demo content (517 templates) gave 126 notes in 58 templates, every one about an
error in the `.tpl` source itself (an `{if}` without a condition, a parser error), which the `.twig` keeps: it prints
what the `.tpl` printed. Fix such a source and convert again.

## 3. Check

```bash
./console exp:template:check --design-dir=extension/<ext>/design/<design>_twig --level=strict
```

Every `.twig` must compile; strict also reports operators that do not exist, includes that do not resolve, and what
the sandbox would refuse.

## 4. Compare pages

Request the same pages from both siteaccesses and compare the HTML. On a development installation the converted media
design printed, for 9 pages (front page, list pages, articles, an author, the contact form), byte for byte what the
`.tpl` design printed, on Apache with PHP-FPM and on Velocity; the only difference allowed is the siteaccess prefix in
links. Compare with the debug output off, or cut the debug panel out of both pages before comparing. Signed-in pages,
forms with validation errors and the basket deserve a look of their own.

## 5. Keep the engines in step, then switch

Until the switch, change the `.tpl` and convert again: a second run writes only what changed and never replaces a
`.twig` that was changed by hand (it says SKIP). When the Twig siteaccess is the one visitors use, the `.twig` files
are the sources; convert `twig-to-tpl` if you need the `.tpl` design back.

## Speed

Cached pages are served before any template runs, so they are as fast with `.twig` as with `.tpl`. Uncached, a
converted page renders about as fast as the `.tpl` page or faster; see [Performance](twig-templates-performance.md).
If the design keeps the whole page in one cache block, keep the user menu (or any per-visitor part) outside it as
[Caching](twig-templates-caching.md) shows.

## Related

- [The converter and porting a design](twig-templates-converter.md)
- [Moving from .tpl to .twig](../help/template-language/moving-from-tpl-to-twig.md)
- [Smarty templates: migrating a design](smarty-templates-migrating.md)
