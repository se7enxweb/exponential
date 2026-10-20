---
title: "Module 4: Content classes and datatypes"
audience: trainer, developer
category: training
order: 40
summary: The fourth module of the Developers course. Learners read the content model by identifiers, change how one field looks with a datatype override, read and write content from PHP, and generate a datatype of their own with the RAD wizard. 3 hours 40 minutes.
keywords: developers course, module 4, content class, attribute, identifier, datatype, attribute_view_gui, override, eZContentFunctions, createAndPublishObject, Datatype wizard, RAD
type: help
---
# Module 4: Content classes and datatypes

Templates show content; this module is about the content itself, from a developer's side. Learners create a small
class, find its identifiers from PHP, change the view of one field with an override, write content from a script,
and finish with a datatype of their own written by the RAD wizard.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 3 hours 40 minutes (with one 10-minute break) |
| Lessons | 4 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 4.1: Classes, attributes and identifiers](classes-attributes-and-identifiers.md) | 50 minutes | Create the **Note** class, list its attributes from PHP |
| [Lesson 4.2: Datatypes and their templates](datatypes-and-their-templates.md) | 45 minutes | Read datatype templates, override the title of notes |
| [Lesson 4.3: Reading and writing content from PHP](content-from-php.md) | 55 minutes | Create and update a note with `eZContentFunctions` |
| [Lesson 4.4: A datatype of your own](a-datatype-of-your-own.md) | 60 minutes | Generate a datatype with the **Datatype wizard**, add it to a class |

## Preparing this module

- Every learner needs an administrator account on their own training installation: the module creates a class,
  writes content and generates an extension.
- On a shared installation, each learner uses their initials in class and extension names (`note_ab`,
  `mysite_rating_ab`) and their own course folder.
- Lesson 4.4 writes files below `extension/`: the web server user must be allowed to write there on the training
  installation (never on a live site).
- Rehearse the wizard once on your own installation and keep the list of files it wrote for the discussion.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 4.1 | 50 |
| Lesson 4.2 | 45 |
| Break | 10 |
| Lesson 4.3 | 55 |
| Lesson 4.4 | 60 |
| **Total** | **220** |

## Related pages

- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
