---
title: Site map and content inventory
audience: website-creator, editor
category: website-creation
order: 50
summary: Make a content inventory and a site map that translate directly into Exponential's content tree, classes, sections and view modes, with templates for both.
keywords: site map, sitemap, content inventory, content audit, information architecture, content tree, folders, classes, navigation, migration, website creator
type: help
---
# Site map and content inventory

This page is for website creators before the first class or folder is created. A content inventory lists what the
site will hold; a site map shows where it lives. Done the way this page describes, both translate one to one into
Exponential's content tree, classes and sections.

## Contents

1. [How the plan maps to Exponential](#how-the-plan-maps-to-exponential)
2. [The content inventory](#the-content-inventory)
3. [The site map](#the-site-map)
4. [From the site map to the content tree](#from-the-site-map-to-the-content-tree)
5. [Checks before you build](#checks-before-you-build)

## How the plan maps to Exponential

| In your plan | In Exponential |
|---|---|
| A page of the site map | A node (location) in the content tree, usually a Folder or an Article |
| A kind of content ("news item", "event", "product") | A content class |
| A field of that content ("teaser", "date", "image") | An attribute of the class, with a datatype |
| An area with its own rules (members only, intranet) | A section, used in roles ([Sections](../../../guides/sections.md)) |
| The same item shown in two places | One object with two locations, or a relation ([Relations and content reuse](relations-and-reuse.md)) |
| A list of items on a page | A folder whose children are listed, or a fetch in a template |
| A landing page with boxes | A layout of the layouts editor ([Landing pages](landing-pages-with-layouts.md)) |

## The content inventory

One row per kind of content. For an existing site, count what is there; for a new site, estimate.

| Kind | Example | Fields | How many | Who writes it | Languages | Shown where |
|---|---|---|---|---|---|---|
| `<news item>` | `<"New office opens">` | `<title, intro, body, image, date>` | `<200 a year>` | `<communications>` | `<eng, ger>` | `<News, front page>` |
| `<event>` | | | | | | |
| `<page>` | | | | | | |

Columns that matter most:

- **Fields** become the attributes of a class ([Choosing datatypes for your fields](choosing-datatypes.md)).
- **How many** tells you whether a folder needs paging, a date-based structure or search.
- **Who writes it** becomes roles and maybe a section ([Roles and policies](../../../guides/roles-and-policies.md)).
- **Languages** decides translation work ([Building a multilingual site](multilingual-sites.md)).
- **Shown where** decides view modes and fetches ([The pagelayout](the-pagelayout.md)).

## The site map

Draw the tree as an indented list. Mark each entry with its kind and with what makes it special:

```text
Home                         (front page; layout)
├── About us                 (folder)
│   ├── Team                 (article)
│   └── Jobs                 (folder; children: job ad)
├── News                     (folder; children: article, newest first, 10 per page)
├── Events                   (folder; children: event)
├── Shop                     (folder; children: product)        -> shop pages
├── Members                  (folder; section "members")        -> login required
└── Contact                  (feedback form)                     -> information collector
```

## From the site map to the content tree

1. Create the classes the inventory needs, or confirm the shipped ones fit
   ([Content classes for websites](content-classes-for-websites.md)).
2. Create the top-level folders under the root folder (`/content/view/full/2` in the administration).
3. Set the sort order of each folder (for news: by publication date, newest first) on the folder's sub-items
   ([The content model and editing](../../../guides/content-model-and-editing.md)).
4. Create sections for areas with their own permissions and assign them to the subtree
   ([Sections and object states](sections-and-object-states.md)).
5. Decide which classes appear in the menus ([Menus and navigation](menus-and-navigation.md)).
6. Fill in a few real items per class before the design work: templates are designed against real content.

## Checks before you build

- [ ] Every kind of content in the inventory has a class (shipped or new).
- [ ] No two classes differ only by a field or two: one class with an optional field is easier to keep.
- [ ] Every page of the site map has an owner who writes and updates it.
- [ ] Areas with special access are marked and have a section.
- [ ] Lists that will grow large have paging or a date structure.
- [ ] Content that appears in several places has one home and is reused, not copied.
- [ ] The site map is not deeper than visitors will follow; addresses grow with the depth
      ([URL aliases and nice URLs](url-aliases-and-nice-urls.md)).

## Related pages

- [Planning your website](planning-your-website.md)
- [Structuring the content tree](structuring-the-content-tree.md)
- [Designing your content model](designing-your-content-model.md)
- [Running content modeling workshops](../../leadership/content-modeling-workshops.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
