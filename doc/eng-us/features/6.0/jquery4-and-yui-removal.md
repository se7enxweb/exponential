# jQuery 4 in the admin, admin4 without jQuery on every page, and YUI gone

This page is for administrators who upgrade an installation, and for developers whose templates or extensions load
JavaScript libraries. On 2026-10-01 and 2026-10-02 the administration interface moved to jQuery 4 and Exponential UI
(`exp::*`), and YUI was removed from the admin designs and from the bundled ezjscore. The admin loads no YUI request
any more. The installed ezjscore is 1.5.4 (`extension/ezjscore/ezinfo.php`). Since 23 October 2026 **admin4 and
admin4l load jQuery only on the pages that request it**; the kernel's own admin4 pages request none (see
[admin4 and admin4l without jQuery](#admin4-and-admin4l-without-jquery), which also lists the packages that still
bring it). The older admin designs and the site designs still load jQuery 4.

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
  name jQuery 1.10.2 and jQuery UI 1.10.3). The older jQuery 3.7.1, Migrate 3.4.1 and jQuery UI 1.10.3 files are
  removed (14 October 2026): nothing in the distribution loaded them, and jQuery UI 1.10.3 has known
  vulnerabilities.
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

## admin4 and admin4l without jQuery

**admin4 and admin4l load no jQuery on every page any more** (23 October 2026). `page_head_script.tpl` no longer
prepends `ezjsc::jquery` and `ezjsc::jqueryio`; its first `ezscript_load` starts with `a4_url.js` and `a4_dom.js`,
then `BackendJavaScriptList[]`, then the scripts the page's templates requested. A page that needs jQuery or an
Exponential UI module requests it itself.

### What runs without jQuery in the kernel

- **The scripts on every page**: the menu bar (`ezadmin_menubar.js`), the content structure tree
  (`contentstructuremenu_dynamic.js`), the context menus (`popupmenu/ezpopupmenu.js`), `a4_url.js`, `a4_dom.js`,
  `admin4.js` and `a4_actions.js`.
- **Node view**: the tabs (`node_tabs.js`) and the sub-items table (`ezajaxsubitems_expdatatable.js`,
  `subitems_priority_drag.js`, `page/children_detailed.js`) on `Exp.datatable.create()`. `children.tpl` and
  `children_detailed.tpl` request `exp::core::shared`, `exp::io` and `exp::datatable`.
- **Content edit and class edit**: `content/edit.tpl` and `class/edit.tpl` request `exp::sticky` for the toolbar;
  the date and date/time fields request `exp::datepicker`; the relation upload requests `exp::io`, `exp::dialog`,
  `exp::upload` and `expajaxuploader.js` (entry point `window.expAjaxUploader`; `$.fn.expAjaxUploader` remains
  where jQuery is on the page); the relation search (`ezajaxrelations_jquery.js`, plain JavaScript despite its name)
  and the asynchronous publishing status (`ezasynchronouspublishing.js`) request `exp::core::shared` and `exp::io`.
  The page scripts share the helpers of `a4_dom.js`.
- **Rich text**: TinyMCE 8 brings its own scripts; ezoe requests jQuery only for the built-in TinyMCE 3 engine
  (`ezxmltext_ezoe.tpl`), and its popups have a page layout of their own that loads it.
- **Dashboard**: the community activity block (`dashboard_community_activity.js`).
- **Image editing**: expimageeditor runs without jQuery.
- Reduced motion also switches jQuery's effects off when a page loads jQuery after `admin4.js`.

Exponential UI 1.0.0.15 (`se7enxweb/expui`) runs without jQuery: `exp::core::shared` boots without it (`Exp.$` stays
undefined), `exp::io` uses `fetch()`, and every module has a plain JavaScript path with element entry points
(`Exp.datatable.create()`, `Exp.sticky.start()`, `Exp.dialog`, `Exp.upload.create()`, ...). Only `exp::compat` still
needs jQuery.

### Requesting jQuery or an Exponential UI module

A template whose scripts need jQuery or an `exp::` module requests them itself, **outside every cache-block**:

```
{ezscript_require( array( 'ezjsc::jquery', 'ezjsc::jqueryio', 'my_admin_page.js' ) )}
{ezscript_require( array( 'exp::core::shared', 'exp::io', 'exp::datatable' ) )}
```

`pagelayout.tpl` of admin4 and admin4l includes `page_head_script.tpl` outside the head's cache-block, so the head
prints what the page requested on every request, and a request made later in the page (by the left menu, for
example) prints its scripts in place. A request inside a `{cache-block}` does not run on a cache hit, and the page
then loses its scripts.

### Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/design.ini` | `JavaScriptSettings` | `BackendJavaScriptList[]` | scripts on every admin page; **only scripts that run without jQuery** (no jQuery, no `exp::` module) | installation, siteaccess |
| `settings/design.ini` | `JavaScriptSettings` | `ClassicBackendJavaScriptList[]` | empty | installation, siteaccess |

`ClassicBackendJavaScriptList[]` is read by the older admin designs a user can switch to (`settings/uiswitch.ini`):
`admin` (with `admin2`) and `admin3` load jQuery on every page, then `BackendJavaScriptList[]` and
`ClassicBackendJavaScriptList[]`. admin4 and admin4l do not read it. An extension that put a jQuery script or an
`exp::` module into `BackendJavaScriptList[]` moves it to `ClassicBackendJavaScriptList[]` for the older designs and
requests it from its own admin4 templates. The Admin UI design (`adminui`, the package `se7enxweb/exp_adminui`) has
its own `page_head_script.tpl` and reads the new list once its package does.

### The extensions installed with Composer

The packages of the root `composer.json` are not part of this repository, and each moves on its own release. Until
then admin4 still loads what they put on every page:

| Package | What still brings jQuery to admin4 | Change |
|---|---|---|
| eztags (`~2.4.24`) | `BackendJavaScriptList[]` names `ezjsc::jquery`, `ezjsc::jqueryUI`, jqModal, jsTree and the `jquery.eztags*.js` plugins: jQuery is on every admin4 page while eztags is active | the admin4 scripts without jQuery (eztags PR #3), and the move of the plugins to `ClassicBackendJavaScriptList[]` with requests in the tags templates |
| expui (`~1.0.0.15`) | `BackendJavaScriptList[]` names the `exp::` modules: no jQuery, but every module on every page | the move of the modules to `ClassicBackendJavaScriptList[]` |
| xrowmetadata (`~1.4.16`) | `admin-maxlength.js` (every page) calls `jQuery( document ).ready()` | the counter on plain JavaScript |
| expai (`~0.0.1.17`) | `expai.js` (every page) and the Vector page run on jQuery | expai PR #11 and #12 |
| ezautosave (`~6.0.15`) | the admin autosave runs on jQuery without requesting it | ezautosave PR #7 |
| ezmultiupload (`~6.1.12`) | the upload pages request jQuery themselves | ezmultiupload PR #12 |
| ezstarrating (`~6.0.15`) | `ezsrrating.tpl` requests jQuery itself | ezstarrating PR #8 |
| explayouts_ui_api (`~1.3.29`) | the layout editor (`app.tpl`) loads jQuery and jQuery UI itself | explayouts_ui_api PR #12 |

While eztags 2.4.24 is active, nothing breaks: jQuery stays on every admin4 page through its list. Once eztags no
longer lists it, xrowmetadata, expai and ezautosave need their releases without jQuery.
`AdminJQueryOnDemandTest::testInstalledPackagesRequestTheJQueryTheyNeed` (JQ-13) names what is still pending in its
skip message, and fails once no every-page list brings jQuery and a package still relies on it.

### What still uses jQuery

Outside admin4 and admin4l jQuery 4 is still loaded:

- ezjscore still ships jQuery 4.0.0, Migrate 4.0.2 and jQuery UI 1.14.2 (`ezjsc::jquery`, `ezjsc::jqueryUI`,
  `ezjsc::jqueryio`, `$.ez`); `PreferredLibrary=jquery` is unchanged.
- The older admin designs `admin`, `admin3` and `adminui`, on every page.
- The site designs and themes, ezoe's TinyMCE 3 and its popups, the expservices portal design `expportal_jquery`,
  `exp::compat`, and the packages above until their releases.

New code does not need jQuery: plain JavaScript, `a4_dom.js` in admin4, and Exponential UI where a widget or a server
call is needed.

### Guard tests

- `tests/tests/design/AdminJQueryOnDemandTest.php` (JQ-01 to JQ-13; the file header lists each rule): admin4's head
  loads no jQuery, `BackendJavaScriptList[]` of the kernel names nothing that needs jQuery or Exponential UI, every
  admin template that loads a script calling jQuery requests it, no such request sits inside a cache-block, the page
  layouts print the head's scripts outside the cache-blocks, and the named pages request only what they use.
- `tests/tests/design/AdminPageScriptsWithoutJQueryTest.php`: the small page scripts of admin4 and `a4_dom.js`.
- `tests/tests/design/AdminPlainScriptsExpUIPacksTest.php`: every page that loads a script on Exponential UI gets the
  `exp::` packs it calls, also on the admin design through `ClassicBackendJavaScriptList[]`.

## How it began: jQuery 3.7.1 in August

On 16 August 2026 (`f7c57376aa`) ezjscore replaced the vendored jQuery 1.10.2 with **jQuery 3.7.1** and
**jQuery Migrate 3.4.1**, and `ezjsc::jquery` loaded Migrate for every caller so that older scripts kept working. The
October change went on to jQuery 4. The 3.7.1 and Migrate 3.4.1 files stayed in
`extension/ezjscore/design/standard/javascript/` until 14 October 2026; since then the folder holds one release of
each library (`ezjscoreVendoredLibrariesTest`).

## Related pages

- [YUI removal (upgrade steps)](../../bc/6.0/yui-removal.md)
- [ezjscore](extensions/ezjscore.md), [the admin4 design](admin4-design.md), [Exp Debug bar](exp-debug-bar.md)
- [Online editor: TinyMCE 8](online-editor-tinymce8.md)
- [The August 2026 security patches](../../specifications/6.0/security-hardening-2026-08.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
- History: [October 2026](../../history/2026/2026-10.md), [August 2026](../../history/2026/2026-08.md), [March 2024](../../history/2024/2024-03.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
