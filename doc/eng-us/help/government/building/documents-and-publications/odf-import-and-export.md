---
title: OpenDocument import and export
audience: government, admin, editor, website-creator
category: public-sector
order: 80
summary: How the ezodf extension imports OpenDocument Writer files as Exponential content and exports content back to Writer - what it needs (LibreOffice or OpenOffice as a converter), its settings, the views, and its documented limits - so documents written in an office suite become accessible web pages.
keywords: ezodf, OpenDocument, ODT, LibreOffice, OpenOffice, import, export, Writer, converter, odf.ini, RegisteredClassArray, ezwt, open standards
type: help
---
# OpenDocument import and export

This page is for administrators, editors and website creators at public bodies that write in office suites. Many
documents are drafted in LibreOffice or OpenOffice Writer; publishing them as web pages, rather than only as PDFs, makes
them easier to read and find. The `ezodf` extension imports a Writer document as a content object and exports objects
back to Writer. Source: [ezodf](../../../../features/6.0/extensions/ezodf.md). The default installation activates the
extension ([Extensions it activates](../../../site-packages/sevenx-multisite/extensions-it-activates.md)).

## Contents

1. [What it does](#what-it-does)
2. [What it needs](#what-it-needs)
3. [Settings](#settings)
4. [Using it](#using-it)
5. [Limits](#limits)

## What it does

| Direction | Result |
|---|---|
| Import | an editor uploads an `.odt` file; a content object is created: the title and a body with headings, paragraphs, lists, tables and images mapped onto class attributes |
| Export | an object is exported to a Writer document using a template |
| Library | a general library for generating Writer documents from your own modules |

OpenDocument is an open, standardized office format; using it fits open standards policies many public bodies have.

## What it needs

- OpenOffice or LibreOffice 2.4 or later, running as a converter service the extension reaches
  (`OOConverterAddress`, `OOConverterPort`, default `127.0.0.1:9090`);
- a `zip` binary (`ZipPath`) and a temporary folder (`TmpDir`).

Running an office suite as a service on the web server is an operations decision; ask your hosting team.

## Settings

`odf.ini` (override in `settings/override/odf.ini.append.php`):

| Block | Key | Default | Meaning |
|---|---|---|---|
| `ODFImport` | `DefaultImportClass` | `article` | class created by an import |
| `ODFImport` | `RegisteredClassArray[]` | article, folder, image, documentation_page, blog_post | classes an import may create |
| `ODFImport` | `PlaceImagesInMedia`, `ImportedImagesMediaNodeName` | `false`, `Imported images` | where images go |
| `<class>` | `DefaultImportTitleAttribute`, `DefaultImportBodyAttribute`, `Attribute[<odf field>]` | per class | mapping onto attributes |
| `ODFExport` | `UseTemplate`, `TemplateName` | `true`, a Writer template | export template |

To import into your own class (a Guidance or Publication class), add it to `RegisteredClassArray[]` and give it a
mapping block.

## Using it

| View | Purpose |
|---|---|
| `ezodf/import`, `ezodf/upload_import` | import a Writer document |
| `ezodf/export`, `ezodf/upload_export` | export an object to Writer |

The website toolbar of ezwt shows import and export buttons for the classes in
`websitetoolbar.ini [WebsiteToolbarSettings] ODFDisplayClasses[]`. After an import, open the new object, check
headings, tables and images, add alternative texts, and publish.

## Limits

From the extension's documentation:

- access checking for images in imported documents is not complete;
- images are ignored on export when the user cannot read them;
- storing imported images in the media folder does not work properly.

Formatting outside headings, lists, tables and images is not carried over. Review every import before publishing.

## Related pages

- [Documents and publications](README.md)
- [Accessible documents](accessible-documents.md)
- [ezwt](../../../../features/6.0/extensions/ezwt.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
