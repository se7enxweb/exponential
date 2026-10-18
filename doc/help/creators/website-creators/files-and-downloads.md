---
title: Files and downloads
audience: website-creator, editor
category: website-creation
order: 350
summary: Offer documents for download - the file class and ezbinaryfile, the content/download view and its permission check, download links in templates, searchable PDFs, protected downloads and large files.
keywords: files, downloads, ezbinaryfile, content/download, PDF, documents, binary file, protected download, members download, file size, enhancedezbinaryfile, website creator
type: help
---
# Files and downloads

This page is for website creators who offer documents, brochures or software for download. Files are content objects
with a **file** field (`ezbinaryfile`); downloads go through the `content/download` view, which checks permissions
before sending the file.

## Contents

1. [How a download works](#how-a-download-works)
2. [Download links in templates](#download-links-in-templates)
3. [Protected downloads](#protected-downloads)
4. [Searching inside files](#searching-inside-files)
5. [Large files](#large-files)
6. [Checklist](#checklist)

## How a download works

- A **File** object (class `file`) holds the document in its `file` attribute (datatype `ezbinaryfile`). Uploads of
  non-image files become File objects (`DefaultClass=file` in `settings/upload.ini`).
- The download address is `content/download/<object ID>/<attribute ID>` (`kernel/content/module.php`, view `download`,
  function `read`). The view checks that the visitor may read the object, then sends the file.
- Because of that check, a file in a members-only section can only be downloaded by members.

## Download links in templates

The datatype's view template renders a link with name and size:

```text
{attribute_view_gui attribute=$node.data_map.file}
```

Your own markup:

```text
{def $file = $node.data_map.file}
{if $file.has_content}
  <a href={concat( 'content/download/', $file.contentobject_id, '/', $file.id, '/file/', $file.content.original_filename )|ezurl}>
    {$node.name|wash} ({$file.content.mime_type|wash}, {$file.content.filesize|si( byte )})
  </a>
{/if}
{undef $file}
```

Tell visitors the format and size before they click; it is also an accessibility requirement for many sites.

## Protected downloads

| Goal | How |
|---|---|
| Members-only files | put them in a section only members may read ([Sections and object states](sections-and-object-states.md)) |
| Files for one customer group | a subtree limitation on `content/read` for that group |
| A time-limited document | hide it, or move it to a section visitors cannot read, when the time is over |

Do not link files directly from `var/.../storage/`: that bypasses the permission check. The shipped rewrite rules
serve stored images directly for speed; for documents rely on `content/download`.

## Searching inside files

`settings/binaryfile.ini` has handlers that extract text from PDF and Word files for the search index (`[PDFHandlerSettings]`,
`[WordHandlerSettings]`), using external programs you install. With Solr (`ezfind`), file contents can be indexed too
([Search with ezfind and Solr](search-with-ezfind-and-solr.md)).

## Large files

- PHP's `upload_max_filesize` and `post_max_size` limit what editors can upload; set them in `php.ini` of the web PHP.
- A file field can have a maximum size in the class.
- Very large files (videos) are better hosted on a service built for them and embedded.
- The `enhancedezbinaryfile` extension adds options to the file datatype
  ([enhancedezbinaryfile](../../../features/6.0/extensions/enhancedezbinaryfile.md)).

## Checklist

- [ ] Download links show format and size.
- [ ] Protected files sit in a protected section, and the download is tested logged out.
- [ ] Upload limits in `php.ini` fit the largest file editors need.
- [ ] File names are readable (they appear in the download address).

## Related pages

- [Organizing the media library](organizing-the-media-library.md)
- [Media and uploads (for editors)](../../media-and-uploads.md)
- [PDF exports](../../../guides/pdf-exports.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
