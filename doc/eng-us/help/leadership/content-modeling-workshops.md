---
title: Running content modeling workshops
audience: project-manager, editor, developer
category: leadership
order: 60
summary: How to run workshops that turn your content into content classes, attributes, a content tree, sections, states, languages and roles; agendas, exercises and a class sheet to copy.
keywords: content modeling, content model, workshop, content class, attribute, datatype, content tree, node, location, section, object state, workflow, roles, taxonomy, eztags, layouts, editor, developer, project manager
type: help
---
# Running content modeling workshops

This page is for project managers who facilitate content modeling, and for the editors and developers who take part.
It answers: how to prepare and run the workshops, which questions to ask, how each answer maps to a concept of
Exponential (class, attribute, datatype, node, section, state, language, role, layout), and what the workshops must
produce so that developers can build and editors can recognize their content. The product terms come from
[The content model and editing content](../../guides/content-model-and-editing.md).

## Contents

1. [Why content modeling matters in Exponential](#why-content-modeling-matters-in-exponential)
2. [The concepts in one table](#the-concepts-in-one-table)
3. [Who takes part](#who-takes-part)
4. [Preparation](#preparation)
5. [Workshop 1: content inventory and types](#workshop-1-content-inventory-and-types)
6. [Workshop 2: attributes and datatypes](#workshop-2-attributes-and-datatypes)
7. [Workshop 3: the tree, relations and taxonomy](#workshop-3-the-tree-relations-and-taxonomy)
8. [Workshop 4: permissions, stages and languages](#workshop-4-permissions-stages-and-languages)
9. [Workshop 5: pages and layouts](#workshop-5-pages-and-layouts)
10. [The class sheet to copy](#the-class-sheet-to-copy)
11. [After the workshops](#after-the-workshops)
12. [Common pitfalls](#common-pitfalls)

## Why content modeling matters in Exponential

"As a CMS its most notable feature is its fully customizable and extendable content model" (README, quoted on
[Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-6-the-kernel)). In
Exponential you define your own types of content instead of fitting your content into fixed pages and posts. That
freedom is the reason to model carefully: every class you agree on becomes forms that editors fill in every day,
templates that developers write, and permissions that administrators assign.

## The concepts in one table

| Concept | What it is | Decided in | Read |
|---|---|---|---|
| **Content class** | A type of content: a name, an identifier and a list of attributes (for example Article: Title, Intro, Body, Image) | Workshop 1 | [Content model, section 1](../../guides/content-model-and-editing.md) |
| **Class group** | A folder of classes in the class editor (Content, Users, Media, Setup) | Workshop 1 | [Class groups](../../guides/class-groups.md) |
| **Attribute and datatype** | One field of a class and its kind: one line, several lines, rich text, image, relation ... | Workshop 2 | [Content model, section 1](../../guides/content-model-and-editing.md) |
| **Content object** | One piece of content, an instance of a class, with versions and languages | | same |
| **Node, location** | A place of an object in the content tree; one object can have several | Workshop 3 | same |
| **Section** | A group of objects for permissions, navigation and designs; not a place in the tree | Workshop 4 | [Sections](../../guides/sections.md) |
| **Object state** | How far along content is (review, legal clearance, embargo, featured) | Workshop 4 | [Object states](../../guides/object-states.md) |
| **Workflow** | Steps such as approval that run before or after an operation | Workshop 4 | [Workflows](../../guides/workflows.md) |
| **Content language** | A language content is written in; shown per siteaccess | Workshop 4 | [Content languages](../../guides/content-languages.md) |
| **Role and policy** | Who may do what, limited by section, class, subtree, language, state | Workshop 4 | [Roles and policies](../../guides/roles-and-policies.md) |
| **Layout, zone, block, rule** | How a page is arranged, and which pages get which layout | Workshop 5 | [The layouts editor](../layouts-editor.md) |

## Who takes part

| Person | Why | Workshops |
|---|---|---|
| Project manager | Facilitates, keeps the decision log, guards the scope | all |
| Lead editor | Knows the content and the daily work | all |
| One or two editors | Will fill in the forms; spot what is missing | 1, 2, 5 |
| Lead developer | Knows what each decision costs to build; maps answers to classes | all |
| Designer | Knows which fields the design needs | 2, 5 |
| Administrator | Roles, sections, languages, siteaccesses | 4 |
| Translator or language owner | For multilingual sites | 4 |

Keep the group small enough to decide. Invite the others to a review of the results instead.

## Preparation

- [ ] A demonstration installation everyone can open, with a site package that has demo content (`sevenx_multisite`
      is the default of `exp:install`; [4.3 Site packages](../../install/04-choosing-an-install-method.md#43-site-packages-what-is-installed)).
      Showing real classes in **Setup > Classes** makes every concept concrete.
- [ ] A content inventory: a list of the content that exists today (pages, documents, products, people, events), with
      rough counts and an owner per area.
- [ ] Ten real examples of content, printed or on screen, of different kinds.
- [ ] The requirement list from discovery ([Planning an Exponential project](planning-an-exponential-project.md)).
- [ ] Copies of [the class sheet](#the-class-sheet-to-copy).
- [ ] A decision log ([Project templates](project-templates.md)).

## Workshop 1: content inventory and types

**Goal.** A list of content classes with a one-sentence purpose each.

Agenda:

1. **Show the product** (15 minutes). The developer opens a class in **Setup > Classes** and creates an object of it,
   following [Content model, sections 2 and 3](../../guides/content-model-and-editing.md). Everyone sees that a class is
   a form and an object is a filled-in form.
2. **Sort the examples** (30 minutes). Lay out the ten examples. Group those that have the same fields. Each group is
   a candidate class.
3. **Name the classes** (20 minutes). A name people read and an identifier code uses (`article`, `event`). Note the
   object name pattern: which field names an object in lists (for example `<title>`).
4. **Check against the inventory** (20 minutes). Does every item of the inventory fit one class? Items that fit none
   are a new class or a sign that two classes should be one.
5. **Reuse** (15 minutes). Which classes already exist in the site package? Reusing a shipped class saves template
   and translation work.

Output: the class list, each class with name, identifier, purpose, owner, estimated number of objects.

> **Tip**
> Ask "what would an editor search for?" rather than "what does the page look like?". A class describes content, not
> a page layout; the layout is decided in workshop 5.

## Workshop 2: attributes and datatypes

**Goal.** For each class, its attributes with their datatype, whether required, and whether translated.

The datatypes available for new attributes are the `AvailableDataTypes[]` list of `settings/content.ini`, 35 entries
in the shipped list ([Content model, section 1](../../guides/content-model-and-editing.md)); extensions add more.
Map the editors' words to them:

| Editors say | Datatype identifier (`settings/content.ini`) |
|---|---|
| a title, a name, one line of text | `ezstring` |
| several lines of plain text | `eztext` |
| formatted text with headings, links, images | `ezxmltext` (rich text, edited in the online editor) |
| a date; a date and time; a time | `ezdate`, `ezdatetime`, `eztime` |
| yes or no | `ezboolean` |
| a number | `ezinteger`, `ezfloat` |
| one choice from a list | `ezselection` |
| a link to another item; several | `ezobjectrelation`, `ezobjectrelationlist` |
| a picture | `ezimage` |
| a file to download; audio or video | `ezbinaryfile`, `ezmedia` |
| a web address; an e-mail address | `ezurl`, `ezemail` |
| keywords | `ezkeyword` |
| a table of values | `ezmatrix` |
| a price | `ezprice`, `ezmultiprice` |
| a country | `ezcountry` |
| a user account | `ezuser` |

Extensions add others, for example tags (`eztags`), map locations (`ezgmaplocation`), star ratings
(`ezstarrating`) and spam protection for forms (`recaptcha`, `hcaptcha`)
([Extensions, themes and packages](../../features/6.0/extensions/README.md)).

For each attribute decide:

- [ ] **Name and identifier.**
- [ ] **Datatype.**
- [ ] **Required?** Image fields can require alternative text ([Content model, section 9](../../guides/content-model-and-editing.md)); decide it here, it is an accessibility requirement.
- [ ] **Translated?** For a multilingual site: is the value the same in every language (a date, a price) or written per language?
- [ ] **Searchable?** Will people search for it?
- [ ] **Shown where?** Full view, list view, both, nowhere (internal).
- [ ] **Example value** from the real examples.

Output: one completed class sheet per class.

## Workshop 3: the tree, relations and taxonomy

**Goal.** Where content lives, how items point to each other, and how content is classified.

Questions:

1. **The tree.** Draw the top two or three levels of the content tree. Which class may be created below which? Content
   that belongs in several places gets several locations (one object, several nodes), not copies.
2. **Relations.** Which items refer to which (an article to its author, an event to its venue)? Each is an
   object relation attribute. Decide what happens when the target is removed; the content checks list "broken
   relations" afterwards ([Content checks](../../features/6.0/content-checks.md)).
3. **Taxonomy.** Is classification free (keywords) or controlled (tags managed by someone)? Controlled tags are
   `eztags` ([eztags](../../features/6.0/extensions/eztags.md)); check the database first, because `eztags` is not
   usable on MongoDB ([MongoDB limits](../database-support.md#mongodb-limits)).
4. **Media.** Where do images and files live: in the media library, below the content that uses them, or both?
5. **Addresses.** Which URL should an item have? URL aliases follow the tree; global aliases and wildcards exist for
   exceptions ([URL aliases](../../guides/urls-and-aliases.md)).

Output: a tree diagram (on paper is fine), a relation list, a taxonomy decision.

## Workshop 4: permissions, stages and languages

**Goal.** Sections, states, workflows, languages and the role plan.

| Question | Product answer | Read |
|---|---|---|
| Are there groups of content only some people may read or edit (members area, intranet department)? | A section per group; roles limited by section | [Sections](../../guides/sections.md) |
| A new installation has five sections (Standard, Users, Media, Setup, Design); which do we add? | New sections; note that the Anonymous role reads only Standard by default | [Sections, section 2](../../guides/sections.md) |
| Does content pass stages, with different people allowed in each stage? | A state group with states; policies limited by state | [Object states](../../guides/object-states.md) |
| Must someone approve before publishing? | A workflow with an Approve event and a trigger; the workflow cronjob must run | [Workflows](../../guides/workflows.md) |
| Which languages, and which is the main language of each item? | Content languages; `SiteLanguageList` per siteaccess; "always available" items | [Content languages](../../guides/content-languages.md) |
| Who may do what? | Roles assigned to user groups | [Roles and policies](../../guides/roles-and-policies.md) |

> **Note**
> Sections, states and the tree are three independent ways to group content. "Sections say *where* content belongs
> ..., and the content tree says *what it sits under*. States say *how far along* content is"
> ([Object states](../../guides/object-states.md)). Use the one that matches the question, not all three for
> everything.

Output: section list, state groups, workflow list, language list, role plan (role, groups, policies, limitations).

## Workshop 5: pages and layouts

**Goal.** Which pages editors arrange themselves, and which are fixed templates.

With Exponential Layouts, "a layout is a saved arrangement of blocks in zones, and a rule decides which pages get it"
([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-layouts)). In the
workshop:

1. Open the **Layouts** tab of the demonstration installation and walk "In action: the layouts editor" of
   [the admin tour](../admin-tour.md).
2. List the page types (home, landing pages, section front pages, article pages).
3. For each: fixed template, or a layout editors change? Which zones? Which blocks?
4. Which blocks are the same on many pages (header, footer, a prefooter)? They belong in a **shared layout**, linked
   from the others ([The layouts editor](../layouts-editor.md)).
5. Who may change layouts? The **Layouts** tab is shown only to roles that may work with layouts.

Output: page type list with layout decision, zone and block list, shared layout list.

## The class sheet to copy

```text
Class: <Name>                         identifier: <identifier>        group: <Content | Media | ...>
Purpose: <one sentence>
Owner (editor): <name>                Expected number of objects: <n>
Created below: <parent classes>       May contain: <child classes>
Object name pattern: <<attribute>>    Section: <section>               States: <state group or none>
Approval before publishing: <yes/no, by whom>

| # | Attribute name | Identifier | Datatype       | Required | Translated | Searchable | Shown in        | Example value |
|---|----------------|------------|----------------|----------|------------|------------|-----------------|---------------|
| 1 | Title          | title      | ezstring       | yes      | yes        | yes        | full, list      | <example>     |
| 2 | Intro          | intro      | ezxmltext      | no       | yes        | yes        | full, list      | <example>     |
| 3 | Image          | image      | ezimage        | no       | no         | no         | full            | <file>        |
| 4 | <name>         | <id>       | <datatype>     | <y/n>    | <y/n>      | <y/n>      | <where>         | <example>     |

Open questions: <list>
Decided on: <date>   by: <names>
```

## After the workshops

1. **Write it up** in one document: class sheets, tree, relations, taxonomy, sections, states, workflows, languages,
   roles, page types. Version it in the project repository next to the code.
2. **Review** with editors who were not in the room. Ask each to place five real items in the model.
3. **Prototype**: a developer creates two or three classes on the test installation (a class is created in minutes,
   [Content model, section 2](../../guides/content-model-and-editing.md)). Editors enter real content into them.
4. **Adjust** and freeze the model for the build. Later changes go through the change process of
   [Estimating and scoping](estimating-and-scoping-with-extensions.md#keeping-the-scope-under-control).
5. **Plan migration and entry** from the class sheets ([Planning, phase 4](planning-an-exponential-project.md#phase-4-content-migration-and-entry)).

## Common pitfalls

| Pitfall | Why it hurts | Remedy |
|---|---|---|
| One class per page design | Many classes with the same fields; templates multiply | Model content; arrange pages with layouts or view templates |
| One "generic page" class for everything | Nothing can be listed, filtered or reused | Split by what editors search for |
| Copies instead of locations | Two objects drift apart | One object with several locations |
| Sections used as folders | Sections are not places in the tree | Use the tree for structure, sections for permissions |
| Languages decided late | Translation flags, siteaccesses and roles change | Decide languages in workshop 4 |
| Too many languages added "just in case" | Each language is a bit of a limited mask (at most 62) and removing one is only possible when nothing uses it | Add a language when it is needed ([Content languages](../../guides/content-languages.md)) |
| Alternative text left optional | Accessibility gaps found in QA | Make it required in workshop 2 |

## Related pages

- [The content model and editing content](../../guides/content-model-and-editing.md)
- [Sections](../../guides/sections.md), [Object states](../../guides/object-states.md), [Workflows](../../guides/workflows.md)
- [The layouts editor](../layouts-editor.md)
- [Planning an Exponential project](planning-an-exponential-project.md)
- [Planning multisite and multilingual projects](multisite-and-multilingual-planning.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
