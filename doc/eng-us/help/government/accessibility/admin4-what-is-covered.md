---
title: What Exponential's admin4 covers for accessibility
audience: government, admin, project-manager, trainer
category: public-sector
order: 90
summary: For administrators and accessibility coordinators - the accessibility features of the admin4 administration that the project builds and tests (skip link, landmarks, keyboard patterns, contrast, error summaries, color modes, right to left), and how to make sure your editors actually use admin4.
keywords: admin4, admin4l, administration, editors, skip link, landmarks, keyboard, content tree, tabs, contrast, dark mode, forced colors, reduced motion, error summary, focus ring, design switch, accessibility
type: help
---
# What Exponential's admin4 covers for accessibility

This page is for administrators who set up the editing environment, accessibility coordinators who evaluate it, and
trainers who teach staff with disabilities to use it. It lists what the admin4 administration design (and its layout
variant admin4l) does for accessibility, as documented in [Accessibility standards](../../../standards/accessibility.md)
and [The admin4 design](../../../features/6.0/admin4-design.md), and how to make admin4 the design your editors get.
The project rates admin4 **Partly complies** with WCAG 2.2 AA.

## Contents

1. [Make admin4 the design editors use](#make-admin4-the-design-editors-use)
2. [Structure and names](#structure-and-names)
3. [Keyboard](#keyboard)
4. [Visual presentation](#visual-presentation)
5. [Forms and errors](#forms-and-errors)
6. [Languages and direction](#languages-and-direction)
7. [Related pages](#related-pages)

## Make admin4 the design editors use

The accessibility work concentrates on admin4 and admin4l. The classic administration (`design/admin`) and admin3
received fixes but are not covered by the contrast, landmark and form-error tests.

1. In the administration siteaccess's `site.ini.append.php` set `[DesignSettings] SiteDesign=admin4`
   ([Switch to it](../../../features/6.0/admin4-design.md#switch-to-it)).
2. Clear the caches: `php bin/php/ezcache.php --clear-all --allow-root-user`.
3. If you offer the per-session design switch, decide which designs it lists
   ([The administration design switch](../../../guides/ui-switch-design.md)). Users who pick the classic design leave
   the tested environment; say so in your editor guidelines.

## Structure and names

- **Skip link**: the first Tab stop is "Skip to main content".
- **Landmarks**: banner, named navigation landmarks (top menu, side menu, breadcrumb), complementary right column,
  footer and exactly one main landmark per page.
- **Current page**: the breadcrumb and the top menu mark it with `aria-current="page"`.
- **Tables**: every header cell in admin4's templates has `scope="col"`.
- **Form fields**: every `input`, `select` and `textarea` has an accessible name; a `title` or `placeholder` does not
  count as one.

Held by `AdminLandmarksTest.php` and `AdminFormLabelsTest.php` ([Landmarks and names](../../../features/6.0/admin4-design.md#landmarks-and-names)).

## Keyboard

- **Node tabs** follow the WAI-ARIA tabs pattern: arrow keys, Home and End.
- **Content tree** follows the tree view pattern: arrows open, close and move; letters jump; Shift+F10 or the context
  menu key opens the item's menu.
- **Context menus** and the header menus work from the keyboard; Escape closes and returns the focus.
- **Reordering sub items** has a keyboard mode on the drag grip.
- **Date fields** open their calendar from a named button and return the focus when it closes.
- The `/` key jumps to the search field.

All keys are listed for users in [Keyboard shortcuts](../../keyboard-shortcuts.md); the details are in
[Keyboard](../../../features/6.0/admin4-design.md#keyboard).

## Visual presentation

- **Contrast**: the color tokens (`--a4-*` in `design/admin4/stylesheets/components.css`) are checked with the WCAG 2
  formula in light and dark mode: 4.5:1 for text, 3:1 for the focus ring, field edges and chart colors.
- **Focus**: one shared, visible focus ring (3 px outline).
- **Target size**: at least 24 x 24 px.
- **Color modes**: light, dark and "system"; `forced-colors` and `prefers-reduced-motion` are honored.
- **Reflow**: admin4 is checked at phone, tablet and desktop widths, and pages at 960 px with 200% zoom.
- **Color is not the only signal**: notices carry an icon as well as a color.

## Forms and errors

- Required fields carry a visible mark and the word for screen readers.
- A failed store shows an error summary that takes the focus and links to the fields; the fields get `aria-invalid`
  and `aria-describedby` pointing at the message.
- Module messages carry `role="alert"` or a status role; toasts use one pair of live regions.

## Languages and direction

- `<html lang>` follows the siteaccess locale.
- Right-to-left interface languages set `dir="rtl"`, mirror the layout and isolate names with `<bdi>`
  ([Right-to-left languages](../../../features/6.0/right-to-left.md)).
- Content in a right-to-left content language is marked with `lang` and `dir` in the edit form and the previews.

## Related pages

- [Accessibility standards](../../../standards/accessibility.md)
- [The admin4 design](../../../features/6.0/admin4-design.md)
- [Keyboard shortcuts](../../keyboard-shortcuts.md)
- [admin4 styling](../../../bc/6.0/admin4-styling.md) (for developers who extend it)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
