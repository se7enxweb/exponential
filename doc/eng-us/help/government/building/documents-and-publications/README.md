---
title: Documents and publications
audience: government, website-creator, project-manager, admin
category: public-sector
order: 10
summary: Index of the guides for publishing documents and publications on a public-sector Exponential site - the File class, a document library, versions, metadata and tags, PDF exports, accessible documents, OpenDocument import and export, and download permissions.
keywords: documents, publications, files, File class, ezbinaryfile, document library, versions, metadata, eztags, PDF export, accessible documents, ezodf, downloads, permissions
type: help
---
# Documents and publications

This folder is for website creators, project managers and administrators who publish documents: reports, plans,
forms, minutes, guidance, data files. Public bodies publish many documents, and many of them are hard to find,
outdated or inaccessible. The guides show how Exponential stores and serves documents, how to organize them, keep
their history, describe them, make them accessible and control who may download them.

## Contents

1. [The pages](#the-pages)
2. [Reading order](#reading-order)
3. [HTML first](#html-first)

## The pages

| Page | The answer in one line |
|---|---|
| [Files and the File class](files-and-the-file-class.md) | The File class and `ezbinaryfile`, uploads, size and type limits, download links with format and size |
| [Document library structure](document-library-structure.md) | Where documents live: the media library or next to their pages, a Publication landing page, relations |
| [Versions of documents](versions-of-documents.md) | Replacing a file keeps its address; the old file stays in versions; when a new object is better |
| [Metadata and tagging](metadata-and-tagging.md) | Titles, dates, reference numbers, types and topics with real datatypes and eztags |
| [PDF export](pdf-export.md) | **Setup > PDF export**: one PDF of a subtree, generated once or on the fly, public or protected |
| [Accessible documents](accessible-documents.md) | HTML summaries, accessible source files, file information in links, what Exponential does not check |
| [OpenDocument import and export](odf-import-and-export.md) | ezodf: Writer documents to content and back, its requirements and limits |
| [Downloads and permissions](downloads-and-permissions.md) | `content/download` checks read access; sections for restricted documents; public stored PDFs |

## Reading order

1. **Files and the File class**, then **Document library structure**.
2. **Metadata and tagging** and **Versions of documents**.
3. **Accessible documents**: before the first publication goes up.
4. **Downloads and permissions**.
5. **PDF export** and **OpenDocument import and export** when you need them.

Up: [Building a public-sector site](../README.md), the index of all building guides, and [Governments: start here](../../government-start-here.md), the start of the Governments
section. Publication classes for specific organizations are proposed in
[Content model for national government](../../levels/national/content-model.md) and the other levels pages.

## HTML first

A document a person must download to read is harder to find, read and keep current than a web page. The usual
public-sector practice:

| Content | Publish as |
|---|---|
| Information people need to act (services, guidance, rules) | HTML pages |
| Formal documents (signed decisions, reports, plans) | the document, with an HTML summary page |
| Forms to fill in | an online form where possible; a document as an alternative |
| Data | open formats (CSV), with a description page |

## Related pages

- [Building a service catalog](../service-catalog/README.md)
- [Files and downloads](../../../creators/website-creators/files-and-downloads.md)
- [Media and uploads](../../../media-and-uploads.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
