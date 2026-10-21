---
title: "Creating and editing objects"
audience: editor, admin, builder
category: books
order: 280
summary: Creating an object from a class, the locations bar (main location, move, add, remove), what extra locations do and do not do, and information collectors.
keywords: create object, create here, edit, locations, main location, move, additional location, information collector, draft, Learning book
type: help
---
# Creating and editing objects

## Creating an object

Objects are created in **Content structure** (and **Media**). Browse the tree to the place where the new object should
live, choose the class in **Create here** (the list holds only the classes your role may create there), and press the
button. The edit screen opens with the class's attributes, the required ones marked. From this moment there is a
**draft** with an object ID and a node ID; nothing is public until you **Publish**.

The step-by-step version for editors is in [Editing content](../editing-content.md) and
[Publishing and versions](../publishing-and-versions.md). The reference is
[The content model and editing content](../../guides/content-model-and-editing.md).

## Locations

The edit screen shows where the object is placed:

- The **main location** is the placement whose position gives the object its address and its default section. One
  location is always main.
- **Move** changes the main location: browse the tree and choose the new parent.
- The **remove** icon deletes one or more of the extra locations of this object.
- **Add locations** opens a browse view where you choose more places for the same object.

The main use of an extra location is an extra **link** to the object: an article lives in the archive tree and also
appears on the front page branch. When the full view of an object is asked for at that place, the object is shown, with
its own full view.

Two things from the book to remember:

- **Children are not inherited.** If an object with children gets another location, the children do not appear there.
  A template that must show them looks up the main node and fetches its children (see [fetch for builders](../extension-development/part-2-builder/fetch-for-builders.md)).
- The section of a new location is the one of its parent; the object keeps the section of its main location.

## Information collectors

Some attributes can be marked as **information collectors** in the class. An object of such a class is then shown as a
form: the data a visitor sends is stored (or mailed to a set address) instead of changing the object. Use it for small
surveys, contact forms and polls. For anything the visitor should publish, create a normal object in a place the role
allows instead. The collected data is in the administration, see
[Collected information](../../guides/collected-information.md) and [Seeing form submissions](../manual/how-do-i-see-form-submissions.md).

Next: [versions, translations and related content](versions-translations-and-related-content.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
