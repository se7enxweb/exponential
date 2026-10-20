---
title: Accessibility basics for themes
audience: designer
category: theme-design
order: 430
summary: The accessibility foundations every Exponential design needs: language and direction on html, landmarks and headings, a skip link, visible focus, contrast, alt text, forms, motion, zoom, and how the project tests (axe with zero serious issues, keyboard, light and dark).
keywords: accessibility, a11y, WCAG, screen reader, keyboard, focus, contrast, alt text, landmarks, skip link, axe, accessible theme, inclusive design
featured: true
type: help
---
# Accessibility basics for themes

This page is for designers who want every visitor to be able to use their design, including people who use a keyboard,
a screen reader, high zoom or high contrast. It lists what a design is responsible for, and links a page for each topic.

## Contents

1. [What the design owns](#what-the-design-owns)
2. [The ten basics](#the-ten-basics)
3. [What editors own](#what-editors-own)
4. [How the project tests](#how-the-project-tests)

## What the design owns

Content, templates and CSS share the work. The design decides the page structure, the navigation, the focus styles, the
colors, the forms' markup and the behavior of scripts. A design that gets these right makes every page accessible by
default; one that gets them wrong cannot be fixed by editors.

## The ten basics

1. **Language and direction.** `<html lang="{$site.http_equiv.Content-language|wash}">` and `dir="rtl"` for
   right-to-left languages ([The pagelayout](the-pagelayout.md)).
2. **Landmarks and one `<h1>`.** `<header>`, `<nav>`, `<main id="main">`, `<footer>`; one `<h1>` per page, headings in
   order ([Landmarks and headings](landmarks-and-headings.md)).
3. **A skip link** as the first focusable element ([Skip links](skip-links.md)).
4. **Visible focus** on every interactive element, never `outline: none` without a replacement
   ([Keyboard and focus](keyboard-and-focus.md)).
5. **Contrast** of at least 4.5:1 for text, 3:1 for large text, icons and focus rings, in light and dark mode
   ([Color contrast](color-contrast.md)).
6. **Alt text** printed from the image field (`|wash`), `alt=""` for decoration ([Images in a design](images-in-a-design.md)).
7. **Forms with labels and error messages** tied to their fields ([Accessible forms](accessible-forms.md)).
8. **Motion only when wanted**: respect `prefers-reduced-motion` ([Reduced motion](reduced-motion.md)).
9. **Zoom and reflow**: works at 200% and on a 320 px viewport ([Testing at zoom levels](testing-at-zoom-levels.md)).
10. **Scripts that keep state in ARIA**: a menu button says `aria-expanded`, a dialog traps and returns focus.

## What editors own

Alternative texts of images, meaningful link texts, heading order inside rich text, captions of videos. The design
helps by printing what editors enter (alt text, captions) and by giving rich text headings sensible styles.

## How the project tests

The administration's pages are checked with these targets, which suit public designs too:

- axe (an automated accessibility checker, run in the browser or by a script) finds **no serious or critical issues**;
- keyboard only: every control reachable with Tab, usable with Enter and Space, Escape closes overlays;
- light and dark mode, at 1440 px, at 960 px scale 2 and at 375 px;
- forced colors (Windows high contrast): admin4's stylesheets have dozens of `@media (forced-colors: active)` rules
  so borders and focus stay visible.

Run the [accessibility checklist](checklist-accessibility.md) before every release of a design.

## Related pages

- [Checklist: accessibility](checklist-accessibility.md)
- [Color contrast](color-contrast.md)
- [Keyboard and focus](keyboard-and-focus.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
