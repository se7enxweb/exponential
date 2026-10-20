---
title: Files and the File class
audience: government, website-creator, editor
category: public-sector
order: 20
summary: How documents are stored in Exponential - the File class with its ezbinaryfile attribute, uploads from the upload page and the edit form, upload limits, file names, download links that show format and size, and searching inside files.
keywords: File class, ezbinaryfile, upload, multi-upload, upload limits, upload_max_filesize, file names, download link, file size, format, binaryfile.ini, search inside files
type: help
---
# Files and the File class

This page is for website creators and editors who put documents on a public-sector site. A document is a **File**
object holding the file in an `ezbinaryfile` attribute; pages link to it or relate it. Sources:
[Files and downloads](../../../creators/website-creators/files-and-downloads.md) and
[Media and uploads](../../../media-and-uploads.md).

## Contents

1. [The File class](#the-file-class)
2. [Uploading](#uploading)
3. [Limits](#limits)
4. [Download links](#download-links)
5. [Searching inside files](#searching-inside-files)

## The File class

| Attribute | Identifier | Datatype | Required |
|---|---|---|---|
| Name | `name` | Text line | yes |
| Description | `description` | XML block | no |
| File | `file` | File (`ezbinaryfile`) | yes |

The `sevenx_classes` package version adds Keywords; on a new installation the File class keeps the base data's
definition shown above ([sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md)). File objects are
"always available", so they show in every language siteaccess.

## Uploading

| Way | Where |
|---|---|
| The upload page: many files by button, drag or paste, with a title per file and **Keep as drafts** | **Upload** on a folder, **Upload files** on the dashboard ([Media and uploads](../../../media-and-uploads.md#upload-files)) |
| From an edit form: **Upload new** in a relation field | creates a File object and relates it |
| Create a File object by hand | **Create new** > **File** |
| ezmultiupload | **Upload multiple files** ([ezmultiupload](../../../../features/6.0/extensions/ezmultiupload.md)) |

Non-image files become File objects (`settings/upload.ini`, `DefaultClass=file`). Give each a real title; the file
name is the default.

## Limits

| Limit | Where |
|---|---|
| Size per upload | PHP `upload_max_filesize` and `post_max_size`; the upload page shows the largest accepted ("At most 64 MB per file", as an example) |
| A maximum size per file attribute | the class attribute's setting |
| Allowed types | the upload settings; dangerous names (for example `shell.php.jpg`) are refused in the editor's upload |

## Download links

A download goes through `content/download/<object ID>/<attribute ID>`, which checks the visitor may read the object.
The file attribute's view template renders a link with name and size:

```text
{attribute_view_gui attribute=$node.data_map.file}
```

Tell visitors the format and size before they click ("Annual report 2025 (PDF, 2.4 MB)"). For your own markup the
attribute's content has the original file name, size and MIME type ([Files and downloads](../../../creators/website-creators/files-and-downloads.md#download-links-in-templates)).

## Searching inside files

`settings/binaryfile.ini` has handlers that extract text from PDF and Word files for the search index
(`[PDFHandlerSettings]`, `[WordHandlerSettings]`), using external programs you install; with eZ Find and Solr, file
contents can be indexed too. Without them, only the name and description are searched: write a good description.

## Related pages

- [Documents and publications](README.md)
- [Document library structure](document-library-structure.md)
- [Organizing the media library](../../../creators/website-creators/organizing-the-media-library.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
