---
title: Upload a PDF report with an accessible summary
audience: government, editor
category: public-sector
order: 90
summary: For editors - publish a report as an HTML summary page with the PDF attached, so everyone can read the key points without downloading, with format and size shown and the file checked before upload.
keywords: PDF, report, upload, accessible summary, HTML summary, publication, File object, relation, download link, file size, accessibility
type: help
---
# Upload a PDF report with an accessible summary

This page is for editors who publish reports, plans and other long documents. Publish a summary page in HTML with the
PDF attached: search finds the summary, everyone can read it, and the address stays stable when the file is replaced
([Accessible documents](../../building/documents-and-publications/accessible-documents.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- The PDF is the final version, exported as a tagged PDF from its source program, with a title and language in its
  file properties. If it is not accessible, ask the author for a better export before publishing.
- The file name is readable: `annual-report-2025.pdf`.
- You know where publications go (a Publications folder, a Publication class, or an Article with a related file).

## Steps

1. Open the publications folder, **Create new**, choose **Publication** (or **Article** if your site has no
   publication class).
2. **Title**: the report's title.
3. **Summary** (or **Intro**): three to six short paragraphs or a list with the main findings, in plain language
   ([Write a plain-language summary](write-a-plain-language-summary.md)).
4. **Related files** (or **Document**): **Upload new**, choose the PDF, give it the report's title. The upload
   creates a File object and relates it.
5. Fill the publication date, reference and type if the class has them.
6. **Preview**, then **Send for publishing**.

## Check the result

- [ ] Signed out, the page shows the summary and a download link with format and size, for example "(PDF, 2.4 MB)".
- [ ] The PDF downloads.
- [ ] The site search finds the report by a word from the summary.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| A summary that says "see attached" | the summary must stand alone |
| The same PDF uploaded twice in different places | upload once, relate it ([Add a related documents list](add-a-related-documents-list.md)) |
| A scanned image PDF | not accessible and not searchable; ask for the source file |
| Personal data in the report's annexes | check before upload |
| Replacing the file later by uploading a new object | edit the File object instead to keep the address ([Replace a document with a new version](replace-a-document-with-a-new-version.md)) |

## Related pages

- [Publishing tasks](README.md)
- [Files and the File class](../../building/documents-and-publications/files-and-the-file-class.md)
- [Document library structure](../../building/documents-and-publications/document-library-structure.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
