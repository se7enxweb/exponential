# ezmultiupload: upload many files at once

`ezmultiupload` ("eZ Multiupload LS") adds **Upload multiple files** to folders and galleries: choose
or drop several files and each becomes a content object of the right class (images in a gallery, files in a
folder) under the current node. The view is `ezmultiupload/upload`; the website toolbar button comes from
[ezwt](ezwt.md).

## Settings (`ezmultiupload.ini`)

| Block | Key | Default | Meaning |
|---|---|---|---|
| MultiUploadSettings | `AvailableClasses[]` | folder, gallery | Classes that offer the upload |
| MultiUploadSettings | `AvailableSubtreeNode[]` | empty | Restrict to subtrees |
| MultiUploadSettings | `MultiuploadHandlers[]` | empty | Extra handlers |
| FileTypeSettings_folder | `FileType[]` | `*.*` | File types accepted in a folder |
| FileTypeSettings_gallery | `FileType[]` | `*.jpg`, `*.png`, `*.gif`, `*.flv`, `*.mp4`, `*.mov` (and upper case) | File types accepted in a gallery |

## What changed in the Exponential 6 releases

* 6.0.5 (1 October 2026): the upload page runs on Exponential UI's `exp::upload` when `expui` is active
  (messages in `Exp.dialog`) and keeps YUI's uploader as the fallback. Compared with the YUI version in the
  admin, admin2 and admin3 designs on Velocity and PHP-FPM, each file makes the same request with the same
  fields, thumbnails, counters and messages. After a **cancel** the next files upload, where YUI uploaded
  nothing more until the page was reloaded. Eight new texts, translated into German. The extension requires
  `se7enxweb/expui ^1.0.0.1`.
* 6.0.6: **the YUI 3 uploader is removed**: `upload.tpl` keeps only its Exponential UI code (`exp::io`,
  `exp::dialog`, `exp::upload`) and `design/standard/javascript/ezmultiupload.js` is removed. If you override
  `upload.tpl`, move to the Exponential UI modules. See [YUI removal](../../../bc/6.0/yui-removal.md).
* 6.0.7, 6.0.8: classes and command helpers, header and copyright notices, English and German translations.

## Related

* [ezwt](ezwt.md)
* [Chronicle](../../../history/extensions/ezmultiupload.md) and [release notes](../../../changelogs/extensions/ezmultiupload.md)
