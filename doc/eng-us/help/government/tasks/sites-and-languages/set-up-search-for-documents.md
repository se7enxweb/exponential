---
title: Set up search for documents
audience: government, admin
category: public-sector
order: 320
summary: Task for administrators: make the text inside PDF (and Word) documents searchable with the binaryfile.ini text extraction handlers and an installed extraction tool, then rebuild the index and test.
keywords: document search, PDF, binaryfile.ini, TextExtractionTool, PDFHandlerSettings, updatesearchindex, searchable file, full text
type: help
---
# Set up search for documents

This task is for administrators. Background: [Searching inside documents](../../building/search/searching-inside-documents.md).

## Before you start

A command-line PDF text extraction tool installed on the web server; test it on a sample file as the web server user.

## Steps

1. Override the handler with your tool:

   ```ini
   # settings/override/binaryfile.ini.append.php
   <?php /*
   [PDFHandlerSettings]
   TextExtractionTool=<full path and options of your tool>
   */ ?>
   ```

   The shipped values (`pstotext`, `wvWare ...`) name old tools.
2. **Setup > Classes**, the File class: make sure the file attribute is **Searchable**.
3. Clear the INI cache and rebuild the index: `php bin/php/updatesearchindex.php --clean`.

## Check

Search the public site for a word that appears only inside one PDF: the document is found.

## Problems

| Problem | Fix |
|---|---|
| Nothing found | the tool fails as the web server user, or the index was not rebuilt |
| Scanned PDFs not found | they have no text layer; run OCR before upload or describe them in the object |

## Related pages

- [Files and downloads](../../../creators/website-creators/files-and-downloads.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
