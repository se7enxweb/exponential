# ezmultiupload: upload many files at once

This page is for editors who add many images or files at a time, and for administrators who set it up.
`ezmultiupload` ("eZ Multiupload LS") adds **Upload multiple files** to folders and galleries. Choose or drop several
files, and each becomes a content object of the right class (images in a gallery, files in a folder) under the
current node.

## Use it

1. Open a folder or gallery.
2. Click **Upload multiple files** (the website toolbar button comes from [ezwt](ezwt.md); the view is
   `ezmultiupload/upload`).
3. Choose or drop the files. Each file shows a thumbnail, a counter and a message when it is stored.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `ezmultiupload.ini` | `MultiUploadSettings` | `AvailableClasses[]` | folder, gallery | Classes that offer the upload |
| `ezmultiupload.ini` | `MultiUploadSettings` | `AvailableSubtreeNode[]` | empty | Restrict to subtrees |
| `ezmultiupload.ini` | `MultiUploadSettings` | `MultiuploadHandlers[]` | empty | Extra handlers |
| `ezmultiupload.ini` | `FileTypeSettings_folder` | `FileType[]` | `*.*` | File types accepted in a folder |
| `ezmultiupload.ini` | `FileTypeSettings_gallery` | `FileType[]` | `*.jpg`, `*.png`, `*.gif`, `*.flv`, `*.mp4`, `*.mov` (and upper case) | File types accepted in a gallery |

## What changed in the Exponential 6 releases

| Version | Date | Change |
|---|---|---|
| 6.0.5 | 1 October 2026 | The upload page runs on Exponential UI's `exp::upload` when `expui` is active (messages in `Exp.dialog`), with YUI's uploader as the fallback. Compared with the YUI version in the admin, admin2 and admin3 designs on Velocity and PHP-FPM, each file makes the same request with the same fields, thumbnails, counters and messages. After a **cancel** the next files upload, where YUI uploaded nothing more until the page was reloaded. Eight new texts, translated into German. Requires `se7enxweb/expui ^1.0.0.1`. |
| 6.0.6 | | **The YUI 3 uploader is removed**: `upload.tpl` keeps only its Exponential UI code (`exp::io`, `exp::dialog`, `exp::upload`) and `design/standard/javascript/ezmultiupload.js` is removed. |
| 6.0.7, 6.0.8 | | Classes and command helpers, header and copyright notices, English and German translations. |

**Upgrade note:** if you override `upload.tpl`, move it to the Exponential UI modules. See
[YUI removal](../../../bc/6.0/yui-removal.md).

## Related pages

- [ezwt](ezwt.md)
- [jQuery 4 and YUI removal](../jquery4-and-yui-removal.md)
- [Velocity engines](../../../bc/6.0/velocity-engines.md)
- [Chronicle](../../../history/extensions/ezmultiupload.md) and [release notes](../../../changelogs/extensions/ezmultiupload.md)
- [Change ledger](../../../history/ledger/ezmultiupload.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- [Month: 2026-10 (all extensions)](../../../history/extensions/months/2026-10.md)
