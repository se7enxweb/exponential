# jQuery 4 in the admin, and YUI gone

This page is for administrators who upgrade an installation, and for developers whose templates or extensions load
JavaScript libraries. On 2026-10-01 and 2026-10-02 the administration interface moved to jQuery 4 and Exponential UI
(`exp::*`), and YUI was removed from the admin designs and from the bundled ezjscore. The admin loads no YUI request
any more. The installed ezjscore is 1.5.4 (`extension/ezjscore/ezinfo.php`).

If a site or extension of yours still uses YUI, follow the upgrade steps in [YUI removal](../../bc/6.0/yui-removal.md).

## Check an installation in two minutes

1. Open any admin page with the browser console open. Expected: no `YUI is not defined` and no Migrate warnings.
2. List what in your own code still asks for YUI (it loads nothing now):

   ```bash
   grep -rn "ezjsc::yui" extension design
   ```

   No output means nothing is left to port.
3. After a deploy that adds an extension, run `./console exp:velocity deploy --allow-root-user`. With the `--packer`
   option it also clears the ezjscore packer cache, right before the template-block cache (cleared alone, cached admin
   page heads would name packed files that no longer exist). It always clears the design_base cache, so a newly
   activated extension's templates are found.

## What you get

- **jQuery 4.0.0 with jQuery Migrate 4.0.2 and jQuery UI 1.14.2** from `ezjsc::jquery` and `ezjsc::jqueryUI`
  (ezjscore 1.4.0). Migrate is the quiet build by default, so older code keeps working without filling the console;
  the reporting build is one setting away. The CDN entries name the same releases as the local files (they used to
  name jQuery 1.10.2 and jQuery UI 1.10.3). The jQuery 3.7.1, Migrate 3.4.1 and jQuery UI 1.10.3 files are still
  shipped for templates that name them directly.
- **The admin's own code is jQuery 4 clean.** Event shorthands (`.click(fn)`, `.change(fn)`), `.bind()`, `.unbind()`,
  boolean `.attr()` and `jQuery.trim()` were replaced by `.on()`, `.off()`, `.trigger()`, `.prop()` and
  `String.prototype.trim()` in the node tabs, class and role editors, translations, locations, the extension list,
  header search, the left menu's width control and the rich text editor's popups.
- **Exponential UI replaces YUI** in the sub-items table (`exp::datatable`), upload dialogs (`exp::dialog`,
  `exp::upload`), date fields (`exp::datepicker`), collapsing menus (`exp::collapse`), the sticky edit toolbar
  (`exp::sticky`), autosave and preview (`exp::autosave`) and the asynchronous publishing status page (`Exp.io`).
- **YUI is removed** from the `admin`, `admin3`, `admin4` and `standard` designs and from ezjscore 1.5.0: the YUI
  libraries, the packer keys `ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io` and their settings.
- Every logo image of the kernel designs shows the Exponential logo; the debug output is headed "Exp Debug".

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `extension/ezjscore/settings/ezjscore.ini` | `eZJSCore` | `LoadFromCDN` | `disabled` (local files) | installation |
| `extension/ezjscore/settings/ezjscore.ini` | `eZJSCore` | `PreferredLibrary` | `jquery` | installation |
| `extension/ezjscore/settings/ezjscore.ini` | `eZJSCore` | `ExternalScripts[jquery]`, `[jqueryMigrate]`, `[jqueryUI]` | `jquery-4.0.0.min.js`, `jquery-migrate-4.0.2.min.js`, `ui/1.14.2/jquery-ui.min.js` from code.jquery.com | installation |
| `extension/ezjscore/settings/ezjscore.ini` | `eZJSCore` | `LocalScripts[jquery]`, `[jqueryMigrate]`, `[jqueryUI]` | `jquery-4.0.0.min.js`, `jquery-migrate-4.0.2.min.js`, `jquery-ui-1.14.2.min.js` | installation |

To load jQuery 4 alone, leave `LocalScripts[jqueryMigrate]` empty.

## Other fixes of the same change

- The upload of object relation fields runs on `exp::dialog` and `exp::upload` when the expui extension is active,
  through `expajaxuploader.js` (copies in `design/admin` and `design/admin4`). The YUI version stays as the fallback
  for installations without expui.
- The rich text editor's custom tag and table cell dialogs called `.size()`, which jQuery 3 had already removed.
- The asynchronous publishing daemon stopped with a type error on PHP 8, because a signal handler receives the signal
  information as an array.
- The CSS packer turned `a :hover` into `a:hover` (a different selector). The space before a colon is now only removed
  inside declaration blocks.

## How it began: jQuery 3.7.1 in August

On 16 August 2026 (`f7c57376aa`) ezjscore replaced the vendored jQuery 1.10.2 with **jQuery 3.7.1** and
**jQuery Migrate 3.4.1**, and `ezjsc::jquery` loaded Migrate for every caller so that older scripts kept working. The
October change went on to jQuery 4. The 3.7.1 and Migrate 3.4.1 files are still in
`extension/ezjscore/design/standard/javascript/` (`ls` that folder to check).

## Related pages

- [YUI removal (upgrade steps)](../../bc/6.0/yui-removal.md)
- [ezjscore](extensions/ezjscore.md), [the admin4 design](admin4-design.md), [Exp Debug bar](exp-debug-bar.md)
- [Online editor: TinyMCE 8](online-editor-tinymce8.md)
- [The August 2026 security patches](../../specifications/6.0/security-hardening-2026-08.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- History: [October 2026](../../history/2026/2026-10.md), [August 2026](../../history/2026/2026-08.md), [March 2024](../../history/2024/2024-03.md)
