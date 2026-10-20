---
title: Tables in content
audience: government, editor, designer
category: public-sector
order: 190
summary: For editors and designers - when to use a table, how to give it a header row, a caption and header scope in Exponential's rich text, how the standard design renders them, and how to keep tables readable on phones and at 200% zoom.
keywords: tables, data tables, header cells, th, scope, caption, summary, layout tables, rich text, online editor, WCAG 1.3.1, reflow, responsive tables, editors
type: help
---
# Tables in content

This page is for editors who publish figures and schedules, and designers who style them. Tables are accessible when
their structure is in the markup: header cells that say what each column or row is, and a caption that says what the
table is about. WCAG 2.2 covers this under 1.3.1 Info and relationships (<https://www.w3.org/TR/WCAG22/>).

## Contents

1. [When to use a table](#when-to-use-a-table)
2. [What Exponential's rich text keeps](#what-exponentials-rich-text-keeps)
3. [Steps for editors](#steps-for-editors)
4. [Rules for designers](#rules-for-designers)
5. [Related pages](#related-pages)

## When to use a table

- **Yes** for data with rows and columns: fees, timetables, results, budgets.
- **No** for layout (putting text side by side) or for a list that has only one column of meaning.
- Keep tables simple: one header row, and if needed one header column. Split a complex table into several.

## What Exponential's rich text keeps

The rich text is stored as XML and rendered by templates. In the standard design:

| Element | Template | Rendered as |
|---|---|---|
| table, with optional caption, summary, title | `design/standard/templates/content/datatype/view/ezxmltags/table.tpl` | `<table>`, `<caption>` when set |
| header cell, with optional scope and abbreviation | `.../ezxmltags/th.tpl` | `<th scope="..." abbr="...">` when set |
| data cell | `.../ezxmltags/td.tpl` | `<td>` |

So the accessibility information an editor enters is carried through to the page. Whether and how the online editor
lets editors set header cells, caption and scope depends on the engine; check it in your editor (see "The online
editor and accessibility").

## Steps for editors

1. Insert the table with the editor's table function.
2. Make the first row (and, where it helps, the first column) **header cells**, not bold normal cells.
3. Add a **caption** that says what the table shows: "Fees for building permits, 2026".
4. Do not merge cells unless you must; merged cells are hard to follow with a screen reader.
5. Do not leave cells empty to create space; write "none" or "-" with meaning.
6. Preview the page at a phone width.

## Rules for designers

- Style header cells and captions visibly; do not hide the caption.
- Make wide tables scroll inside their own container on narrow screens, so the page itself does not scroll
  sideways (WCAG 1.4.10 Reflow allows two-dimensional scrolling for data tables).
- Keep the contrast of zebra stripes and borders sufficient in every color scheme.
- In admin4 every header cell carries `scope="col"`; follow the same practice in your own list templates.

## Related pages

- [Accessible content for editors](accessible-content-for-editors.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- [Editing content](../../editing-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
