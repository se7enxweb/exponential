---
title: PDF and document formats for disclosures
audience: government, editor, admin
category: public-sector
order: 190
summary: Which formats to use for published responses, reports and registers, how Exponential's PDF export turns a part of the content tree into one PDF, and the accessibility and machine-readability points to check.
keywords: PDF, PDF export, document formats, accessible PDF, CSV, ODF, page text, disclosures, reports, Setup PDF export, generated once, on the fly
type: help
---
# PDF and document formats for disclosures

This page is for editors and administrators who choose the format of published documents. Exponential publishes any
file you upload, and can make one PDF from a part of the content tree with **Setup > PDF export**
([PDF exports](../../../guides/pdf-exports.md)).

## Contents

1. [Choosing a format](#choosing-a-format)
2. [Page text first](#page-text-first)
3. [The PDF export of Exponential](#the-pdf-export-of-exponential)
4. [Accessibility of documents](#accessibility-of-documents)
5. [Checklist](#checklist)

## Choosing a format

| Content | Best format | Also offer |
|---|---|---|
| A short answer, a notice | page text | |
| A report people read | page text or an accessible PDF | |
| A signed or official document | PDF | page text summary |
| Tables of figures | CSV | PDF or page for reading |
| Registers | content objects, exported as CSV/JSON | |
| Editable templates | an open document format | |

## Page text first

Text written in the editor is searchable, translatable, accessible with the site's templates, versioned field by
field and has no hidden layers. Prefer it for anything short; attach files only where the document itself matters.

## The PDF export of Exponential

**Setup > PDF export** (`/pdf/list`) makes one PDF of a source node: its name as the first heading, then its
attributes; a **tree** export adds the nodes below it of the classes you tick.

| Mode | Behavior | Use |
|---|---|---|
| Generated once | written when the export is saved and on **Regenerate**; stored in `var/<site>/storage/pdf/` and served at a public address | an annual compilation, frozen at a date |
| Generated on the fly | made anew for every download | a printable version of a section that changes |

The stored PDF shows the content as it was when generated; regenerate it after changes, and note the date in it.
Check the result for accessibility before relying on it: this documentation does not state that the generated PDFs
are tagged for assistive technology.

## Accessibility of documents

Many public bodies must make documents accessible; in the EU the [Web Accessibility Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj)
covers public sector websites and the documents on them, with exceptions defined in the directive. Practical
checks: real text (not scans), headings, alternative text for images, a document language, tables with header rows.
Exponential's own accessibility status is described in [Accessibility standards](../../../standards/accessibility.md);
it does not cover the files you upload.

## Checklist

- [ ] Short content as page text.
- [ ] Every PDF has real text, headings and a language set.
- [ ] Tables also as CSV.
- [ ] Generated PDFs regenerated after content changes, dated.
- [ ] Download links show format and size.

## Related pages

- [PDF exports](../../../guides/pdf-exports.md)
- [Files and downloads](../../creators/website-creators/files-and-downloads.md)
- [Accessibility standards](../../../standards/accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
