---
title: "Designing view modes"
audience: theme-designer
category: theme-design
order: 9
summary: How theme designers plan the views of each content class - full, line, embed, embed-inline and custom modes such as card - with a view matrix, how few modes a design really needs, and what to hand the developer.
keywords: theme designer, view modes, full view, line view, embed, embed-inline, card, teaser, view matrix, custom view mode, node_view_gui
type: help
---
# Designing view modes

This page is for theme designers who plan how each kind of content looks in every place it appears. In Exponential
the same object is drawn by different templates depending on where it is shown: its own page, an item in a list, an
embed in another text. Each of these is a **view mode**. The technical side, including which templates exist and how a
mode is chosen, is in [View modes](../designers/templates/view-modes.md); this page is about deciding which modes your
design needs and drawing them.

## Contents

1. [The modes you will meet](#the-modes-you-will-meet)
2. [The view matrix](#the-view-matrix)
3. [How many modes a design needs](#how-many-modes-a-design-needs)
4. [Designing each mode](#designing-each-mode)
5. [Adding a mode of your own](#adding-a-mode-of-your-own)
6. [What to hand the developer](#what-to-hand-the-developer)

## The modes you will meet

| Mode | Where it appears | Design focus |
|---|---|---|
| `full` | the object's own page, inside the frame | reading, hierarchy, all fields |
| `line` | an item in a list of children, search results and some blocks | scanning: title, date, one line of context |
| `embed` | an object placed by an editor inside rich text, as its own block | fits in a text column; image or file embeds are common |
| `embed-inline` | an object placed inside a line of text | no more than a link or a small label |
| custom (for example `card`) | lists, grids, layout blocks | whatever your design defines |

The shipped kernel design has further short modes (`text_linked`, `tiny`, `listitem`, `thumb`, `print` and others);
most designs leave them as they are.

## The view matrix

Make a matrix of classes against modes and mark which cells your design uses:

| Class | full | line | card | embed | embed-inline |
|---|---|---|---|---|---|
| article | yes | yes | yes | yes | link |
| folder | yes | yes | yes | no | link |
| image | yes | no | no | yes | no |
| event | yes | yes | yes | yes | link |
| file | download | yes | no | yes | link |

Every "yes" is one mockup and later one template (or one case in a shared template). Every "no" still renders
something, through the default template for that mode, so check what it looks like and decide whether it is acceptable.

## How many modes a design needs

Fewer than you think. Each extra mode is a template per class to write, test and maintain. A typical content site
needs `full`, `line`, one card mode and `embed`. Before adding a mode, ask whether the difference can be:

- a **CSS context** instead: the same `line` markup styled differently inside a sidebar block;
- a **layout block view type** instead: a grid or list style editors choose per block
  ([Designing layout blocks](designing-layout-blocks.md));
- an **image alias** instead: the same card with a different crop.

## Designing each mode

**Full.** Design the reading experience: headline, metadata, lead, body, related content. Draw it with the optional
fields empty as well ([Designing for editors](designing-for-editors.md)). The frame around it is the pagelayout, which
you design once ([Designing headers and footers](designing-headers-and-footers.md)).

**Line.** One line or two: title as a link, date or type, at most one line of intro. Lines appear in long lists, so
vertical rhythm matters more than decoration ([Designing content listings and cards](designing-content-listings-and-cards.md)).

**Card.** Image, title, short text, maybe a label. Cards appear in grids of two to four columns and must work with
and without an image.

**Embed.** Must fit the text column of any rich text field, at any width, and must look intentional between two
paragraphs. Images embedded by editors usually get their size from the editor's choice of alias in the online editor.

**Embed-inline.** Keep it to inline elements; anything block-level breaks the paragraph around it.

## Adding a mode of your own

A custom mode is a new name (for example `card`) with a default template for all classes and overrides for the
classes that need their own. The steps are in [Custom view modes](../designers/templates/custom-view-modes.md), and
the media theme shows examples ([A tour of the media theme](../designers/theme-design/media-theme-tour.md)). Name your
modes after their role (`card`, `teaser`) rather than their look (`blue_box`), because the look will change.

## What to hand the developer

- The view matrix, with "yes", "no" and "default is fine" in every cell.
- One mockup per "yes", with the shortest and longest content.
- For each mode, the fields used, from the [mapping sheet](mapping-a-design-to-content-classes.md#the-mapping-sheet).
- Which image alias each mode uses ([Designing image crops and art direction](designing-image-crops-and-art-direction.md)).

## Related pages

- [View modes](../designers/templates/view-modes.md)
- [Full view](../designers/templates/full-view.md), [Line view](../designers/templates/line-view.md), [Embed view](../designers/templates/embed-view.md)
- [Node templates by class](../designers/templates/node-templates-by-class.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
