---
title: The PDF export
audience: government, admin, editor
category: public-sector
order: 140
summary: For administrators and records staff - what Exponential's PDF export (Setup > PDF export) produces from a node or a tree, generated once or on the fly, the fact that stored PDFs are public by address, fonts for any language, and its limits as a records or accessible format.
keywords: PDF export, Setup PDF export, pdf/edit, generated once, on the fly, stored file, public address, var storage pdf, TrueType font, footer, front page, records copy, untagged PDF, public sector
type: help
---
# The PDF export

This page is for administrators and records staff. **Setup > PDF export** (`/pdf/list`, policy `pdf/edit`) makes one
PDF document from a **source node**, or from a tree of nodes of chosen classes
([PDF exports](../../../guides/pdf-exports.md), [PDF export](../../../features/6.0/pdf-export.md)).

![PDF exports: exports of parts of the content tree, stored or made for each download](../../images/government/gov-pdf.webp)

## Contents

1. [Two kinds of export](#two-kinds-of-export)
2. [Creating one](#creating-one)
3. [Public by address](#public-by-address)
4. [Use and limits for records](#use-and-limits-for-records)
5. [Related pages](#related-pages)

## Two kinds of export

| Kind | When the PDF is made | Shows | Access |
|---|---|---|---|
| Generated once | when the export is saved, and on **Regenerate** | the content as it was then | the stored file is public at `var/<site>/storage/pdf/<file name>` |
| Generated on the fly | at every download | always current | only through `/pdf/edit/<id>/generate`, with `pdf/edit` |

## Creating one

1. **New PDF export**: a title (for the list only).
2. **Content**: the source node with **Browse**; **Node** or **Tree**; for a tree tick the classes, including the folders
   that hold them (a node of an unticked class is left out with everything below it).
3. Optional **Frontpage** text and **Footer text**.
4. **Export type** and **File name** (letters, digits, `.`, `_`, `-`).
5. **OK**: errors are listed at the top with links to their fields.

For languages beyond Latin script, a TrueType font must be configured in `settings/override/pdf.ini.append.php`
([PDF export: Print any language](../../../features/6.0/pdf-export.md#print-any-language-use-a-truetype-font)).

## Public by address

A stored PDF is served by the web server to anybody who knows its address, signed in or not: the shipped
`.htaccess_root` has a rule for `var/<site>/storage/pdf/`. Do not give an export a file name you would not want
guessed, and use an export generated on the fly for content that is not for everybody.

## Use and limits for records

- Useful as a dated, human-readable snapshot of a part of the site, for example the notices of a year, generated once
  and filed in your records system with the date.
- The export prints the node's name as the first heading and then its attributes; it is not a pixel copy of the web
  page.
- The repository documents no tagging, PDF/UA or PDF/A support for this export: treat its output as an untagged PDF;
  if your archive requires PDF/A, convert and validate with a tool your archive accepts.
- Generated-once files are replaced on **Regenerate**: keep the copy you filed, not the live file.

## Related pages

- [PDF exports](../../../guides/pdf-exports.md)
- [File formats for preservation](file-formats-for-preservation.md)
- [Exporting content](exporting-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
