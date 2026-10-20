---
title: The Layouts editor, a tour
audience: editor, website-creator, designer, admin
category: build-two-ways
order: 250
summary: Every part of the Layouts editor screen as it is in Exponential 6.0.15+ - the top bar, the block palette and its groups, zones, block headers, the properties panel with its Content and Design tabs, Discard, Cancel and Publish layout.
keywords: layouts editor, tour, palette, block types, zones, block header, properties panel, Content tab, Design tab, Discard, Publish layout, Cancel, drag and drop
type: help
---
# The Layouts editor, a tour

Open any layout with **Layouts > Layouts** (or **Shared layouts**) and its name. The editor fills the browser window.

![The editor with the Prefooter layout: top bar, the zone MAIN with a Column, a Title and a List, the properties panel on the right](images/pf-02-prefooter-editor.webp)

## Contents

1. [The top bar](#the-top-bar)
2. [The block palette](#the-block-palette)
3. [Zones and blocks](#zones-and-blocks)
4. [The properties panel](#the-properties-panel)
5. [Leaving the editor](#leaving-the-editor)

## The top bar

| Part | Does |
|---|---|
| Layout name with a pencil | the layout's name |
| **Discard** | throw away the draft (asks first) |
| **Cancel** | shown while a block is selected |
| **Publish layout** | make the draft the published layout |
| `...` | more actions |

![The Discard changes question: Are you sure you want to discard your changes to the layout draft? with Cancel and Discard](images/ed-01-discard-confirm.webp)

## The block palette

The **+** button at the top left opens the palette. Its groups and blocks on this installation:

| Group | Blocks |
|---|---|
| Basic | Title, Text, Button / Link, Rich text, External video, Map, Markdown, HTML snippet, Image, Spacer, Divider, Card, Alert, Badge, Progress bar, Accordion, Tabs |
| Components | Hero, About, Features, Logos, Quote, Lead |
| Listing | List, Grid, Zig-Zag (List), Accordion (List) |
| Gallery | Gallery, Slider, Thumb gallery, Grid gallery, Sushi bar, Carousel |
| Containers | Column, Two columns, Three columns, Four columns |
| Placeholders | Template block, Full view, Single content |

Drag a block from the palette into a zone. Extensions can add their own ([Add a block type](add-a-block-type.md)).

## Zones and blocks

Zones are the dashed areas, labelled with their identifier (HEADER, MAIN, PRE_FOOTER ...). A **linked** zone shows the
blocks of a shared layout ([Shared layouts and zone linking](shared-layouts-and-zone-linking.md)).

Each block has a header: a drag handle, the **view type** in capitals, the **block type's name**, and a `...` menu. The
middle of the block shows a preview. Containers (Column, Two columns ...) hold other blocks in their placeholders.

## The properties panel

Click a block to select it. The panel on the right has two tabs:

- **Content**: the block label, and for listing blocks the **Collection type** and the **Items**
  ([Manual and dynamic collections](manual-and-dynamic-collections.md)).
- **Design**: the view type and the block's options ([The Design tab](design-tab-options.md)).

Changes are saved into the draft as you make them and the preview in the middle follows, with one exception on the
Design tab ([Known editor behaviors](known-editor-behaviors.md)).

## Leaving the editor

Nothing is visible on the site until **Publish layout**. A draft stays in the database when you leave the editor
(`./console exp:layouts list --drafts` lists open drafts); **Discard** removes it.

## Related pages

- [Change the prefooter in the Layouts editor](prefooter-in-the-layouts-editor.md)
- [The layouts editor](../layouts-editor.md)
- [Known editor behaviors](known-editor-behaviors.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
