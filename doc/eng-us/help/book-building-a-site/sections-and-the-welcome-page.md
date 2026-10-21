---
title: "Sections and the welcome page"
audience: builder, editor
category: books
order: 120
summary: Creating the first folders and sections of the club site, writing the welcome article, making it the front page with IndexPage, and giving it a template of its own with an override rule.
keywords: section, folder, article, IndexPage, welcome page, front page, template override, Match node, override.ini, Building book
type: help
---
# Sections and the welcome page

## Sections

The club site has five parts: the welcome page, news, members, the guestbook and links. Give the **guestbook** a section of its
own: later, the approval workflow and the permission for visitors to write are limited to it, and nothing else on the site
should be affected. Create the section (**Setup > Sections**, name `Guestbook`), and assign it to the guestbook folder when
you have made it. See [Sections](../../guides/sections.md) and
[the node tree and sections](../book-learning/the-node-tree-and-sections.md) for how a new child inherits the section of
its parent.

## The welcome article

Create the content in the administration, as an editor would. In **Content structure** at the root:

1. Choose the class **Article** in the **Create here** list and press the button.
2. Fill in **Title** ("Welcome to the TSCM website!"), **Intro** and **Body**; the Article class of Exponential 6 also has
   a short title, an author, a switch for comments and an **Image** that relates to an image object.
3. Add an image: the image is a separate object in the **Media** tree (upload it there, then choose it in the **Image**
   field), see [Inserting and uploading images](../media-and-uploads.md).
4. **Send for publishing**.

(The book notes a bug in version 3.2 where a first image upload needed repeating. That bug is long gone.)

## Making it the front page

By default the site shows the root node. To show the welcome article instead, find its **node ID**: the number at the end of
the address of the item in the administration (`.../content/view/full/44`) or shown in its details. Then set, in your
siteaccess (`extension/exp_tscm_settings/settings/siteaccess/tscm/site.ini.append.php`):

```ini
[SiteSettings]
IndexPage=/content/view/full/44
```

The shipped value is `/content/view/full/2/`, the root node. Clear the ini cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`)
and load the site: the welcome article is shown with the standard Article template.

## A template of its own

The standard look is not what the club wants for its welcome page. Give that one node a template. The book did it in the
administration's template editor, which still exists (**Design > Template editor**; see
[Template overrides: the basics](../extension-development/part-2-builder/template-overrides-basics.md)). Writing the rule by hand is
the same thing, and is kept in the extension:

```ini
# extension/exp_tscm_settings/settings/siteaccess/tscm/override.ini.append.php
[full_view_welcome]
Source=node/view/full.tpl
MatchFile=full/welcome.tpl
Subdir=templates
Match[node]=44
```

and the template `extension/exp_tscm_design/design/tscm/override/templates/full/welcome.tpl`:

```
{* The welcome page: show the article's own attributes. *}
<h1>{$node.name|wash}</h1>
{attribute_view_gui attribute=$node.data_map.intro}
{attribute_view_gui attribute=$node.data_map.body}
```

(`$node` is the node being shown; `$node.data_map` holds its attributes by identifier.) Clear the override cache, as always for a
new template ([which cache?](../manual/which-cache-do-i-clear.md)) and reload the front page. The rule's block name is free; the
book called it `full_view_welcome` too. Other match keys, for a class, a section, a depth: [Override conditions](../extension-development/part-2-builder/override-conditions.md).

Next: [the news pages](the-news-pages.md).

## Source and license

Adapted from the welcome page and news chapters of of the tutorial *Building an eZ publish site* (written for eZ publish 3.2, 2003), originally by
ez.no and released again under the GNU Free Documentation License by 7x in 2024,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x and Graham Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later
version, with no Invariant Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rewritten for Exponential 6: override rules in an extension, current fetch and view parameters.
