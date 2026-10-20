---
title: Landmarks and headings
audience: designer
category: theme-design
order: 460
summary: The page structure screen reader users navigate by: landmark elements and roles in the pagelayout, one main region, labeled navigation, one h1 per page and heading levels in view templates and layout blocks.
keywords: landmarks, headings, h1, heading order, main, nav, header, footer, aside, role, aria-label, page structure, screen reader navigation, document outline
type: help
---
# Landmarks and headings

This page is for designers who write the pagelayout and view templates. Screen reader users jump between landmarks and
headings the way sighted users scan a page. The page answers: which landmarks a page needs and how to keep headings in
order across pagelayout, views and layout blocks.

## Contents

1. [Landmarks in the pagelayout](#landmarks-in-the-pagelayout)
2. [Labeling repeated landmarks](#labeling-repeated-landmarks)
3. [Headings](#headings)
4. [Headings in layouts](#headings-in-layouts)
5. [Checks](#checks)

## Landmarks in the pagelayout

```tpl
<body>
<a class="skip-link" href="#main">{'Skip to main content'|i18n( 'design/mytheme' )}</a>
<header class="site-header">
    <a class="site-name" href={'/'|ezurl}>{$site.title|wash}</a>
    <nav aria-label="{'Main navigation'|i18n( 'design/mytheme' )}">...</nav>
</header>
<main id="main" tabindex="-1">
    {$module_result.content}
</main>
<footer class="site-footer">...</footer>
</body>
```

- Exactly one `<main>`. `tabindex="-1"` lets the skip link move focus into it.
- `<header>` and `<footer>` directly in `<body>` are the page's banner and content info.
- Side content goes in `<aside>`.

admin4 uses the same structure with roles on `div` elements: `role="banner"`, `role="main"` (with `tabindex="-1"`),
`role="navigation"` with `aria-label` for the breadcrumb, `role="complementary"` for the tools column,
`role="contentinfo"` (`design/admin4/templates/pagelayout.tpl`).

## Labeling repeated landmarks

When a page has more than one `<nav>` (main menu, breadcrumb, footer links, paging), give each a translated
`aria-label`, so the list of landmarks reads "Main navigation, Breadcrumb, Footer".

## Headings

- **One `<h1>` per page**: the title of the content. In a content view, the full view template prints it; the pagelayout
  does not (the site name in the header is not an `<h1>` on content pages).
- **No skipped levels**: `h1`, then `h2`, then `h3`. Choose the element by structure and style it with classes, not the
  other way round.
- **Line views and teasers** in a list under an `h2` use `h3` for their titles.
- **Rich text**: editors' headings start at the level the datatype template gives them; check how your
  `ezxmltags/header.tpl` maps levels if you override it.

## Headings in layouts

Pages built with the layouts editor are composed of blocks; each block template decides its own heading element. The
layouts extension's `block/title.tpl` takes the element from the block (`tag`, or `h` plus `level`), so the editor
chooses it. In your block templates:

- give list and grid blocks a configurable heading level, or use `h2` for block titles and `h3` for items;
- make sure the page still has exactly one `h1`, usually from a title or full-view block.

## Checks

- [ ] A landmarks list (browser extension or screen reader rotor) shows banner, navigation(s), main, content info.
- [ ] Each navigation has a distinct name.
- [ ] Headings list starts with one `h1` and has no jumps.
- [ ] Error pages and search pages have an `h1` too.

## Related pages

- [Skip links](skip-links.md)
- [The pagelayout](the-pagelayout.md)
- [Block view templates](block-view-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
