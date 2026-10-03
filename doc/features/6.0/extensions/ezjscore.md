# ezjscore: JavaScript and CSS packer, server calls, jQuery

`ezjscore` ("eZ JS Core") is the JavaScript foundation of Exponential: it **packs** scripts and stylesheets into one cached file per page, loads **jQuery** and
**jQuery UI**, and provides **server calls** (an Ajax interface to PHP classes, protected by role policies), template operators for encoding and loading
assets, and the function library several extensions rely on ([ezstarrating](ezstarrating.md), [ezautosave](ezautosave.md), [ezwt](ezwt.md) and others).
The standalone repository `se7enxweb/ezjscore` carries the same code as the copy shipped in the Exponential kernel; 1.4.0 (1 October 2026) brought it back
in step after it had been emptied in 2013 when the code moved into the legacy kernel, so it can be installed and followed on its own again
(`composer require se7enxweb/ezjscore`).

How server calls are written and secured is described in [Backend ezjscore services](../../../bc/6.0/backend_ezjscore_services.md); the removal of YUI is
described in [YUI removal](../../../bc/6.0/yui-removal.md).

## What changed in the Exponential 6 releases

| Release | Change |
|---|---|
| 1.4.0 (1 October 2026) | The repository carries the current ezjscore again: the packer and its template operators, server calls, encoding operators and the jQuery loading. `ezjsc::jquery` loads **jQuery 4.0.0** followed by **jQuery Migrate 4.0.2**, and `ezjsc::jqueryUI` loads **jQuery UI 1.14.2**; the jQuery 3 files are still shipped, and the CDN entries, which named jQuery 1.10.2, name the same releases. `composer.json`, `ezinfo.php`, a README and a contributing guide are included |
| 1.4.1 (2 October) | **CSS packer** fix: at pack level 3 every space before a colon was removed, also in selectors, so `a :hover` became `a:hover` and `:where(#a) :where(.b)` became `:where(#a):where(.b)`, each a different selector, and rules silently stopped matching. The space before `:` is removed only inside declaration blocks (`color : red` becomes `color:red`) |
| 1.5.0 | **YUI removed**: the YUI 2 and YUI 3 libraries (`design/standard/lib/yui`), the packer keys `ezjsc::yui2`, `ezjsc::yui3` and `ezjsc::yui3io` and their config functions in `ezjscServerFunctionsJs`, the `[YUI3]` settings and the yui entries in `ExternalScripts`, `LocalScripts` and `LocalScriptBasePath` of `ezjscore.ini`. `PreferredLibrary` is `jquery`. This is a breaking change for code that asks for YUI |
| 1.5.1, 1.5.2 | Command line scripts, cronjob parts and module views are classes the files call; copyright notices and the about page name 1998 - 2026 7x & Exponential Foundation first, above the eZ Systems notices; the extension is identical to the copy in the kernel |
| 1.5.3, 1.5.4 | **`expsubitems`** server functions serve the admin sub-items list's **Table options**: registered as `[ezjscServer_expsubitems]` with the class Exponential 6.0.15 ships, every function checking access itself; new server function blocks in the settings for the sub-items columns. See [Sub-items table options](../../../bc/6.0/subitems-table-options.md) |
| 1.5.5 | The server router lets `expservices` classes answer in their **own envelope**, errors included |

## Upgrading to 1.5

* Search your designs and extensions for `ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io` and `[YUI3]` and replace them with `ezjsc::jquery`, `ezjsc::jqueryUI` or the
  Exponential UI modules of `expui` (see the table of replacements in [YUI removal](../../../bc/6.0/yui-removal.md)).
* If you load jQuery 3 files from `ezjscore.ini` yourself, keep them as they are: they are still shipped.
* After updating, clear the packer output (`php bin/php/ezcache.php --clear-tag=template,content --allow-root-user`, and the `ezjscore-packer` cache) so
  packed files are rebuilt.

## Related

* [Backend ezjscore services](../../../bc/6.0/backend_ezjscore_services.md)
* [Chronicle](../../../history/extensions/ezjscore.md) and [release notes](../../../changelogs/extensions/ezjscore.md)
