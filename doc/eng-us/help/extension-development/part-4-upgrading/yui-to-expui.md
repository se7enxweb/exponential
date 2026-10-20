---
title: From YUI to expui
audience: developer, designer
category: extension-development
order: 170
summary: YUI 2 and 3 are gone from Exponential 6.0.15. Find an old extension's YUI code and move it to jQuery and Exponential UI (expui): server calls, dialogs, tables, date pickers and events.
keywords: YUI, YUI 2, YUI 3, YAHOO, expui, Exponential UI, jQuery, Exp.io.call, Exp.dialog, exp::datatable, exp::datepicker, ezjsc::yui3, upgrade
type: help
---
# From YUI to expui

Part 4, chapter 17. For years the administration ran on YUI, the Yahoo! User Interface library, and many
extensions used it too: for Ajax calls, dialogs, data tables and date pickers. Exponential 6.0.15 removed YUI from the
kernel, every design and every shipped extension. Code that still asks for YUI gets nothing: `YUI` and `YAHOO` are
undefined, and the page's script stops at the first use. This chapter shows how to move an old extension to
**Exponential UI** (the extension `expui`, JavaScript object `Exp`) and jQuery.

The full reference is [YUI removed from Exponential](../../../bc/6.0/yui-removal.md). Exponential UI's own guide,
with an example for every YUI module, is `doc/CONVERTING_YUI_to_EXPUI.md` in the
[expui repository](https://github.com/se7enxweb/expui).

## Find YUI in your extension

The kernel's documentation gives this search:

```bash
grep -rnE "ezjsc::yui|YUI\(|YAHOO\.|YUI3_config|YUILoader|yui3-js-enabled" extension/myoldext/
```

And on a page in the browser's developer tools: a request whose URL contains `yui` means something still asks for
it, and an error such as "YUI is not defined" points at the line.

## Loading: what to write instead

Old templates load YUI with ezjscore's packer keys:

```text
{ezscript_require( array( 'ezjsc::yui3', 'ezjsc::yui3io', 'myoldext.js' ) )}
```

Those keys load nothing any more. Replace them with jQuery and the Exponential UI modules you need:

```text
{ezscript_require( array( 'ezjsc::jquery', 'exp::core::shared', 'exp::io', 'myoldext.js' ) )}
```

In the administration, Exponential UI's core is already loaded on every page (expui adds its files to the
backend list), so an admin page usually needs only its own file and the modules it uses. A front-end design loads it
itself; the kernel's documentation shows this:

```text
{exp_config()}
{ezscript_require( array( 'ezjsc::jquery', 'exp::core::shared', 'exp::datepicker' ) )}
{ezcss_require( array( 'exp/core.css', 'exp/datepicker.css' ) )}
```

## The replacements

| YUI | Use instead |
|---|---|
| `Y.io.ez( 'fn::args', ... )` (`ezjsc::yui3io`) | `Exp.io.call( 'fn', [args] )` (`exp::io`), or jQuery's `$.ez( 'fn::args', post, callback )` (`ezjsc::jqueryio`) |
| `Y.one`, `Y.all`, `Y.on`, `Y.Node` | jQuery `$( ... )` |
| YUI 2 DataTable and Paginator | `exp::datatable` (`$.fn.expDataTable`) |
| YUI 2 Calendar, `ezdatepicker.js` | `exp::datepicker` |
| `ezmodalwindow`, YUI Dialog | `exp::dialog` (`Exp.dialog`) |
| `ezajaxuploader` | `exp::upload` |
| `ezcollapsiblemenu` | `exp::collapse` (`Exp.collapse`) |
| `fixed_toolbar.js` | `exp::sticky` |
| YUI drag and drop | native drag events or pointer events |
| `.yui3-js-enabled` on `<html>` | `.exp-js` |

## Example: a server call

The most common YUI code in extensions is a call to an ezjscore server function. The server function stays exactly
as it is; only the call changes. From Exponential UI's conversion guide:

```javascript
// before: YUI 3 io-ez
Y.io.ez( 'mysite::latest::' + nodeId, {
    on: {
        success: function( id, r ) {
            var data = r.responseJSON;
            if ( data.error_text ) { alert( data.error_text ); return; }
            Y.one( '#latest' ).setHTML( data.content );
        },
        failure: function() { alert( 'failed' ); }
    }
});

// after: Exponential UI
Exp.io.call( 'mysite::latest', [nodeId] ).then( function( content ) {
    $( '#latest' ).html( content );
}, function( error ) {
    alert( error.message );
} );
```

`Exp.io.call()` returns a Promise. The error case receives an object whose `kind` tells you what happened (the guide
lists `signedout`, `refused`, `server`, `network`, `timeout` and `invalid`), so you can tell a signed-out user from a
broken server. POST data goes into the third argument: `Exp.io.call( 'mysite::save', [], { data: { title: title } } )`.

## Example: a dialog

```javascript
// before: the admin's YUI modal window
var win = new Y.eZ.ModalWindow( { window: '#my-window', width: 650 } );
win.setTitle( 'Rename' ); win.setContent( html ); win.open();

// after
var d = Exp.dialog.create( { title: 'Rename', width: 650 } );
d.setContent( html );
d.open().then( function( value ) { /* the value it was closed with, or null */ } );
```

A confirmation becomes one line: `Exp.dialog.confirm( 'Remove?' ).then( function( ok ) { ... } );`. Exponential UI's
dialogs use the browser's own `<dialog>` element, so the template needs no window markup.

## Example: events and elements

| YUI | jQuery |
|---|---|
| `Y.one( '#menu' )` | `$( '#menu' )` |
| `node.ancestor( 'form' )` | `$el.closest( 'form' )` |
| `node.on( 'click', fn )` | `$el.on( 'click', fn )` |
| `Y.delegate( 'click', fn, '#list', 'li' )` | `$( '#list' ).on( 'click', 'li', fn )` |
| `e.halt()` | `e.preventDefault(); e.stopPropagation();` |
| `YAHOO.util.Dom.get( 'menu' )` | `document.getElementById( 'menu' )` |
| `YAHOO.util.Dom.getRegion( el )` | `el.getBoundingClientRect()` |

## Settings and stylesheets left behind

- `ezjscore.ini` overrides that set `ExternalScripts[yui*]`, `LocalScripts[yui*]`, `LocalScriptBasePath[yui*]` or a
  `[YUI3]` section can be deleted from your extension's settings.
- CSS written for YUI's class names (`.yui-dt-*`, `.yui3-*`) styles nothing now. Remove it, and style the
  replacement with the administration's components ([admin4 styling for extension pages](admin4-styling-for-extension-pages.md)).
- Template overrides of kernel templates renamed during the removal must be renamed too; the reference lists them.

## Supporting older releases at the same time

An extension released for several kernel versions can keep a YUI path for old installations and use Exponential UI
when it is there. Test for the object, not for a version number:

```javascript
if ( window.Exp && Exp.io ) {
    Exp.io.call( 'mysite::latest', [nodeId] ).then( show );
} else if ( window.YUI ) {
    // the old code path, for installations before 6.0.15
}
```

Exponential UI's guide has a section on this ("Keep the YUI version as the fallback"). Drop the old path when you
stop supporting those releases.

## Check your work

- The search at the top of this page finds nothing.
- On each page of the extension: no request containing `yui`, and in the console `typeof YUI` is `"undefined"` while
  everything still works.

## Related pages

- Previous: [Inline CSS to stylesheets](inline-css-to-stylesheets.md)
- Next: [From old jQuery to jQuery 4](old-jquery-to-jquery-4.md)
- [YUI removed from Exponential](../../../bc/6.0/yui-removal.md)
- [jQuery 4 in the admin, and YUI gone](../../../features/6.0/jquery4-and-yui-removal.md)
- [Ajax with ezjscore server functions](../part-3-extension-developer/ajax-with-ezjscore-server-functions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
