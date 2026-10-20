---
title: No inline JavaScript
audience: designer, developer
category: theme-design
order: 620
summary: Writing designs without inline script code: no script blocks with code, no on* handler attributes, no javascript: links; event delegation with data attributes, JSON configuration blocks, progressive enhancement, and the tests that check templates.
keywords: inline javascript, inline script, onclick, event handler attribute, javascript url, addEventListener, event delegation, data attributes, json config, csp script-src, unobtrusive javascript
type: help
---
# No inline JavaScript

This page is for designers and front-end developers. Templates of Exponential designs carry no script code: every line
of JavaScript is in a file under `javascript/`, loaded through ezjscore. The page shows the patterns that replace inline
code.

## Contents

1. [What counts as inline](#what-counts-as-inline)
2. [Handlers: data attributes and delegation](#handlers-data-attributes-and-delegation)
3. [Values for scripts: JSON blocks](#values-for-scripts-json-blocks)
4. [Progressive enhancement](#progressive-enhancement)
5. [Checking](#checking)

## What counts as inline

The same three kinds that the design tests count (`AdminContentSecurityPolicyTest`, `ExtensionInlineScriptsTest`):

- a `<script>` element with code inside (a `<script src="...">`, a JSON data block and a `<template>` are fine);
- an event handler attribute: `onclick=""`, `onchange=""`, `onload=""` ...;
- a `javascript:` URL.

All three are blocked by a policy with `script-src 'self'`.

## Handlers: data attributes and delegation

Markup says *what*; the script file says *how*:

```tpl
<button type="button" class="js-toggle" data-target="site-nav" aria-expanded="false" aria-controls="site-nav">
    {'Menu'|i18n( 'design/mytheme' )}
</button>
<nav id="site-nav" hidden>...</nav>
```

```js
/* design/mytheme/javascript/mytheme.js */
document.addEventListener('click', function (event) {
  var button = event.target.closest('.js-toggle');
  if (!button) return;
  var target = document.getElementById(button.dataset.target);
  var open = button.getAttribute('aria-expanded') === 'true';
  button.setAttribute('aria-expanded', String(!open));
  target.hidden = open;
});
```

One listener on the document handles every toggle, also ones added later by AJAX. The admin uses the same idea with
`data-a4-*` actions (the CSP feature page lists them as the replacement for handlers).

## Values for scripts: JSON blocks

```tpl
<script type="application/json" id="mytheme-map">{json_encode( hash(
    'lat', $node.data_map.location.content.latitude,
    'lng', $node.data_map.location.content.longitude,
    'label', $node.name ) )}</script>
```

```js
var data = JSON.parse(document.getElementById('mytheme-map').textContent);
```

`json_encode` (ezjscore) escapes the values for JSON. A JSON block is data, not code, and is not blocked.

## Progressive enhancement

Build pages that work without the script, then add behavior:

- a menu that is a plain list of links; the script turns it into a toggle;
- a form that submits normally; the script adds live validation or AJAX;
- `<details>` for disclosures, `<dialog>` for dialogs.

admin4's head script sets the class `a4-js` on `<html>` when scripts run, so stylesheets can draw the scripted state
only then. Do the same in your design (set a class from your head script file).

## Checking

```bash
grep -rn -E "on(click|change|load|submit|keyup|keydown|mouseover)=|javascript:|<script>" extension/mytheme/design --include=*.tpl
```

and look at the browser console of every page type with the site's policy in report-only mode.

## Related pages

- [CSP-safe design](csp-safe-design.md)
- [Loading JavaScript](loading-javascript.md)
- [No inline CSS](no-inline-css.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
