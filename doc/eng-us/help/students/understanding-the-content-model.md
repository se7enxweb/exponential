---
title: Understanding the content model
audience: student
category: learning
order: 18
summary: An explanation of Exponential's content model for learners - content classes, attributes and datatypes, objects, versions and translations, nodes and the content tree, sections and object states - with a worked example, the most common confusions, and exercises to check your understanding.
keywords: content model, content class, attribute, datatype, object, version, translation, node, location, content tree, section, object state, explanation, learner
type: help
---
# Understanding the content model

The content model is the idea every Exponential role uses, from editor to developer. Its README calls the "fully
customizable and extendable content model" the system's most notable feature
([Press, media and influencers: start here](../press/press-start-here.md#exponential-in-one-paragraph)). This page
explains it for learners, with one worked example. The reference is
[The content model and editing content](../../guides/content-model-and-editing.md).

## Contents

1. [The words](#the-words)
2. [A worked example](#a-worked-example)
3. [Objects and nodes](#objects-and-nodes)
4. [Versions and translations](#versions-and-translations)
5. [Sections and states](#sections-and-states)
6. [Common confusions](#common-confusions)
7. [Check your understanding](#check-your-understanding)

## The words

| Term | Meaning |
|---|---|
| **Content class** | A type of content: a name, an identifier and a list of attributes (for example Article: Title, Intro, Body, Image) |
| **Class group** | A folder of classes in the class editor (Content, Users, Media, Setup) |
| **Attribute** | One field of a class, with a datatype |
| **Datatype** | The kind of one attribute: `ezstring` (one line), `eztext` (several lines), `ezxmltext` (rich text), `ezimage`, `ezobjectrelationlist` and so on |
| **Content object** | One piece of content, an instance of a class; has versions (draft, published, archived) and languages |
| **Node / location** | A place of an object in the content tree; one object can have several |

Source: [The content model and editing content](../../guides/content-model-and-editing.md#1-how-the-content-model-fits-together).
The datatypes available for new attributes are the `AvailableDataTypes[]` list of `settings/content.ini`; the guide
counts 35 entries in the shipped list, and extensions can add more.

## A worked example

Think of a school newspaper:

```text
Class "Article"          attributes: Title (Text line), Intro (Text block), Body (Rich text), Image (Image)
  |
  +-- Object 4711        "School garden opens"        versions 1, 2, 3 (3 is published)
        |                                             translations: eng-US (main), ger-DE
        +-- Node 120     Home / News / School garden opens
        +-- Node 233     Home / Garden club / School garden opens
```

One **class** describes what every article has. One **object** is one article. It appears in the tree at two
**nodes**: under News and under Garden club. Editing it once changes both places, because there is only one object.

## Objects and nodes

- The **content tree** is the hierarchy of nodes shown in the administration; node 1 is its root
  ([Glossary](../../glossary.md#c)).
- An item's page shows both numbers, **Node** and **Object** ([A tour of the administration](../admin-tour.md#an-items-page)).
- The **Locations** tab of an item lists its nodes. Adding a location does not copy the object.
- The **Content finder** opens an item from its number, its path or its address
  ([Searching](../searching.md#open-an-item-by-its-number-or-address)).

Developers meet the same distinction in templates: `$node` is a node, and `$node.data_map` gives the attributes of
its object ([Templates and design](../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes)).

## Versions and translations

- Each save of an edit creates a **version**; an unfinished edit is a **draft**. The number of archived versions
  kept is limited per class (`[VersionManagement] DefaultVersionHistoryLimit` in `settings/content.ini`, 45 by
  default) ([The content model and editing content](../../guides/content-model-and-editing.md#3-create-and-edit-an-object)).
- A **translation** is a language version of an object. Each object has a **main language**, and can be set to be
  **always available**, so it is shown in its main language where no listed language exists
  ([Content languages](../../guides/content-languages.md#6-main-language-and-always-available)).
- Publishing one translation does not change the others ([Languages](../languages.md#translate-an-item)).

## Sections and states

- A **section** is a label on content that selects a design and permissions ([Glossary](../../glossary.md#s),
  [Sections](../../guides/sections.md)). Roles can be limited to a section.
- An **object state** is a label from an object state group, for example the lock state "Not locked", that policies
  can test; it is not the same as the status of a version ([Glossary](../../glossary.md#s),
  [Object states](../../guides/object-states.md)).

## Common confusions

| Confusion | Clear version |
|---|---|
| "Class" means a PHP class | Here it usually means a **content class**, defined in the administration |
| Moving an item copies it | Moving changes its node; the object stays the same |
| Two locations are two copies | Two nodes, one object: one edit changes both |
| A draft is visible to visitors | Only published versions are shown on the site |
| Section and folder are the same | A folder is a node in the tree; a section is a label that many nodes can carry |
| Object state and version status are the same | A state is a label for policies; draft, published and archived are version statuses |

## Check your understanding

1. An object has two locations. You edit its title. How many places on the site change?
2. Which setting file lists the datatypes you can choose for a new attribute?
3. Where in the administration do you see an item's node and object numbers?
4. Name two things a section can decide.
5. Practice: do [project 1](project-01-a-content-class-of-your-own.md) and draw your class like the worked example.

<details>
<summary>Answers</summary>

1. Both, because there is one object.
2. `settings/content.ini`, the `AvailableDataTypes[]` list.
3. On the item's page, in the line with its version and numbers, and on the **Details** tab.
4. Which design is used, and which permissions apply.

</details>

## Related pages

- [Learning the template language](learning-the-template-language.md)
- [Understanding siteaccesses and settings](understanding-siteaccesses-and-settings.md)
- [Glossary for learners](glossary-for-learners.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
