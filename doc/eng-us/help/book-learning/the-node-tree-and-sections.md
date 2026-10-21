---
title: "The node tree and sections"
audience: user, editor, developer, builder
category: books
order: 210
summary: Content objects versus the nodes that place them in the tree, how a branch inherits its section, and what sections are used for (permissions and presentation).
keywords: node, node tree, content object, location, section, inheritance, main node, tree, structure, Learning book
type: help
---
# The node tree and sections

## Objects and nodes

The structure of a site is a **tree**. The book's example tree holds a document branch, ordered galleries and a forum.
The key idea of this chapter is a distinction that is easy to miss and important to keep:

- A **content object** is the information itself: its attributes, versions and translations. It has one object ID.
- A **node** is a *placement* of an object in the tree. It has a node ID, a parent node and a place in the order.

One object can have more than one node, that is, appear in several places, without being copied. Delete one placement
and the object stays where it is still placed. The name belongs to the object, and is shared by the versions of one
translation. In the administration, the places of an object are its **locations** (the **Locations** part of an item
in **Content structure**), and one of them is the **main location**: the one whose address is the object's own.

The address of a node on the site comes from its names along the path (see [Links, URL aliases and
wildcards](../../guides/urls-and-aliases.md)); `/content/view/full/<node id>` is the plain form and always works.

## Sections

A **section** is a virtual collection of nodes that belong together by meaning or by function. Sections do two jobs:

1. **Permissions.** A role can be limited to a section ("editors may edit articles in the News section").
2. **Presentation.** A template can look at the section and show a different menu or even a different page layout for it.

Rules the book states and that still hold:

- A node belongs to one section.
- A new child node **inherits** the section of its parent, so assigning a section to the top of a branch sets it for
  what is created below it later. (What already exists below needs **Assign content** on the section's page, which gives the section to the
  chosen item and everything below it.)
- There is always at least one section, the default one.

How to create and assign sections, and what the section also controls, is in [Sections](../../guides/sections.md) and
[Sections, roles and access for builders](../extension-development/part-2-builder/sections-roles-and-access-for-builders.md).

## Try it

1. Open **Content structure** and note the tree on the left.
2. Open any item and look at its **Locations**: the main location, and any other.
3. Add a second location for an item (the **Locations** window of its edit form) and see the same item appear in two branches. Change the title
   in one place, and both show it.

Next: [classes, attributes and objects](classes-attributes-and-objects.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
