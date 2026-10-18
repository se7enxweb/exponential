---
title: "Security fixes in themes"
audience: designer, developer
category: theme-lifecycle
order: 180
summary: The security problems a theme can have - unescaped output, output in the wrong context, inline script injection, links opening other sites, exposed settings - how to find them, and a real fix from a published theme.
keywords: security, XSS, escaping, wash, wash javascript, context, inline script, template security, fix
type: help
---
# Security fixes in themes

This page is for designers maintaining a theme. Templates print content that editors and visitors wrote; a template
that prints it without escaping lets someone run script in other people's browsers (cross-site scripting).

## Typical problems

| Problem | Example | Fix |
|---|---|---|
| Output not escaped | `{$node.name}` in HTML | `{$node.name\|wash}` (Wash and escaping) |
| Escaped for the wrong context | Escaped for JavaScript only, but the value ends up in HTML | Escape for each context it passes through |
| Values in inline scripts | A title written into a script block | Pass it in a `data-*` attribute; no inline script |
| Visitor input in URLs | A search term in a link | Escape for the URL, then for HTML |
| Settings or paths shown | Debug output on a live site | Debug only for your IP; never print paths |

## A real fix

`cjw_themes_jumper` 0.1.4: "The single-location map escapes the address and the title for HTML before it escapes them
for JavaScript (`|wash|wash( 'javascript' )`). Both go into `L.popup().setContent()`, which reads HTML, so markup in an
address or a title ran in the popup." The value passed through two contexts, so it needed escaping for both.

## Step by step: look for problems

1. Search for output without `wash`:

   ```bash
   grep -rn -E "\{\\\$[a-z_.]+\}" extension/mytheme/design | grep -v wash | head -50
   ```

   Read each hit; numbers and values already escaped are fine.
2. Search for values written into scripts: `grep -rn "<script" extension/mytheme/design`.
3. Fix, test with a title containing `<b>"'` characters, and release
   ([Publishing a security release](publishing-a-security-release.md)).

## Related pages

- [Handling security reports](handling-security-reports.md)
- Checklist: code review for templates
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
