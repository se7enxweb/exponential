---
title: jQuery in designs
audience: designer
category: theme-design
order: 310
summary: When a design needs jQuery, how to load the shipped jQuery 4 with ezjsc::jquery and ezjsc::jqueryUI, jQuery Migrate for older code, local versus CDN loading, the removal of YUI, and when plain JavaScript is the better choice.
keywords: jquery, jquery 4, jquery migrate, jquery ui, ezjsc::jquery, ezjsc::jqueryUI, LoadFromCDN, LocalScripts, YUI removed, vanilla javascript, design scripts
type: help
---
# jQuery in designs

This page is for designers whose scripts use jQuery, or who wonder whether they should. It answers: which jQuery the
installation ships, how to load it, and what changed with jQuery 4 and the removal of YUI.

## Contents

1. [What is shipped](#what-is-shipped)
2. [Loading it](#loading-it)
3. [Local or CDN](#local-or-cdn)
4. [Older code and jQuery Migrate](#older-code-and-jquery-migrate)
5. [YUI is gone](#yui-is-gone)
6. [Do you need jQuery at all?](#do-you-need-jquery-at-all)

## What is shipped

`extension/ezjscore/design/standard/javascript/` holds jQuery 4.0.0, jQuery Migrate 4.0.2 and jQuery UI 1.14.2, with
their license files. `ezjscore.ini [eZJSCore]` names them:

```ini
LocalScripts[jquery]=jquery-4.0.0.min.js
LocalScripts[jqueryMigrate]=jquery-migrate-4.0.2.min.js
LocalScripts[jqueryUI]=jquery-ui-1.14.2.min.js
```

## Loading it

Name the generator key before your own script in the same call, so the order is right:

```tpl
{ezscript_load( array( 'ezjsc::jquery', 'mytheme.js' ) )}
```

`ezjsc::jquery` prints jQuery followed by jQuery Migrate; `ezjsc::jqueryUI` prints jQuery UI. Several templates may ask
for `ezjsc::jquery`; it is printed once.

## Local or CDN

`LoadFromCDN=disabled` (default) serves the local files, which the packer can bundle and a Content Security Policy
with `script-src 'self'` allows. With `LoadFromCDN=enabled` the URLs of `ExternalScripts[]` (code.jquery.com) are
used instead; that needs the CDN host in the site's policy and sends visitors' requests to a third party. Keep the
default unless you have a reason.

## Older code and jQuery Migrate

jQuery 4 removed old APIs. jQuery Migrate 4.0.2, loaded after jQuery by `ezjsc::jquery`, restores them so older code
keeps working. The shipped build is quiet; to see every old call it restores in the browser console, set the reporting
build on a development siteaccess:

```ini
# ezjscore.ini.append.php of a development siteaccess
[eZJSCore]
LocalScripts[jqueryMigrate]=jquery-migrate-4.0.2.js
```

Fix the calls it reports; then a design can load jQuery 4 alone (`LocalScripts[jqueryMigrate]=` empty, per the comment
in `ezjscore.ini`).

## YUI is gone

ezjscore 1.5.0 removed YUI 2 and 3 (`ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io` and the `[YUI3]` settings). A design
that still asks for them gets nothing. Search your design:

```bash
grep -rn "ezjsc::yui2\|ezjsc::yui3\|YUI(" extension/mytheme/design
```

and replace each use with jQuery, the Exponential UI modules of `expui`, or plain JavaScript
([ezjscore](../../../features/6.0/extensions/ezjscore.md), "Upgrade to 1.5").

## Do you need jQuery at all?

For a menu toggle, a dialog or a fetch call, current browsers' own APIs (`querySelector`, `classList`, `fetch`,
`<dialog>`, `addEventListener`) are enough. A public design without jQuery saves its download on every first visit.
Use jQuery when you build on templates or extensions that need it (the simple design loads it, as do several
extensions).

## Related pages

- [Loading JavaScript](loading-javascript.md)
- [No inline JavaScript](no-inline-javascript.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
