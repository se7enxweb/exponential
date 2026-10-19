---
title: The expui JavaScript API
audience: developer, designer
category: extension-development
order: 340
summary: A reference card of Exponential UI, the admin's JavaScript library: packer keys, exp_config(), the Exp core, Exp.io server calls, dialogs, tables, date pickers, uploads and toolbars.
keywords: expui, Exponential UI, Exp, Exp.io.call, Exp.io.form, Exp.io.poll, Exp.dialog, exp::datatable, exp::datepicker, exp::upload, exp::collapse, exp::sticky, exp_config, Exp.token, Exp.prefs, JavaScript API
type: help
---
# The expui JavaScript API

Part 8, reference 30. Exponential UI (the extension `expui`, object `Exp` in the browser) is the JavaScript library
of the administration since YUI was removed. Extensions use it for their admin pages. This page summarizes its API
documentation (`doc/API.md` of the [expui repository](https://github.com/se7enxweb/expui), version 1.0.0.1 at the time
of writing); the full description of each module is in its `doc/modules/` folder. Moving old YUI code to it is in
[From YUI to expui](../part-4-upgrading/yui-to-expui.md).

## Loading

| Template call | Loads |
|---|---|
| `{exp_config( hash( 'prefs', array( ... ), 'strings', array( ... ) ) )}` | the page's configuration block: preferences and translated texts for your script |
| `{ezscript_require( 'exp::core' )}` | jQuery 4 and the core |
| `{ezscript_require( array( 'exp::core', 'exp::io' ) )}` | and the server calls |
| `{ezcss_require( 'exp/core.css' )}` | the design tokens (`--exp-*`) and utilities |

Module keys: `exp::collapse`, `exp::sticky`, `exp::dialog`, `exp::upload`, `exp::datatable`, `exp::datepicker`,
`exp::autosave`, each with its stylesheet `exp/<module>.css`. **The admin designs load all of them on every page**, so
an admin page of your extension usually needs only its own script.

## The core (Exp)

| API | Does |
|---|---|
| `Exp.ready( fn )` | run when the page is ready |
| `Exp.i18n( text, params )` | a text from the `strings` of `exp_config()` |
| `Exp.on( name, fn )`, `Exp.off( name, fn )`, `Exp.emit( name, data )` | page-wide events; prefix your names (`myext:saved`) |
| `Exp.register( name, init )`, `Exp.start( root, only )` | start behavior from HTML (`data-*` markup) |
| `Exp.prefs.get( name, fallback )`, `Exp.prefs.set( name, value )` | user preferences |
| `Exp.keys.bind( combo, fn, options )` | keyboard shortcuts |
| `Exp.token()` | the page's form token |
| `Exp.reducedMotion` | `true` when the user asked for less motion |

## Server calls (Exp.io)

| API | Does |
|---|---|
| `Exp.io.call( fn, args, options )` | an ezjscore server function; POST by default; returns a Promise with the function's `content`, rejects with an `Exp.io.Error` |
| `Exp.io.form( form, options )` | post a form with its files, adding the form token |
| `Exp.io.poll( fn, args, options )` | call again every `every` ms until `until( content )` is true |
| `Exp.io.url( path )` | the address of a path in the current siteaccess |

```javascript
Exp.io.call( 'myext::items', [ 2 ], { method: 'GET' } ).then( function ( items ) {
    // use items
}, function ( error ) {
    // error.kind: signedout, refused, server, network, timeout, invalid
} );
```

## Modules

| Module | API |
|---|---|
| `exp::dialog` | `Exp.dialog.open( options )`, `confirm( text )`, `alert( text )`, `form( url, options )`, `create( options )`; markup `data-exp-dialog` |
| `exp::datatable` | `$( el ).expDataTable( options )`; markup `data-exp-datatable` |
| `exp::datepicker` | `$( el ).expDatePicker( options )`; `window.showDatePicker()` |
| `exp::upload` | `$( el ).expUpload( options )`; markup `data-exp-upload` |
| `exp::collapse` | `Exp.collapse( conf )`, `$( el ).expCollapse( conf )` |
| `exp::sticky` | `$( toolbar ).expSticky( options )`, `Exp.sticky.start( toolbar, options )` |
| `exp::autosave` | `new Exp.autosave.AutoSubmit( conf )`, `$( form ).expAutosave( conf )`, `$( el ).expPreview( conf )` |

All dialogs return Promises and use the browser's own `<dialog>` element.

## Under a Content Security Policy

Exponential UI is loaded from files, and its modules start from `data-*` markup, so a page built with it needs no
inline script ([CSP: moving inline scripts into files](../part-4-upgrading/csp-moving-inline-scripts-out.md)). Pass
your page's texts through `exp_config()` rather than an inline script.

## Related pages

- Previous: [Error codes and module results](error-codes-and-module-results.md)
- Next: [admin4 components and tokens](admin4-components-and-tokens.md)
- [ezjscore server functions reference](ezjscore-server-functions-reference.md)
- [YUI removed from Exponential](../../../bc/6.0/yui-removal.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
