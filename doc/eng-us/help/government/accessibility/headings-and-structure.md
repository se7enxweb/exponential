---
title: Headings and structure
audience: government, editor, designer
category: public-sector
order: 170
summary: For editors and designers - how headings work in Exponential's rich text (heading level 1 becomes h2 under the page title in the standard design), why order matters to screen reader users, and how to keep lists, paragraphs and page titles structured.
keywords: headings, heading levels, h1, h2, structure, rich text, online editor, lists, page title, screen reader, outline, WCAG 1.3.1, 2.4.6, 2.4.2, editors
type: help
---
# Headings and structure

This page is for editors who write pages and designers who build page templates. Screen reader users move through a
page by its headings; a sensible heading outline is the single most useful thing an editor can give them. WCAG 2.2
covers this under 1.3.1 Info and relationships and 2.4.6 Headings and labels (<https://www.w3.org/TR/WCAG22/>).

## Contents

1. [How headings come out of Exponential](#how-headings-come-out-of-exponential)
2. [Rules for editors](#rules-for-editors)
3. [Rules for designers](#rules-for-designers)
4. [Page titles](#page-titles)
5. [Related pages](#related-pages)

## How headings come out of Exponential

- The **object's name or title field** is usually printed by the full view template as the page's `h1`.
- Rich text is stored as XML. A heading in rich text has a level; in the standard design the template
  `design/standard/templates/content/datatype/view/ezxmltags/header.tpl` turns **level 1 into `h2`**, level 2 into
  `h3`, and so on, because the page already has its `h1`.
- Each rich text heading also gets an anchor (`eztoc...`), which a table of contents can link to.

Check what your own design does: a design may override `header.tpl`.

## Rules for editors

1. Use the editor's heading formats for headings, never bold or a larger font.
2. Start with the first heading level and do not skip levels going down (a level 3 heading belongs under a level 2).
3. Make headings descriptive: "Opening hours" rather than "Information".
4. Use the editor's list buttons for lists, not dashes typed by hand.
5. Keep one topic per section; long pages with clear headings are easier to use than many short pages.

## Rules for designers

- One `h1` per page (the page name); landmarks (`header`, `nav`, `main`, `footer`); headings in block templates that
  fit under the page's outline ([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)).
- Do not use heading elements for visual effects in blocks (a teaser title in a list can be an `h2` or `h3` according
  to the outline, not by its look).
- Landing pages built with the layouts editor: check that the block templates produce a logical outline
  ([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md)).

## Page titles

WCAG 2.4.2 asks for descriptive page titles. The `<title>` is built by your pagelayout from the content's name and
the site name ([The pagelayout](../../creators/website-creators/the-pagelayout.md)). Editors control the first part:
give every object a clear, unique name. The administration's own titles are built by `page_head.tpl`.

## Related pages

- [Accessible content for editors](accessible-content-for-editors.md)
- [Editing content](../../editing-content.md)
- [The pagelayout](../../creators/website-creators/the-pagelayout.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
