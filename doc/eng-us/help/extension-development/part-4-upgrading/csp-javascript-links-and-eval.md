---
title: "CSP: javascript: links, eval and string timers"
audience: developer, designer
category: extension-development
order: 150
summary: The remaining kinds of inline code a Content Security Policy blocks - javascript: links, eval(), new Function(), string timers, scripts inserted as HTML and JSONP - and how to replace each in an old extension.
keywords: CSP, javascript: link, href javascript, eval, new Function, setTimeout string, unsafe-eval, innerHTML script, JSONP, upgrade
type: help
---
# CSP: javascript: links, eval and string timers

Part 4, chapter 15. After script blocks and event handler attributes, a few smaller kinds of inline code remain.
Each is blocked by a policy without `'unsafe-inline'` or `'unsafe-eval'`, and each is easy to replace once you know
it is there.

## Find them

```bash
cd extension/myoldext
grep -rnE "href\s*=\s*[\"']\s*javascript:|action\s*=\s*[\"']\s*javascript:" --include='*.tpl' design/
grep -rnE "\beval\s*\(|new\s+Function\s*\(|set(Timeout|Interval)\s*\(\s*[\"']" --include='*.js' --include='*.tpl' design/
grep -rnE "\.(html|append|prepend|after|before)\(\s*[^)]*<script|globalEval|callback=\?" --include='*.js' design/
```

## javascript: links

```text
<a href="javascript:void(0);" onclick="myOldExtToggle( 'details' );">Details</a>
<a href="javascript:history.back();">Back</a>
<a href="javascript:window.print();">Print</a>
```

A `javascript:` URL is inline code. Replace each according to what it does:

| Old link | Replacement |
|---|---|
| `href="javascript:void(0)"` with an `onclick` | a `<button type="button">` with a data attribute, handled by your file's click listener |
| `href="javascript:history.back()"` | a real link to the previous page, if you know it (`{$node.parent.url_alias\|ezurl}`), or a button with `data-<ext>-back` |
| `href="javascript:window.print()"` | a button with `data-<ext>-print` |
| `href="javascript:document.forms.x.submit()"` | a real submit button in the form |

A button is the right element for "do something on this page"; a link is the right element for "go somewhere". Using
the right one also fixes keyboard and screen reader behavior. A `<button>` in a form submits the form unless it has
`type="button"`.

```text
<button type="button" class="button" data-myoldext-toggle="details" aria-expanded="false" aria-controls="details">{'Details'|i18n( 'extension/myoldext' )}</button>
<div id="details" hidden>...</div>
```

```javascript
document.addEventListener( 'click', function( e )
{
    var button = e.target && e.target.closest ? e.target.closest( '[data-myoldext-toggle]' ) : null;
    if ( !button )
        return;
    var panel = document.getElementById( button.getAttribute( 'data-myoldext-toggle' ) );
    if ( panel )
    {
        panel.hidden = !panel.hidden;
        button.setAttribute( 'aria-expanded', panel.hidden ? 'false' : 'true' );
    }
} );
```

## eval() and new Function()

`eval()` and `new Function( '...' )` run a string as code. A policy blocks them unless it contains `'unsafe-eval'`,
which Exponential's shipped directives do not. Old code used them for two reasons:

| Use | Replacement |
|---|---|
| Parsing JSON: `eval( '(' + text + ')' )` | `JSON.parse( text )` |
| Calling a function by name: `eval( name + '()' )` | a map of functions: `var actions = { save: save, reset: reset }; actions[name]();` |
| Reading a value by path: `eval( 'config.' + key )` | `config[key]` |

The JSON case is also a security fix: `eval()` runs whatever the server sent, and `JSON.parse()` only reads data.

## String timers

```javascript
// before: a string is code, like eval()
setTimeout( "myOldExtRefresh('" + id + "')", 1000 );

// after: a function
setTimeout( function() { myOldExtRefresh( id ); }, 1000 );
```

`setInterval()` works the same way.

## Scripts inserted as HTML

Old Ajax code often loaded a block of HTML with a `<script>` inside and inserted it, relying on jQuery to run the
script:

```javascript
$( '#panel' ).html( response );   // response contains <script>...</script>
```

Under the policy, jQuery's evaluation of that inline script is blocked. Change the server side so it returns only
data (JSON) or only markup, and keep the code in your file:

```javascript
jQuery.getJSON( url, function( data )
{
    var panel = document.getElementById( 'panel' );
    panel.textContent = data.message;
} );
```

With ezjscore, the server side is a server function and the client calls it through the kernel's JavaScript API; see
[Ajax with ezjscore server functions](../part-3-extension-developer/ajax-with-ezjscore-server-functions.md).

## JSONP

JSONP (`callback=?` in a jQuery URL, or a `<script src="...?callback=...">` built by hand) loads a script from another
host. Under `script-src 'self'` it is blocked unless that host is allowed. Most services that offered JSONP now offer
JSON with CORS; use `fetch()` or `jQuery.getJSON()` without the callback, and add the host to `connect-src` instead.

## Allowing what really must be allowed

Some things cannot be moved into your own files, for example a map service's script. For those, add the exact host
to the policy for the siteaccesses that need it, in the settings, not by loosening it for everyone. How the settings
work, including additional sources per directive, is in
[Content Security Policy](../../../features/6.0/content-security-policy.md).

## Related pages

- Previous: [CSP: event handler attributes to data attributes](csp-event-handlers-to-data-attributes.md)
- Next: [Inline CSS to stylesheets](inline-css-to-stylesheets.md)
- [Content Security Policy for extensions](../part-3-extension-developer/content-security-policy-for-extensions.md)
- [Content Security Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/CSP) on MDN
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
