# The online editor engines: TinyMCE 3 and TinyMCE 8

Read this page if your editors use the online editor (ezoe) to edit XML text (`ezxmltext`) attributes, or if you
want to offer TinyMCE 8 or your own editor. Since Exponential 6.0.15 ezoe runs on an editor engine chosen from a
registry. Nothing changes for an existing installation: the default stays TinyMCE 3. One server-side check is new:
the embed dialog's upload refuses some file types (see "Upload check").

## In short

| | |
|---|---|
| What changed | Two engines ship: TinyMCE 3.5.12 (default) and TinyMCE 8.9.2 (opt-in, GPL-2.0-or-later, `license_key: 'gpl'`, no cloud call). `ezoe/upload` checks file types. |
| Who is affected | Sites that switch to TinyMCE 8; editors who upload unusual file types through the embed dialog. |
| How to check | `grep -n "EditorEngine\|EngineSwitch\|UploadExtensionCheck" extension/ezoe/settings/ezoe.ini` |
| How to fix | Add missing upload types to `UploadFileExtensions[]`. Switch engines with `EditorEngine` or per user. |

Both engines write the same XHTML that `eZOEXMLInput` and `eZOEInputParser` read, so the stored XML is the same
whichever engine an editor uses.

## Try TinyMCE 8 in one minute

1. As an editor, open `/user/preferences/set/ezoe_engine/tinymce8`.
2. Edit an article. The editor is TinyMCE 8.
3. To go back, open `/ezoe/engine` and choose TinyMCE 3, or reset to the site default.

To make TinyMCE 8 the default for everyone without a preference, set `EditorEngine=tinymce8` (see "Settings").

## Settings

File `extension/ezoe/settings/ezoe.ini`, block `[EditorSettings]`. Every key can be overridden per siteaccess as for
any INI file.

| Key | Default | Meaning |
|---|---|---|
| `EditorEngine` | `tinymce3` | The engine of every user without a preference. |
| `EngineSwitch` | `disabled` | `enabled` shows a button below the editor for each other registered engine. |
| `Engines[<id>]` | `tinymce3`, `tinymce8` | The registry: identifier = class implementing `expOEEditorEngine`. |
| `UploadFileExtensions[]` | images, pdf, office, text, zip, audio, video | File types of the embed dialog's upload tab. |
| `UploadExtensionCheck` | `engine` | Server-side check of those types: `engine` (only when the user's engine is not tinymce3), `always`, `disabled`. |

The block `[Engine_tinymce8]` holds `ToolbarMap[<[EditorLayout] button>]=<toolbar item>` and
`ExternalPlugins[<name>]=<design path>`. The engine class reads them; they are not hard coded in the template.

## Which engine an editor gets

`expOEEditor::resolve()` takes the first of: the user preference `ezoe_engine`, the siteaccess `EditorEngine`, the
global `EditorEngine`, then `tinymce3`. An unknown, broken or unavailable choice is skipped, never an error.

A user chooses with:

- the page `/ezoe/engine` (a select over the registry; needs the policy `ezoe/editor`);
- the switch button below the editor (`EngineSwitch=enabled`);
- the URL `/user/preferences/set/ezoe_engine/tinymce8`;
- the remote service `expeditor::set` (POST `engine`; empty means the site default).

Each choice through the page, the button or the service writes the audit event `content.ezoe.engine.change`
(channel `content`; before and after engine).

## Upload check

- The embed dialog uploads through `ezoe/upload` and `eZContentUpload` (class set by `upload.ini`; access by the
  policy `ezoe/relations` and `canEdit()`).
- Since 6.0.15 that view refuses a file whose type is not in `UploadFileExtensions[]` (see `UploadExtensionCheck`),
  and any name that contains an executable extension (`shell.php.jpg`), because a request can skip the dialog.
- If an editor's upload is refused, add the type to `UploadFileExtensions[]` in an override.

## Other server-side notes

- The TinyMCE 8 plugins call only existing endpoints (ezoe views, ezjscore functions of ezoe). They add no ezjscore
  function and no new unauthenticated endpoint.
- The Exp Debug bar shows and changes `EditorEngine` and `EngineSwitch` (group Extensions).
- `setup/rad` counts every `Engines[]` entry as an extension point ("Online editor engines") and shows an entry whose
  class is missing or is not an `expOEEditorEngine` as broken.

## Remote services (`ezjscore/call/expeditor::<service>`)

| Service | Access | Writes | Returns |
|---|---|---|---|
| `engines` | `ezoe/editor` | no | registered engines, the current one, the default, problems |
| `get` | `ezoe/editor` | no | preference, engine in use, default, switch enabled |
| `set` | `ezoe/editor` | POST + token | chooses the engine of the current user |
| `uploadExtensions` | `ezoe/editor` | no | allowed extensions, check mode, whether it is enforced |
| `config` | `ezoe/editor` | no | engine, toolbar map, plugins, buttons of the layout, upload types |

## Extension points

1. **An engine:** one class and one INI line.
2. **Toolbar items and plugins** of an engine: `[Engine_<id>]` `ToolbarMap[]` and `ExternalPlugins[]`.
3. **Upload types:** `UploadFileExtensions[]`.
4. **Audit:** the branch `expOEAuditBranch` (`audit.ini.append.php`).

### Example: add a third engine

```php
// extension/myeditor/classes/myeditorengine.php
class myEditorEngine extends expOEEditorEngineBase
{
    public function identifier() { return 'myeditor'; }
    public function label()      { return 'My editor'; }          // translated in design/standard/ezoe
    public function isAvailable() { return is_file( __DIR__ . '/../design/standard/javascript/my.js' ); }
    public function template()   { return 'design:content/datatype/edit/ezxmltext_myeditor.tpl'; }
}
```

```ini
# extension/myeditor/settings/ezoe.ini.append.php
[EditorSettings]
Engines[myeditor]=myEditorEngine
[Engine_myeditor]
ToolbarMap[bold]=bold
```

Then:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

The template receives `attribute`, `input_handler`, `attribute_base` and `editorRow`; `$input_handler.engine` gives
its identifier, label, toolbar map, plugins and config. The engine now appears on `/ezoe/engine`, in the switch
buttons, in `expeditor::engines` and in `setup/rad`. An engine that returns `''` from `template()` is rendered by the
built-in TinyMCE 3 markup of `ezxmltext_ezoe.tpl`.

## Upstream code

The TinyMCE 8 distribution is vendored unmodified in `extension/ezoe/design/standard/javascript/tinymce8/` (README,
license and notices included); only German is bundled. The browser tests are in `extension/ezoe/tests/tinymce8/`
(`npm install && npm test`).

## Tests

```bash
php vendor/bin/phpunit tests/tests/extension/ezoe/
```

The tests run against the live installation, with no test database. Every test puts the admin's engine preference
and the INI values back.

| File | Covers |
|---|---|
| `expOEEditorRegistryTest` | registry, resolution (preference, siteaccess, global), unknown / broken / unavailable engines, third engine, audit, setup/rad |
| `expOEEngineConfigTest` | each engine's template, toolbar map, plugins and config, shipped defaults, vendored TinyMCE 8 and its license, translations |
| `expOEUploadRulesTest` | allowed and refused file names, the three check modes, the order of the check in the upload view |
| `expOEViewsJsonTest` | every ezoe PHP file has no text before `<?php` or after `?>`, the module definition prints nothing, `ezoe/load` answers JSON with nothing in front of it, browse and bookmarks |
| `expOEEngineViewTest` | the page `/ezoe/engine`: select, saving, reset, unknown engine, policy |
| `expOEEditorServicesTest` | the `expeditor` services, their guards and the catalogue |
| `expOERoundTripTest` | the XHTML of both editors gives the same XML; XML shown to an editor and saved again keeps its content |

`tests/tests/extension/ezoe/helpers/run_ezoe_view.php` runs one view in a process of its own, because a view that ends
with `cleanExit()` cannot run inside PHPUnit.

A view or module definition with text outside the PHP tags breaks the editor's dialogs (`JSON.parse: unexpected
character` on the final OK of the embed dialog). That is why the tests look at the raw output.

## Related pages

- [The online editor with TinyMCE 8](../../features/6.0/online-editor-tinymce8.md) and [ezoe](../../features/6.0/extensions/ezoe.md)
- [Behaviour changes of 1 and 2 October 2026](behaviour-changes-2026-10.md#access-and-security) (the upload check)
- [Audit trail upgrade](audit.md)
- [Backend ezjscore services](backend_ezjscore_services.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
