---
title: "Checklist: CSP"
audience: designer, developer
category: theme-design
order: 890
summary: A Content Security Policy checklist for a design: no inline scripts, handlers, javascript links or inline styles, configuration as JSON data, pre-paint code in files, nonces never in cached output, third-party origins documented, and a report-only period before enforcing.
keywords: csp checklist, content security policy checklist, inline script check, inline style check, nonce, report-only, enforce, csp.log, design security review
type: help
---
# Checklist: CSP

This page is for designers and developers who make sure a design works under an enforced Content Security Policy. Background:
[CSP-safe design](csp-safe-design.md).

## Contents

1. [Templates](#templates)
2. [Scripts and styles](#scripts-and-styles)
3. [Origins](#origins)
4. [Before enforcing](#before-enforcing)

## Templates

- [ ] No `<script>` element with code (a `src` script, a JSON data block and a `<template>` are fine).
- [ ] No `on*=` handler attributes.
- [ ] No `javascript:` URLs.
- [ ] No `<style>` blocks and no `style=""` attributes ([No inline CSS](no-inline-css.md)).
- [ ] `csp_nonce()` only where a file is truly impossible, and never inside a cache-block or a view-cached template.

```bash
grep -rn -E "<script>|on(click|change|load|submit|input|keyup|keydown|focus|blur|mouseover)=|javascript:|<style|style=\"" \
     extension/mytheme/design --include=*.tpl
```

## Scripts and styles

- [ ] Every script is a file under `javascript/`, loaded with the ezjscore operators ([No inline JavaScript](no-inline-javascript.md)).
- [ ] Values for scripts come from `data-*` attributes or `<script type="application/json">` blocks.
- [ ] Code that must run before the first paint is a small file loaded in the head.
- [ ] No `eval`, `new Function` or string arguments to `setTimeout` in the design's scripts.

## Origins

- [ ] Every third-party origin the design needs is listed in the theme's README, with the directive it needs.
- [ ] Fonts, icons and libraries are local unless there is a reason.

## Before enforcing

- [ ] The siteaccess ran with `[ContentSecurityPolicy] Mode=report-only` while every page type and every dialog was used.
- [ ] The browser console and `var/log/csp.log` (with `ReportEndpoint=enabled`) show no reports for the design.
- [ ] For admin designs and extensions: `AdminContentSecurityPolicyTest` and `ExtensionInlineScriptsTest` pass.
- [ ] Then `Mode=enforce` on one siteaccess first, INI cache cleared, PHP reloaded.

## Related pages

- [CSP-safe design](csp-safe-design.md)
- [Content Security Policy](../../../features/6.0/content-security-policy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
