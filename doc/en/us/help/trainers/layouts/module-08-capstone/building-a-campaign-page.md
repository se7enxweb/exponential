---
title: "Lesson 8.2: Capstone, building a campaign page"
audience: trainer, editor, website-creator, designer
category: training
order: 82
summary: Learners build the campaign page they planned, layout, blocks, collections, linked zones, publishing and a time-limited mapping, on their own, and the trainer checks it against a list. 90 minutes.
keywords: exponential layouts, capstone, campaign page, landing page, hero, list, manual, dynamic, pinning, linked zones, mapping, time condition, publish, checklist
type: help
---
# Lesson 8.2: Capstone, building a campaign page

The last lesson of the course builds the plan of lesson 8.1 without step-by-step instructions. It uses every module:
a new layout, blocks, collections, linked zones, publishing and a mapping. The checklist at the end is the trainer's
own assessment, not an official certification; the partner program's certifications are separate and their details
are not published ([Certifications](../../../partners/certifications.md)).

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 8: Capstone](README.md) |
| Duration | 90 minutes (5 presenting, 70 exercise, 15 review) |
| Level | Intermediate |
| Audience | Learners who completed modules 1 to 7 and lesson 8.1 |
| Prerequisites | [Lesson 8.1](planning-a-landing-page.md): a reviewed plan; a published campaign page per learner |

## Learning objectives

After this lesson, learners can:

- build a layout from a plan without instructions;
- combine shared zones, a hero, a manual and a dynamic list, offset and a pin;
- publish it and map it to one page for a period;
- verify the result signed out and hand it over with no draft open.

## Trainer's notes

### How to present it

1. **The brief again** (5 minutes). Learners build their own plan. Each learner has a published content page
   `Autumn campaign - <first name>` in their course folder (create it before the class, or let learners create it as
   step 1).
2. **During the exercise** (70 minutes). Walk around; answer with a question ("Which module covered that?"). The most
   common blocker is a mapping that loses to a higher priority: send learners to lesson 7.3.
3. **Review** (15 minutes). Open three pages signed out on the projector and go through the checklist together.

### Common questions

**May I use the lessons?** Yes.

**What if the time condition hides my page today?** Choose a period that includes today.

**What if I run out of time?** Publish what you have and map it; the review covers the rest.

### Pitfalls

- **Mapping someone else's page.** Each learner maps only their own campaign page.
- **Changing the shared header.** Not part of this task.
- **Cache confusion.** If the page does not change, check the mapping first, then clear caches.

## Hands-on exercise

Build your plan from lesson 8.1. Work in any order; the list is a reminder, not a recipe.

1. Create the layout (type from your plan), named `Campaign - <first name>`, identifier `campaign_<first name>`.
2. Link the header and footer zones to the shared header/footer layout.
3. Add the banner (a **Hero** block or a hero component) with a title, a sentence and a button.
4. Add a **List** with view type `grid_featured` and a manual collection of three items.
5. Add a **List** with a dynamic collection: the newest articles of the trainer's folder, content type *Article*,
   newest first, limit 6, and an offset if it shares a query with another list.
6. Pin one item at the first place of the dynamic list and note why in the block's **Name**.
7. Add a **Full view** block if your campaign page has its own text.
8. Name every block clearly; apply one theme CSS class.
9. Publish the layout (**Publish layout**, **Clear caches** if asked).
10. Create a mapping: node target = your campaign page, a **time** condition that includes today, a priority above the
    page's normal mapping; enable it.
11. Open your campaign page signed out and at phone width; fix what is wrong.
12. Make sure no draft of your layout is open; write a three-line handover note.

## Expected result

The trainer checks each campaign page against this list:

| Check | Where to look |
|---|---|
| Layout of the planned type, named and identified as agreed | **Layouts** list |
| Header and footer linked to the shared layout | The editor (linked zones) or the page |
| Banner with title, sentence and button | The page |
| Manual list of three items | The page and the block's collection |
| Dynamic list: articles, newest first, limit 6, no duplicates with the manual list | The page |
| One pinned item with the reason in the block's name | The editor |
| Layout published, no draft open | Opening the layout |
| Mapping: node target, time condition, enabled, wins over the normal mapping | **Layout mappings** and the page signed out |

A learner who meets six of the eight checks has the skills of this course; the trainer suggests the lessons behind
any missing check for review.

## Quiz

1. In which order do you publish and map, and why?
2. Your page shows the old layout signed out. Which three things do you check, in order?
3. How do you keep the dynamic list from repeating the manual highlights?
4. Why note the reason for a pin in the block's name?
5. What does the time condition do after the campaign month?

<details>
<summary>Answers</summary>

1. Publish the layout, then map (or enable the mapping): a mapping to an unpublished layout shows nothing new.
2. The mappings (priority, enabled), the layout's publish state, then caches
   ([Layout rules](../../../creators/website-creators/layout-rules.md#troubleshooting)).
3. Use an offset when both use the same query, or choose a query that excludes the highlighted items.
4. So the next editor knows why that position never changes.
5. The mapping no longer matches, so the page's normal mapping takes over.

</details>

## Further reading

- [Exponential Layouts: Walkthrough B](../../../../bc/6.0/LAYOUTS.md#walkthrough-b-building-a-new-layout-from-scratch):
  the same task in the reference.
- [The layouts editor](../../../layouts-editor.md): the editor in short, for learners to keep.

## Related pages

- [Module 8: Capstone](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
