---
title: "Frequently asked questions as an accordion"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1113
summary: The Accordion block (Items "Title|Content" per line, Open first item), or the Accordion (List) block over content items.
keywords: FAQ, accordion, questions, Accordion block, Accordion (List), list_accordion
type: help
---
# Frequently asked questions as an accordion

Five questions and answers that open on click.

*Life cycle: Build, example 14 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Accordion** (group *Basic*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Items (Title|Content per line)**, one question per line as `Question|Answer`, and **Open first item**.
4. **Publish layout**. The pages that use the layout show the block on their next view.

When the questions are content items (for example the class *Accordion item*), use **Accordion (List)** (group *Listing*) with a collection instead.

## With templates

1. Fetch the questions as content and print them with `<details>`:

```smarty
{def $faq = fetch( 'content', 'list', hash( 'parent_node_id', <folder node id>, 'class_filter_type', 'include', 'class_filter_array', array( 'ng_accordion_item' ) ) )}
{foreach $faq as $q}
<details><summary>{$q.name|wash}</summary>{attribute_view_gui attribute=$q.data_map.description}</details>
{/foreach}
{undef $faq}
```

The class *Accordion item* (`ng_accordion_item`) has the fields `title` and `description`.

## Which to choose

A few questions on one page: the Accordion block. Many questions reused on several pages: content items and a list.

## Check

- Each question opens and closes with the keyboard too.

## Related pages

- [Manual and dynamic collections](../../manual-and-dynamic-collections.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
