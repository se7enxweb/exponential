---
title: "Agenda 2.4: The layouts day"
audience: trainer, editor, website-creator
category: training
order: 24
summary: A one-day workshop on Exponential Layouts, layouts, zones and blocks, creating and publishing a layout, block properties, shared layouts and linked zones, and mappings that decide where a layout is used. 6 hours of sessions.
keywords: one day, layouts, exponential layouts, workshop agenda, zones, blocks, shared layout, linked zone, mapping, rules, page builder
type: help
---
# Agenda 2.4: The layouts day

Exponential Layouts lets editors and site builders compose pages from blocks without code. In one day learners can
build, publish and map a landing page, and understand how a shared header and footer reach every page.

## At a glance

| | |
|---|---|
| Course | [Workshops and bootcamps](../README.md) |
| Module | [Module 2: One-day agendas](README.md) |
| Duration | 6 hours of sessions (9:00 to 16:30 with breaks and lunch) |
| Level | Beginner to intermediate |
| Audience | Editors and site builders, 6 to 12 learners; Editors 101 basics expected |
| Prerequisites | A training installation per learner (the `sevenx_multisite` package, which has layouts) with administrator or layout-editing access |

## Learning objectives

At the end of the day, learners can:

- explain layouts, layout types, zones, blocks, mappings and shared layouts;
- create a layout, add and configure blocks, and publish it;
- tell **Publish layout**, **Save draft and close**, **Discard** and **Cancel** apart;
- read the mappings and say which layout a page uses.

## The agenda

| Time | Block | Uses | Checkpoint |
|---|---|---|---|
| 9:00 | Welcome; what layouts are (45 min) | [The layouts editor: layouts, zones and blocks](../../../layouts-editor.md#layouts-zones-and-blocks) | Concepts named |
| 9:45 | The Layouts Dashboard and the list (30 min) | [Find a layout](../../../layouts-editor.md#find-a-layout) | A layout opened and previewed |
| 10:15 | Break (15 min) | | |
| 10:30 | Create a layout; the editor (75 min) | [Create a layout](../../../layouts-editor.md#create-a-layout), [The editor](../../../layouts-editor.md#the-editor) | A two-column layout with a Title block |
| 11:45 | Lunch (45 min) | | |
| 12:30 | Blocks: content and design properties (75 min) | [Add a block](../../../layouts-editor.md#add-a-block), [Change a block](../../../layouts-editor.md#change-a-block) | Four blocks configured |
| 13:45 | Break (15 min) | | |
| 14:00 | Publishing, drafts and discarding (30 min) | [Publish, keep or throw away](../../../layouts-editor.md#publish-keep-or-throw-away) | Layout published |
| 14:30 | Shared layouts and linked zones (45 min) | [Layouts, zones and blocks](../../../layouts-editor.md#layouts-zones-and-blocks) | A linked zone found in a layout |
| 15:15 | Mappings; wrap-up (75 min) | [Which pages use a layout](../../../layouts-editor.md#which-pages-use-a-layout) | The mapping of one page found |
| 16:30 | End | | |

Cut items, in order: creating a new mapping (read only); the design properties.

## Trainer's notes

- **Mappings decide what whole parts of a site look like.** On a shared site, let learners read mappings but create
  them only on their own installation.
- **Always a draft.** Opening a published layout makes a draft; the site shows the published layout until you publish.
- **Caches**: where the site does not clear its caches after a publish, the editor asks; choose **Clear caches**.
- **After the day**, point to the full Exponential Layouts course.

## Hands-on exercise: the capstone of the day

On the learner's own installation:

1. Press **New layout**, choose a two-column **Layout type**, name it "Bike Day landing" and press **Create layout**.
2. Add a Title block and a Rich text block to the left zone, and an Image block to the right zone.
3. Fill in their **Content** properties; change one **Design** property.
4. Use **Save draft and close**; reopen it; then **Publish layout**.
5. Open **Rules** and find which layout the front page uses.

## Expected result

- "Bike Day landing" is published with three configured blocks.
- The learner can name the layout and the mapping of the front page.

## Quiz (end of day)

1. What decides which zones a layout has?
2. What does **Save draft and close** do to the published layout?
3. What is a shared layout for?
4. Two mappings match a page. Which wins?

<details>
<summary>Answers</summary>

1. The layout type ([The layouts editor](../../../layouts-editor.md#layouts-zones-and-blocks)).
2. Nothing; the published layout is unchanged.
3. Holding blocks that many pages use, such as a header and footer; other layouts link a zone to it.
4. The first enabled mapping by priority whose targets and conditions match.

</details>

## Further reading

- [The layouts editor](../../../layouts-editor.md)
- [Talking points: Exponential Layouts](../../../advocates/talking-points-exponential-layouts.md)

## Related pages

- [Module 2: One-day agendas](README.md)
- [Workshops and bootcamps: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
