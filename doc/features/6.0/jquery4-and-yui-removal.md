# jQuery 4 in the admin, and YUI gone

On 2026-10-01 and 2026-10-02 the administration interface moved to jQuery 4 and Exponential UI (`exp::*`), and
YUI was removed from the admin designs and from the bundled ezjscore. The admin loads no YUI request any more.

The installed ezjscore is 1.5.4 (`extension/ezjscore/ezinfo.php`). Upgrade steps for sites and extensions that still use YUI: [doc/bc/6.0/yui-removal.md](../../bc/6.0/yui-removal.md).

## How it began: jQuery 3.7.1 in August

On 16 August 2026 (`f7c57376aa`) ezjscore replaced the vendored jQuery 1.10.2 with **jQuery 3.7.1** and
**jQuery Migrate 3.4.1**, and `ezjsc::jquery` loaded Migrate for every caller so that older scripts kept working.
This was the first step; the October change below went on to jQuery 4. The 3.7.1 and Migrate 3.4.1 files are
still in `extension/ezjscore/design/standard/javascript/` (`ls` that folder to check). See
[August 2026](../../history/2026/2026-08.md) and the [6.0.15 changelog](../../changelogs/6.0/6.0.15.md).

## What you get

- **jQuery 4.0.0 with jQuery Migrate 4.0.2 and jQuery UI 1.14.2** from `ezjsc::jquery` and `ezjsc::jqueryUI`
  (ezjscore 1.4.0). Migrate is the quiet build by default, so older code keeps working without filling the
  console; the reporting build is one setting away. The CDN entries name the same releases as the local files
  (they used to name jQuery 1.10.2 and jQuery UI 1.10.3). The jQuery 3.7.1, Migrate 3.4.1 and jQuery UI 1.10.3
  files are still shipped for templates that name them directly.
- **The admin's own code is jQuery 4 clean**: event shorthands (`.click(fn)`, `.change(fn)`), `.bind()`,
  `.unbind()`, boolean `.attr()` and `jQuery.trim()` were replaced by `.on()`, `.off()`, `.trigger()`, `.prop()`
  and `String.prototype.trim()` in the node tabs, class and role editors, translations, locations, the extension
  list, header search and the left menu's width control; the rich text editor's popups too.
- **Exponential UI replaces YUI** in the sub-items table (`exp::datatable`), upload dialogs (`exp::dialog`,
  `exp::upload`), date fields (`exp::datepicker`), collapsing menus (`exp::collapse`), the sticky edit toolbar
  (`exp::sticky`), autosave and preview (`exp::autosave`) and the asynchronous publishing status page (`Exp.io`).
- **YUI is removed** from the `admin`, `admin3`, `admin4` and `standard` designs and from ezjscore 1.5.0: the YUI
  libraries, the packer keys `ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io` and their settings.
- Every logo image of the kernel designs shows the Exponential logo; the debug output is headed "Exp Debug".

## Settings (extension/ezjscore/settings/ezjscore.ini)

| Block | Key | Value shipped | Scope |
|---|---|---|---|
| `eZJSCore` | `LoadFromCDN` | `disabled` (local files) | installation |
| `eZJSCore` | `PreferredLibrary` | `jquery` | installation |
| `eZJSCore` | `ExternalScripts[jquery]`, `[jqueryMigrate]`, `[jqueryUI]` | `jquery-4.0.0.min.js`, `jquery-migrate-4.0.2.min.js`, `ui/1.14.2/jquery-ui.min.js` from code.jquery.com | installation |
| `eZJSCore` | `LocalScripts[jquery]`, `[jqueryMigrate]`, `[jqueryUI]` | `jquery-4.0.0.min.js`, `jquery-migrate-4.0.2.min.js`, `jquery-ui-1.14.2.min.js` | installation |

Leave `LocalScripts[jqueryMigrate]` empty to load jQuery 4 alone.

## Check an installation

- Open any admin page: the browser console shows no `YUI is not defined` and no Migrate warnings.
- `grep -rn "ezjsc::yui" extension design` in your own code lists what still asks for YUI; it loads nothing now.
- After a deploy that adds an extension, use `./console exp:velocity deploy --allow-root-user`; with the new
  `--packer` option it also clears the ezjscore packer cache, right before the template-block cache (cleared alone,
  cached admin page heads would name packed files that no longer exist). It always clears the design_base cache
  now, so a newly activated extension's templates are found.

## Other fixes of the same change

- The upload of object relation fields runs on `exp::dialog` and `exp::upload` when the expui extension is active, through
  `expajaxuploader.js` (copies in `design/admin` and `design/admin4`); the YUI version stays as the fallback for installations without expui.

- The rich text editor's custom tag and table cell dialogs called `.size()`, which jQuery 3 had already removed.
- The asynchronous publishing daemon stopped with a type error on PHP 8 because a signal handler receives the
  signal information as an array.
- The CSS packer kept `a :hover` as `a:hover` (a different selector); the space before a colon is now only removed
  inside declaration blocks.

Related: [October 2026 chronicle](../../history/2026/2026-10.md), [admin4 design](admin4-design.md).

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [Exp Debug bar](exp-debug-bar.md).
