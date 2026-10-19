---
title: "Lesson 2.3: Writing an edition"
audience: trainer, editor
category: training
order: 23
summary: Learners create an edition in their list, add two newsletter articles, publish, and look at the HTML and text previews on the edition's page. 60 minutes.
keywords: newsletters with cjw newsletter, edition, cjw_newsletter_edition, newsletter article, cjw_newsletter_article, create newsletter here, preview, html, text, copy edition
type: help
---
# Lesson 2.3: Writing an edition

An edition is one issue of a list: a piece of content with its own articles. Because it is content, editors use
what they already know from editing pages: drafts, versions, the online editor and preview. In this lesson learners
write a small edition and look at how it will look as a mail, in HTML and in plain text.

> **Note**
> Writing and publishing an edition sends nothing. Sending is a separate step (module 4).

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 2: Systems, lists and editions](README.md) |
| Duration | 60 minutes (15 presenting, 45 exercise) |
| Level | Beginner |
| Audience | Editors who write newsletters |
| Prerequisites | [Lesson 2.2](creating-and-configuring-a-list.md): the learner has a list; basic editing (Editors 101, module 3) |

## Learning objectives

After this lesson, learners can:

- create an edition with **Create newsletter here** on a list's page;
- fill in the required fields **Title** and **Description**;
- add newsletter articles to the edition;
- open the **HTML / Text** and **Text** previews of an edition;
- copy an existing edition as the start of the next one.

## Trainer's notes

### How to present it

Sources: [Writing and sending an edition](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#writing-and-sending-an-edition)
and [Creating and editing content](../../../editing-content.md).

1. **The edition class** (4 minutes). `CJW Newsletter Edition` has **Title** (required), **Short title**,
   **Short description**, **Description** (required; the main text of the issue) and **Newsletter edition**, the
   field that carries the newsletter's own data. Its name in lists is the short title when filled in.
2. **Articles** (3 minutes). An edition is a container. Its items of the class `CJW Newsletter Article` have
   **Title** and **Short description**, both required. The default skin prints the description, then the articles.
3. **Preview** (4 minutes). On the edition's page, the tabs **HTML / Text** and **Text** show the edition rendered as
   the mail parts. This is a preview only; the real mail is rendered again at sending, in the skin of the send.
4. **Copying** (2 minutes). The edition page has **Copy**. Copy last month's edition, change it, and you keep the
   structure. A schedule can also copy a template edition (lesson 2.4).
5. **Status** (2 minutes). Point at **Statistics** and **Newsletter processing info** on the page: empty until the
   edition is sent.

### Common questions

**Can I use images?** Yes, in the rich text fields, as on any page. Mail programs often block images until the
reader allows them, so the text must make sense without them.

**What if I change an edition after sending?** The send keeps what was rendered at sending time. A change makes a
new version, which you would send again as a new send.

**Can an edition be translated?** Yes; it is content. A list can offer several languages in 4.2 (see the rendering
guide linked from the [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).

**Where do I write the subject?** The skin sets the subject, normally from the edition's title. Choose the title
with that in mind.

### Pitfalls

- **Description left empty.** It is required; the form refuses to publish and names the field.
- **Articles created outside the edition.** An article created in the wrong place is not part of the issue. Check
  the path before **Create new**.
- **Long previews.** A large edition renders slowly in the preview tab on a small training server; wait for it.

## Hands-on exercise

Use the training installation and your account. Replace `<name>` with your first name.

1. Open your list `Weekly tips <name>` from lesson 2.2.
2. Press **Create newsletter here**.
3. In **Title**, type `Tips for week 1`.
4. Leave **Description** empty and press **Send for publishing**. Read the message, then type two sentences of
   introduction into **Description**.
5. Press **Send for publishing**. The edition appears under **Newsletter editions** on the list's page.
6. Open the edition. Under **Sub items**, press **Create new** and choose **CJW Newsletter Article**.
7. **Title**: `Tip 1: Short subject lines`. **Short description**: one sentence of advice. Publish.
8. Create a second article, `Tip 2: One idea per issue`, the same way.
9. On the edition's page, open the **HTML / Text** tab and read the rendered newsletter.
10. Open the **Text** tab and compare: what happened to the headings and links?
11. Press **Copy** on the edition and keep the copy in the same list. Rename the copy to `Tips for week 2`.

## Expected result

- The list shows two editions, *Tips for week 1* and *Tips for week 2*, each with two articles.
- The **HTML / Text** tab shows the introduction and both tips in the default skin; the **Text** tab shows the same
  content as plain text.
- **Newsletter processing info** of both editions is empty: nothing was sent.
- The trainer checks by opening one learner's preview tabs on the projector.

## Quiz

1. Which button on a list's page creates an edition?
2. Which two fields of an edition are required?
   A. **Title** and **Short title** B. **Title** and **Description** C. **Description** and **Short description**
   D. **Newsletter edition** and **Title**
3. True or false: publishing an edition sends it to the subscribers.
4. Where do you see how the edition will look as plain text?
5. Why should the text of an edition make sense without images?

<details>
<summary>Answers</summary>

1. **Create newsletter here**.
2. **B.** On a standard installation of `cjw_newsletter`; check your own form.
3. **False.** Sending is a separate step with the send form and cron ([Writing and sending an edition](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#writing-and-sending-an-edition)).
4. In the **Text** tab of the edition's page.
5. Many mail programs block images until the reader allows them ([Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md#before-every-send)).

</details>

## Further reading

- [Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#writing-and-sending-an-edition):
  the edition in the whole workflow.
- [Creating and editing content](../../../editing-content.md): the edit form, drafts and conflicts.
- [Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md#before-every-send): the
  checklist before every send.

## Related pages

- [Module 2: Systems, lists and editions](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
