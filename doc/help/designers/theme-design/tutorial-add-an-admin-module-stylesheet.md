---
title: "Tutorial: add an admin module stylesheet"
audience: designer, developer
category: theme-design
order: 850
summary: Step by step, style an extension's administration page the admin4 way: components first, then one stylesheet in the extension's admin design, registered in BackendCSSFileList, tokens only, dark and right-to-left ready, deployed through the packer and checked at two widths.
keywords: tutorial, admin stylesheet, extension admin page, BackendCSSFileList, admin4 components, tokens, admin css, module page, extension design admin4, styling admin page
type: help
---
# Tutorial: add an admin module stylesheet

This tutorial is for designers and developers who build an administration page for an extension (`myext`) and need
some CSS of their own. You will first use the shared components, then add one small stylesheet in the right place.
Allow one hour.

## Contents

1. [Step 1: start with components](#step-1-start-with-components)
2. [Step 2: what is left](#step-2-what-is-left)
3. [Step 3: the stylesheet](#step-3-the-stylesheet)
4. [Step 4: register it](#step-4-register-it)
5. [Step 5: deploy and check](#step-5-deploy-and-check)

## Step 1: start with components

Build the page from `.a4-*` classes: `.a4-page`, `.a4-pagehead`, `.a4-card`, `.a4-table`, `.a4-btn`, `.a4-notice`,
`.a4-empty` ([admin4 components](admin4-components.md)). Look at the page in light and dark: most pages need no
CSS of their own.

## Step 2: what is left

Suppose the page shows a timeline of import runs with a colored bar per run, which no component draws. That is the part
for your stylesheet. Write down what the rules must do before writing them.

## Step 3: the stylesheet

`extension/myext/design/admin4/stylesheets/myext-admin.css`:

```css
/* myext admin pages. Tokens only (doc/bc/6.0/admin4-styling.md); light and dark from the tokens. */
.myext-runs { display: grid; gap: var(--a4-space-2); }
.myext-run {
  display: grid;
  grid-template-columns: 10rem minmax(0, 1fr);
  gap: var(--a4-space-3);
  padding-block: var(--a4-space-2);
  border-block-end: 1px solid var(--a4-line);
}
.myext-run-bar {
  block-size: var(--a4-space-2);
  border-radius: var(--a4-radius-s);
  background: var(--a4-accent-soft);
}
.myext-run.is-bad .myext-run-bar { background: var(--a4-bad-bg); }
```

- Your own prefix (`myext-`), one component class plus at most one state.
- Only tokens: no `#fff`, no `12px`; the bar's width comes from `.a4-pct-*` classes on it, not `style=""`.
- Logical properties (`border-block-end`, `padding-block`).

## Step 4: register it

`extension/myext/settings/design.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
DesignExtensions[]=myext

[StylesheetSettings]
BackendCSSFileList[]=myext-admin.css
*/ ?>
```

admin4's head loads `BackendCSSFileList` after the components and module stylesheets, so your rules can refine them.

## Step 5: deploy and check

```bash
./console exp:velocity deploy --packer --allow-root-user
```

(on installations without Velocity: clear the INI, template-block and content caches). Then:

- [ ] light and dark, at 1440 px and at 960 px with scale 2;
- [ ] no horizontal overflow, contrast at least 4.5:1;
- [ ] no `<style>` and no `style=""` in the page's templates;
- [ ] a right-to-left interface language: the page mirrors.

## Related pages

- [admin4 module stylesheets](admin4-module-stylesheets.md)
- [admin4 tokens](admin4-tokens.md)
- [The admin styling rules test](admin-styling-rules-test.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
