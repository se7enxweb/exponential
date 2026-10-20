# YUI removed from Exponential

Read this page if your templates, designs or extensions use YUI (the Yahoo! User Interface library, YUI 2 and
YUI 3), or if you override admin templates or styles. Exponential 6.0.15 no longer ships, loads or calls YUI in the
kernel, in any design, or in any of its extensions. Everything that used YUI runs on jQuery 4 and Exponential UI
(extension `expui`). Code that still asks for YUI gets nothing, so it must be moved.

The classic grey administration interface is still available, YUI-free and looking as it always did, as the
siteaccess `classic`.

## In short

| | |
|---|---|
| What changed | No YUI anywhere. ezjscore 1.5.0 removes the YUI libraries and the `ezjsc::yui*` packer keys. |
| Who is affected | Templates and extensions that load `ezjsc::yui2`, `ezjsc::yui3` or `ezjsc::yui3io`, or use `YUI(...)` / `YAHOO.*`; overrides of the renamed templates, classes and stylesheets. |
| How to check | `grep -rnE "ezjsc::yui\|YUI\(\|YAHOO\.\|YUI3_config\|YUILoader\|yui3-js-enabled" extension/ design/ settings/` |
| How to fix | Follow "How to fix" steps 1 to 5. |

## What changed

| Area | Before | Now |
|---|---|---|
| Admin pages (`admin`, `admin3`, `admin4`, `classic`) | YUI 3 and 2 loaded on every page by `design.ini [JavaScriptSettings] BackendJavaScriptList` | jQuery 4 + Exponential UI (`exp::*`); no YUI request, no `YUI` or `YAHOO` object |
| Sub-items table, upload dialogs, date fields, collapsing menus, sticky edit toolbar, autosave, publishing queue page | YUI with an Exponential UI version beside it | Exponential UI only |
| ezjscore | bundled YUI 2.8.2 and 3.17.2; packer keys `ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io` | **1.5.0**: removed; `PreferredLibrary=jquery` |
| Logos | eZ logo images in the admin and standard designs | the same files show the Exponential logo |
| Debug output | headed "eZ debug" | headed "Exp Debug" |

### Extension releases without YUI

The root `composer.json` requires these versions.

| Extension | Version | Notes |
|---|---|---|
| ezjscore | 1.5.0 | YUI libraries, `ezjsc::yui*` keys and `[YUI3]` settings removed (breaking, see below) |
| expui (Exponential UI) | 1.0.0.2 | the replacement library; its comments no longer refer to YUI |
| ezautosave | 6.0.7 | admin and ezwebin autosave on `exp::autosave` |
| eztags | 2.4.9 | tag children table on `exp::datatable`; `eztags_children_yui.tpl` renamed `eztags_children_table.tpl` |
| ezstarrating | 6.0.6 | jQuery only (`ezstarrating_yui3.js` removed) |
| ezmultiupload | 6.0.6 | upload on `exp::upload` only |
| ezwt | 6.0.8 | sort page drag and drop on jQuery; ezdemo date fields on `exp::datepicker` |
| ezwebin | 6.0.15 | date fields on `exp::datepicker` |
| ezflow | 6.1.4 | page editor, block tools, push to block, schedule dialog, timeline on jQuery; tab classes `.ezpage-tabs*` |
| ezdemo | 6.0.8 | galleries, flyouts, campaign block on jQuery |
| cjw_newsletter | 4.1.9 | dead YUI drag-and-drop of the admin2 list removed |
| sevenx_themes_simple | 1.0.20 | YUI settings and calendar styles removed |

## How to check

1. Search your own code:

   ```bash
   grep -rnE "ezjsc::yui|YUI\(|YAHOO\.|YUI3_config|YUILoader|yui3-js-enabled" extension/ design/ settings/
   ```

   No output means nothing is left. (The shipped extensions are already clean; a hit is in your own code or an
   override.)

2. In a browser's developer tools, on any admin page: no request whose URL contains `yui`; in the console
   `typeof YUI` and `typeof YAHOO` are `"undefined"`, and `typeof Exp` is `"object"`.

## How to fix

1. **Move YUI code to jQuery or Exponential UI.** `{ezscript_require( 'ezjsc::yui3' )}`, `'ezjsc::yui2'` and
   `'ezjsc::yui3io'` load nothing any more, and `YUI(...)`, `YAHOO.*`, `YUI3_config` and `YUILoader` are undefined.

   | YUI | Use instead |
   |---|---|
   | `ezjsc::yui3` + `ezjsc::yui3io`, `Y.io.ez('fn::args', ...)` | `ezjsc::jquery` + `ezjsc::jqueryio`, `$.ez('fn::args', post, callback)`; or `exp::io`, `Exp.io.call(fn, args)` |
   | `Y.one`, `Y.all`, `Y.on`, `Y.Node` | jQuery `$(...)` |
   | YUI 2 DataTable / Paginator | `exp::datatable` (`$.fn.expDataTable`) |
   | YUI 2 Calendar, `ezdatepicker.js` | `exp::datepicker` (defines `showDatePicker()`) |
   | `ezmodalwindow`, YUI Dialog / SimpleDialog | `exp::dialog` (`Exp.dialog`) |
   | `ezajaxuploader` | `exp::upload` + `expajaxuploader.js` (`$.fn.expAjaxUploader`) |
   | `ezcollapsiblemenu` | `exp::collapse` (`Exp.collapse`) |
   | `fixed_toolbar.js` | `exp::sticky` |
   | `ezautosubmit`, `ezcontentpreview` | `exp::autosave` (`Exp.autosave.AutoSubmit`, `Exp.autosave.Preview`) |
   | YUI DD (drag and drop) | native drag events or pointer events with jQuery |
   | `.yui3-js-enabled` on `<html>` | `.exp-js` (set by Exponential UI) |

   The step-by-step guide with examples for every module is Exponential UI's
   `extension/expui/doc/CONVERTING_YUI_to_EXPUI.md`; the API is in `extension/expui/doc/API.md`.

2. **Front-end designs load Exponential UI themselves.** The admin's script list is not loaded there:

   ```
   {exp_config()}
   {ezscript_require( array( 'ezjsc::jquery', 'exp::core::shared', 'exp::datepicker' ) )}
   {ezcss_require( array( 'exp/core.css', 'exp/datepicker.css' ) )}
   ```

3. **Rename overrides and CSS for renamed names.**
   - eZ Tags' `eztags_children_yui.tpl` is `eztags_children_table.tpl`.
   - The admin trash table's `yui-dt*` classes are `admin-dt*`.
   - ezflow's tab classes are `.ezpage-tabs*`, and the timeline body class is `ezflow-skin`.

4. **Stylesheets.** The admin designs' `theme/yui_datatable.css`, `yui_menu.css` and `yui_container.css` are gone. The
   admin's own rules from them (sub-items toolbar, table options, masks) are in `theme/admin_datatable.css`.

5. **Settings.** `ezjscore.ini` overrides that set `ExternalScripts[yui*]`, `LocalScripts[yui*]`,
   `LocalScriptBasePath[yui*]` or `[YUI3]` can be deleted. `PreferredLibrary=yui3` no longer means anything.

## The classic grey administration interface (`classic`)

The siteaccess `classic` (reached at `/classic`) uses the original grey `admin` design alone, with no additional
designs. It is YUI-free and pixel-faithful to the original: the screenshots of every widget that used YUI were
compared with the YUI originals (the calendar is drawn as the YUI one was; the trash and setup pages are identical).
It also gained what the newer designs have: a left menu that collapses and is resized by dragging (remembered per
user), and a content tree that scrolls inside the menu.

To add it to an installation:

1. Copy the admin siteaccess settings to `settings/siteaccess/classic/`.
2. In `settings/siteaccess/classic/site.ini.append.php` set:

   ```ini
   [DesignSettings]
   SiteDesign=admin
   AdditionalSiteDesignList[]
   ```

3. Add `classic` to `[SiteAccessSettings] AvailableSiteAccessList[]` and `RelatedSiteAccessList[]`, and to
   `[SiteSettings] SiteList[]`.
4. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

## In one sentence

Exponential 6.0.15 is free of YUI in the kernel, every administration design and every extension; the classic grey
administration interface is available as the siteaccess `classic` and runs on jQuery 4 and Exponential UI like the
others, looking as it always did.

## Related pages

- [jQuery 4 in the admin, and YUI gone](../../features/6.0/jquery4-and-yui-removal.md)
- [ezjscore: JavaScript and CSS packer, server calls, jQuery](../../features/6.0/extensions/ezjscore.md)
- [Behaviour changes of 1 and 2 October 2026](behaviour-changes-2026-10.md)
- [March 2024](../../history/2024/2024-03.md)
