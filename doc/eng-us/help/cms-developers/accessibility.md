---
title: Accessibility
audience: cms-developer
category: cms-development
order: 44
summary: For CMS developers, building accessible sites on Exponential. It covers where the product stands against WCAG 2.2 AA, what the content model contributes (alternative text, headings, structured rich text), templates and designs, forms, testing with keyboard, zoom and contrast, and what editors need to know.
keywords: accessibility, a11y, WCAG 2.2, AA, WAI-ARIA, EN 301 549, Section 508, alternative text, headings, landmarks, skip links, keyboard, focus, contrast, zoom, forms, labels, screen reader, reduced motion, right-to-left, dark mode
type: help
---
# Accessibility

This page is for developers responsible for making a site usable by everyone, including people who use a screen
reader, a keyboard, high zoom or high contrast. On an Exponential site, accessibility depends mostly on your design and
templates, and partly on the content model, which can make good practice the default for editors.

## Contents

1. [Where the product stands](#where-the-product-stands)
2. [What the content model can do](#what-the-content-model-can-do)
3. [Templates and designs](#templates-and-designs)
4. [Forms](#forms)
5. [Testing](#testing)
6. [Editors](#editors)
7. [Checklist](#checklist)

## Where the product stands

The project's own assessment is in [Accessibility standards](../../standards/accessibility.md): against WCAG 2.2 level
AA and WAI-ARIA 1.2 the product **partly complies**: the admin4 administration was built and tested against AA
criteria, the classic administration partly fixed, and the public designs and the online editor are not audited.
EN 301 549 and Section 508 are unverified. For a public site, that means **your design is what an audit judges**:
plan for it in your own templates and test it yourself.

## What the content model can do

| Decision | Effect |
|---|---|
| Image attributes with **Alternative text is required** | editors cannot store an image without a text ([Images and image aliases](images-and-image-aliases.md#alternative-text)) |
| Image objects related or embedded instead of images in many fields | one alternative text, kept up to date in one place |
| Rich text instead of many small fields | real headings, lists and tables, not visual imitations ([Rich text](rich-text.md)) |
| A small set of custom tags with accessible templates | fact boxes, quotes and calls to action with correct markup everywhere |
| A `title` attribute used for the page's `<h1>` | one heading per page from content |
| A language per translation | the pagelayout can set the page's `lang` attribute from the content language ([Translations and languages](translations-and-languages.md)) |

## Templates and designs

The designers' help covers each topic with examples:

| Topic | Page |
|---|---|
| The basics for a theme | [Accessibility basics for themes](../designers/theme-design/accessibility-basics-for-themes.md) |
| Landmarks and headings | [Landmarks and headings](../designers/theme-design/landmarks-and-headings.md) |
| Skip links | [Skip links](../designers/theme-design/skip-links.md) |
| Keyboard and focus | [Keyboard and focus](../designers/theme-design/keyboard-and-focus.md) |
| Color contrast | [Color contrast](../designers/theme-design/color-contrast.md) |
| Reduced motion | [Reduced motion](../designers/theme-design/reduced-motion.md) |
| Right-to-left languages | [Right-to-left designs](../designers/theme-design/right-to-left-designs.md) |

Rules that hold on every Exponential design: CSS in stylesheets with tokens and a dark value for each color token, no
inline CSS, no inline JavaScript ([No inline CSS](../designers/theme-design/no-inline-css.md)). Layouts blocks are
templates too: check their markup like any other ([Styling layout blocks](../designers/theme-design/styling-layout-blocks.md)).

## Forms

The shipped collect and user templates print inputs without ids; give your copies ids so labels work, show
validation messages next to the field and in text, and use `autocomplete` on sign-in and registration forms
([Accessible forms](../designers/theme-design/accessible-forms.md),
[User and login templates](../designers/templates/user-and-login-templates.md),
[Forms and information collection](forms-and-information-collection.md)).

## Testing

- **Keyboard only**: every link, menu, form and dialog reachable and usable, focus always visible.
- **Zoom**: at 200% and at a narrow width nothing overlaps or scrolls sideways
  ([Testing at zoom levels](../designers/theme-design/testing-at-zoom-levels.md)).
- **Contrast**: text at least 4.5:1, in light and in dark mode.
- **A screen reader** on the main templates: front page, a list, a full view, a form, search.
- **Automated checks** help find missing labels and alternative texts; they do not replace the manual tests.
- Your own design checks: [Checklist: accessibility](../designers/theme-design/checklist-accessibility.md).

## Editors

Accessible templates can still get inaccessible content. Brief editors on alternative texts, meaningful link texts,
heading order in rich text and captions for video. The editors' course has the material
([Editors 101](../trainers/editors-101/README.md)); trainers who teach accessibility find it in
[Train the trainer, module 5](../trainers/train-the-trainer/module-05-accessibility-and-inclusion/README.md).

## Checklist

- [ ] Image attributes require alternative text; decorative images have an empty `alt` in the template.
- [ ] One `<h1>` per page, headings in order, landmarks present, a skip link first.
- [ ] Every form field has a label; errors are announced in text.
- [ ] Keyboard, zoom and contrast tested in light and dark mode on the main templates.
- [ ] The `lang` of each page matches its content language.
- [ ] Editors briefed.

## Related pages

- [Accessibility standards](../../standards/accessibility.md)
- [Project launch checklist](project-launch-checklist.md)
- [Designers: start here](../designers/designers-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
