---
title: Reduced motion
audience: designer
category: theme-design
order: 490
summary: Respecting visitors who ask for less motion: the prefers-reduced-motion media query, which animations to turn off, sliders and autoplay, smooth scrolling, and how admin4 does it.
keywords: reduced motion, prefers-reduced-motion, animation, transitions, autoplay, carousel, slider, smooth scroll, vestibular, motion sensitivity
type: help
---
# Reduced motion

This page is for designers who add transitions, animations, sliders or videos. Some visitors get dizzy or sick from
motion and turn on "reduce motion" in their operating system. Browsers report it with the media query
`prefers-reduced-motion`.

## Contents

1. [The pattern](#the-pattern)
2. [What to turn off](#what-to-turn-off)
3. [Sliders, carousels and videos](#sliders-carousels-and-videos)
4. [In scripts](#in-scripts)
5. [How admin4 does it](#how-admin4-does-it)

## The pattern

Write motion as an enhancement, only for visitors who did not ask for less:

```css
@media (prefers-reduced-motion: no-preference) {
  .card { transition: transform 150ms ease-out; }
  .card:hover { transform: translateY(-2px); }
  html { scroll-behavior: smooth; }
}
```

or switch it off for those who did:

```css
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
}
```

The first form is safer: motion is opt-in by default.

## What to turn off

- Parallax, zooming backgrounds, large movements across the screen.
- Smooth scrolling for in-page links.
- Animated page transitions.

Small color or opacity fades are usually fine; motion across space is what causes trouble.

## Sliders, carousels and videos

- No autoplay, or a visible pause button that works with the keyboard.
- With reduced motion, show the first slide and let visitors move by hand.
- The layouts editor's slider, carousel and gallery blocks render with the design's templates and scripts: check them
  with reduced motion on.

## In scripts

```js
const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (!reduce) { startAutoplay(); }
```

## How admin4 does it

admin4's stylesheets have `@media (prefers-reduced-motion: reduce)` blocks that turn off transitions (for example the
"More" menu icon's rotation in `admin4.css`), and hover effects run only under
`(hover: hover) and (pointer: fine) and (prefers-reduced-motion: no-preference)`.

## Related pages

- [Accessibility basics for themes](accessibility-basics-for-themes.md)
- [Keyboard and focus](keyboard-and-focus.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
