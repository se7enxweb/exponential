---
title: Add a view type
audience: designer, developer
category: build-two-ways
order: 520
summary: Give an existing block type a new look that editors can choose per block - one INI line and one template in your design extension. Worked example list_compact for the List block, from the INI to the editor's Design tab to the published page.
keywords: view type, ViewTypes, BlockDefinition_list, list_compact, block template, explayouts/block/list, Design tab, mysite, new block look
type: help
---
# Add a view type

A **view type** is one look of a block type. The List block offers `list`, `grid`, `list_numbered`, `list_zigzag`,
`list_accordion` and `grid_featured`. This page adds a seventh, `list_compact`: a plain list of links with dates.

## Contents

1. [Declare it](#1-declare-it)
2. [Write its template](#2-write-its-template)
3. [Clear the caches](#3-clear-the-caches)
4. [Choose it in the editor](#4-choose-it-in-the-editor)
5. [The result](#5-the-result)

## 1. Declare it

`extension/mysite/settings/explayouts.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

# A new view type for the List block: a compact list of links
[BlockDefinition_list]
ViewTypes[]=list_compact

*/ ?>
```

Without a reset line (`ViewTypes[]` alone) the value is added to the six view types of
`extension/explayouts/settings/explayouts.ini.append.php`. Check it:

```text
$ ./console exp:ini where explayouts.ini/BlockDefinition_list/ViewTypes site
...
In effect:
      ViewTypes[]=list_compact
      ViewTypes[]=list
      ViewTypes[]=grid
      ViewTypes[]=list_numbered
      ViewTypes[]=list_zigzag
      ViewTypes[]=list_accordion
      ViewTypes[]=grid_featured
```

## 2. Write its template

The media design renders a List block with `explayouts/block/list.tpl`, which includes
`explayouts/block/list/<view type>.tpl`. So the new template is
`extension/mysite/design/media/templates/explayouts/block/list/list_compact.tpl`:

```smarty
{if $block.values.items|count|gt( 0 )}
<ul class="list-compact">
{foreach $block.values.items as $lc_item}
    <li class="list-compact-item">
        <a href={$lc_item.url_alias|ezurl}>{$lc_item.name|wash}</a>
        <time datetime="{$lc_item.object.published|datetime( 'custom', '%Y-%m-%d' )}">{$lc_item.object.published|l10n( 'shortdate' )}</time>
    </li>
{/foreach}
</ul>
{/if}
```

`$block.values.items` holds the content nodes of the block's collection, manual or dynamic.

## 3. Clear the caches

```text
./console exp:cache clear --tag=template,content,ini
```

## 4. Choose it in the editor

Select a List block, **Design** tab, **View type**: `list_compact` is offered. Choosing it hides the options that
belong to the other view types (columns, item view type).

![The Design tab with View type list_compact](images/vt-01-design-tab-list-compact.webp)

**Note:** before explayouts_ui_api 1.3.29, changing only the view type to one like this, which leaves no item view
type to choose, did not save the block; the change was stored together with the next change of another field (on the
test copy a key typed and deleted in **CSS class**). From 1.3.29 the change is saved at once. Check the block header:
it shows the saved view type in capitals, here `LIST_COMPACT`.

![The block now shows LIST_COMPACT in its header and the items as a list](images/vt-02-draft-list-compact.webp)

## 5. The result

After **Publish layout** the prefooter of the article pages shows the compact list:

![The Most popular articles band as a plain list of four links with dates](images/vt-03-prefooter-list-compact-site.webp)

Style `.list-compact` in your design's stylesheet ([Styling your blocks](styling-your-blocks.md)).

## Related pages

- [How a block maps to its templates](block-to-template-map.md)
- [Add a block type](add-a-block-type.md)
- [Your own design extension](your-own-design-extension.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
