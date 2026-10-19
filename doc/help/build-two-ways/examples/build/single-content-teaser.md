---
title: "One content item as a teaser"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1131
summary: The Single content block (Node ID, Show name, Show intro).
keywords: single content, teaser, Single content block, node ID, intro
type: help
---
# One content item as a teaser

The privacy policy as a short teaser on the contact page.

*Life cycle: Build, example 32 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Single content** (group *Placeholders*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Node ID** (90 for Privacy Policy), **Show name** and **Show intro**.
4. **Publish layout**. The pages that use the layout show the block on their next view.

## With templates

1. `{fetch( 'content', 'node', hash( 'node_id', 90 ) )}` and print its name and intro in a template.

## Which to choose

The block for a teaser an editor may retarget; it shows the node's name, intro and link.

## Check

- The teaser links to the item.

## Related pages

- [Find a node ID](../../find-a-node-id.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
