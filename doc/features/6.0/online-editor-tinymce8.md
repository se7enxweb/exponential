# The online editor on TinyMCE 8 (opt-in, next to TinyMCE 3)

Exponential 6.0.15 ships TinyMCE 8.9.2 as a second engine of the eZ Online Editor. TinyMCE 3.5.12 stays the
default and nothing changes for existing installations. The new engine writes the same XHTML that the ezoe input
handler reads, so stored XML is identical with both engines and editors can switch back and forth.

Added 2026-10-02. Reference: [doc/bc/6.0/ezoe-tinymce8.md](../../bc/6.0/ezoe-tinymce8.md).

## Try it in one minute

Per user, without touching settings: open `/user/preferences/set/ezoe_engine/tinymce8`, then edit any article.
Back: `/user/preferences/set/ezoe_engine/tinymce3`. Or let editors choose on the page `/ezoe/engine`.

For a siteaccess, in `settings/siteaccess/<name>/ezoe.ini.append.php`:

```ini
[EditorSettings]
EditorEngine=tinymce8
EngineSwitch=enabled
```

`EngineSwitch=enabled` shows a button below the editor to switch engines (stored as the user preference
`ezoe_engine`).

## What the TinyMCE 8 engine does

| Content | Dialogs and behaviour (like TinyMCE 3) |
|---|---|
| Embed and embed-inline | search via `ezjsc::search` with the class filter and a preview column, Upload tab to upload and embed a new object, edit an existing embed (view, class, size for images, align, inline, custom attributes), double click and context toolbar |
| Links | ezoe dialog to browse the content tree or search for a node or object; anchors saved as `<a name>` like TinyMCE 3 |
| Custom tags | insert, edit, remove with the tag's custom attributes (text, textarea, int, number, email, select, checkbox, color, hidden), validation of required and numeric values |
| Literal tags | dialog like the TinyMCE 3 general tag dialog |
| Tables, rows, cells | classes and custom attributes (`content.ini [table] Defaults` for the size of new tables) |
| Paragraphs, headings, lists, strong, emphasize | the general tag dialog for class and custom attributes |
| Status bar | the ezxml tag path, for example "Path: paragraph » embed"; clicking an element opens its dialog |
| Look | with `Skin=o2k7` the editor and its dialogs look like the TinyMCE 3 o2k7 skin |

## Settings (extension/ezoe/settings/ezoe.ini, [EditorSettings])

| Key | Default | Meaning |
|---|---|---|
| `EditorEngine` | `tinymce3` | engine for users without a preference |
| `EngineSwitch` | `disabled` | show the switch button |
| `Engines[<id>]` | `tinymce3=expOETinyMCE3Engine`, `tinymce8=expOETinyMCE8Engine` | registry of classes implementing `expOEEditorEngine`; an extension adds a third engine with one class and one line |
| `UploadExtensionCheck` | `engine` | server-side file type check: `engine` (non-TinyMCE-3), `always`, `disabled` |
| `UploadFileExtensions[]` | images, documents, media | file types of the Upload tab |

Resolution order: user preference `ezoe_engine`, siteaccess, global, then `tinymce3`.

## Security

`ezoe/upload` refuses a file whose last extension is not in `UploadFileExtensions[]` and any name with an
executable extension anywhere in it (`shell.php.jpg`), because a request can skip the dialog.

## Tests

`extension/ezoe/tests/tinymce8` holds browser tests (puppeteer-core with Firefox or Chrome, real mouse clicks)
that can be run against an installation at any time, plus 267 PHPUnit tests for the registry, upload rules, JSON
answers and the XHTML round trip. The bundled TinyMCE 8.9.2 is used under the GPL (version 2 or later); its
README states the licences and where the corresponding source is.

Related: [remote services `expeditor`](remote-services-expservices.md), [the audit trail](audit-trail.md)
(`content.ezoe.engine.change`), [October 2026 chronicle](../../history/2026/2026-10.md).
