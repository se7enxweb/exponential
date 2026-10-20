---
title: Content Security Policy for extension authors
audience: developer, app-creator, designer
category: extension-development
order: 320
summary: What a Content Security Policy means for templates and scripts: the modes, why inline code fails, the data-attribute pattern, csp_nonce() and its limits, and finding violations.
keywords: content security policy, CSP, script-src, inline script, onclick, javascript:, style attribute, nonce, csp_nonce, report-only, enforce, csp.log, data attributes, unsafe-inline
type: help
---
# Content Security Policy for extension authors

Part 3, chapter 32. A Content Security Policy (CSP) tells the browser which scripts and styles a page may run. A strict
policy stops injected scripts from running even where a template forgot to escape something. Exponential sends one
from `site.ini [ContentSecurityPolicy]`; the complete reference is
[Content Security Policy](../../../features/6.0/content-security-policy.md). This chapter is what an extension author
needs to know.

## The policy

| Setting | Meaning |
|---|---|
| `Mode` | `disabled`, `report-only` (the browser reports, blocks nothing), `enforce` (the browser blocks), `auto` |
| `Directives[]` | the policy: `script-src 'self'`, `style-src 'self'`, `default-src 'self'` and the rest |
| `Nonce`, `NonceDirectives[]` | a per-request nonce for scripts that must be inline |
| `ReportURI` | where reports go |

`auto` means report-only on administration siteaccesses and off elsewhere; installations can enforce it, and the
administration of the reference installation runs with `Mode=enforce`. **Write your extension for an enforced policy**:
then it works everywhere.

## What fails under `script-src 'self'`

| In a template | Why it fails | Instead |
|---|---|---|
| `<script>doSomething();</script>` | inline script | a file in `design/<design>/javascript/`, loaded through `design.ini` or `ezscript_require` |
| `<button onclick="...">` | inline event handler | `data-*` attribute + `addEventListener` in your file |
| `<a href="javascript:...">` | `javascript:` URL | a button with a data attribute |
| `setTimeout( "code", 100 )`, `eval()`, `new Function()` | string evaluation | real functions |
| `style="color:red"`, `<style>` | inline style under `style-src 'self'` | a class in your stylesheet |

These rules hold in the administration's own code: its templates carry no `<style>`, no `style=""`, no inline scripts,
and a test fails the build if one appears ([Admin pages in the admin4 style](admin-pages-in-admin4-style.md)).

## The data-attribute pattern

Template:

```text
<button type="button" class="a4-btn" data-mysite-greet="{$name|wash}">{'Greet'|i18n( 'extension/mysite_hello' )}</button>
<output data-mysite-greeting></output>
```

Script (`design/standard/javascript/mysite_hello.js`):

```javascript
document.addEventListener( 'click', function ( e ) {
    var button = e.target.closest( '[data-mysite-greet]' );
    if ( !button )
        return;
    var out = document.querySelector( '[data-mysite-greeting]' );
    if ( out )
        out.textContent = 'Hello, ' + button.getAttribute( 'data-mysite-greet' ) + '!';
} );
```

One listener on the document handles every button, including those added later. Data goes from the template to the
script through escaped attributes; the script writes with `textContent`. Many extensions of this project were moved
to this pattern; Part 4 shows real before and after examples
([CSP: event handler attributes to data attributes](../part-4-upgrading/csp-event-handlers-to-data-attributes.md)).

## The nonce, for the rare case

When a script really must be inline, the policy can carry a nonce and the template prints it:

```text
<script nonce="{csp_nonce()}">...</script>
```

`csp_nonce()` returns an empty string when the policy has no nonce. **Never print it inside a `{cache-block}`, a
view-cached template or a static cache**: the cached copy would carry another request's nonce and be blocked. A file is
almost always the better answer.

## Finding violations

- The browser's developer console names every blocked script, handler and style.
- With a `ReportURI`, reports are collected; on the reference installation they are written to `var/log/csp.log`.
- Test your extension's pages in a siteaccess whose `Mode` is `enforce` before release.

## Related pages

- Previous: [Security in extensions](security-in-extensions.md)
- Next: [Admin pages in the admin4 style](admin-pages-in-admin4-style.md)
- [Content Security Policy](../../../features/6.0/content-security-policy.md)
- [CSP: moving inline scripts into files](../part-4-upgrading/csp-moving-inline-scripts-out.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
