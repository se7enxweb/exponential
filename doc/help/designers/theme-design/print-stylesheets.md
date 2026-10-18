---
title: Print stylesheets
audience: designer
category: theme-design
order: 330
summary: Making pages print well: print rules in your stylesheet or a separate print file, what to hide, link URLs, page breaks, colors, and the print view mode the kernel offers.
keywords: print, print stylesheet, media print, @media print, print css, page break, print view, printable page, hide navigation when printing
type: help
---
# Print stylesheets

This page is for designers who want articles, recipes and forms to print cleanly. It answers: where print rules go and
what they usually contain.

## Contents

1. [Where print rules go](#where-print-rules-go)
2. [A starting set of rules](#a-starting-set-of-rules)
3. [The print view mode](#the-print-view-mode)
4. [Testing](#testing)

## Where print rules go

Either inside your main stylesheet:

```css
@media print {
  /* rules below */
}
```

which costs no extra request, or in a separate file loaded for print only:

```tpl
{ezcss( array( 'print.css' ), 'print' )}
```

(the second parameter of `ezcss` is the `media` attribute). The ezwebin design links its `stylesheets/print.css` with
`media="print"` in `page_head_style.tpl`.

## A starting set of rules

```css
@media print {
  .site-header nav, .site-footer, .skip-link, .cookie-banner, .share-buttons { display: none; }
  body { color: #000; background: #fff; font-size: 11pt; }
  a[href^="http"]::after { content: " (" attr(href) ")"; font-size: 0.9em; }
  img, figure, table, pre { break-inside: avoid; }
  h2, h3 { break-after: avoid; }
}
```

- Hide navigation, banners and interactive controls.
- Black on white, whatever the screen theme: print does not need your dark mode.
- Print link targets after external links.
- Avoid breaks inside images, tables and code.

## The print view mode

The kernel has a `print` node view template (`design/standard/templates/node/view/print.tpl`), reached as
`/content/view/print/<node id>`. A design can override it for a reduced page. For most sites, `@media print` on the
normal page is simpler and needs no extra URL.

## Testing

Use the browser's print preview, and the developer tools' "emulate print media" to inspect print rules.

## Related pages

- [Loading stylesheets](loading-stylesheets.md)
- [View modes](../templates/view-modes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
