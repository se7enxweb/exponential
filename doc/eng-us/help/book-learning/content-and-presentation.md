---
title: "Content and presentation"
audience: user, developer, designer, builder
category: books
order: 230
summary: Why content, logic and presentation are kept apart, and the two places a page is made: the page layout and the content view.
keywords: separation, presentation, page layout, pagelayout, content view, full view, template, navigation, Learning book
type: help
---
# Content and presentation

One of the basic properties of the system is the separation of **content**, the **application logic** that handles it and
its **presentation** for a browser or for other output (a PDF, a feed, an API). The same object can be shown as a full
page, a line in a list, a teaser, a feed entry, or JSON, each by a template for that purpose.

## Two areas of a page

A page has two main parts:

- The **page layout** (`pagelayout.tpl`): the frame around everything. Menus, header, footer, scripts and styles live
  here. In the book's figure these are the parts marked P.
- The **content view**: the part for the thing asked for (an article, a folder listing, a form, an administration
  screen). Content views come from the kernel modules for the address you asked for. These are the parts marked C.
  The content view is put inside the page layout as `$module_result.content`.

Examples of content views: an article, a folder with its children, a search form, role editing, the output of an
extension's module.

## Structure is not layout, but they can agree

The tree is independent of the page layout. Yet a well-planned tree can also drive the layout: the top-level nodes can
be the main menu, and their children (mostly folders) the entries of a sub-menu. Templates can read the same objects
the same way everywhere. See [menus](../template-operators/menu-and-tree-operators.md).

## Where to learn the templates

- [Templates for builders](../extension-development/part-2-builder/templates-for-builders.md)
- [View modes](../extension-development/part-2-builder/view-modes-for-builders.md)
- [The design chain](../extension-development/part-2-builder/the-design-chain.md)
- [The template language](../template-language/what-the-template-language-is.md)

Next: [roles and policies, step by step](roles-and-policies-step-by-step.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
