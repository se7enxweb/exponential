---
title: PDF export
audience: government, admin, website-creator
category: public-sector
order: 60
summary: How to produce one PDF of a part of the content tree with Exponential's PDF export in Setup > PDF export - generated once or on the fly, classes in a tree export, the public address of stored PDFs, fonts and characters, and the accessibility limits of generated PDFs.
keywords: PDF export, Setup PDF export, pdf/list, generated once, on the fly, tree export, public address, storage pdf, pdf.ini, fonts, UTF-8, ttf, accessibility, tagged PDF
type: help
---
# PDF export

This page is for administrators and website creators who want a printable PDF of a handbook, a set of guidance pages or
another part of the site. **Setup > PDF export** (`/pdf/list`) makes one PDF from a source node and, for a tree export,
the nodes below it. It needs the `pdf/edit` policy. Sources: [PDF exports](../../../../guides/pdf-exports.md) and
[PDF export](../../../../features/6.0/pdf-export.md).

## Contents

1. [Two kinds of export](#two-kinds-of-export)
2. [Creating an export](#creating-an-export)
3. [Public or protected](#public-or-protected)
4. [Characters and fonts](#characters-and-fonts)
5. [Accessibility limits](#accessibility-limits)

## Two kinds of export

| Kind | Behavior | Use |
|---|---|---|
| Generated once | the PDF is written when the export is saved and again with **Regenerate**; stored in `var/<site>/storage/pdf/<file name>` | a stable edition, fast to download |
| Generated on the fly | made anew for every download through `/pdf/edit/<id>/generate` | always current, slower for large trees, only for users with `pdf/edit` |

## Creating an export

1. **Setup > PDF export**, create a new export.
2. Choose the source node; for a tree export, tick the classes to include.
3. Choose generated once or on the fly, give a file name.
4. Save; **Download** on the card. The list shows figures, badges (Generated once, On the fly, File ready, Source
   missing) and **Regenerate**.

## Public or protected

A stored PDF is public: the web server serves `var/<site>/storage/pdf/<file name>` to anyone, signed in or not, and
the card shows that **Public address**. Do not use a guessable file name for content that is not for everybody, and
use an export generated on the fly for restricted content: it is downloaded only through `/pdf/edit/<id>/generate` by
users with `pdf/edit`. A server that keeps its rewrite rules elsewhere (a vhost, nginx, Velocity) needs the same rule
for the path.

## Characters and fonts

`settings/pdf.ini [PDFGeneral]`:

| Key | Default | Note |
|---|---|---|
| `Font` | `lib/ezpdf/classes/fonts/Helvetica` | a `.ttf` path embeds a Unicode font with selectable text; DejaVu Sans covers Latin, Greek, Cyrillic, Hebrew, Armenian, Georgian; set a font with Chinese, Japanese or Korean for those |
| `OutputCharset` | `windows-1252` | ignored with a `.ttf` font |
| `Format`, `Orientation`, margins | A4, portrait | page layout |

For a multilingual site, set a `.ttf` font that covers your languages.

## Accessibility limits

The documentation of the PDF export does not describe tagged (structured) PDF output, and nothing in it claims PDF/UA
or accessibility conformance. Treat an exported PDF as a printable convenience copy:

| Practice | Why |
|---|---|
| Keep the HTML pages as the main publication | they are the accessible version |
| Offer the PDF as "print version" | expectations are clear |
| For a formal accessible PDF, produce it from an authoring tool that writes tagged PDFs | and upload it as a File |

See [Accessible documents](accessible-documents.md) and [Accessibility standards](../../../../standards/accessibility.md).

## Related pages

- [Documents and publications](README.md)
- [Downloads and permissions](downloads-and-permissions.md)
- [Templates and design](../../../../guides/templates-and-design.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
