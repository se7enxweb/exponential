---
title: "Lesson 7.2: Creating a mapping"
audience: trainer, editor, website-creator
category: training
order: 72
summary: Learners create a mapping with New mapping for their own test page, choose the layout, a node target and conditions, set a priority above broader mappings, enable it and check the page. 60 minutes.
keywords: exponential layouts, new mapping, mapping, rule, node target, subtree, condition, class, siteaccess, time, priority, enable, view in cms, map layout
type: help
---
# Lesson 7.2: Creating a mapping

Learners now point their course layout at a page of their own. They create a mapping with a node target, give it a
priority above the broader mappings, enable it and check the page. Every learner uses their own test page, so no
mapping touches another learner's page.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 7: Mapping rules](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Site builders and senior editors |
| Prerequisites | [Lesson 7.1](reading-layout-mappings.md); a published course layout (module 6, or published by the trainer) |

## Learning objectives

After this lesson, learners can:

- create a mapping with **New mapping**, choose the layout and add a node target;
- add a condition (for example a siteaccess) where needed;
- set a priority that beats broader mappings, and enable the mapping;
- check the result on the page and with **View in CMS**;
- use the mapping's quick actions to disable, copy or delete it.

## Trainer's notes

### How to present it

Sources: [explayouts_ui: Map a layout to a page](../../../../features/6.0/extensions/explayouts_ui.md#map-a-layout-to-a-page)
and [Layout rules: Recipes](../../../creators/website-creators/layout-rules.md#recipes).

1. **New mapping** (4 minutes). On **Layout mappings**, **New mapping** opens a panel; a new mapping starts with a
   priority that actually resolves. Choose the layout, add a target and, if needed, conditions. Enable it.
2. **Node target** (3 minutes). For one page, a node target: the page's location. It follows the page when it is
   renamed or moved.
3. **Conditions** (3 minutes). A **siteaccess** condition limits the mapping to one site of a multisite installation;
   a **time** condition with `from` and `to` makes a campaign layout for a period.
4. **Checking** (3 minutes). **View in CMS** on a node target opens the node; load the page on the site signed out.
   If nothing changes: a higher-priority mapping matches first, the layout is not published, or a cached page is
   shown.
5. **Quick actions** (2 minutes). **More actions** on a mapping: enable, disable, unlink layout, copy, delete, and a
   cache clear for that mapping. Copying a mapping is the quickest way to build a family of similar ones.

### Common questions

**What priority should I choose?** Higher than any broader mapping that would also match the page. Read the list
(lesson 7.1) before choosing.

**Do I have to publish a mapping?** Enable it; the layout it points at must be published to be shown.

**Can I map the same layout to many pages?** Yes, with several targets or a subtree target.

### Pitfalls

- **Mapping a learner's layout to a shared page.** Each learner maps only their own test page.
- **Forgetting the layout is a draft.** A mapping to a never-published layout shows nothing new.
- **Cached pages.** If the page does not change, clear its cache with the mapping's cache clear or ask the trainer.

## Hands-on exercise

Use the training installation. Each learner has a published test page `Layout test - <first name>` in their course
folder (created by the learner in Editors 101 style, or by the trainer), and a published course layout.

1. Open **Layouts** > **Layout mappings** and press **New mapping**.
2. Choose your course layout `Training - <your first name>`.
3. Add a target of the node type and choose your test page.
4. Read the priority the panel proposes. Compare it with the priority of the broader mapping that serves pages of your
   course folder (find it as in lesson 7.1) and make sure yours is higher.
5. If the training installation has several siteaccesses, add a **siteaccess** condition for the public site.
6. Enable the mapping and save it.
7. Use **View in CMS** and then open your test page on the public site in a private window. Your course layout should
   frame it.
8. Open **More actions** on your mapping and disable it. Reload the page: it is back to its previous look (after the
   cache is renewed).
9. Enable it again.

## Expected result

- The learner's test page shows the course layout on the site (header and footer from the shared layout, the blocks
  of modules 3 and 4, and the page's own content in the Full view block).
- Disabling the mapping returned the page to its previous look; enabling it brought the layout back.
- The trainer checks by opening two learners' test pages in a private window on the projector.

![The Layouts tab with Map layout, which starts a mapping for this location](../../../images/trainers/ly-map.webp)

## Quiz

1. Which target type do you use for one page?
2. Why must your mapping's priority be higher than the broader mapping of the folder?
3. What does a **time** condition do?
4. Your mapping is enabled but the page has not changed. Name three things to check.
5. Which quick action is the fastest way to build a similar mapping for another page?
   A. Delete B. Copy C. Disable D. Unlink layout

<details>
<summary>Answers</summary>

1. A node target ([Layout rules](../../../creators/website-creators/layout-rules.md#targets)).
2. The first match by priority wins; otherwise the broader mapping catches the page first.
3. It limits the mapping to a period (`from` and `to`).
4. A higher-priority mapping matches first; the layout is not published; the page's cached copy is still served.
5. **B.**

</details>

## Further reading

- [explayouts_ui: Map a layout to a page](../../../../features/6.0/extensions/explayouts_ui.md#map-a-layout-to-a-page)
- [Layout rules: Recipes](../../../creators/website-creators/layout-rules.md#recipes)

## Related pages

- [Module 7: Mapping rules](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
