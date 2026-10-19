---
title: "Removing inline scripts and styles"
audience: designer, developer
category: theme-lifecycle
order: 130
summary: Move a maintained theme toward a strict Content Security Policy - find inline script blocks, event handler attributes, javascript: links and style attributes, move them into files and data attributes - following how the published 7x themes did it in October 2026.
keywords: inline scripts, inline styles, CSP, unsafe-inline, onclick, javascript: links, data attributes, style attribute, migration
type: help
---
# Removing inline scripts and styles

This page is for designers maintaining a theme written before Content Security Policies were common. The how-to of
CSP-safe templates is in [CSP-safe design](../theme-design/csp-safe-design.md), [No inline JavaScript](../theme-design/no-inline-javascript.md)
and [No inline CSS](../theme-design/no-inline-css.md); this page is the migration routine.

## Why now

Exponential sends a Content Security Policy from `site.ini [ContentSecurityPolicy]`: report-only on the administration
by default and off on sites, with `script-src 'self'` and `style-src 'self'`
([Content Security Policy](../../../features/6.0/content-security-policy.md)). A site owner who switches it on for
their site needs a theme without inline code.

The 7x themes did this migration in their releases of 17 October 2026: `cjw_themes_jumper` v0.1.6 ("the starter
design without inline scripts"), `sevenx_themes_simple` v1.0.24 and `sevenx_themes_media` v1.5.70.

## Step by step

1. **Find** inline code:

   ```bash
   grep -rn -E "<script[^>]*>[^<]|on(click|change|load|submit)=|javascript:|style=\"" extension/mytheme/design
   ```

2. **Move scripts into files** in `design/mytheme/javascript/` and load them through the design's script list.
3. **Replace event attributes and `javascript:` links** with `href="#"` or buttons plus `data-*` attributes that a
   script file reads, as `cjw_themes_jumper` 0.1.6 did with `data-cjw-bubble`.
4. **Pass values** from templates in `data-*` attributes instead of writing them into script blocks.
5. **Replace `style=""`** with classes in the stylesheet; per-element values go into `data-*` attributes or custom
   properties set from a class.
6. **Test** with the policy in report-only mode on a test siteaccess (`Mode=report-only`) and read the browser console:
   no reports.

## Related pages

- [A CSP audit for every release](csp-audit-per-release.md)
- [Checklist: CSP](../theme-design/checklist-csp.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
