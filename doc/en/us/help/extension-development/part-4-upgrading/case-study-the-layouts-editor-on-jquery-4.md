---
title: "Case study: the layouts editor on jQuery 4"
audience: developer
category: extension-development
order: 240
summary: How the layouts editor, a single-page application with jQuery 2.2.4 and jQuery UI 1.10.4 in its bundle, moved to the admin's jQuery 4: the plan, the removed calls, the renamed globals, the test by use.
keywords: case study, layouts editor, explayouts_ui_api, explayouts_ui, jQuery 4, jQuery UI 1.14, bundle, noConflict, Bootstrap, single-page application, upgrade
type: help
---
# Case study: the layouts editor on jQuery 4

Part 4, chapter 24. Most extensions use a little jQuery. A few ship a whole JavaScript application with its own
copy of jQuery inside a bundle. The layouts editor of Exponential Layouts (the extensions explayouts_ui_api and
explayouts_ui) was one of them. This chapter tells how it moved to the administration's jQuery 4 in two commits of
1 October 2026, so you can plan the same move for a large script of your own.

## The starting point

| Part | Before |
|---|---|
| The editor (`explayouts_ui_api`, a single-page application) | a bundle, `netgen-layouts.js`, with jQuery 2.2.4 and jQuery UI 1.10.4 built in, calling `jQuery.noConflict( true )` |
| The layouts admin pages (`explayouts_ui`) | `layouts-ibexa.js` with jQuery 3.7.1 built in, and Bootstrap 4.6.2 |
| The administration around them | jQuery 4.0.0, jQuery Migrate 4.0.2, jQuery UI 1.14.2 from ezjscore 1.4.0 |

So a page of the layouts editor carried up to three jQuery versions. The code came from an upstream project for a
different platform (credited in the extensions), with that project's names in its globals and page tags.

## The plan

1. Load the page's jQuery and jQuery UI before the bundle.
2. Remove the copies from the bundle, and `noConflict( true )` with them.
3. Replace every call jQuery 3 and 4 removed or deprecated.
4. While every global and page tag is being touched anyway, give them the extension's own names.
5. Test by use, in every administration design, on both application servers.

## Step 1: load the page's libraries first

The editor's shell template (`app.tpl`) gained, before the deferred bundle:

```text
{* The app bundle uses the page's jQuery and jQuery UI (ezjscore's), so they
   load first: these run before the deferred scripts below. *}
{ezscript( array( 'ezjsc::jquery', 'ezjsc::jqueryUI' ) )}
```

The comment says why the order matters: the bundle is loaded with `defer`, so it runs after the parser has passed
these normal scripts.

## Step 2: remove the copies and noConflict

From the commit message of se7enxweb/explayouts_ui_api, 8273a03: "the bundle uses the page's jQuery: its own copies
are gone, jQuery.noConflict(true) is gone (it would have removed the page's jQuery)".

`noConflict( true )` is the trap in this kind of move. It was correct while the bundle brought its own jQuery: it
handed the global names back to whatever was there before. Against the page's jQuery it removes `jQuery` and `$`
from the page, and every other script on the page stops.

## Step 3: the removed calls

The commit lists what it replaced: "jqXHR.success, jQuery.camelCase, jQuery.proxy, the event shorthands,
attr(name, false) kept as removeAttr as before".

The last item is a detail worth noticing. In old jQuery, `.attr( 'disabled', false )` removed the attribute. The
replacement keeps that behavior exactly (`removeAttr`) instead of switching to `.prop()`, because the editor's own
code read the attribute later. When you replace a removed call, keep the behavior, not just the name.

The admin pages' script (se7enxweb/explayouts_ui, e3d2723) needed less, because its Bootstrap 4.6.2 "accepts
jQuery 4"; it lost its built-in jQuery 3.7.1 and used the page's.

The kernel's list of removed calls and their replacements is in
[From old jQuery to jQuery 4](old-jquery-to-jquery-4.md).

## Step 4: names

Both commits renamed the upstream project's prefixes in globals, page tags (`<meta>` names the JavaScript reads),
session keys and file names to `explayouts` and `exp` names, for example the meta tags `nglayouts-route-prefix` and
`nglayouts-base-path` became `explayouts-route-prefix` and `explayouts-base-path`, and `layouts-ibexa.js` became
`layouts-exponential.js`. These were the extension's internals: nothing outside the extension read them, so they
could be renamed in the same commit as everything that used them. (Compare
[Naming and branding during an upgrade](naming-and-branding-during-an-upgrade.md): names that other code uses are
never renamed like this.)

One file was found to be dead on the way: `layouts-admin.js`'s "content browser stopped at once without Netgen's
page tags, so none of it ever ran on these pages", so the pages stopped loading it. An upgrade is a good time to
find out what never ran.

## Step 5: test by use

Both commits say how they were verified, and the lists are worth copying. The editor: "Verified by use on
Exponential Velocity and PHP-FPM in the admin, admin2 and admin3 designs: opening a layout, the menus, duplicating,
deleting through the modal, sorting by drag, adding blocks from the palette, editing in the modal, the code editor,
the sidebar tabs, inline editing and discarding, with no error, no failed request and no jQuery Migrate warning."
The admin pages: "every layouts page loads with no page error, no console error, no missing file and no jQuery
Migrate warning."

Three things make this a good test:

- **Every feature, by hand**, not just "the page loads". A broken drag-and-drop shows only when you drag.
- **Every design and both servers**, because each loads scripts in its own order.
- **A clear pass criterion**: no error, no failed request, no Migrate warning.

## What came after

These two commits were about jQuery. The editor's shell template still had inline scripts (the translations and a
few values), and its script links still carried version queries; the CSP measurement of 17 October 2026 still
listed explayouts_ui_api among the extensions with inline code
([Content Security Policy](../../../features/6.0/content-security-policy.md)). One large upgrade at a time is fine,
as long as each one is finished and released before the next begins.

The commit also stated a requirement: "Needs ezjscore 1.4.0 or later." When your extension starts depending on a
newer library from another extension, say so in the commit, the changelog and the `composer.json` requirement.

## Related pages

- Previous: [Case study: from eZChangeclass to expchangeclass](case-study-expchangeclass.md)
- Next: [Case study: two-factor authentication without inline scripts](case-study-two-factor-authentication.md)
- [From old jQuery to jQuery 4](old-jquery-to-jquery-4.md)
- [jQuery 4 in the admin, and YUI gone](../../../features/6.0/jquery4-and-yui-removal.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
