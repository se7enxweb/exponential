# ezodf: OpenDocument import and export

This page is for editors who write in OpenOffice or LibreOffice Writer, and for administrators who set up the
conversion. `ezodf` ("eZ OpenOffice.org LS") imports and exports OpenDocument Writer documents:

- **Import**: an editor uploads a `.odt` file and gets a content object. The title, and a body with headings,
  paragraphs, lists, tables and images, are mapped onto class attributes.
- **Export**: the same object can be exported back to a Writer document using a template.
- **Library**: a general library for generating Writer documents from your own modules.

It needs OpenOffice or LibreOffice 2.4 or later for conversions.

## Use it

| View | Purpose |
|---|---|
| `ezodf/import`, `ezodf/upload_import` | Import a Writer document |
| `ezodf/export`, `ezodf/upload_export` | Export an object to Writer |
| `ezodf/authenticate` | Sign in for the import and export |

The toolbar of [ezwt](ezwt.md) shows the import and export buttons for the classes listed in
`websitetoolbar.ini [WebsiteToolbarSettings] ODFDisplayClasses[]`.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `odf.ini` | `ODFSettings` | `ZipPath` | empty | Path of the zip binary |
| `odf.ini` | `ODFSettings` | `TmpDir` | `/tmp` | Temporary folder |
| `odf.ini` | `ODFImport` | `DefaultImportClass` | `article` | Class created by an import |
| `odf.ini` | `ODFImport` | `DefaultImportImageClass` | `image` | Class of imported images |
| `odf.ini` | `ODFImport` | `RegisteredClassArray[]` | article, folder, image, documentation_page, blog_post | Classes an import may create |
| `odf.ini` | `ODFImport` | `ImportedImagesMediaNodeName`, `PlaceImagesInMedia` | `Imported images`, `false` | Where images go |
| `odf.ini` | `ODFImport` | `OOConverterAddress`, `OOConverterPort` | `127.0.0.1`, `9090` | The converter service |
| `odf.ini` | `ODFExport` | `UseTemplate`, `TemplateName`, `TemplateRepository` | `true`, `ezpublish.ott`, `extension/ezodf/templates` | Writer template for export |
| `odf.ini` | `ODFExport` | `ClassAttributeMappingToHeader` | `enabled` | Map class attributes to the document header |
| `odf.ini` | `<class>` | `DefaultImportTitleAttribute`, `DefaultImportBodyAttribute`, `Attribute[<odf field>]` | per class | Map document parts onto attributes |

## Limits

From the extension README:

- Access checking for images in imported documents is not complete.
- Images are ignored on export when the user cannot read them.
- Storing imported images in the media folder does not work properly.

## What changed in the Exponential 6 releases

| Version | Change |
|---|---|
| 6.1.0 | Module views are safe on a persistent worker (Velocity); see [behaviour changes](../../../bc/6.0/extensions-behaviour-changes.md#1-persistent-php-workers-module-views-no-longer-declare-at-file-level-without-a-guard). |
| 6.1.1 to 6.1.4 | `ezinfo.php`, extension name, license in full, `<br>` as HTML5 does, description names Exponential. |
| 6.1.5 | Command line scripts and module views are classes the files call; copyright notices name 1998 - 2026 7x & Exponential Foundation first. |
| 6.1.6 | English and German translations for every string the admin showed untranslated. |

## Related pages

- [ezwt](ezwt.md), [ezdemo](ezdemo.md)
- [Velocity engines](../../../bc/6.0/velocity-engines.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/ezodf.md) and [release notes](../../../changelogs/extensions/ezodf.md)
- [Change ledger](../../../history/ledger/ezodf.md)
- Months: [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
