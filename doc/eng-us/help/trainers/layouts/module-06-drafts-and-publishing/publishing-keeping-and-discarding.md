---
title: "Lesson 6.1: Publishing, keeping and discarding a draft"
audience: trainer, editor, website-creator
category: training
order: 61
summary: Learners publish their course layout, publish and continue editing, keep a draft for later, discard a draft, answer the cache question, and continue an existing draft with New draft or Edit existing. 45 minutes.
keywords: exponential layouts, draft, publish layout, publish and continue editing, save draft and close, discard, discard changes, cancel, clear caches, new draft, edit existing
type: help
---
# Lesson 6.1: Publishing, keeping and discarding a draft

Everything learners did so far stayed in drafts. This lesson makes the course layout live, and teaches every way out of
the editor, so that nobody publishes by accident and nobody loses a morning's work.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 6: Drafts and publishing](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors and site builders |
| Prerequisites | [Module 5](../module-05-shared-layouts-and-zone-linking/README.md) |

## Learning objectives

After this lesson, learners can:

- explain that the editor always works on a draft, saved as they go, and that visitors see only published layouts;
- choose between **Publish layout**, **Publish and continue editing**, **Save draft and close**, **Discard** and
  **Cancel**;
- answer the cache question after publishing;
- continue an existing draft or start a new one when opening a layout.

## Trainer's notes

### How to present it

Sources: [The layouts editor: Publish, keep or throw away](../../../layouts-editor.md#publish-keep-or-throw-away) and
[Exponential Layouts: Drafts, publishing and versions](../../../../bc/6.0/LAYOUTS.md#drafts-publishing-and-versions).

1. **Drafts** (3 minutes). Opening a published layout makes a draft of it. Changes are saved to the draft
   continuously; there is no separate save step. The site keeps showing the published layout until you publish.
2. **The ways out** (5 minutes):

   | Button | What it does |
   |---|---|
   | **Publish layout** | Publishes the draft and closes the editor; pages that use the layout show it |
   | **Publish and continue editing** (menu with three dots) | Publishes and keeps the editor open with a new draft |
   | **Save draft and close** (menu with three dots) | Keeps the draft for later; the published layout is unchanged |
   | **Discard** | Throws the draft away after **Discard changes** asks *Are you sure you want to discard your changes to the layout draft?*; **Cancel** in that dialog keeps it |
   | **Cancel** | Leaves the editor; the draft stays |

   Leaving the page otherwise may ask *There could be unsaved changes. Are you sure you want to leave?*
3. **Caches** (3 minutes). Where a site does not clear its caches by itself after a publish, the editor asks: *Automatic
   cache clear for this site is turned off. Do you want to clear caches after publish?* Choose **Clear caches** so
   visitors see the change; **Do not clear caches** leaves cached pages until they expire.
4. **Opening a layout with a draft** (2 minutes). When a draft exists, the editor may ask whether to continue it
   (**Edit existing**) or start a **New draft** from the published layout. Drafts belong to the layout, not to a
   person: someone else's draft is shared work.

### Common questions

**Does Discard delete the layout?** No. It throws away the draft; the published layout stays. A layout that was never
published has nothing left to show.

**When should I use Publish and continue editing?** When a first change must go live now and you continue with more.

**Why does the page still look old after Publish layout?** Cached pages; or the page is served by another mapping
(module 7).

### Pitfalls

- **Publish layout on a shared layout.** It changes every linking page. Check twice (module 5).
- **Discard instead of Cancel.** Discard throws work away; Cancel keeps the draft. Read the dialog.
- **Two people, one layout.** One person's **Discard** throws away the other's changes in the shared draft.

## Hands-on exercise

Use the training installation and your course layout (with the blocks of modules 3 to 5).

1. Open your course layout with **Open**. If the editor asks, choose **Edit existing** to continue your draft.
2. Check the blocks: title, image, container with rich text and button, Full view, lists, linked header and footer.
3. Press **Publish layout**. If the editor asks about caches, choose **Clear caches**.
4. Open the layout again. Note whether the editor asks about an existing draft (it should not after publishing).
5. Change the title text, open the menu with three dots and choose **Publish and continue editing**.
6. Change the title again, then press **Discard**. Read **Discard changes** and press **Cancel** in the dialog: the
   draft stays.
7. Press **Discard** again and confirm this time. The editor closes; the published title is the one of step 5.
8. Open the layout, add a **Spacer** block, and choose **Save draft and close**. Check with **Preview** on the
   **Layouts** list that the published layout has no spacer.
9. Open the layout once more, choose **Edit existing**, delete the spacer, and press **Publish layout**.

## Expected result

- The learner's course layout is published with the step 5 title and no spacer.
- The learner used every way out: publish, publish and continue, discard (cancelled and confirmed), save draft.
- The trainer checks by previewing two learners' layouts on the projector.

## Quiz

1. What does the site show while you work on a draft?
2. Which choice publishes and keeps the editor open?
   A. **Publish layout** B. **Publish and continue editing** C. **Save draft and close** D. **Cancel**
3. What is the difference between **Discard** and **Cancel**?
4. The editor asks about clearing caches after publishing. What do you choose, and why?
5. True or false: drafts belong to one person, so two people can safely edit the same layout.

<details>
<summary>Answers</summary>

1. The published layout ([The layouts editor](../../../layouts-editor.md#the-editor)).
2. **B.**
3. **Discard** throws the draft away (after a confirmation); **Cancel** leaves the editor and keeps the draft.
4. **Clear caches**, so visitors see the change.
5. **False.** Drafts belong to the layout; two people overwrite each other.

</details>

## Further reading

- [The layouts editor: Publish, keep or throw away](../../../layouts-editor.md#publish-keep-or-throw-away)
- [A tour of the administration: Draft or publish](../../../admin-tour.md#7-draft-or-publish)

## Related pages

- [Module 6: Drafts and publishing](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
