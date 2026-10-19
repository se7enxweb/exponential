---
title: Run-once
audience: designer, developer
category: templates
order: 140
summary: The run-once function prints its body only the first time it is reached during a request, which keeps markup that several included templates need (a sprite, a dialog, a data block) from appearing twice.
keywords: run-once, run once, print once, avoid duplicates, included templates, svg sprite, dialog markup, once per page
type: help
---
# Run-once

This page is for designers whose parts may be included several times on a page but need some markup only once.

## Contents

1. [Syntax](#syntax)
2. [When to use it](#when-to-use-it)
3. [Cautions](#cautions)

## Syntax

From `lib/eztemplate/classes/eztemplateblockfunction.php`:

```tpl
{run-once}
<p>This appears only one time</p>
{/run-once}
```

The first time the block is reached in a request, its body is printed; every later time, it is skipped. The key is the
block's place in the template, so two different `{run-once}` blocks are independent.

## When to use it

A gallery part that is included for every gallery on a page needs one shared dialog for the large image view:

```tpl
{* design/mytheme/templates/parts/gallery.tpl *}
{run-once}
<dialog class="lightbox" id="lightbox">
    <button type="button" class="lightbox-close">{'Close'|i18n( 'design/mytheme' )}</button>
    <img alt="">
</dialog>
{ezscript_require( array( 'lightbox.js' ) )}
{/run-once}
<ul class="gallery">...</ul>
```

For stylesheets and scripts alone, `ezcss_require` and `ezscript_require` already avoid duplicates.

## Cautions

- In a view-cached template, "once" means once when the cache was written; the cached HTML then always contains it.
- Inside a cache-block, the same: the stored output decides.
- Prefer putting shared markup in the pagelayout when every page needs it.

## Related pages

- [Cache-block](cache-block.md)
- [Include](include.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
