---
title: "Checklist: accessibility"
audience: designer
category: theme-design
order: 870
summary: An accessibility checklist for a design: page structure, keyboard, focus, contrast, images, forms, motion, zoom and reflow, dark mode and forced colors, and the automated check with zero serious issues.
keywords: accessibility checklist, a11y checklist, WCAG checklist, keyboard test, contrast check, axe, screen reader test, design accessibility review
type: help
---
# Checklist: accessibility

This page is for designers who review a design for accessibility. Run it on every page type: front page, article,
list, search results, a form, an error page, a layouts page.

## Contents

1. [Structure](#structure)
2. [Keyboard and focus](#keyboard-and-focus)
3. [Visual](#visual)
4. [Content the design prints](#content-the-design-prints)
5. [Forms](#forms)
6. [Automated check](#automated-check)

## Structure

- [ ] `lang` on `<html>`, `dir="rtl"` for right-to-left languages.
- [ ] Landmarks: header, navigation (each labeled), main, footer ([Landmarks and headings](landmarks-and-headings.md)).
- [ ] One `h1`; headings without skipped levels.
- [ ] A skip link as the first focusable element, working ([Skip links](skip-links.md)).
- [ ] The page title names the page, then the site.

## Keyboard and focus

- [ ] Every link, button and field reachable with Tab, in a sensible order ([Keyboard and focus](keyboard-and-focus.md)).
- [ ] Focus always visible, with 3:1 contrast.
- [ ] Menus, dialogs, sliders work with the keyboard; Escape closes; focus returns.
- [ ] No keyboard trap.

## Visual

- [ ] Text contrast at least 4.5:1 (3:1 for large text) in light and dark ([Color contrast](color-contrast.md)).
- [ ] Information not by color alone (errors, required fields, links in text).
- [ ] 200% zoom and 320 px wide: nothing lost, no horizontal page scroll ([Testing at zoom levels](testing-at-zoom-levels.md)).
- [ ] Motion respects `prefers-reduced-motion`; no autoplay without a pause ([Reduced motion](reduced-motion.md)).
- [ ] Forced colors (Windows high contrast): borders, focus and icons still visible.

## Content the design prints

- [ ] Images: alt text from the image field, `alt=""` for decoration.
- [ ] Icon-only buttons have a name.
- [ ] Link texts make sense out of context ("Read more about X", not "here").
- [ ] Embedded videos have captions available (an editor's task; the player must show them).

## Forms

- [ ] Every field has a visible label tied to it ([Accessible forms](accessible-forms.md)).
- [ ] Required fields marked in text, not only by color.
- [ ] Errors listed in a summary and next to the field; focus moves to the summary.
- [ ] `autocomplete` on personal data fields.

## Automated check

- [ ] axe (browser extension or a test script) finds **no serious or critical issues** on each page type, light and dark.
- [ ] No errors in the browser console.

Automated checks find only part of the problems; the keyboard and zoom checks above are not optional.

## Related pages

- [Accessibility basics for themes](accessibility-basics-for-themes.md)
- [Checklist: theme launch](checklist-theme-launch.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
