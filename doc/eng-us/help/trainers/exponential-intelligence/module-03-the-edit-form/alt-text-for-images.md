---
title: "Lesson 3.3: Alt text for images"
audience: trainer, editor
category: training
order: 33
summary: Learners suggest an alternative text with Write alt text, choose Decorative (empty alt text) where right, review the suggestion against accessibility rules, and find images without alt text with the Missing alt text report. 45 minutes.
keywords: exponential intelligence, alt text, alternative text, write alt text, decorative, vision model, missing alt text, report, queue suggestions as drafts, accessibility, images
type: help
---
# Lesson 3.3: Alt text for images

Writing good alternative texts is slow work, and many sites have hundreds of images without one. The assistant can
look at an image and suggest a text; the editor reviews and decides. This lesson covers the button in the edit form
and the report that finds images without alt text.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 3: The edit form](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Editors |
| Prerequisites | [Lesson 3.2](fill-the-fields-presets-and-improve.md); Editors 201 lesson 8.2 (accessible content) helps |

## Learning objectives

After this lesson, learners can:

- suggest an alternative text with **Write alt text** and undo it;
- choose **Decorative (empty alt text)** for an image that carries no information, and know when it is not offered;
- review a suggestion against the accessibility rules;
- open **Missing alt text**, show the images of a subtree and know what **Queue suggestions as drafts** does.

## Trainer's notes

### How to present it

Source: the alt text page under **EI: Help** (`extension/expai/doc/alt-text.md`).

1. **The buttons** (3 minutes). Every image field gets two buttons above its **Alternative image text**: **Write alt
   text** (sends the stored image, the page title, the other fields' text and the language to a model that can look at
   images) and **Decorative (empty alt text)** (empties the field; no request). **Undo** restores the text. The image
   must be stored in the draft first.
2. **The rules** (4 minutes). The model is asked for what matters for the page, one concise sentence or phrase, no
   "Image of", visible text when it carries the message, no repeated caption, no guessed names, no judging people by
   appearance, in the content's language. The model never decides that an image is decorative; the editor does.
3. **Required alt text** (2 minutes). Where a field requires an alternative text, **Decorative** is disabled.
4. **Privacy** (2 minutes). The image goes to the provider inside the request; the site's data policy applies to it,
   and can be stricter for images. Never use this on photos of people without consent.
5. **The report** (3 minutes). **Missing alt text** (in the Design tab's left menu): **Node ID of the subtree** and
   **Show** list the images (**Content**, **Class**, **Field**, **Language**, **Size**); **Queue suggestions as
   drafts**, with an optional **Budget of this job group (optional, USD)**, creates drafts with suggestions through
   the job queue. Each draft still needs an editor's review.

### Common questions

**Why is Write alt text disabled?** No model that can look at images is available to you, the data policy keeps the
image away from every such model, or the image is not stored in the draft yet. The button says why.

**Is the suggestion always right?** No. It cannot know why the image is on the page as well as you do. Read it.

**Does the report change published images?** No. It queues drafts; nothing is published.

### Pitfalls

- **Photos of people.** Do not send them without consent; describe them yourself.
- **Decorative by default.** Most images on a content page carry information.
- **Queuing a whole site.** It creates many drafts and costs; start with one folder and a budget.

## Hands-on exercise

Use the training installation and the sample images from your course folder (no photos of people).

1. Open one of your images with **Edit**. Find **Alternative image text** and the two buttons above it.
2. Press **Write alt text**. Read the suggestion and the note (model, image size sent, estimated cost).
3. Compare the suggestion with the rules the trainer projected. Improve it by hand if needed.
4. Press **Undo** and **Write alt text** once more; keep the version you prefer. Store the draft and publish it.
5. On a purely decorative image (a pattern or divider the trainer provided), press **Decorative (empty alt text)** and
   publish.
6. Open **Missing alt text** in the Design tab's left menu (if your role has it). Type your course folder's node number
   in **Node ID of the subtree** and press **Show**.
7. Read the list. Do not press **Queue suggestions as drafts** unless the trainer asks (it creates drafts and costs).

## Expected result

- One image has a reviewed alternative text, one decorative image has an empty one.
- The report listed the images of the learner's folder that still lack alt text (possibly none).
- The trainer checks by reading two alternative texts aloud.

## Quiz

1. What does **Write alt text** send to the model?
2. Who decides that an image is decorative?
   A. The model B. The editor C. The site D. Nobody
3. Why might **Decorative (empty alt text)** be disabled?
4. Name two rules for a good alternative text.
5. What does **Queue suggestions as drafts** create?

<details>
<summary>Answers</summary>

1. The stored image, the page title, the text of the other fields and the language.
2. **B.**
3. The field requires an alternative text.
4. Any two of: what matters for the page; one concise sentence or phrase; no "Image of"; the visible text when it
   carries the message; no repeated caption; no guessed names; the content's language.
5. Drafts with suggested alternative texts, through the job queue, for editors to review.

</details>

## Further reading

- The alt text page under **EI: Help** (`extension/expai/doc/alt-text.md`).
- [Accessibility standards](../../../../standards/accessibility.md): what depends on editors.
- [Images, media and uploads](../../../media-and-uploads.md#good-practice)

## Related pages

- [Module 3: The edit form](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
