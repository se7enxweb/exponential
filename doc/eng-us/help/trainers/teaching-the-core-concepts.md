---
title: "Teaching the core concepts"
audience: trainer, editor, admin, developer
category: training
order: 5
summary: The dozen ideas every Exponential learner needs, content class, attribute, content object, node, version, translation, section, role and policy, siteaccess, design and template, extension, INI settings, with a plain explanation, a classroom picture, a one-hour session plan and a quiz.
keywords: core concepts, content model, content class, attribute, content object, node, version, draft, translation, section, role, policy, siteaccess, design, template, extension, ini, teaching, explanation
type: help
---
# Teaching the core concepts

Every Exponential course, for editors, administrators or developers, rests on the same dozen ideas. Learners who get
them early understand everything after faster; learners who miss them ask the same question in every module. This
page gives trainers one plain sentence per concept, a picture that works in a classroom, and a one-hour session that
teaches them all. The definitions follow the [glossary](../../glossary.md).

## The concepts

| Concept | In one sentence | A picture for the classroom | Where learners see it |
|---|---|---|---|
| **Content class** | The definition of a kind of content (for example Article) as a list of attributes | A blank form | **Setup > Classes** (administrators) |
| **Attribute** | One field of a content class, typed by a datatype (text, image, relation) | One box on the form | The fields of the edit form |
| **Content object** | One piece of content, an instance of a class, with versions and translations | A filled-in form | Any item you open |
| **Node** | One place of a content object in the content tree, with a parent, a sort order and a visibility | A shortcut in a folder: the same document can have several | The tree in **Content structure** |
| **Version and draft** | Every edit makes a new version; a draft becomes the published version when it is published | Numbered copies of a document; only one is on the notice board | **Manage versions** on an item |
| **Translation** | A language version of a content object | The same form filled in in another language | The language list next to **Edit** |
| **Section** | A label on content that selects a design and permissions | A colored sticker on a folder | The **Details** tab of an item |
| **Role and policy** | A role is a named set of policies (permissions), assigned to users and groups | A key ring; each key opens one kind of door | **Users > Roles and policies** |
| **Siteaccess** | One way of reaching the site (by URL, host or port) with its own settings and design, such as `site` and `admin` | Several doors into the same building | The address bar: `/admin`, the editor host |
| **Design and template** | A design is a set of templates, styles and images; a template is a `.tpl` file that draws part of a page | The print layout of a magazine | The public site's look |
| **Extension** | A package that adds or changes features without changing the kernel | An add-on module for a machine | **Setup > Extensions** |
| **INI settings** | Settings files in blocks of `Key=Value`, read in order: defaults, extensions, siteaccess, override | Factory settings, then local adjustments on top | `settings/`, `exp:ini` |

Two distinctions trip learners up most often; say them twice:

- **Object and node.** The object is the content; a node is where it appears. Moving a node moves the content's place,
  not the content. Adding a location gives the same object a second node.
- **Version status and object state.** Draft, published and archived are statuses of a version. An object state (for
  example "Not locked") is a label from an object state group that policies can test.

## A one-hour session

| Time | Activity |
|---|---|
| 0:00 | The form picture: draw a blank form (class), its boxes (attributes), a filled-in copy (object) |
| 0:10 | Learners open **Content structure**, open an article, press **Edit** and name three attributes of its class |
| 0:20 | Object and node: open an item's **Locations** tab; explain the shortcut picture |
| 0:25 | Versions: learners edit and publish a test item twice and open **Manage versions** |
| 0:35 | Sections, roles and policies: the sticker and the key ring; open **Users > Roles and policies** and read one role as sentences |
| 0:45 | Siteaccesses: open the public site and the administration side by side; same content, different doors |
| 0:50 | Designs, extensions and INI in two minutes each (for administrators and developers) |
| 0:55 | The quiz below |

For editors, stop after 0:45 and use the last 15 minutes for questions. For developers, extend 0:50 into a
look at `settings/site.ini` and `php bin/php/console exp:ini` ([exp:ini](../../features/6.0/exp-ini-command.md)).

## Hands-on exercise

On a training installation, as an administrator:

1. Open **Content structure** and open any article.
2. Press **Edit**, write down the names of the fields (attributes), then press **Discard draft** and confirm.
3. On the article's page, open the tab **Details** and find its node ID, object ID and section.
4. Open **Users > Roles and policies**, open the role **Editor**, and read its first three policies as sentences.
5. Open the public site in a second tab and find the same article. Compare the address in both tabs: which part
   names the siteaccess?

**Expected result:** learners can name three attributes, two IDs and a section of one item, say what one policy of the
Editor role allows, and point at the siteaccess part of an address.

## Quiz

1. What is the difference between a content class and a content object?
2. An article appears in two places of the tree. How many objects and how many nodes are there?
3. Is "draft" an object state?
4. What does a role do before it is assigned?
5. Name two siteaccesses of a standard installation.

<details>
<summary>Answers</summary>

1. The class is the definition (the blank form); the object is one piece of content made from it (a filled-in form).
2. One object, two nodes.
3. No. Draft is a status of a version; object states are labels from an object state group
   ([glossary](../../glossary.md)).
4. Nothing; a role gives access only when it is assigned to users or groups
   ([Roles and policies](../../guides/roles-and-policies.md)).
5. For example `site` and `admin` (and the editor siteaccess on new installations).

</details>

## Further reading

- [Glossary](../../glossary.md): every term with a link to the page that teaches it.
- [The content model and editing content](../../guides/content-model-and-editing.md)
- [Getting started](../getting-started.md) and [A tour of the administration](../admin-tour.md)

## Related pages

- [Trainers: start here](trainers-start-here.md)
- [Course catalogue](course-catalogue.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
