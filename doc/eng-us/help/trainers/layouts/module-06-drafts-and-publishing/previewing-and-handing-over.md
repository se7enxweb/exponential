---
title: "Lesson 6.2: Previewing, checking and handing over"
audience: trainer, editor, website-creator
category: training
order: 62
summary: Learners preview a published layout against real content, check it at phone width and signed out, confirm no draft is outstanding before a handover, and know the share link and backup options. 45 minutes.
keywords: exponential layouts, preview, layout preview, check, phone width, signed out, versions, outstanding draft, handover, share link, export, backup
type: help
---
# Lesson 6.2: Previewing, checking and handing over

A layout is finished when it has been checked where visitors see it and when nobody has unfinished work in it. This
lesson teaches the checks and the handover habits of a layouts team.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 6: Drafts and publishing](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors and site builders |
| Prerequisites | [Lesson 6.1](publishing-keeping-and-discarding.md) |

## Learning objectives

After this lesson, learners can:

- preview a layout with **Preview** and explain what content it uses;
- check a published page at phone width and signed out;
- confirm that no draft is outstanding before handing work over;
- name the backup and sharing options (export, share links) and when they are used.

## Trainer's notes

### How to present it

Sources: [explayouts_ui: Preview a layout](../../../../features/6.0/extensions/explayouts_ui.md#preview-a-layout) and
[Exponential Layouts: Drafts, publishing and versions](../../../../bc/6.0/LAYOUTS.md#drafts-publishing-and-versions).

1. **Preview** (3 minutes). **Preview** in the **Layouts** list renders a layout against a representative content item,
   resolved from its mapping's target, so content-driven blocks show real content; **Load more** works there too.
2. **Checking where visitors are** (4 minutes). Open the mapped page in a private window (signed out) and at phone
   width (the browser's responsive view). Check the order of blocks, the images, the links, the header and footer.
3. **Outstanding drafts** (3 minutes). The reference describes a versions view listing a layout's published and draft
   versions with their times; at least, opening a layout tells you whether a draft exists. Before handing work over,
   publish or discard your draft.
4. **Sharing and backup** (3 minutes). **Share links** grant a preview of a layout without an admin login, for someone
   without an account; tokens are created and revoked per layout. **Export** keeps a copy before a large change.
   Check on your installation where these are offered and who may use them.

### Common questions

**Why does the preview show different content from the page?** It uses a representative item from the mapping; the
page uses its own.

**How do I show a layout to a client without an account?** A share link, where your installation offers it, or a
published page on a staging site.

**What should a handover note say?** Which layouts changed, which mappings point at them, and that no draft is open.

### Pitfalls

- **Checking only signed in.** Editors may see uncached or permission-dependent content.
- **Leaving a draft open at the end of the day.** The next person continues or discards it without knowing.
- **Share links left active.** Revoke them when the review is over.

## Hands-on exercise

Use the training installation, your published course layout and your test page.

1. Open **Layouts** > **Layouts**, find your course layout and press **Preview**.
2. Write down which content the Full view block and the lists show in the preview.
3. If your course layout is mapped to your test page (module 7, or the trainer mapped it), open the test page in a
   private window; otherwise use the preview.
4. Switch the browser to phone width and check each block. Note one thing to improve.
5. Open your course layout with **Open**. Does the editor report an existing draft? If yes, decide: publish it or
   discard it.
6. Close the editor with **Cancel** (no draft should remain if there was none).
7. Write a three-line handover note for your layout: what it contains, where it is used, and that no draft is open.
8. The trainer shows on the projector where the training installation offers share links and export, if it does.

## Expected result

- The learner previewed the layout, checked it signed out and at phone width, and noted one improvement.
- No draft of the learner's layout is open at the end.
- Each learner has a handover note.
- The trainer checks by opening two layouts: the editor reports no existing draft.

## Quiz

1. What content does **Preview** use?
2. Why check a page in a private window?
3. What must be true before you hand a layout over?
   A. It has a share link B. No draft is outstanding C. It is shared D. It has five blocks
4. What is a share link for?
5. Name one thing a handover note should say.

<details>
<summary>Answers</summary>

1. A representative content item resolved from the layout's mapping
   ([explayouts_ui](../../../../features/6.0/extensions/explayouts_ui.md#preview-a-layout)).
2. To see the page as a visitor sees it, signed out and through the site's caches.
3. **B.**
4. A preview of a layout for someone without an admin login.
5. Which layouts changed, which mappings use them, and that no draft is open.

</details>

## Further reading

- [explayouts_ui: Preview a layout](../../../../features/6.0/extensions/explayouts_ui.md#preview-a-layout)
- [Exponential Layouts: Import, export and share links](../../../../bc/6.0/LAYOUTS.md#import-export-and-share-links)
- [Landing pages with the layouts editor: Handing over to editors](../../../creators/website-creators/landing-pages-with-layouts.md#handing-over-to-editors)

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
