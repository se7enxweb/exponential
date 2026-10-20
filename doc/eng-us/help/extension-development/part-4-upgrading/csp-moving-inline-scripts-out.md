---
title: "CSP: moving inline scripts into files"
audience: developer, designer
category: extension-development
order: 130
summary: Move an old extension's inline script blocks into JavaScript files loaded with ezscript, pass template values through data attributes, and keep the page working under a Content Security Policy without unsafe-inline.
keywords: CSP, Content Security Policy, inline script, ezscript, data attributes, script-src self, unsafe-inline, JavaScript file, template, upgrade
type: help
---
# CSP: moving inline scripts into files

Part 4, chapter 13. This is the first of three chapters about the Content Security Policy (CSP). It covers the
largest job: `<script>` blocks written into templates. After it, the page's code lives in files, and the page works
under the policy `script-src 'self'`, which the administration of Exponential 6 is moving to.

## Why this matters now

Exponential 6 sends a Content Security Policy from `site.ini [ContentSecurityPolicy]`. With `Mode=auto`, the default,
it is **report-only** on the administration: the browser reports in its console what the policy would block, and
blocks nothing. With `Mode=enforce`, the browser blocks inline scripts, and an old extension's buttons and menus are
drawn but do nothing, without an error on the page. The details, including how to turn it on, are in
[Content Security Policy](../../../features/6.0/content-security-policy.md).

The kernel's administration templates carry no inline code any more. What is left is in extensions, and fixing those
is the purpose of this chapter. Part 3 explains the policy for new code:
[Content Security Policy for extensions](../part-3-extension-developer/content-security-policy-for-extensions.md).

## See what the policy reports

1. Open one of your extension's pages in the administration.
2. Open the browser's developer tools, tab **Console**.
3. Reload. Each line such as "Refused to execute inline script because it violates the following Content Security
   Policy directive" (wording differs per browser) is one inline script or one handler.

The search from the inventory finds them in the files:

```bash
grep -rnE '<script[^>]*>[^<]|<script>\s*$|<script type="text/javascript">\s*$' --include='*.tpl' extension/myoldext/design/
```

## The pattern, step by step

Here is a real one: the setup page of se7enxweb/sevenx_authentication_2fa, commit e4c0319. The page shows the
enrolment of an authenticator app only while that method is picked.

### Before

```text
{if $show_enrol}
<script type="text/javascript">
{literal}
(function () {
    var form = document.getElementById( 'tfa-setup-form' ), enrol = document.getElementById( 'tfa-enrol' );
    if ( !form || !enrol ) return;
    function update() {
        var picked = form.querySelector( 'input[name="Method"]:checked' );
        enrol.hidden = !picked || picked.value !== 'totp';
    }
    form.addEventListener( 'change', function ( e ) { if ( e.target && e.target.name === 'Method' ) update(); } );
    update();
})();
{/literal}
</script>
{/if}
```

### Step 1: move the code into a file

Create `design/standard/javascript/<extension>_<page>.js` in the extension. Give it a name that cannot collide with
another extension's file, because all designs share one `javascript/` lookup. The file from the commit:

```javascript
(function () {
    function start() {
        var form = document.getElementById( 'tfa-setup-form' ), enrol = document.getElementById( 'tfa-enrol' );
        if ( !form || !enrol || form.getAttribute( 'data-tfa-ready' ) ) return;
        form.setAttribute( 'data-tfa-ready', '1' );
        function update() {
            var picked = form.querySelector( 'input[name="Method"]:checked' );
            enrol.hidden = !picked || picked.value !== 'totp';
        }
        form.addEventListener( 'change', function ( e ) { if ( e.target && e.target.name === 'Method' ) update(); } );
        update();
    }
    if ( document.readyState === 'loading' ) { document.addEventListener( 'DOMContentLoaded', start ); } else { start(); }
})();
```

Two things were added, and you need both in almost every converted script:

- **Wait for the page.** The inline script ran exactly where it stood, after the form. A file may run earlier or
  later, so it waits for `DOMContentLoaded` when the page is still loading.
- **Set up once.** The template may be printed twice, and the file may be loaded twice. A marker
  (`data-tfa-ready`) stops the second run.

### Step 2: print the file where the script stood

```text
{if $show_enrol}
{* the enrolment is shown while "totp" is picked: sevenx_authentication_2fa_setup.js, no inline script *}
{ezscript( array( 'sevenx_authentication_2fa_setup.js' ) )}
{/if}
```

`ezscript` is ezjscore's template function that prints a `<script src=...>` tag for a file of the design chain.
Use `ezscript` in the page content, not `ezscript_load`: the kernel's notes explain that `ezscript_load` in a page's
content prints every file collected so far at that place and can change their order.

### Step 3: values from the template become data attributes

Inline scripts often contain template values:

```text
<script type="text/javascript">
var myOldExtUrl = {'/myoldext/save'|ezurl};
var myOldExtConfirm = '{'Really delete?'|i18n( 'extension/myoldext' )}';
</script>
```

Put them on the element the script works on, washed for HTML:

```text
<form id="myoldext-form" method="post" action={'/myoldext/save'|ezurl}
      data-myoldext-confirm="{'Really delete?'|i18n( 'extension/myoldext' )|wash}">
```

and read them in the file:

```javascript
var form = document.getElementById( 'myoldext-form' );
var question = form.getAttribute( 'data-myoldext-confirm' );
```

For structured values, JSON in one attribute:

```text
<div id="myoldext-chart" data-myoldext-conf="{json_encode( hash( 'max', $max, 'labels', $labels ) )|wash}"></div>
```

```javascript
var conf = JSON.parse( document.getElementById( 'myoldext-chart' ).getAttribute( 'data-myoldext-conf' ) || '{}' );
```

The `wash` operator escapes the value for an HTML attribute, so a quote in a translation cannot end the attribute.
The old inline form had that problem too: a translation with an apostrophe broke the script.

### Step 4: a test that keeps it that way

The commit added a test that reads the templates without the kernel and fails on any inline script:

```php
<?php
$root = dirname( __DIR__ );
$found = array();
$it = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $root . '/design', FilesystemIterator::SKIP_DOTS ) );
foreach ( $it as $file )
{
    if ( substr( (string)$file, -4 ) !== '.tpl' )
    {
        continue;
    }
    $source = preg_replace( '/\{\*.*?\*\}/s', '', file_get_contents( (string)$file ) );
    if ( preg_match( '/<script\b(?![^>]*\bsrc\s*=)(?![^>]*type\s*=\s*["\']application\/(?:ld\+)?json["\'])[^>]*>/i', $source ) )
    {
        $found[] = substr( (string)$file, strlen( $root ) + 1 );
    }
}
echo $found ? 'FAIL: inline script in ' . implode( ', ', $found ) . "\n" : "PASS: no inline script\n";
exit( $found ? 1 : 0 );
```

It allows a `<script src=...>` and a JSON data block (`type="application/json"`), and ignores template comments.
More in [Tests and CI for an upgraded extension](tests-and-ci-for-an-upgraded-extension.md).

## Special cases

| Inline script that... | Do this |
|---|---|
| Sets global configuration for a library (`var MyLib_config = {...}`) | a JSON data block `<script type="application/json" id="myoldext-conf">...</script>`, read with `JSON.parse( el.textContent )` |
| Is printed once per item in a loop | one file, and one data attribute per item |
| Calls `document.write()` | build the elements with DOM methods in the file |
| Must run before the page is drawn (a theme or color guard) | a small file loaded in `<head>` as a normal, blocking script |
| Comes from a third-party service's embed code | load the service's script file and add its host to `script-src`, as the kernel documents for maps and reCAPTCHA |

## What not to do

- Do not add `'unsafe-inline'` to the policy to make the extension work. It switches off the protection for every
  page of the siteaccess, not only for yours.
- Do not move the code into a file and keep calling it from `onclick="..."`. The attribute is inline code too; see
  [CSP: event handler attributes to data attributes](csp-event-handlers-to-data-attributes.md).
- Do not add `?v=` or other version queries to script URLs. ezjscore's packer puts the newest modification time of
  the files it bundles into the bundle's name, so browsers fetch a changed file anyway (after the caches are cleared).

## Related pages

- Previous: [Modernizing composer.json](modernizing-composer-json.md)
- Next: [CSP: event handler attributes to data attributes](csp-event-handlers-to-data-attributes.md)
- [Content Security Policy](../../../features/6.0/content-security-policy.md)
- [Case study: two-factor authentication without inline scripts](case-study-two-factor-authentication.md)
- [CSS and JavaScript in an extension](../part-3-extension-developer/css-and-javascript-in-an-extension.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
