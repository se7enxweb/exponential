---
title: File formats for preservation
audience: government, editor, project-manager, admin
category: public-sector
order: 220
summary: For public bodies keeping web content and documents for the long term - general guidance on open, documented file formats, what formats Exponential stores and exports (rich text as XML, images, uploaded files as uploaded, JSON, PDF exports), and what to agree with your archive.
keywords: file formats, digital preservation, long-term preservation, open formats, PDF/A, XML, JSON, CSV, images, PNG, JPEG, TIFF, uploads, rich text XML, conversions, archive, public sector
type: help
---
# File formats for preservation

This page is for records managers, editors and project managers. Whether a record can still be read in twenty years
depends a lot on its format. General preservation practice prefers open, documented, widely used formats without
encryption or dependencies on one program. Your archive's list of accepted formats is the authority; this page relates
the general practice to what Exponential stores and exports.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [What Exponential stores](#what-exponential-stores)
2. [What it exports](#what-it-exports)
3. [General guidance](#general-guidance)
4. [Agreeing with your archive](#agreeing-with-your-archive)
5. [Related pages](#related-pages)

## What Exponential stores

| Content | Stored as |
|---|---|
| Rich text | XML, rendered by templates (not free HTML) |
| Simple fields | values in the database |
| Images | the uploaded file, accepted by its content from an allow-list (JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP, SVG as shipped); SVG sanitized on upload; variations generated from the original |
| Other uploads | the file as uploaded; Exponential does not convert or check it |
| Audit records | JSON lines, archived compressed (gzip, bzip2, xz, zstd or zip) with signed manifests |

## What it exports

| Export | Format |
|---|---|
| REST API | JSON |
| Packages | `.ezpkg` (gzip-compressed tar with XML or JSON definitions) |
| PDF export | PDF; no documented tagging or PDF/A support |
| Form submissions, account list, consent log | CSV, UTF-8 with a byte order mark |
| Audit export | CSV, JSON lines, JSON, signed bundle |

## General guidance

- Text records: plain text, XML or JSON with documentation of the structure; PDF/A (an ISO family of archival PDF
  profiles) where your archive asks for PDF.
- Images: lossless formats (PNG, TIFF) for masters; JPEG is common for photographs.
- Tables: CSV with a description of the columns.
- Avoid formats that need a specific program, macros, encryption or external links.
- Keep checksums with every file.

Exponential's PDF export is not a PDF/A producer as far as the repository documents; convert and validate with a tool
your archive accepts if you need PDF/A.

## Agreeing with your archive

1. Get the archive's list of accepted formats and metadata requirements.
2. Tell editors which formats to upload for documents that will become records (for example PDF/A made by the office
   program, rather than a word processor file).
3. Decide who converts formats that are not accepted, and when (at upload, at transfer).
4. Test a transfer early with a few records.

## Related pages

- [Documents and PDFs (accessibility)](../accessibility/documents-and-pdfs.md)
- [Exporting content](exporting-content.md)
- [SVG images](../../../bc/6.0/svg-images.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
