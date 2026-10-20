---
title: Skip links
audience: designer
category: theme-design
order: 470
summary: The skip link that lets keyboard users jump past the header to the content: markup in the pagelayout, CSS that shows it on focus, translated text, and the examples in admin4 and the media design.
keywords: skip link, skip to content, skip navigation, keyboard, bypass blocks, accessibility, a4-skip-link, focus, main content
type: help
---
# Skip links

This page is for designers who build the pagelayout. A skip link is the first link of the page; it is hidden until it
receives keyboard focus, and jumps to the main content, so keyboard users do not tab through the whole header on every
page.

## Contents

1. [Markup](#markup)
2. [CSS](#css)
3. [Examples in the shipped designs](#examples-in-the-shipped-designs)
4. [Checks](#checks)

## Markup

The first element inside `<body>`:

```tpl
<a class="skip-link" href="#main">{'Skip to main content'|i18n( 'design/mytheme' )}</a>
...
<main id="main" tabindex="-1">
```

The target has `tabindex="-1"` so browsers move focus into it, not only scroll.

## CSS

Hidden off screen until focused, then shown on top:

```css
.skip-link {
  position: fixed;
  inset-block-start: 0.5rem;
  inset-inline-start: 0.5rem;
  z-index: 1000;
  padding: 0.5rem 1rem;
  background: var(--site-surface);
  color: var(--site-ink);
  transform: translateY(-200%);
}
.skip-link:focus {
  transform: none;
  outline: 3px solid var(--site-focus);
  outline-offset: 2px;
}
```

Do not hide it with `display: none` or `visibility: hidden`: it could then never be focused. Logical properties
(`inset-inline-start`) put it at the right edge in right-to-left pages.

## Examples in the shipped designs

- **admin4**: `<a class="a4-skip-link" href="#maincontent">` (or `#a4-main`) in `design/admin4/templates/pagelayout.tpl`,
  styled by `.a4-skip-link` in `components.css` (fixed position, its own z-index token `--a4-z-skip-link`, a focus ring).
- **media**: `pagelayout/accessibility_links.tpl` prints a "Skip to main content" link to `#main`, included right after
  `<body>`.

![The skip link of the administration, shown when Tab is pressed first on a page, outlined](../../images/designers/skip-link.webp)

## Checks

- [ ] Press Tab once on a fresh page: the skip link appears.
- [ ] Press Enter: focus is in the main content (the next Tab goes to the first link of the content).
- [ ] The text is translated in every language of the site.

## Related pages

- [Keyboard and focus](keyboard-and-focus.md)
- [Landmarks and headings](landmarks-and-headings.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
