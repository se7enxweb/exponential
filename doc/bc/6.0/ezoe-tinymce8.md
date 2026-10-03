# The online editor engines: TinyMCE 3 and TinyMCE 8

The online editor (ezoe) edits ezxmltext attributes. Since Exponential 6.0.15 it runs on an editor engine
chosen from a registry. Two engines ship: TinyMCE 3.5.12 (the default, nothing changes for existing
installations) and TinyMCE 8.9.2 (opt-in, GPL-2.0-or-later, `license_key: 'gpl'`, no cloud call). Both write
the same XHTML that `eZOEXMLInput` and `eZOEInputParser` read, so the stored ezxml is the same.

## Settings (extension/ezoe/settings/ezoe.ini, [EditorSettings])

| Setting | Default | Meaning |
|---|---|---|
| `EditorEngine` | `tinymce3` | the engine of every user without a preference; siteaccess override works as for any ini |
| `EngineSwitch` | `disabled` | `enabled` shows a button below the editor for each other registered engine |
| `Engines[<id>]` | tinymce3, tinymce8 | the registry: identifier = class implementing `expOEEditorEngine` |
| `UploadFileExtensions[]` | images, pdf, office, text, zip, audio, video | file types of the embed dialog's upload tab |
| `UploadExtensionCheck` | `engine` | server-side check of those types: `engine` (only when the user's engine is not tinymce3), `always`, `disabled` |

The block `[Engine_tinymce8]` holds `ToolbarMap[<[EditorLayout] button>]=<toolbar item>` and
`ExternalPlugins[<name>]=<design path>`; they are read by the engine class, not hard coded in the template.

## Which engine an editor gets

`expOEEditor::resolve()`: the user preference `ezoe_engine`, then the siteaccess `EditorEngine`, then the
global one, then `tinymce3`. An unknown, broken or unavailable choice is skipped, never an error.

The user chooses with:

- the page `/ezoe/engine` (a select over the registry; needs the policy ezoe/editor),
- the switch button below the editor (`EngineSwitch=enabled`),
- the URL `/user/preferences/set/ezoe_engine/tinymce8`,
- the remote service `expeditor::set` (POST `engine`, empty = site default).

Each choice through the page, the button or the service writes the audit event
`content.ezoe.engine.change` (channel content; before/after engine).

## Server side

- The upload of the embed dialog goes through `ezoe/upload` and `eZContentUpload` (class by `upload.ini`, access
  by the policy ezoe/relations and `canEdit()`). Since 6.0.15 that view also refuses a file whose type is not in
  `UploadFileExtensions[]` (see `UploadExtensionCheck`), and any name that contains an executable extension
  (`shell.php.jpg`), because a request can skip the dialog.
- The TinyMCE 8 plugins call only existing endpoints (ezoe views, ezjscore functions of ezoe). They add no
  ezjscore function and no new unauthenticated endpoint.
- The Exp Debug bar shows and changes `EditorEngine` and `EngineSwitch` (group Extensions).
- setup/rad counts every `Engines[]` entry as an extension point ("Online editor engines") and shows an entry
  whose class is missing or is not an `expOEEditorEngine` as broken.

## Remote services (ezjscore/call/expeditor::<service>)

| Service | Access | Writes | Returns |
|---|---|---|---|
| `engines` | ezoe/editor | no | registered engines, the current one, the default, problems |
| `get` | ezoe/editor | no | preference, engine in use, default, switch enabled |
| `set` | ezoe/editor | POST + token | chooses the engine of the current user |
| `uploadExtensions` | ezoe/editor | no | allowed extensions, check mode, whether it is enforced |
| `config` | ezoe/editor | no | engine, toolbar map, plugins, buttons of the layout, upload types |

## Extension points

1. **An engine**: one class and one ini line.
2. **Toolbar items and plugins** of an engine: `[Engine_<id>]` `ToolbarMap[]` / `ExternalPlugins[]`.
3. **Upload types**: `UploadFileExtensions[]`.
4. **Audit**: the branch `expOEAuditBranch` (`audit.ini.append.php`).

### Worked example: a third engine

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

Run `php bin/php/ezpgenerateautoloads.php -e` and clear the ini cache. The template receives `attribute`,
`input_handler`, `attribute_base` and `editorRow`; `$input_handler.engine` gives its identifier, label,
toolbar map, plugins and config. The engine now appears on `/ezoe/engine`, in the switch buttons, in
`expeditor::engines` and in setup/rad. An engine that returns `''` from `template()` is rendered by the
built-in TinyMCE 3 markup of `ezxmltext_ezoe.tpl`.

## Upstream notes

The TinyMCE 8 distribution is vendored unmodified in `design/standard/javascript/tinymce8/` (README, license
and notices included); only German is bundled. The browser tests are in `extension/ezoe/tests/tinymce8/`
(`npm install && npm test`); the PHP tests are `tests/tests/extension/expservices/misc/expEditorServicesTest.php`.

## Tests

`php vendor/bin/phpunit tests/tests/extension/ezoe/` (live installation, no test database; the admin's engine
preference and the ini values are put back by every test):

| File | Covers |
|---|---|
| `expOEEditorRegistryTest` | registry, resolution (preference, siteaccess, global), unknown / broken / unavailable engines, third engine, audit, setup/rad |
| `expOEEngineConfigTest` | each engine's template, toolbar map, plugins and config, shipped defaults, vendored TinyMCE 8 and its license, translations |
| `expOEUploadRulesTest` | allowed and refused file names, the three check modes, the order of the check in the upload view |
| `expOEViewsJsonTest` | every ezoe PHP file has no text before `<?php` or after `?>`, the module definition prints nothing, `ezoe/load` answers JSON with nothing in front of it, browse and bookmarks |
| `expOEEngineViewTest` | the page `/ezoe/engine`: select, saving, reset, unknown engine, policy |
| `expOEEditorServicesTest` | the `expeditor` services, their guards and the catalogue |
| `expOERoundTripTest` | the XHTML of both editors gives the same ezxml; ezxml shown to an editor and saved again keeps its content |

`tests/tests/extension/ezoe/helpers/run_ezoe_view.php` runs one view in a process of its own, because a view that
ends with `cleanExit()` cannot run inside PHPUnit.

A view or module definition with text outside the PHP tags breaks the editor's dialogs (`JSON.parse: unexpected
character` on the final OK of the embed dialog): the tests above look at the raw output for that reason.
