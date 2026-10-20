---
title: The perceivable principle
audience: government, editor, designer, developer
category: public-sector
order: 300
summary: WCAG principle 1, perceivable, explained for a public body running Exponential - what editors, designers and developers each do for text alternatives, media, adaptable structure and distinguishable presentation, and what the admin4 administration already provides.
keywords: perceivable, WCAG principle 1, text alternatives, alt text, captions, adaptable, info and relationships, distinguishable, contrast, reflow, resize text, 1.1, 1.2, 1.3, 1.4, editors, designers, developers
type: help
---
# The perceivable principle

This page is for everyone on a public website team. WCAG 2.2 groups its success criteria under four principles; the
first says that information and interface components must be presentable to users in ways they can perceive
(<https://www.w3.org/TR/WCAG22/>). Below, the four guidelines of this principle, with what each role does in an
Exponential project and what the administration already does.

## Contents

1. [1.1 Text alternatives](#11-text-alternatives)
2. [1.2 Time-based media](#12-time-based-media)
3. [1.3 Adaptable](#13-adaptable)
4. [1.4 Distinguishable](#14-distinguishable)
5. [Related pages](#related-pages)

## 1.1 Text alternatives

| Role | Does |
|---|---|
| Editors | fill **Alternative image text** for every informative image; describe charts in text |
| Administrators | switch on "Alternative text is required" on image attributes of the classes editors use |
| Designers | decide how decorative images are marked; icon-only links and buttons get a name |
| Developers | override the image template if decorative images must render `alt=""` (the standard template falls back to the object name) |

Admin4: every form control has an accessible name (`AdminFormLabelsTest.php`). See "Alternative text for images".

## 1.2 Time-based media

| Role | Does |
|---|---|
| Editors | captions for video with speech, transcripts for audio and video, no autoplay |
| Developers | add caption tracks in the media template if you need them (the standard template writes none) |

See "Captions and media".

## 1.3 Adaptable

| Role | Does |
|---|---|
| Editors | real headings in order, lists, tables with header cells and captions |
| Designers | reading order equals visual order; no meaning by position alone |
| Developers | landmarks, one `h1`, `scope` on header cells, labeled form fields, `autocomplete` on personal fields |

Admin4: landmarks, skip link, `scope="col"`, required fields marked for screen readers, direction for right-to-left
languages (`AdminLandmarksTest.php`, `AdminRightToLeftTest.php`).

## 1.4 Distinguishable

| Role | Does |
|---|---|
| Editors | never color alone ("the fields in red"); no images of text |
| Designers | text 4.5:1, controls and focus 3:1, links distinguishable, reflow at 320 px, 200% zoom, text spacing |
| Developers | tokens, no fixed heights that clip text, no horizontal page scrolling, respect user settings |

Admin4: contrast tokens checked in light and dark mode, notices with icons as well as colors, responsive widths, forced
colors and reduced motion (`AdminTokenContrastTest.php`, `AdminNoticesTest.php`, `AdminResponsiveTest.php`).

## Related pages

- [Mapping WCAG 2.2 AA to Exponential](wcag-2-2-mapping.md)
- [Color and contrast in your theme](color-and-contrast.md)
- [Alternative text for images](alternative-text-for-images.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
