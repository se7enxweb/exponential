---
title: "A standards-compliant site: markup, style and structure"
audience: designer, builder, developer
category: books
order: 560
summary: The book's case for web standards and its planning method for a department site (page types become data classes), updated from XHTML 1.0 and CSS 2 to HTML5, accessible markup and the content model of Exponential 6.
keywords: web standards, HTML, CSS, accessibility, bandwidth, semantics, validation, page types, data classes, case study, templates, Learning book
type: help
---
# A standards-compliant site: markup, style and structure

The last chapter of the book is a case study: a website for a university department, built with templates that produce
clean markup. Its tools (XHTML 1.0, CSS 2, a browser list that starts with Netscape 4) are history. Its argument and its
planning method are not.

## Why standards

The book's four reasons, in today's words:

- **Accessibility.** Semantic markup (headings that are headings, lists that are lists, tables only for data, text
  alternatives for images) is what screen readers and keyboards rely on. [Required alternative text](../../guides/content-model-and-editing.md#9-required-alternative-text-trimmed-text-lines-and-names-made-by-php)
  helps you keep it so.
- **Bandwidth.** Presentation in a shared style sheet, not in every page, means smaller pages and a style sheet that the browser
  keeps.
- **Future proofing.** Pages that follow the standard keep working in browsers that do not exist yet; pages that depend on
  one browser's quirks do not.
- **Ease of maintenance.** One place changes the look of the whole site.

The standard of today is HTML (the living standard, "HTML5") for markup and CSS for style, checked with the W3C validators
(`validator.w3.org`). Aim for valid markup, and look at the page without the style sheet: it should read in order.

## Why the system helps

The book names two benefits that map onto the standard's own split: **presentation separated from code through templates**,
and **modular, reusable content structures**. In a template that means: the markup is yours, written once in the design's
templates ([template principles](template-principles.md)); content comes from the object's attributes, so no markup is stored in
the content except in [rich text](rich-text-tags.md), where the tags are semantic (headers, lists, tables, embeds), and
the templates turn them into the markup you chose. Style goes in the design's style sheets, loaded from `design.ini`, never inline
(see [The design chain](../extension-development/part-2-builder/the-design-chain.md)); script goes in files, because the site's
content security policy blocks inline script.

## Planning the structure: page types become data classes

The method of the case study works for any site, and is the part to keep. List every page of the planned site and write
next to it what kind of page it is: a **simple page** (a title and text), a **list** of one kind of thing, or a **page that
needs its own data**. The pages that need their own data become content classes. The department site came to these:

| Kind of page | Became |
|---|---|
| Directions, What is geomatic engineering?, Industry, Courses summary | one class, **Simple page** (and the front page picks some of them to show) |
| Publications, grouped by year | class **Publication** |
| Vacancies (PhD, research, other) | class **Vacancy** |
| Research areas with projects | class **Project** |
| Staff, students, categorized | class **Person**, in category folders |
| Spin-off companies and links | class **Link** |
| News | class **News story** (vacancies appear here too) |

Two things the book notes about the process are worth copying: the structure got **simpler** while planning (the news
section, first complicated, ended as one class and a list), and the **front page** was designed from the other pages, taking
a latest story, a featured project and a few quick links from the sections below it, which in templates are fetches
([fetching content](fetching-content-in-templates.md)) in a front page template that overrides the folder's ([overriding templates](overriding-templates.md)).

Do the exercise on paper first ([planning an example site](planning-an-example-site.md)); then create the classes
([creating a content class](creating-a-content-class.md)); then write the templates.

This is the end of the Learning book's chapters in this help. [Back to the start](README.md).

## Source and license

Adapted from chapter 9, "Creating a Standards-Compliant eZ publish Site", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: standards updated from XHTML and CSS 2 to HTML and current practice; the planning method kept.
