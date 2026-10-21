---
title: "Project 1: a content class of your own"
audience: student
category: learning
order: 25
summary: Study project 1 - design and create a content class of your own (a club event) with the class editor, choose datatypes, set required fields and an object name pattern, create and publish objects of it, and hand in a short design note with screenshots, graded with a rubric.
keywords: study project, content class, class editor, attribute, datatype, Text line, Text block, XML block, Image, Date, object name pattern, required, learner
type: help
---
# Project 1: a content class of your own

In this project you design a content class for a kind of content that does not exist yet on your study site, create
it in the class editor, and publish a few objects of it. It is the first step of every site you will ever build.

## Contents

1. [At a glance](#at-a-glance)
2. [Goal](#goal)
3. [Before you start](#before-you-start)
4. [Tasks](#tasks)
5. [Hand in](#hand-in)
6. [Rubric](#rubric)
7. [Stretch goals](#stretch-goals)

## At a glance

| | |
|---|---|
| Level | Beginner |
| Time | About 2 hours |
| Paths | Administrator, developer |
| Prerequisites | A study site and its administrator sign-in; [Understanding the content model](understanding-the-content-model.md) |

## Goal

A class **Club event** with at least five attributes, three published events in a folder of your site, and a short
design note that explains your choices.

## Before you start

Read [The content model and editing content](../../guides/content-model-and-editing.md), sections 1 to 3, and
[Class groups](../../guides/class-groups.md). Plan on paper first:

| Attribute | Identifier | Datatype (name in the class editor) | Required |
|---|---|---|---|
| Title | `title` | Text line | yes |
| Date | `event_date` | Date | yes |
| Summary | `summary` | Text block | no |
| Description | `description` | XML block (rich text) | no |
| Poster | `poster` | Image | no |
| Seats | `seats` | Integer | no |

The names in the third column are the datatype names the kernel gives them (for example "Text line" for `ezstring`
and "XML block" for `ezxmltext`, in `kernel/classes/datatypes/`). Identifiers are lower case, with underscores.

## Tasks

1. **Open the class groups.** In the administration open `/class/grouplist`. Expected: the class groups, among them
   **Content**.
2. **Create the class.** Click **Content**, then **New class**. Name `Club event`, identifier `club_event`, object name
   pattern `<title>`.
3. **Add the attributes.** For each row of your plan choose the datatype in **Add attribute**, click **Add**, and fill
   in the name and identifier; tick **Required** where planned.
4. **Save.** Click **OK**. Expected: the class appears in the list of the Content group, and its page
   (**Setup > Classes**, or `/class/view/<id>`) shows your attributes
   ([The content model and editing content](../../guides/content-model-and-editing.md#2-create-a-class)).
5. **Create a folder** "Events" in the content structure.
6. **Create three events** in it: select the folder, choose **Club event** in the create menu, **Create here**, fill in
   the fields and **Send for publishing**. Leave the title empty once, on purpose, and note the message you get.
7. **Look at them on the site.** Open the folder on the public siteaccess. If the new items do not appear in a list,
   clear the content cache once ([Understanding caches](understanding-caches.md)).
8. **Edit one event twice** and look at its versions
   ([The content model and editing content](../../guides/content-model-and-editing.md#3-create-and-edit-an-object)).

## Hand in

- A design note (half a page): why each attribute exists, why each datatype, and which fields are required and why.
- Screenshots: the class page with all attributes; the folder with three events; the message from task 6.
- The class identifier and the object numbers of your events.
- The release you worked on.

## Rubric

Score the four common criteria of [Grading your own work](grading-your-own-work.md), and these checks (8):

1. The class has at least five attributes.
2. Every identifier is lower case, without spaces.
3. The object name pattern makes the event's title its name.
4. Title and date are required, and the note says why.
5. At least four different datatypes are used, each with a reason.
6. Three events are published in one folder.
7. One event has at least three versions.
8. The note cites the pages used.

## Stretch goals

- Add an **Object relations** attribute that points to a "Venue" class you also create.
- Translate the class name for a second language, if your site has one: classes have a **Translations** window on
  their page under **Setup > Classes** ([Content languages](../../guides/content-languages.md#7-translating-content-in-the-editor)).
- Make an attribute not searchable and explain the effect after [project 6](project-06-search-that-finds-the-right-things.md).
- Read [Content classes for builders](../extension-development/part-2-builder/content-classes-for-builders.md) and note one
  thing you would design differently now.

## Related pages

- [Study projects: overview](study-projects-overview.md)
- [Project 2: a small site structure](project-02-a-small-site-structure.md)
- [Understanding the content model](understanding-the-content-model.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
