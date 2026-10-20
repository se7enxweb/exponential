---
title: Searching inside documents
audience: government, admin, website-creator
category: public-sector
order: 50
summary: How to make the text of PDF and Word documents findable on a public site built with Exponential: the binaryfile.ini text extraction handlers and the external tools they need, Solr as the alternative, scanned documents, and checks.
keywords: document search, PDF search, full text, binaryfile.ini, PDFHandlerSettings, WordHandlerSettings, TextExtractionTool, text extraction, OCR, scanned PDF, ezfind
type: help
---
# Searching inside documents

This page is for administrators and site builders whose public site publishes many documents: reports, minutes,
decisions. Without text extraction only the document's name and the fields of its object are searchable
([Files and downloads](../../../creators/website-creators/files-and-downloads.md#searching-inside-files)).

## Contents

1. [The text extraction handlers](#the-text-extraction-handlers)
2. [Setting them up](#setting-them-up)
3. [With Solr](#with-solr)
4. [Scanned documents](#scanned-documents)
5. [Checking](#checking)

## The text extraction handlers

`settings/binaryfile.ini` has handlers that extract text from PDF and Word files for the search index, using
external programs you install on the server:

| Block | Key | Shipped value |
|---|---|---|
| `[PDFHandlerSettings]` | `TextExtractionTool` | `pstotext` |
| `[WordHandlerSettings]` | `TextExtractionTool` | `wvWare -x /usr/local/wv/wvText.xml` |

The shipped values name old tools; set the tool you install, with its full path, in
`settings/override/binaryfile.ini.append.php`, and test it on the command line first.

## Setting them up

1. Install a command-line text extraction tool for PDF (and Word, if you publish Word files) on the web server.
2. Override `TextExtractionTool` with the command.
3. Mark the file attribute of the File class **Searchable**.
4. Rebuild the index: `php bin/php/updatesearchindex.php --clean`.

## With Solr

With the `ezfind` extension and a Solr server, file contents can be indexed too
([Search with ezfind and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md)); check the extension's
documentation in `extension/ezfind/` for the setting in your version.

## Scanned documents

A scan without a text layer has no text to extract and is not accessible either. Publish documents with real text,
or run OCR before upload; a summary in the object's description makes even a scan findable by its subject.

## Checking

Search for a word that appears only inside one document. If it is not found: check the tool runs as the web server
user, the file attribute is searchable, and the index was rebuilt.

## Related pages

- [Files and downloads](../../../creators/website-creators/files-and-downloads.md)
- [Searchable fields and indexing](searchable-fields-and-indexing.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
