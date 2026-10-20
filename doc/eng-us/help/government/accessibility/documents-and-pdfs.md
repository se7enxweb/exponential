---
title: Documents and PDFs
audience: government, editor, project-manager
category: public-sector
order: 200
summary: For editors and project managers in public bodies - when to publish a web page instead of a document, how to check PDF and office files before upload, how Exponential delivers files, and what its built-in PDF export does and does not promise.
keywords: documents, PDF, office files, Word, accessible PDF, tagged PDF, file upload, content/download, PDF export, EN 301 549 clause 10, editors, forms as documents, scanned documents
type: help
---
# Documents and PDFs

This page is for editors who upload documents and project managers who set the rules. Documents are content: an
inaccessible PDF linked from an accessible page is still a barrier. EN 301 549 has a clause of its own for non-web
documents (clause 10), and many public bodies publish large numbers of reports, forms and minutes as files.

## Contents

1. [Page first, document second](#page-first-document-second)
2. [Before you upload a document](#before-you-upload-a-document)
3. [How Exponential delivers files](#how-exponential-delivers-files)
4. [The built-in PDF export](#the-built-in-pdf-export)
5. [Scanned documents and old files](#scanned-documents-and-old-files)
6. [Related pages](#related-pages)

## Page first, document second

Publish information as a web page whenever you can: it reflows on phones, follows the visitor's zoom and color
settings, is found by search, and can be translated in Exponential's language versions. Offer a document in addition
only when people need one (to print, to sign, as the formal version).

## Before you upload a document

Check in the program that made the document, before exporting to PDF:

- [ ] Real headings (the program's heading styles), in order.
- [ ] Alternative text on images and charts.
- [ ] Tables with a marked header row.
- [ ] Document title and language set in the document properties.
- [ ] Meaningful link texts.
- [ ] Enough contrast; color not the only signal.
- [ ] Exported as a **tagged** PDF (an accessibility option of the export dialog in most office programs).
- [ ] Checked with an accessibility checker for PDF.

In the link on the page, say the format and size: "Council minutes, 12 May (PDF, 300 KB)".

## How Exponential delivers files

- A file is uploaded into a file field (or as a file object in the media library) and delivered through
  `content/download`, which checks that the reader may read it; uploads other than images are never served directly
  from the storage directory ([13.2 What the web server must never hand out](../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)).
- Exponential does not change the file. It does not check or repair its accessibility: what you upload is what
  visitors get.
- Uploading many files at once and the options of the upload are described in
  [Images, media and uploads](../../media-and-uploads.md).

## The built-in PDF export

**Setup > PDF export** makes one PDF of a node, or of a part of the tree, generated once or on the fly
([PDF exports](../../../guides/pdf-exports.md), [PDF export](../../../features/6.0/pdf-export.md)). The repository
documents no tagging or PDF/UA support for this export, so treat its output as an untagged PDF: useful for printing
and archiving, but not as the accessible version of your content. Keep the web pages as the primary, accessible form.

## Scanned documents and old files

- A scan without text recognition is an image: unusable with a screen reader. Run text recognition and check the
  result, or publish the text on a page.
- For large archives of old documents your law may allow exceptions for documents published before a given date; ask
  your legal counsel and record the decision in your accessibility statement. Offer the content in an accessible form
  on request.

## Related pages

- [Accessible content for editors](accessible-content-for-editors.md)
- [Files and downloads](../../creators/website-creators/files-and-downloads.md)
- [Images, media and uploads](../../media-and-uploads.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
