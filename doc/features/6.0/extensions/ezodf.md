# ezodf: OpenDocument import and export

`ezodf` ("eZ OpenOffice.org LS") imports and exports OpenDocument (OpenOffice and LibreOffice Writer)
documents. An editor uploads a `.odt` file and gets a content object (a title, a body with headings,
paragraphs, lists, tables and images mapped onto class attributes); the same object can be exported back
to a Writer document using a template. It also ships a general library for generating Writer documents
from your own modules. It needs OpenOffice or LibreOffice 2.4 or later for conversions, and its views
are `ezodf/import`, `ezodf/export`, `ezodf/upload_import`, `ezodf/upload_export` and `ezodf/authenticate`.
The toolbar of [ezwt](ezwt.md) shows the import and export buttons for the classes in
`websitetoolbar.ini [WebsiteToolbarSettings] ODFDisplayClasses[]`.

## Settings (`odf.ini`)

| Block | Key | Default | Meaning |
|---|---|---|---|
| ODFSettings | `ZipPath` | empty | Path of the zip binary |
| ODFSettings | `TmpDir` | `/tmp` | Temporary folder |
| ODFImport | `DefaultImportClass` | `article` | Class created by an import |
| ODFImport | `DefaultImportImageClass` | `image` | Class of imported images |
| ODFImport | `RegisteredClassArray[]` | article, folder, image, documentation_page, blog_post | Classes an import may create |
| ODFImport | `ImportedImagesMediaNodeName`, `PlaceImagesInMedia` | `Imported images`, `false` | Where images go |
| ODFImport | `OOConverterAddress`, `OOConverterPort` | `127.0.0.1`, `9090` | The converter service |
| ODFExport | `UseTemplate`, `TemplateName`, `TemplateRepository` | `true`, `ezpublish.ott`, `extension/ezodf/templates` | Writer template for export |
| ODFExport | `ClassAttributeMappingToHeader` | `enabled` | |
| `<class>` | `DefaultImportTitleAttribute`, `DefaultImportBodyAttribute`, `Attribute[<odf field>]` | per class | Map document parts onto attributes |

Known limitations (from the extension README): access checking for images in imported documents is not
complete, images are ignored on export when the user cannot read them, and storing imported images in
the media folder does not work properly.

## What changed in the Exponential 6 releases

* 6.1.0: module views are safe on a persistent worker (Velocity); see
  [Behaviour changes](../../../bc/6.0/extensions-behaviour-changes.md#1-persistent-php-workers-module-views-no-longer-declare-at-file-level-without-a-guard).
* 6.1.1 to 6.1.4: `ezinfo.php`, extension name, license in full, `<br>` as HTML5 does, description names Exponential.
* 6.1.5: command line scripts and module views are classes the files call; copyright notices name 1998 - 2026
  7x & Exponential Foundation first.
* 6.1.6: English and German translations for every string the admin showed untranslated.

## Related

* [ezwt](ezwt.md), [ezdemo](ezdemo.md)
* [Chronicle](../../../history/extensions/ezodf.md) and [release notes](../../../changelogs/extensions/ezodf.md)
