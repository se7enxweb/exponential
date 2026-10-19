---
title: Inline CSS to stylesheets
audience: developer, designer
category: extension-development
order: 160
summary: Move an old extension's style blocks and style attributes into stylesheet files loaded through design.ini, so the packer bundles them and a Content Security Policy with style-src self does not drop them.
keywords: inline CSS, style attribute, style block, stylesheet, BackendCSSFileList, FrontendCSSFileList, CSSFileList, packer, CSP, style-src, upgrade
type: help
---
# Inline CSS to stylesheets

Part 4, chapter 16. Old templates are full of `style="..."` attributes and `<style>` blocks. A Content Security
Policy with `style-src 'self'` drops both: the page still works, but looks wrong. They are also hard to maintain,
because the same rule is copied into many templates, and they cannot follow the administration's dark mode. This
chapter moves them into stylesheet files.

## Find them

```bash
cd extension/myoldext
grep -rnoE 'style\s*=\s*"[^"]*"' --include='*.tpl' design/ | sed 's/.*style=//' | sort | uniq -c | sort -rn | head -20
grep -rln '<style' --include='*.tpl' design/
```

The first command counts each distinct `style` value. Usually a few values make up most of them, and each becomes one
class.

## A real one

se7enxweb/AdminAid, commit e5a665c, "The class view carries no inline CSS; its flag uses the shared a4-flag class":

```text
before:
<img src="{$language_code|flag_icon}" alt="{$language_code}" style="vertical-align: middle;" />

after:
<img src="{$language_code|flag_icon}" alt="{$language_code}" class="a4-flag" />
```

`a4-flag` is a class of the administration's shared components (`components.css`), so the extension needed no CSS
of its own. Always look there first: in an admin page, most inline styles of old extensions have a shared class
already. See [admin4 styling for extension pages](admin4-styling-for-extension-pages.md).

## The steps for your own styles

### 1. Create the stylesheet

Put it in the extension's design, under `stylesheets/`, with a name that cannot collide with another extension's:

```text
extension/myoldext/design/standard/stylesheets/myoldext.css
```

Use `design/standard` for styles needed in every design, `design/admin` (or the admin design you target) for styles
of the administration only.

### 2. Turn each inline value into a class

```text
before:
<td style="width: 30%; white-space: nowrap;">...</td>
<div style="display:none" id="myoldext-advanced">...</div>

after:
<td class="myoldext-label">...</td>
<div id="myoldext-advanced" hidden>...</div>
```

```css
.myoldext-label { width: 30%; white-space: nowrap; }
```

Two tips from that example:

- **`display: none` becomes `hidden`.** The `hidden` attribute is not CSS, so the policy does not touch it, and
  scripts toggle it with `element.hidden = false`.
- **Prefix every class with the extension's name** (`myoldext-`). Short names such as `.label` or `.box` collide with
  the kernel's and other extensions' rules.

### 3. Values that are really data

Some inline styles carry a value from the content: a width in percent, a color picked by an editor, a progress bar.
Those cannot be a fixed class. Options:

| Value | Solution |
|---|---|
| A few known values (left, center, right) | one class per value: `myoldext-align-{$align\|wash}` |
| A percentage in steps | classes per step (`myoldext-w-10` ... `myoldext-w-100`), rounded |
| Anything else | a `data-*` attribute and a few lines in your script file that set the style from it (scripts may set `element.style` under the policy; only the attribute in the markup is blocked) |
| A real `<progress>` or `<meter>` | the HTML element with its `value` attribute, no CSS needed |

### 4. Load the stylesheet

Register it in the extension's `settings/design.ini.append.php`:

```ini
<?php /*

[StylesheetSettings]
# frontend designs
FrontendCSSFileList[]=myoldext.css
# administration designs
BackendCSSFileList[]=myoldext.css

*/ ?>
```

`CSSFileList[]`, which old extensions use, still works but is marked deprecated in the kernel's `design.ini` in
favor of the two lists above. Files in these lists are bundled by ezjscore's packer with the rest of the page's
styles, so they cost no extra request.

> **Note**
> `AdminComponentCSSFileList[]` in the same section is the kernel's list of its own admin component stylesheets. The
> comment in `design.ini` says it plainly: "An extension adds its file to BackendCSSFileList, not here."

### 5. Clear the caches and look

```bash
php bin/php/ezcache.php --clear-tag=ini
php bin/php/ezcache.php --clear-id=template,template-block
```

Then reload the page in the browser, bypassing its cache (a hard reload). The packed stylesheet link is part of
cached page output in some designs, which is why the template block cache is cleared too.

### 6. `<style>` blocks

A `<style>` block in a template moves into the same file, with its selectors prefixed if they are not already. If
the block was printed only on one page, its rules now load on every page: that is fine for a few lines, and the
reason to keep selectors specific.

## Check your work

- The searches at the top of this page find nothing (or only values you decided to keep, with a comment).
- The page looks the same in the browser as before, in light and dark mode for admin pages.
- The browser console shows no "Refused to apply inline style" lines.

## Related pages

- Previous: [CSP: javascript: links, eval and string timers](csp-javascript-links-and-eval.md)
- Next: [From YUI to expui](yui-to-expui.md)
- [admin4 styling for extension pages](admin4-styling-for-extension-pages.md)
- [Styling the administration](../../../bc/6.0/admin4-styling.md)
- [CSS and JavaScript in an extension](../part-3-extension-developer/css-and-javascript-in-an-extension.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
