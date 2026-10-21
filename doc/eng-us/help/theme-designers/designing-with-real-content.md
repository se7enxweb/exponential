---
title: "Designing with real content"
audience: theme-designer
category: theme-design
order: 7
summary: Why and how theme designers draw with the content an Exponential site really holds - where to get it, how to measure the extremes, a set of stress cases, and how to test a design against content on a development installation.
keywords: theme designer, real content, lorem ipsum, content audit, text length, stress test, extremes, demo content, development installation, sample content
type: help
---
# Designing with real content

This page is for theme designers who want their mockups to survive contact with the content editors write. A
content managed site shows text and images nobody on the design team chose. Placeholder text hides exactly the
problems that show up after launch: the three-line headline, the missing image, the 40-character word in German.

## Contents

1. [Where to get real content](#where-to-get-real-content)
2. [Measuring the extremes](#measuring-the-extremes)
3. [The stress set](#the-stress-set)
4. [Real content in the mockup](#real-content-in-the-mockup)
5. [Testing on a development installation](#testing-on-a-development-installation)
6. [When there is no content yet](#when-there-is-no-content-yet)

## Where to get real content

| Situation | Source |
|---|---|
| Redesign of a running site | The site itself; a development copy of its database and storage, prepared by the developer |
| New site, content being written | The editors' drafts, the old site, the content plan |
| A theme for many sites | The demo content a theme ships ([Demo content for a theme](../designers/distribution/demo-content.md)) plus your own stress set |

Ask the developer for a development copy rather than browsing the live administration: you will want to edit content
to test your design, and that must never happen on the live site. Personal data in a copy (users, form submissions,
newsletter subscribers) is not needed for design work and should be removed or replaced with `example.invalid`
addresses before the copy reaches you ([Customer data and privacy](../designers/sales/customer-data-and-privacy.md)).

## Measuring the extremes

For every field in your [mapping sheet](mapping-a-design-to-content-classes.md#the-mapping-sheet), write down:

- the **shortest** realistic value (a one-word title, an intro of one sentence);
- the **typical** value;
- the **longest** realistic value (the longest title in the archive, a 900-word intro);
- how often it is **empty**, if optional.

On a running site, browse a long list sorted by different criteria, or ask the developer for counts from the
development copy. Write the numbers into the mapping sheet; they are design requirements.

## The stress set

Keep a small set of test objects that every design must survive. Create them on the development installation, in a
folder editors never see:

| Case | Example |
|---|---|
| Long headline | 140 characters with no short title |
| Long single word | a compound word or a URL in a title |
| No image | every optional image empty |
| Odd image | a very tall portrait and a very wide panorama |
| Rich text everything | every heading level, nested lists, a table, an embed, a quote, a long link |
| Many authors | five authors in an `ezauthor` field |
| Other scripts | a right-to-left title, a title in a script with tall glyphs ([Designing for many languages](designing-for-many-languages.md)) |
| Empty list | a folder with no children ([Designing empty and error states](designing-empty-and-error-states.md)) |
| Huge list | a folder with hundreds of children, to see pagination |

## Real content in the mockup

In the design tool, keep real text in the components, and duplicate each key frame for the shortest and longest case.
Two rules make this work:

1. Text boxes grow; nothing is clipped unless the design decides to clip it, and then the design shows how (an
   ellipsis, a "more" link).
2. Images are placed in their crop frames with real, imperfect pictures, not stock photos chosen to fit.

## Testing on a development installation

Once templates exist, the best test is the site itself:

1. Open each object of the stress set in its full view, and the stress folder in its list view.
2. Check at 1440 px, at 960 px with 200% zoom and at 375 px, in light and dark.
3. Place the stress objects in each layout block view type, if the site uses layouts.
4. Write down every break, with a screenshot and the template that drew it (template path comments tell you which:
   [Template path comments](../designers/templates/template-path-comments.md)).

The [design QA checklist](design-qa-checklist.md) repeats these steps as boxes to tick.

## When there is no content yet

Write content-shaped text, not lorem ipsum: headlines that sound like headlines, at the measured lengths of a
comparable site. Agree with the editors on target lengths, and put those targets in the edit form where the class
allows a description of the field, so editors see them while writing. Content length limits on a field are a
content model decision for the developer ([Choosing datatypes](../cms-developers/choosing-datatypes.md)).

## Related pages

- [Designing for editors](designing-for-editors.md)
- [Design QA checklist](design-qa-checklist.md)
- [Common template mistakes](../designers/templates/common-template-mistakes.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
