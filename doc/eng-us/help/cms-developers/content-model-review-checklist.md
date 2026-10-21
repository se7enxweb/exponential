---
title: Content model review checklist
audience: cms-developer
category: cms-development
order: 47
summary: For CMS developers and their reviewers, a checklist to review a content model before it is built and again before launch. It covers classes, identifiers, datatypes, relations, the tree, access, languages, editorial workflow, templates and portability, with the question behind each item.
keywords: content model review, review checklist, classes, identifiers, datatypes, relations, content tree, sections, roles, translations, workflows, view modes, packages, remote id, model quality
type: help
---
# Content model review checklist

This page is for developers who review a content model, their own or a colleague's. A model is cheap to change on
paper and expensive once content exists, so review it twice: before building, with an editor and a designer in the
room, and before launch. Each item has the question to ask; the pages linked explain the answers.

## Contents

1. [How to run the review](#how-to-run-the-review)
2. [Classes](#classes)
3. [Identifiers and names](#identifiers-and-names)
4. [Datatypes](#datatypes)
5. [Relations and reuse](#relations-and-reuse)
6. [The tree](#the-tree)
7. [Access](#access)
8. [Languages](#languages)
9. [Editorial workflow](#editorial-workflow)
10. [Presentation](#presentation)
11. [Portability](#portability)
12. [Recording the result](#recording-the-result)

## How to run the review

1. Bring the model as written ([Designing a content model, step 8](designing-a-content-model.md#step-8-write-it-down-and-review-it)):
   a table per class, a sketch of the tree, the roles.
2. Bring real content: three real items per class, not lorem ipsum.
3. Ask an editor to "create" each item on paper with the fields given, and a designer to point at where each field
   appears on the page.
4. Go through the lists below; every "no" is either fixed or written down as an accepted risk.

## Classes

- [ ] Does each class stand for one real kind of content? (No class whose "type" field changes everything.)
- [ ] Are two classes that share every field really different? (If only their place differs, merge them.)
- [ ] Is every container a container, and every leaf not one?
- [ ] Is the object name pattern meaningful, so names in lists and URLs are recognizable?
  ([Content classes and attributes](content-classes-and-attributes.md))

## Identifiers and names

- [ ] Are identifiers lower case, English, and the same for the same meaning in every class (`title`, `intro`,
      `image`)?
- [ ] Are project classes prefixed where they could clash with a site package's classes?
- [ ] Are the human names and descriptions written for editors, translated where editors need it?

## Datatypes

- [ ] Does each field use the datatype that matches its meaning (dates as dates, choices as selections or tags)?
- [ ] Is long text rich text, and short text plain?
- [ ] Are required, searchable and translatable set on purpose for each field?
- [ ] Is every extension datatype from an extension that is active on all siteaccesses that need it?
  ([Choosing datatypes](choosing-datatypes.md))

## Relations and reuse

- [ ] Is everything used in more than one place an object of its own, referenced by a relation?
- [ ] Is each reference stored on one side only, with the reverse found by a fetch?
- [ ] Are relation attributes limited to the classes they may point to, with a sensible default location?
- [ ] Are shared images in the media library rather than copied into many fields?
  ([Relations and embeds](relations-and-embeds.md), [Images and image aliases](images-and-image-aliases.md))

## The tree

- [ ] Does the tree below the root mirror the navigation editors and visitors expect?
- [ ] Is each container's sort order set (priority for hand-made order, date for news)?
- [ ] Will large collections stay usable (paging, dates) at the volume expected in three years?
- [ ] Are second locations used only where content truly belongs in two places?
  ([Objects, nodes and locations](objects-nodes-and-locations.md))

## Access

- [ ] Is every "only X may see or edit Y" rule expressed as a section or subtree plus a role?
- [ ] Is the Anonymous role still minimal?
- [ ] Are stages beyond draft and published modeled as object states, not as folders?
  ([Sections, roles and policies](sections-roles-and-policies.md))

## Languages

- [ ] Are the languages and the fallback order decided per siteaccess?
- [ ] Are fields that must not differ per language untranslatable?
- [ ] Are shared objects (folders, media, people) always available?
  ([Translations and languages](translations-and-languages.md))

## Editorial workflow

- [ ] Is it clear who creates, approves and publishes each class, and is that a workflow or a habit?
- [ ] Are dated hiding and removal configured where content expires?
- [ ] Are version limits right for classes edited very often?
  ([Workflows and triggers](workflows-and-triggers.md), [Versions, drafts and publishing](versions-drafts-and-publishing.md))

## Presentation

- [ ] Does each class have its view modes listed (full, line, a card, embed)?
- [ ] Are pages that editors compose from blocks identified, and do they use Layouts rather than many optional fields?
- [ ] Are image aliases defined for every size the design shows?
  ([View modes](view-modes.md), [Layouts for CMS developers](layouts-for-cms-developers.md))

## Portability

- [ ] Are classes in packages in the repository?
- [ ] Do code and settings refer to identifiers and remote IDs, not node or object numbers?
- [ ] Do imported objects carry remote IDs from their source?
  ([Content classes and attributes](content-classes-and-attributes.md#classes-in-version-control))

## Recording the result

Write the date, the reviewers, the changes agreed and the accepted risks into the model document in your repository.
Review again before launch with the [Project launch checklist](project-launch-checklist.md), and whenever a new class is
added after launch.

## Related pages

- [The content model](the-content-model.md)
- [Mapping content to Exponential](../../install/migration/content-model-mapping.md)
- [Content classes for builders](../extension-development/part-2-builder/content-classes-for-builders.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
