---
title: "Creating a content class"
audience: admin, builder, developer
category: books
order: 250
summary: The steps and the choices in creating a class: groups, name, identifier, the object name pattern, attributes, and what happens to existing objects when you change it.
keywords: content class, class group, identifier, object name pattern, URL alias pattern, attribute, class edit, Learning book
type: help
---
# Creating a content class

A class is where a kind of content gets its shape. The book's steps still apply; the screens moved.

1. **Setup > Classes** (the class groups). Classes are kept in **class groups**, which group by content type or by
   function, for example the *Users* group with the user and user-group classes. See [Class groups](../../guides/class-groups.md).
2. Open or create a group, then **New class**.
3. A class has a few generic properties:
   - **Name**: what editors see.
   - **Identifier**: a short, stable machine name (`article`, `product`). Templates refer to it, so choose it once and
     do not change it.
   - **Object name pattern**: how the name of each object is made from its attributes, such as `<title>` or
     `<first_name> <last_name>`. This matters: the name is the base of the address of the object on the site.
   - **URL alias name pattern**: since the book, a separate pattern for the address; leave it empty to use the name.
   - **Container**: whether objects of this class may have children.
   - **Always available**: whether an object of this class is shown in another language when it has no translation in the one asked for.
4. Add attributes. Pick a datatype from the list ([the datatypes tour](the-datatypes-tour.md)), give each a name and an
   identifier, and set the datatype's own options (a maximum length, a default, a size limit).
5. Mark attributes **Required**, **Searchable**, **Information collector** and **Translatable** as needed. An attribute
   that is not translatable (a photograph) is the same in every language.
6. **Store**. Put the class in more than one group if it belongs to several.

The new class is available in the **Create here** list of the places where a role lets the user create it.

## Changing a class later

- A changed **object name pattern** does not rename published objects. The new pattern is used the next time an object is
  published, so a mass change needs a republish.
- Removing an attribute removes its content from all objects of the class. Back up first.
- Adding an attribute is safe: existing objects show it empty until edited.
- To move one object to another class, see [Changing the content class of an object](../manual/how-do-i-change-the-content-class-of-an-object.md).

More: [Content classes for builders](../extension-development/part-2-builder/content-classes-for-builders.md),
[The content model and editing content](../../guides/content-model-and-editing.md) and
[Choosing datatypes](../creators/website-creators/choosing-datatypes.md).

Next: [the datatypes tour](the-datatypes-tour.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
