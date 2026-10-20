---
title: Accessibility for site builders
audience: website-creator, editor
category: website-creation
order: 600
summary: Build a public site everyone can use - what the template author controls (landmarks, headings, language, focus, contrast, forms), what editors control (alternative text, link text, structure), and the Exponential features that help.
keywords: accessibility, a11y, WCAG, screen reader, keyboard, landmarks, headings, alternative text, contrast, focus, forms, language, lang, RTL, website creator
type: help
---
# Accessibility for site builders

This page is for website creators who want a site that people with disabilities can use with screen readers, keyboards,
magnification or voice control. Accessibility is built in templates and stylesheets and kept up by editors. The
reference most legislation points to is the W3C's Web Content Accessibility Guidelines
([WCAG](https://www.w3.org/WAI/standards-guidelines/wcag/)).

## Contents

1. [What the template author controls](#what-the-template-author-controls)
2. [What editors control](#what-editors-control)
3. [Exponential features that help](#exponential-features-that-help)
4. [Forms](#forms)
5. [Checklist](#checklist)

## What the template author controls

| Area | Do |
|---|---|
| Language | `<html lang="...">` from `$site.http_equiv.Content-language`; `lang` on content in another language; `dir="rtl"` for right-to-left ([Right-to-left sites](right-to-left-sites.md)) |
| Landmarks | `header`, `nav` (with `aria-label` when there are several), `main`, `footer` |
| Skip link | a "Skip to content" link first in the page, to `#main` ([The pagelayout](the-pagelayout.md)) |
| Headings | one `h1` per page (the page name), headings in order |
| Navigation | the current page marked with `aria-current="page"` ([Menus and navigation](menus-and-navigation.md)) |
| Focus | a visible focus style on every link and control; never `outline: none` without a replacement |
| Contrast | text at least 4.5:1 against its background, large text 3:1 |
| Images | `alt` from the image's alternative text; decorative images `alt=""` |
| Motion | respect `prefers-reduced-motion` for sliders and animations |
| Zoom | layouts that work at 200% zoom and on narrow screens, no horizontal scrolling |

## What editors control

- **Alternative texts** for images: require them on image fields
  ([The content model and editing](../../../guides/content-model-and-editing.md)).
- **Link texts** that say where they lead.
- **Headings** in rich text used as headings, in order, not for size.
- **Tables** with header rows for data, not for layout.
- **Plain language**, short paragraphs.

Give editors a short list of these with their handover ([Handing the site over to editors](handing-over-to-editors.md)).

## Exponential features that help

- Image fields can require alternative text.
- The online editor writes structured rich text (headings, lists, tables) rather than free HTML.
- Right-to-left content is marked with `lang` and `dir` by `parts/exp_content_direction.tpl`.
- The administration itself follows an accessibility design ([admin4 design](../../../features/6.0/admin4-design.md)),
  so editors with disabilities can work too.
- Content checks find problems in content ([Content checks](../../../features/6.0/content-checks.md)).

## Forms

- Every input has a visible `<label>`.
- Required fields are marked in text.
- Errors are shown next to the field and summarized at the top, and the summary receives focus.
- CAPTCHAs have an accessible alternative (both shipped CAPTCHA services offer one).

## Checklist

- [ ] Landmarks, skip link and one `h1` in every page type.
- [ ] Keyboard only: every menu, form and dialog can be used and the focus is always visible.
- [ ] Contrast checked for text, links and buttons, in every color scheme the design has.
- [ ] Images have alternative texts; decorative images are empty.
- [ ] Forms labeled; errors announced.
- [ ] 200% zoom and a phone width work.
- [ ] Tested with an automatic checker and by hand ([Testing your site's accessibility](testing-accessibility.md)).

## Related pages

- [Testing your site's accessibility](testing-accessibility.md)
- [Accessibility, security and compliance checklists](../../leadership/accessibility-security-and-compliance-checklists.md)
- [Right-to-left sites](right-to-left-sites.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
