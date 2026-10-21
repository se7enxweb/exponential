---
title: "The links page: categories, link objects and a tree menu"
audience: builder, designer
category: books
order: 170
summary: A links section made of category folders and Link objects, shown as an expandable tree, and the same content in two places with a second location.
keywords: links, link class, folder, category, tree menu, treemenu, location, additional location, subtree, section, Building book
type: help
---
# The links page: categories, link objects and a tree menu

The last page of the club site is a collection of links, in categories, shown as a tree: the category folders open and close, and
the links inside them are listed. The tree shape is the point: the structure of the site and the structure of the menu are
the same thing ([the node tree](../book-learning/the-node-tree-and-sections.md)).

## The content

1. Create a folder **Links** at the root and give it a section of its own (**Links**), for the same reason as before: a
   template override can look at the section.
2. Inside it, folders as categories: *Chess sites*, *Educational sites*, *Local sites*, *News sites*, each with a description.
3. In each, objects of the built-in **Link** class: a **Name** and a **Location** (the link, with its own text; the description can stay empty). The book called these fields Title and URL.

## The template

The book's menu is built from the path and the children of the node. Today the operator for exactly this is `treemenu`
([menu and tree operators](../template-operators/menu-and-tree-operators.md)): it takes the path of the current page and returns
the menu items in order, each with its level and a flag for the selected one. A tree style display for the Links folder:

```
{def $items=treemenu( $module_result.path, false(), false(), 1, 3 )}
<ul class="link-tree">
{foreach $items as $item}
    <li class="level-{$item.level}{if $item.is_selected} selected{/if}">
        <a href={$item.url_alias|ezurl}>{$item.text|wash}</a>
    </li>
{/foreach}
</ul>
```

(Indent by the class `level-N` in the style sheet.) The items of a category are the Link objects; show one with the link's own
text and address:

```
{foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                         'class_filter_type', 'include',
                                         'class_filter_array', array( 'link' ) ) ) as $link}
    <p>{attribute_view_gui attribute=$link.data_map.location}</p>
{/foreach}
```

(The Link class has the attributes `name`, `description` and `location`, a datatype that stores a link with its text. The attribute's own view draws it.
Check the identifiers of your site in **Setup > Classes**.) Put the template in a
rule for the **section** `Links` (`Match[section_identifier]=links`, source `node/view/full.tpl`) so that every folder under it is drawn as
a tree.

## The same content at two places

The book finishes with a small trick: a link that belongs to two categories. You do not copy the object. You give it a second
**location**: in the edit form, **Locations > Add locations** and choose the second category. Both categories now list the same object, and a
change shows in both ([the node tree and sections](../book-learning/the-node-tree-and-sections.md),
[creating and editing objects](../book-learning/creating-and-editing-objects.md)).

## What you have

A welcome page, news with an archive, a members list, a moderated guestbook and a tree of links: five pages, two extensions,
a handful of classes and templates, no PHP. Where next: the full list of what the club could ask for is in
[Building a site without writing code](../extension-development/part-2-builder/building-a-site-without-code.md) (members only areas,
menus with cache) and in [Learning Exponential](../book-learning/README.md).

## Source and license

Adapted from the links page chapter of of the tutorial *Building an eZ publish site* (written for eZ publish 3.2, 2003), originally by
ez.no and released again under the GNU Free Documentation License by 7x in 2024,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x and Graham Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later
version, with no Invariant Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rewritten for Exponential 6: the treemenu operator and the Link class as shipped.
