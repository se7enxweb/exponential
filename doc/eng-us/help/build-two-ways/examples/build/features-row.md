---
title: "A row of three features"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1102
summary: Three short selling points: the Features block, or a Three columns container with three Cards, or one template.
keywords: features, selling points, Features block, three columns, cards, components
type: help
---
# A row of three features

Three short points side by side: "Plans for every level", "Recipes", "Videos".

*Life cycle: Build, example 3 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Three columns** (group *Containers*) into the zone `main`. Its placeholders are `col_1`, `col_2`, `col_3`.
3. Open the block palette (**+** at the top left) and drag **Card** (group *Basic*) into the zone `col_1`. Do the same for `col_2` and `col_3`.
4. Select the block, open the **Design** tab and fill on each Card **Title**, **Content**, **Image URL**, **Link URL** and **Link label** (default "Read more").
5. **Publish layout**. The pages that use the layout show the block on their next view.

The **Features** block (group *Components*) is the one-block alternative with the shared component fields.

## With templates

1. Write `extension/mysite/design/media/templates/explayouts/tpl_block/features_three.tpl` in your design extension, clear the template cache, and place it with a **Template block** whose **Template name** is `features_three` ([Your own template blocks](../../template-blocks.md)).
2. Loop over three content items (for example the children of a folder "Features").

```smarty
{def $features = fetch( 'content', 'list', hash( 'parent_node_id', <folder node id>, 'limit', 3, 'sort_by', array( 'priority', true() ) ) )}
<div class="container features-three">
{foreach $features as $f}
    <div class="feature"><h3>{$f.name|wash}</h3>{attribute_view_gui attribute=$f.data_map.short_description}</div>
{/foreach}
</div>
{undef $features}
```

## Which to choose

The columns-and-cards build lets an editor change one card without touching the others; a folder-driven template lets editors change the points as content.

## Check

- Three `ngl-card` blocks inside `ngl-three_columns` (block way).

## Related pages

- [The Design tab](../../design-tab-options.md)
- [Your own template blocks](../../template-blocks.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
