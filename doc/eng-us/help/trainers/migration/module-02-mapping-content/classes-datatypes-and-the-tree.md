---
title: "Lesson 2.1: Classes, datatypes and the content tree"
audience: trainer, admin, developer
category: training
order: 21
summary: Learners map the content types of any source system to Exponential classes, attributes and datatypes, place items in the content tree with remote ids, and write the mapping table before any import code. 60 minutes.
keywords: migrating to exponential, content model mapping, class, attribute, datatype, ezstring, ezxmltext, location, main location, remote id, taxonomy, eztags, translations, mapping table
type: help
---
# Lesson 2.1: Classes, datatypes and the content tree

Whatever the source, a migration ends in the same building blocks: classes with attributes, objects with versions
and translations, and locations in a tree. This lesson teaches the mapping table that the common checklist asks for
before any import code is written.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 2: Mapping content to Exponential](README.md) |
| Duration | 60 minutes (25 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Developers and administrators who design the import |
| Prerequisites | [Module 1](../module-01-planning-a-migration/README.md); an administrator account on a training installation |

## Learning objectives

After this lesson, learners can:

- translate the terms of other systems (content type, field, entry, category) into Exponential terms;
- choose a datatype for each source field from the mapping table;
- decide between locations, keywords and tags for a source's taxonomy;
- explain why every imported object gets a remote id that names its source;
- write a mapping table with the columns the checklist asks for.

## Trainer's notes

### How to present it

Source: [Mapping content to Exponential](../../../../install/migration/content-model-mapping.md), sections 1 to 4
and 8.

1. **The vocabulary** (5 minutes). Show the table "The building blocks at a glance": content type becomes **content
   class**, field becomes **attribute** of a **datatype**, entry becomes **content object**, category tree becomes
   **locations**, and so on.
2. **Datatypes** (7 minutes). Show the table of section 2. Stress three rows: rich text (`ezxmltext`, HTML converted;
   scripts, styles and inline event handlers do not survive, by design), references (`ezobjectrelationlist`, resolved
   by remote id after all items exist), and repeating groups (no nested repeater datatype: child objects or
   `ezmatrix`).
3. **The class in the admin** (4 minutes). On the projector, open **Setup > Classes**, the **Content** group, then
   **Article**. Show **IDENTIFIER**, **OBJECT NAME PATTERN**, **URL ALIAS NAME PATTERN** and the list of
   **Attributes** with their identifiers and datatypes (for example **Text line**).
4. **Objects and the tree** (5 minutes). One source item becomes one object with a remote id (`wp-post-1234`);
   translations are versions of the same object; each object has one main location, extra categories can be
   additional locations. Sort order per parent node maps the source's menu order.
5. **Taxonomy** (4 minutes). Section 8: a category tree that is also the navigation becomes folders; flat tags
   `ezkeyword` or `eztags`; controlled vocabularies `eztags`.

### Common questions

**One class per source type, or one class for everything?** The checklist says: decide per type. One class per source
type that has its own fields is the default advice of the mapping page.

**Should we import old revisions?** The mapping page says they are rarely worth importing; archive them instead.

**What is "Always available"?** An object can be shown in languages it is not translated into; decide per class
(mapping page, section 3).

**Why short identifiers?** Templates, settings and imports refer to them; they should be short and stable.

### Pitfalls

- **Mapping in code first.** Write the table first; code written before it is rewritten later.
- **Relations in the first pass.** Related items may not exist yet. Import in dependency order and resolve relations
  by remote id in a second pass.
- **Changing a class on a live site during the exercise.** Learners only look at classes in this lesson; they do not
  press **Edit**.

## Hands-on exercise

Use your own training installation and an administrator account. You only look; do not change any class.

1. In the admin, open **Setup > Classes**.
2. Open the class group **Content** and then the class **Article** (on a standard site package; take a similar class
   if your installation has no Article).
3. Note the **IDENTIFIER**, the **OBJECT NAME PATTERN** and the **URL ALIAS NAME PATTERN**.
4. Write down every attribute: its name, its identifier and its datatype as the page shows it.
5. Copy the mapping table of [the checklist, section 3](../../../../install/migration/checklist.md#3-map-the-content-model)
   into your notes, with the columns **Source type**, **Source field**, **Exponential class**, **Attribute
   (identifier)**, **Datatype**, **Conversion**, **Notes**.
6. Fill it in for a WordPress post with these fields: `post_title`, `post_content`, `post_excerpt`, featured image,
   categories, tags, author, SEO title and description. Use the
   [WordPress mapping table](../../../../install/migration/wordpress.md#2-mapping-wordpress-to-exponential) and
   the datatype table of the [mapping page](../../../../install/migration/content-model-mapping.md#2-content-classes-and-datatypes).
7. Mark each source field that has no attribute in the Article class of your installation. For each, decide: add an
   attribute to a new class, or drop it consciously.
8. Write the remote id pattern you will use for posts and for attachments.

## Expected result

- A list of the Article attributes with identifiers (for example `title` as **Text line**).
- A mapping table where every WordPress field is mapped or marked as dropped, with a conversion noted for
  `post_content` (HTML to XmlText) and categories mapped to locations or tags.
- Remote id patterns such as `wp-post-<id>` and `wp-attachment-<id>`.
- The trainer checks two tables: is every field covered, and are relations planned for a second pass?

## Quiz

1. What is a "field" of another system called in Exponential?
   A. A location B. An attribute of a datatype C. A section D. An object state
2. Which datatype receives a rich HTML body, and what happens to inline scripts?
3. An article belongs to three categories. How can the tree express that?
4. Why does every imported object get a remote id made from the source id? Give two reasons.
5. True or false: Exponential has a nested "repeater" datatype for repeating groups of fields.

<details>
<summary>Answers</summary>

1. **B** ([mapping page, section 1](../../../../install/migration/content-model-mapping.md#1-the-building-blocks-at-a-glance)).
2. `ezxmltext`; scripts, styles and inline event handlers do not survive, by design
   ([mapping page, section 2](../../../../install/migration/content-model-mapping.md#2-content-classes-and-datatypes)).
3. One main location and two additional locations, or keywords or tags
   ([mapping page, section 4](../../../../install/migration/content-model-mapping.md#4-locations-the-content-tree)).
4. Re-running the import updates instead of duplicating, and relations and redirects can be resolved from it
   ([mapping page, section 3](../../../../install/migration/content-model-mapping.md#3-objects-versions-and-translations)).
5. **False.** Use child objects of a small class, or `ezmatrix` (mapping page, section 2).

</details>

## Further reading

- [Mapping content to Exponential](../../../../install/migration/content-model-mapping.md): every building block in
  the terms of a migration.
- [The common migration checklist, section 3](../../../../install/migration/checklist.md#3-map-the-content-model):
  the mapping table.
- [Content model and editing](../../../../guides/content-model-and-editing.md): classes and objects for
  administrators.

## Related pages

- [Module 2: Mapping content to Exponential](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
