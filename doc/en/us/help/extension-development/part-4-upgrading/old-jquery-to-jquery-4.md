---
title: From old jQuery to jQuery 4
audience: developer, designer
category: extension-development
order: 180
summary: Load the jQuery 4 that ezjscore provides instead of a bundled old copy, and replace the jQuery calls that versions 3 and 4 removed, with the list the layouts editor went through and jQuery Migrate as a safety net.
keywords: jQuery 4, jQuery Migrate, jQuery UI 1.14, ezjsc::jquery, noConflict, removed APIs, .live, .size, .bind, event shorthands, jQuery.trim, upgrade
type: help
---
# From old jQuery to jQuery 4

Part 4, chapter 18. Exponential's administration runs on jQuery 4.0.0, with jQuery Migrate 4.0.2 and jQuery UI
1.14.2, all loaded by ezjscore's packer key `ezjsc::jquery` (and `ezjsc::jqueryUI`). Old extensions often bring their
own jQuery 1.x or 2.x, or call functions that jQuery 3 and 4 removed. This chapter covers both. The kernel's
summary of the change is [jQuery 4 in the admin, and YUI gone](../../../features/6.0/jquery4-and-yui-removal.md).

## Step 1: use the page's jQuery, not your own copy

An extension that ships `jquery-1.7.2.min.js` and loads it next to the kernel's puts two jQuery versions on the page.
The second one replaces the global `jQuery` and `$`, and the plugins registered on the first one disappear, which
breaks the kernel's scripts or yours, depending on the order.

Find copies:

```bash
grep -rlE 'jQuery JavaScript Library v[0-9]|jQuery v[0-9]' extension/myoldext/design/
```

Delete the copy (after checking its license file stays where a remaining library needs it), and load the shared one
before your file:

```text
{ezscript_require( array( 'ezjsc::jquery', 'myoldext.js' ) )}
```

Also remove `jQuery.noConflict( true )` from your code. With your own copy it was harmless; with the page's jQuery it
removes the page's `jQuery` and `$`. se7enxweb/explayouts_ui_api, commit 8273a03, did exactly this for the layouts
editor: "its own copies are gone, jQuery.noConflict(true) is gone (it would have removed the page's jQuery)".

## Step 2: replace removed calls

jQuery 1.9, 3 and 4 removed a number of functions that were deprecated for years, and deprecated others (`.bind()`,
`jQuery.proxy()` and the event shorthands among them). A deprecated function still runs, but jQuery Migrate reports
it and a later version will remove it, so replace both kinds. The ones old extensions use most:

| Old | Replacement |
|---|---|
| `.live( 'click', fn )`, `.die()` | `$( document ).on( 'click', selector, fn )`, `.off()` |
| `.bind( 'click', fn )`, `.unbind()`, `.delegate()`, `.undelegate()` | `.on()`, `.off()` |
| `.size()` | `.length` |
| `.andSelf()` | `.addBack()` |
| `jQuery.browser` | feature detection |
| `jQuery.trim( s )` | `s.trim()` (`String.prototype.trim`) |
| `jQuery.isArray( a )` | `Array.isArray( a )` |
| `jQuery.parseJSON( s )` | `JSON.parse( s )` |
| `jQuery.camelCase( s )` | your own one-line function |
| `jQuery.proxy( fn, ctx )` | `fn.bind( ctx )` |
| `jQuery.isFunction( f )` | `typeof f === 'function'` |
| `jqXHR.success( fn )`, `.error( fn )`, `.complete( fn )` | `.done( fn )`, `.fail( fn )`, `.always( fn )` |
| Event shorthands `.click( fn )`, `.change( fn )`, `.submit( fn )` | `.on( 'click', fn )` and so on; `.trigger( 'click' )` to fire |
| `.attr( 'checked', true )`, `.attr( 'disabled', false )` | `.prop( 'checked', true )`, `.prop( 'disabled', false )` |
| `$( '<div>' ).load( url, fn )` for scripts | `jQuery.ajax()` with JSON; no scripts in responses ([CSP: javascript: links, eval and string timers](csp-javascript-links-and-eval.md)) |

The layouts editor's commit lists the ones it met: "jqXHR.success, jQuery.camelCase, jQuery.proxy, the event
shorthands, attr(name, false) kept as removeAttr as before".

Find them:

```bash
grep -rnE '\.(live|die|bind|unbind|delegate|undelegate|size|andSelf)\(|jQuery\.(browser|trim|isArray|parseJSON|camelCase|proxy|isFunction)\b|\$\.(trim|isArray|parseJSON|camelCase|proxy|isFunction)\b|\.(success|error|complete)\(' --include='*.js' --include='*.tpl' extension/myoldext/design/
```

Some hits are false: `.error(` may be your own object's method, `.bind(` may be JavaScript's own
`Function.prototype.bind`. Read each line.

## Step 3: jQuery Migrate as the safety net, not the fix

jQuery Migrate restores many removed functions and, in its reporting build, writes a warning to the console for each
use. ezjscore loads the quiet build by default, "so older code keeps working without filling the console; the
reporting build is one setting away" (see the kernel page linked above).

Use it like this:

1. On your development copy, switch to the reporting build (the `LocalScripts[jqueryMigrate]` setting of ezjscore's
   `ezjscore.ini` names the file).
2. Use every page and feature of your extension with the console open.
3. Fix every "JQMIGRATE" warning that points at your file.
4. Verify with Migrate switched off: leaving `LocalScripts[jqueryMigrate]` empty loads jQuery 4 alone.

When your extension works without Migrate, it will keep working when an installation drops it.

## Step 4: jQuery UI

jQuery UI 1.14 runs on jQuery 4. Widgets kept their names, but some options and the old CSS framework classes
changed between 1.8 or 1.10 and 1.14. Load it through `ezjsc::jqueryUI`, after `ezjsc::jquery`, and test every
widget your extension uses. For dialogs and date pickers, consider Exponential UI's `exp::dialog` and
`exp::datepicker` instead, which the administration itself uses ([From YUI to expui](yui-to-expui.md)).

## Step 5: plugins

A bundled plugin (a lightbox, a tooltip library) written for jQuery 1.x may use the removed calls inside. Look for a
newer release of the plugin first. If there is none, and the license allows it, fix it in place and write down what
you changed at the top of the file and in your changelog.

## A real one, end to end

The layouts editor is a single-page application that came with jQuery 2.2.4 and jQuery UI 1.10.4 built into its
bundle. Its move to the page's jQuery 4 is told in
[Case study: the layouts editor on jQuery 4](case-study-the-layouts-editor-on-jquery-4.md). The commit records how it
was verified: "by use on Exponential Velocity and PHP-FPM in the admin, admin2 and admin3 designs: opening a layout,
the menus, duplicating, deleting through the modal, sorting by drag, adding blocks from the palette, editing in the
modal, the code editor, the sidebar tabs, inline editing and discarding, with no error, no failed request and no
jQuery Migrate warning." That list is a good model for your own test.

## Related pages

- Previous: [From YUI to expui](yui-to-expui.md)
- Next: [admin4 styling for extension pages](admin4-styling-for-extension-pages.md)
- [jQuery 4 in the admin, and YUI gone](../../../features/6.0/jquery4-and-yui-removal.md)
- [jQuery 4.0 upgrade guide](https://jquery.com/upgrade-guide/4.0/) and [jQuery 3.0 upgrade guide](https://jquery.com/upgrade-guide/3.0/) on jquery.com
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
