---
title: The Design tab
audience: editor, website-creator, designer
category: build-two-ways
order: 260
summary: The options on the Design tab of a block in the Layouts editor - view type, item view type, columns, background color and image, vertical whitespace, pagination, CSS class and ID, container - and the classes they put on the block's wrapper in the page.
keywords: Design tab, view type, item view type, number of columns, background color, background image, vertical whitespace, pagination, CSS class, CSS ID, wrap in container, container size, block options
type: help
---
# The Design tab

Select a block, then **Design**. The options depend on the block type; a List block shows the most. The media design's
`explayouts/block/block.tpl` turns them into classes on the block's wrapper, so a designer can style them.

![The Design tab of a List block: View type grid, Item view type, Number of columns, background, whitespace, pagination, CSS class, CSS ID, Wrap in container](images/pf-09-list-design-tab.webp)

## The options

| Option | Values on this installation | In the page |
|---|---|---|
| Block label | free text, shown in the editor | |
| View type | the block type's `ViewTypes[]`, for List: list, grid, list_numbered, list_zigzag, list_accordion, grid_featured | `ngl-vt-<view type>`, and the template of that view type |
| Item view type | Standard, Standard (with intro), Overlay, Card, Card (with intro), Line, Mini, List item, List item (with intro); only the ones that suit the view type are offered | the content view used for each item |
| Number of columns | 2, 3, 4 or 6 columns (grid views) | the grid template for that number |
| Set background color | a color of the design (the header column uses `primary`, the Prefooter column `secondary`) | `bg-color-<color>` |
| Set background image | an image | `with-bg-image` |
| Use vertical whitespace | on top and on bottom: None and the design's sizes (the Prefooter uses `medium` and `small`) | `whitespace-top-<size> whitespace-bottom-<size>` |
| Enable pagination | list blocks: page through the items | an `ajax-collection` wrapper with page navigation |
| CSS class | your class | added to the wrapper |
| CSS ID | an ID | `id="..."` on the wrapper |
| Wrap in container | with **Container size** (Regular ...) | an inner `<div class="container">` |

Other blocks show their own fields here too, from their handler: the Template block its **Template name**, the Notice
block of [Add a block type](add-a-block-type.md) its **Content**.

## Example

The Prefooter's Column has a background color and vertical whitespace on, which gives:

```html
<div class="ngl-block ngl-column ngl-vt-column whitespace-top-medium whitespace-bottom-medium bg-color-secondary">
```

## Related pages

- [The Layouts editor, a tour](layouts-editor-tour.md)
- [How a block maps to its templates](block-to-template-map.md)
- [Styling your blocks](styling-your-blocks.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
