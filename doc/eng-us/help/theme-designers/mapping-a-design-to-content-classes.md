---
title: "Mapping a design to content classes"
audience: theme-designer
category: theme-design
order: 6
summary: How theme designers turn mockups into a map of content classes, fields and view modes - reading a class, what each common datatype can look like, a mapping sheet to fill in, and what to ask the developer when a field is missing.
keywords: theme designer, content class, fields, attributes, datatypes, mapping, content model, mockup, design inventory, ezstring, ezxmltext, ezimage, relation
type: help
---
# Mapping a design to content classes

This page is for theme designers who need to connect their mockups to the content an installation really stores. In
Exponential every page shows an object of a **content class**, and every visible piece of text or media comes from
one of the class's **fields** (attributes). A mapping sheet, filled in before you draw details, keeps the design and
the content model in step. The content model itself is the developer's work:
[Designing a content model](../cms-developers/designing-a-content-model.md) explains how it is made.

## Contents

1. [Why map at all](#why-map-at-all)
2. [Reading a class](#reading-a-class)
3. [What each kind of field can look like](#what-each-kind-of-field-can-look-like)
4. [The mapping sheet](#the-mapping-sheet)
5. [A worked example](#a-worked-example)
6. [When the design needs a field that does not exist](#when-the-design-needs-a-field-that-does-not-exist)

## Why map at all

A mockup can show anything; a template can only show what a field holds. Three problems are found by mapping and
are expensive to find later:

- a visual element with no field behind it (a subtitle, a reading time, a colored label);
- a field the design forgot (an author, a caption, a date) that editors fill in and expect to see;
- an element drawn as structured that is stored as free rich text, or the other way round.

## Reading a class

Ask for an editor or administrator account on a development installation, then open **Setup > Classes** in the
administration and open the class. For each field note the **identifier** (for example `title`, `intro`, `image`),
the **datatype**, and whether it is **required**. Required fields are always there; every optional field needs a
design for "empty" ([Designing for editors](designing-for-editors.md)).

A developer can also give you the list from the command line or from the class definition in the project's package.
Background: [Content classes and attributes](../cms-developers/content-classes-and-attributes.md).

## What each kind of field can look like

| Datatype | Holds | Design questions |
|---|---|---|
| `ezstring` | one line of text | Longest realistic length? Does it wrap or truncate? |
| `eztext` | plain text with line breaks | Paragraphs or one block? |
| `ezxmltext` | rich text: headings, lists, links, embeds, tables | Style every element editors can insert ([Rich text templates](../designers/templates/rich-text-templates.md)) |
| `ezimage` | one image with alternative text | Which crops and sizes? ([Designing image crops and art direction](designing-image-crops-and-art-direction.md)) |
| `ezobjectrelation`, `ezobjectrelationlist` | links to other objects | Shown as embed, card, list or link? |
| `ezdate`, `ezdatetime`, `eztime` | dates and times | Format per language ([Dates, numbers and l10n](../designers/templates/dates-numbers-and-l10n.md)) |
| `ezboolean` | yes or no | Usually changes behavior, not appearance |
| `ezselection` | one or more choices from a list | A label, a badge, a filter? |
| `ezkeyword` | free keywords | Tags, links to tag pages? |
| `ezurl`, `ezemail` | a link or an address | Link text when the label is empty? |
| `ezauthor` | one or more authors | Byline layout for one, two, many |
| `ezbinaryfile`, `ezmedia` | a file, audio or video | Download link with type and size, a player? |
| `ezmatrix` | a small table | Narrow screens? |

The kernel ships view templates for each datatype under `content/datatype/view/`, so every field already renders
somehow; your design decides how ([Datatype view templates](../designers/templates/datatype-view-templates.md)).

## The mapping sheet

Make one row per visible element of each mockup:

| Mockup element | Class | Field identifier | Datatype | Required | View modes | If empty |
|---|---|---|---|---|---|---|
| Headline | article | `title` | ezstring | yes | full, line, card | never empty |
| Lead paragraph | article | `intro` | ezxmltext | yes | full, card | never empty |
| Hero image | article | `image` | ezobjectrelation | no | full, card | layout without image |

Keep it next to the mockups in the project repository. Developers write templates from it, and reviewers check
against it.

## A worked example

On the demonstration installation the Article class has `title` (required), `short_title`, `author`, `intro`
(required), `body`, `enable_comments` and `image`, where `image` is a relation to an Image object, not an image
field. That has three design consequences:

1. The card can use `short_title` when it is filled, and `title` otherwise: design a card title that works at both
   lengths.
2. The image comes from another object, so its alternative text and caption belong to that Image object: design the
   caption from there.
3. `body` is optional: an article may be intro only, so the full view needs a design that does not look broken without
   a body.

Check your own installation; classes differ between sites.

## When the design needs a field that does not exist

Do not fake it in the template. Write a short request to the developer: the element, why it matters, the datatype
you expect, whether it is required, and in which views it appears. Adding a field is a change to the content model
and to editors' work, so it is decided together ([Working with developers](working-with-developers.md)).

Things that are better **not** stored as fields: values computed from other fields (reading time), values that belong
to the frame (the site name), and purely decorative choices. Those belong in the template or in settings.

## Related pages

- [Designing view modes](designing-view-modes.md)
- [Node templates by class](../designers/templates/node-templates-by-class.md)
- [Nodes, objects and attributes](../designers/templates/nodes-objects-attributes.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
