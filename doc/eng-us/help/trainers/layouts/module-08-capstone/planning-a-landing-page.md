---
title: "Lesson 8.1: Planning a landing page"
audience: trainer, editor, website-creator, designer
category: training
order: 81
summary: Learners plan a campaign landing page on a planning sheet, layout type, zones, blocks with view types, collections and the mapping, and review a partner's plan before building. 45 minutes.
keywords: exponential layouts, planning, planning sheet, landing page, campaign page, layout type, zones, blocks, collections, mapping, review
type: help
---
# Lesson 8.1: Planning a landing page

Good layouts are planned before they are built. In this lesson learners plan a campaign landing page on paper, with the
planning sheet from the site builders' documentation, and review each other's plans. Lesson 8.2 builds it.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 8: Capstone](README.md) |
| Duration | 45 minutes (10 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Editors, site builders and designers who completed modules 1 to 7 |
| Prerequisites | [Module 7](../module-07-mapping-rules/README.md) |

## Learning objectives

After this lesson, learners can:

- turn a brief into a plan: layout type, zones, blocks with view types, collections, mapping;
- decide what is linked to shared layouts, what is manual and what is dynamic;
- choose a mapping with a target, conditions and a priority;
- review a plan against a checklist.

## Trainer's notes

### How to present it

Sources: [Layout types, zones and blocks: Planning sheet](../../../creators/website-creators/layout-types-zones-and-blocks.md#planning-sheet)
and [Landing pages with the layouts editor: A campaign page from scratch](../../../creators/website-creators/landing-pages-with-layouts.md#a-campaign-page-from-scratch).

1. **The brief** (3 minutes). Project it: *An autumn campaign page for the training site. A big banner with a title,
   a sentence and a button; three hand-picked highlights; the six newest articles of a folder below them; the site's
   header and footer. The campaign layout should be used on the campaign page only, during the month of the campaign.*
2. **The planning sheet** (4 minutes). Columns: page, layout type, zone, blocks (type / view type), collection. Show the
   example of the documentation: a front page on `layout_2`, header and footer linked, a hero and a `list` with
   `grid_featured` in `main`, a dynamic collection of the newest articles with limit 7.
3. **Review checklist** (3 minutes): one Full view block or none (and why); header and footer linked; manual only for
   curated items; dynamic lists with a content type filter and a sort; offset where two lists share a query; a node
   target; a time condition for the campaign period; a priority above the page's normal mapping.

### Common questions

**Which block makes the banner?** A **Hero** block, or a hero component with a style, in `post_header`.

**Should the campaign page's own text appear?** If the campaign page item has text, a **Full view** block shows it.

**How long should a campaign mapping last?** The time condition's `from` and `to` set it; afterwards the page's normal
mapping takes over again.

### Pitfalls

- **Plans without a mapping.** The layout would never show. The mapping row is part of the sheet.
- **Too many blocks.** A landing page with fifteen blocks is a long scroll. Cut.
- **Manual lists for news.** Use dynamic.

## Hands-on exercise

Work alone, then in pairs. Use paper or a copy of the planning sheet.

1. Read the brief.
2. Choose the layout type and write it on the sheet.
3. For each zone, list the blocks with their type and view type.
4. Mark which zones are linked to the shared header/footer layout.
5. For each listing block, write the collection: manual (which items) or dynamic (which query, filter, sort, offset,
   limit).
6. Write the mapping: target, conditions (including the time), and how its priority compares with the page's normal
   mapping.
7. Swap plans with your partner and review theirs against the checklist the trainer projected. Write two comments.
8. Improve your plan with your partner's comments.

## Expected result

- Each learner has a complete plan, for example: `layout_2`; *HEADER* and *FOOTER* linked; *POST_HEADER*: Hero;
  *MAIN*: List (`grid_featured`, manual, three items), List (`list`, dynamic, Article below the campaign folder, newest
  first, limit 6); mapping: node = the campaign page, time from the first to the last day of the month, priority above
  the page's normal mapping.
- Each plan has two review comments.
- The trainer checks two plans on the projector.

## Quiz

1. Which five columns does the planning sheet have?
2. Which condition limits a mapping to the campaign month?
3. Why link the header and footer zones instead of building them?
4. When is a manual collection right on this page, and when dynamic?
5. Your plan has a node target and a time condition. What else must the mapping have to win over the page's normal
   mapping?

<details>
<summary>Answers</summary>

1. Page, layout type, zone, blocks (type / view type), collection
   ([Layout types, zones and blocks](../../../creators/website-creators/layout-types-zones-and-blocks.md#planning-sheet)).
2. A **time** condition with `from` and `to`.
3. They are defined once in a shared layout; changes there reach every page.
4. Manual for the three curated highlights; dynamic for the newest articles.
5. A higher priority.

</details>

## Further reading

- [Landing pages with the layouts editor](../../../creators/website-creators/landing-pages-with-layouts.md)
- [Layout rules: Recipes](../../../creators/website-creators/layout-rules.md#recipes)

## Related pages

- [Module 8: Capstone](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
