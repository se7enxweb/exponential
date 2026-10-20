---
title: "CSP: event handler attributes to data attributes"
audience: developer, designer
category: extension-development
order: 140
summary: Replace onclick, onchange, onmouseover and other event handler attributes with data attributes and one delegated listener in a script file, with the context menu fixes of ezownerchange and expchangeclass.
keywords: CSP, onclick, onmouseover, onchange, onsubmit, event handler attribute, data attribute, delegated listener, addEventListener, context menu, upgrade
type: help
---
# CSP: event handler attributes to data attributes

Part 4, chapter 14. An `onclick="..."` attribute is inline code, exactly like a `<script>` block, and a Content
Security Policy without `'unsafe-inline'` refuses to run it. Old templates have far more of these than script
blocks: a measurement on an installation with many extensions in October 2026 counted 165 to 167 event handler
attributes per administration page before the kernel's own templates were converted
([Content Security Policy](../../../features/6.0/content-security-policy.md)). This chapter turns them into data
attributes handled by a script file.

## Find them

```bash
grep -rnoE '\son[a-z]+\s*=\s*"[^"]*"' --include='*.tpl' extension/myoldext/design/ | sort | uniq -c | sort -rn | head -30
```

The most frequent ones are usually a handful of patterns repeated many times: a confirmation before delete, "check
all" checkboxes, submit on change, open a popup. Convert the pattern once, then apply it everywhere.

## The pattern

1. Put **what** should happen into data attributes on the element: `data-myoldext-confirm="Delete?"`.
2. Write **one** listener per event type in a file, on `document`, that looks at the element the event came from and
   reads its data attributes.
3. Print the file with `ezscript` in the template, once or many times; it sets itself up once.

One listener on `document` handles every matching element, including ones added later by other scripts. That is
called event delegation.

### A confirmation before a delete button

Before:

```text
<input type="submit" class="button" name="RemoveButton" value="{'Remove'|i18n( 'extension/myoldext' )}"
       onclick="return confirm( '{'Really remove the selected items?'|i18n( 'extension/myoldext' )}' );" />
```

After, in the template:

```text
<input type="submit" class="button" name="RemoveButton" value="{'Remove'|i18n( 'extension/myoldext' )|wash}"
       data-myoldext-confirm="{'Really remove the selected items?'|i18n( 'extension/myoldext' )|wash}" />
{ezscript( array( 'myoldext_actions.js' ) )}
```

and in `design/standard/javascript/myoldext_actions.js`:

```javascript
( function()
{
    if ( window.myOldExtActionsReady )
        return;
    window.myOldExtActionsReady = true;

    document.addEventListener( 'click', function( e )
    {
        var el = e.target && e.target.closest ? e.target.closest( '[data-myoldext-confirm]' ) : null;
        if ( el && !window.confirm( el.getAttribute( 'data-myoldext-confirm' ) ) )
        {
            e.preventDefault();
        }
    } );

    document.addEventListener( 'change', function( e )
    {
        var el = e.target;
        if ( el && el.hasAttribute && el.hasAttribute( 'data-myoldext-submit-on-change' ) && el.form )
        {
            el.form.submit();
        }
    } );
} )();
```

The second listener covers another frequent attribute, `onchange="this.form.submit()"`, which becomes
`data-myoldext-submit-on-change` on the `<select>`.

> **Tip**
> In the admin4 design the kernel already has such actions: `data-a4-confirm`, `data-a4-submit-on-change`,
> `data-a4-toggle-checkboxes` and more, handled by `design/admin4/javascript/a4_actions.js`. An extension that runs
> only in admin4 may use them. An extension that runs in every administration design, or on older releases, ships its
> own small file, as the examples below do.

## A real one: context menu entries

Context menus are where old extensions put most of their handlers, because the kernel's own menus used to be built
that way. Two extensions show the two ways the old entries worked.

### An entry that opens a URL: ezownerchange

se7enxweb/ezownerchange, commit a5ebbb8, "The Change owner entries of the context menus need no inline script".
Before, the template added the entry to the kernel's `menuArray` with an inline script and pointed at it with
`onmouseover`:

```text
<script type="text/javascript">
menuArray['ContextMenu']['elements']['menu-change-owner'] = [];
menuArray['ContextMenu']['elements']['menu-change-owner']['url'] = {"/owner/change/%objectID%/(node)/%nodeID%"|ezurl};
</script>
<hr/>
<a id="menu-change-owner" href="#" onmouseover="ezpopmenu_mouseOver( 'ContextMenu' );">{"Change owner"|i18n( 'extension/ezownerchange' )}</a>
```

After, the entry carries its menu and URL in data attributes, and a file does both jobs:

```text
<hr/>
<a id="menu-change-owner" href="#" data-ezownerchange-menu="ContextMenu" data-ezownerchange-url="{'/owner/change/%objectID%/(node)/%nodeID%'|ezurl( 'no' )|wash}">{"Change owner"|i18n( 'extension/ezownerchange' )}</a>
{ezscript( array( 'ezownerchange_menu.js' ) )}
```

The file `ezownerchange_menu.js` (shortened):

```javascript
function register()
{
    var items = document.querySelectorAll( 'a[data-ezownerchange-menu]' );
    for ( var i = 0; i < items.length; i++ )
    {
        var item = items[i], menuID = item.getAttribute( 'data-ezownerchange-menu' );
        if ( item.getAttribute( 'data-ezownerchange-added' ) || !item.id || window.menuArray === undefined )
            continue;
        var menu = window.menuArray[menuID];
        if ( !menu || typeof menu !== 'object' )
            continue;
        if ( !menu['elements'] )
            menu['elements'] = {};
        menu['elements'][item.id] = { 'url': item.getAttribute( 'data-ezownerchange-url' ) };
        item.setAttribute( 'data-ezownerchange-added', '1' );
    }
}
```

and one `mouseover` listener on `document` that calls `ezpopmenu_mouseOver()` for those entries, as the attribute
did. Note `ezurl( 'no' )`: the URL is written without quotes, because the attribute already has them, and `wash`
escapes it for HTML.

### An entry that submits a form: expchangeclass

se7enxweb/expchangeclass, commit 88b60d9. Before:

```text
<a id="menu-class-change" href="#" onmouseover="ezpopmenu_mouseOver( 'ContextMenu' )"
   onclick="ezpopmenu_submitForm( 'menu-form-class-change' ); return false;">{"Change content class"|i18n("design/admin/changeclass")}</a>
```

After:

```text
<a id="menu-class-change" href="#" data-menu-form="menu-form-class-change">{"Change content class"|i18n("design/admin/changeclass")}</a>
...
{ezscript( array( 'expchangeclass_menu.js' ) )}
```

Here the extension uses the kernel's own declaration, `data-menu-form`, which the kernel's `ezpopupmenu.js` handles
from Exponential 6.0.15 on. For an older `ezpopupmenu.js` without declared menus, `expchangeclass_menu.js` submits
the form itself, and does nothing when the kernel already handles it. That way one release of the extension works
with old and new kernels.

> **Warning**
> Never assign `menuArray` anew in an extension (`var menuArray = new Array();` or `menuArray = {};`). The kernel
> creates it and adds the menus declared with `data-menu-*` attributes; replacing it makes the kernel's own context
> menus stop opening. Add to it.

## The handlers you will meet, and their replacements

| Old attribute | Replacement |
|---|---|
| `onclick="return confirm(...)"` | `data-<ext>-confirm` and a click listener |
| `onclick="this.form.submit()"`, `onchange="this.form.submit()"` | `data-<ext>-submit-on-change` and a change listener |
| `onclick="ezjs_toggleCheckboxes( document.myform, 'DeleteIDArray[]' ); return false;"` | `data-<ext>-toggle="DeleteIDArray[]"` and a click listener (in admin4: `data-a4-toggle-checkboxes`) |
| `onclick="window.open( ... )"` | a normal link with `target="_blank"` and `rel="noopener"`, or a data attribute with the URL |
| `onload="..."` on `<body>` | a `DOMContentLoaded` listener in the page's file |
| `onsubmit="return check()"` | a `submit` listener on the form |
| `onmouseover` on a menu entry | the kernel's `data-menu-*` declaration, or your own listener as above |

## Check your work

1. The search at the top of this page finds nothing in your templates.
2. In the browser console of each page, no "Refused to execute inline event handler" lines.
3. Each converted control still does what it did: click every button, change every select, open every menu entry.
4. A test in the extension, like the one in
   [CSP: moving inline scripts into files](csp-moving-inline-scripts-out.md), also fails on `\son[a-z]+\s*=`.

## Related pages

- Previous: [CSP: moving inline scripts into files](csp-moving-inline-scripts-out.md)
- Next: [CSP: javascript: links, eval and string timers](csp-javascript-links-and-eval.md)
- [Context menus and admin integration](context-menus-and-admin-integration.md)
- [Content Security Policy for extensions](../part-3-extension-developer/content-security-policy-for-extensions.md)
- [Event delegation](https://developer.mozilla.org/en-US/docs/Learn_web_development/Core/Scripting/Event_bubbling) on MDN
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
