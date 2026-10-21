---
title: "What is content management?"
audience: user, editor, developer, builder
category: books
order: 200
summary: What a content management system does (collect, manage, publish), what that means in Exponential, and the design step that comes before any click: planning the structure.
keywords: content management, CMS, content, components, versions, workflow, structure, taxonomy, planning, Learning book
type: help
---
# What is content management?

Content management is organizing, categorizing and structuring information so that it can be stored, published and
reused in several ways. A **content management system** (CMS) collects, manages and publishes content. The book
describes four things a CMS does, and each one has a place in Exponential:

| A CMS should | In Exponential |
|---|---|
| Store content as parts (with metadata) or as whole documents | Content is stored as **objects** made of typed **attributes**. A document, such as a PDF, is a file attribute of an object. See [classes, attributes and objects](classes-attributes-and-objects.md). |
| Keep the links between parts | Objects relate to each other, and one object can sit in several places of the tree. See [the node tree and sections](the-node-tree-and-sections.md). |
| Keep track of versions | Every object is versioned. See [versions, translations and related content](versions-translations-and-related-content.md). |
| Run a workflow: approvals, notifications | Workflows run on events such as publishing, and notifications go out by e-mail. See [workflows and triggers](workflows-and-triggers.md). |

## How the content gets out

The book names the protocols: HTTP for the browser, and WebDAV and SOAP for other programs. Today the
site and administration use HTTP, and programs use the REST API ([REST API](../../guides/rest-api-v1-and-v2.md)). The
text format is still a structured
XML of headings, lists, tables, links and embedded objects ([rich text tags](rich-text-tags.md)).

## Design before you build

The book's first practical advice has not aged: **plan the content before you build it**. There are no fixed rules,
only good practices from the people who build taxonomies for document collections. A short list:

1. Write down the kinds of content (articles, products, people, events) and what each one needs. These become content
   classes ([creating a content class](creating-a-content-class.md)).
2. Sketch the tree: what belongs under what. The top entries often become the main menu.
3. Decide who may do what: groups and roles ([roles and policies, step by step](roles-and-policies-step-by-step.md)).
4. Decide the sections: parts of the site that need a different look or different rights.
5. Only then create the first class.

For a small project with experience behind you, the book allows starting at once with a rough outline made in the
administration. For a large project, take the time at the blackboard. The pages
[Designing your content model](../creators/website-creators/designing-your-content-model.md) and
[planning an example site](planning-an-example-site.md) work through it.

Next: [the node tree and sections](the-node-tree-and-sections.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
