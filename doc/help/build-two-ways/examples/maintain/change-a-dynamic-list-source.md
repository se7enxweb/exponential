---
title: "Point a dynamic list at another section"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1302
summary: Change the Parent location of a dynamic collection; the list fills from the new section at once.
keywords: dynamic collection, parent location, change source, section, maintenance
type: help
---
# Point a dynamic list at another section

The "Latest" list should show Healthy eating instead of Fitness.

*Life cycle: Maintain, example 3 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Select the List; on the Content tab change **Parent location** with its folder button (content browser).
2. Check the preview in the middle, **Publish layout**.

## With templates

1. Change the `parent_node_id` of the `fetch( 'content', 'list', ... )` in the template and deploy.

## Which to choose

Dynamic collections let editors retarget lists without a developer.

## Check

- The list shows articles of the new section.

## Related pages

- [Manual and dynamic collections](../../manual-and-dynamic-collections.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
