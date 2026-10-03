# The online editor on TinyMCE 8 (opt-in, next to TinyMCE 3)

This page is for editors who want a modern rich text editor, and for administrators who decide which editor a site
uses. Exponential 6.0.15 ships TinyMCE 8.9.2 as a second engine of the eZ Online Editor. TinyMCE 3.5.12 stays the
default, and nothing changes for existing installations. The new engine writes the same XHTML that the ezoe input
handler reads, so stored XML is identical with both engines and editors can switch back and forth. Added 2026-10-02.
Reference: [ezoe and TinyMCE 8](../../bc/6.0/ezoe-tinymce8.md).

## Try it in one minute

For yourself, without touching settings:

1. Open `/user/preferences/set/ezoe_engine/tinymce8`.
2. Edit any article. The editor is TinyMCE 8.
3. To go back, open `/user/preferences/set/ezoe_engine/tinymce3`.

Editors can also choose on the page `/ezoe/engine`.

For a whole siteaccess, in `settings/siteaccess/<name>/ezoe.ini.append.php`:

```ini
[EditorSettings]
EditorEngine=tinymce8
EngineSwitch=enabled
```

`EngineSwitch=enabled` shows a button below the editor to switch engines; the choice is stored as the user preference
`ezoe_engine`.

## What the TinyMCE 8 engine does

| Content | Dialogs and behaviour (like TinyMCE 3) |
|---|---|
| Embed and embed-inline | Search via `ezjsc::search` with the class filter and a preview column; Upload tab to upload and embed a new object; edit an existing embed (view, class, size for images, align, inline, custom attributes); double click and context toolbar |
| Links | ezoe dialog with Browse, Search and Bookmarks tabs (the user's own bookmarks, paged, with a preview column) to choose a node or object; anchors saved as `<a name>` like TinyMCE 3 |
| Custom tags | Insert, edit, remove with the tag's custom attributes (text, textarea, int, number, email, select, checkbox, color, hidden); validation of required and numeric values |
| Literal tags | Dialog like the TinyMCE 3 general tag dialog |
| Tables, rows, cells | Classes and custom attributes (`content.ini [table] Defaults` for the size of new tables). Insert table / Table properties open the TinyMCE 3 dialog design by default (size grid, width and border with px / %, class, summary, caption); `TableDialog=modern` in `[Engine_tinymce8]` selects the plain TinyMCE 8 form |
| Paragraphs, headings, lists, strong, emphasize | The general tag dialog for class and custom attributes |
| Status bar | The ezxml tag path, for example "Path: paragraph » embed"; clicking an element opens its dialog |
| Look | With `Skin=o2k7` the editor and its dialogs look like the TinyMCE 3 o2k7 skin |

## Settings

All keys are in `extension/ezoe/settings/ezoe.ini`, block `EditorSettings`. Scope: user preference, siteaccess or
global. Resolution order: user preference `ezoe_engine`, siteaccess, global, then `tinymce3`.

| Key | Default | Meaning |
|---|---|---|
| `EditorEngine` | `tinymce3` | Engine for users without a preference |
| `EngineSwitch` | `disabled` | Show the switch button |
| `Engines[<id>]` | `tinymce3=expOETinyMCE3Engine`, `tinymce8=expOETinyMCE8Engine` | Registry of classes implementing `expOEEditorEngine`; an extension adds a third engine with one class and one line |
| `UploadExtensionCheck` | `engine` | Server-side file type check: `engine` (non-TinyMCE-3), `always`, `disabled` |
| `UploadFileExtensions[]` | images, documents, media | File types of the Upload tab |
| `UploadFromUrl` | `enabled` | `enabled` / `disabled`: the "From a URL" choice on the Upload tab (both editors) |
| `UploadFromUrlMaxSize` | `145M` | Largest file fetched from a URL (K, M, G). Only this applies, not PHP's `upload_max_filesize` |
| `UploadFromUrlTimeout` | `300` | Total seconds a fetch may take (connecting must succeed within 5 seconds) |

Check the shipped values:

```bash
grep -n 'EditorEngine\|EngineSwitch\|Engines\[' extension/ezoe/settings/ezoe.ini
```

The registry classes are `extension/ezoe/classes/expoeeditorengine.php`, `expoetinymce3engine.php` and
`expoetinymce8engine.php`; the engine page is the view `engine` of the `ezoe` module.

## Security

`ezoe/upload` refuses a file whose last extension is not in `UploadFileExtensions[]`, and any name with an executable
extension anywhere in it (`shell.php.jpg`), because a request can skip the dialog. An engine change is recorded in the
[audit trail](audit-trail.md) as `content.ezoe.engine.change`.

### Upload from a URL

The Upload tab of the embed dialog (TinyMCE 8 and TinyMCE 3) offers "From your computer" or "From a URL". The server
fetches the address and creates the object like a browser upload: same class choice (`upload.ini`), location, name and
description fields, same embed afterwards, same permission checks and form token. The fetch is a server-side request
for a user, so:

- only `http` and `https`; no credentials in the URL; the host is resolved once, every address must be public (no
  loopback, private, link-local, multicast, reserved, carrier-grade NAT, IPv4-mapped or NAT64 IPv6 variants) and the
  connection is made to the checked address, so DNS rebinding cannot swap it;
- redirects are followed by hand, at most 3, every target checked again;
- connect timeout 5 s, total timeout `UploadFromUrlTimeout`; the size limit is enforced while the body streams to a
  temporary file under `var/tmp/ezoe_url/` (never held in memory) and the file is deleted after the request;
- the name comes from Content-Disposition or the URL path and is sanitized; the extension must pass
  `UploadFileExtensions[]` (whatever `UploadExtensionCheck` says), executable names are refused, and the content
  (finfo) must agree with the extension, executables and web pages are refused whatever the name;
- a successful fetch is recorded as `content.ezoe.upload.url` in the audit trail.

## Tests

- `extension/ezoe/tests/tinymce8` holds browser tests (puppeteer-core with Firefox or Chrome, real mouse clicks) that
  can be run against an installation at any time.
- PHPUnit tests in `tests/tests/extension/ezoe/` cover the registry, engine configuration, upload rules, JSON answers,
  views and the XHTML round trip. The count was 267 when written (not re-counted for this page). Run them with
  `php vendor/bin/phpunit tests/tests/extension/ezoe/`.

The bundled TinyMCE 8.9.2 is used under the GPL (version 2 or later); its README states the licences and where the
corresponding source is.

## Related pages

- [ezoe and TinyMCE 8 (bc guide)](../../bc/6.0/ezoe-tinymce8.md)
- [Remote services `expeditor`](remote-services-expservices.md) and the [expservices specification](../../specifications/6.0/expservices.md)
- [ezoe](extensions/ezoe.md), [ezautosave](extensions/ezautosave.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- [October 2026 chronicle](../../history/2026/2026-10.md)
