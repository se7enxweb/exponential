---
title: "Lesson 9.1: Working in the Editor"
audience: trainer, editor
category: training
order: 91
summary: Learners sign in to the Editor siteaccess, compare it with the administration, and explain why some tabs and pages are not there. 30 minutes.
keywords: editors 101, editor siteaccess, editor interface, edit host, administration, siteaccess, tabs, admin4l, simplified admin, compare
type: help
---
# Lesson 9.1: Working in the Editor

Many installations give their editors the **Editor**: an administration of its own that shows only what writing and
publishing need. Everything learned in this course works there the same way. This lesson shows learners the Editor
next to the administration, so they know what to expect when their own site uses it.

## At a glance

| | |
|---|---|
| Course | [Editors 101](../README.md) |
| Module | [Module 9: The editor siteaccess](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Beginner |
| Audience | New editors and authors |
| Prerequisites | Modules 1 to 8; the address of the Editor on the training installation |

## Learning objectives

After this lesson, learners can:

- open the Editor's address and sign in to it;
- name the tabs the Editor shows and compare them with the administration;
- explain that the Editor and the administration work on the same content;
- say why pages for settings, layouts and the system are not in the Editor, and where they are instead.

## Trainer's notes

### How to present it

Sources: [Logging into the Editor](../../../logging-into-the-editor.md) and
[The setup wizard's new look, and the editor siteaccess](../../../../features/6.0/setup-wizard-and-editor-siteaccess.md).

1. **Three parts of one installation** (3 minutes). New installations come with three siteaccesses: the site for
   visitors, the admin for administrators, and the **editor** for people who edit content but do not administer the
   site. By convention the Editor has a host name such as `edit.example.com`, or a path such as `/editor`. Its title
   says *Editor*, for example on the sign-in page (*Administration of Editor*) and in the browser tab
   (*Dashboard - Editor*).
2. **What it shows** (4 minutes). Only the content tabs: on a typical installation **Dashboard**, **Content
   structure** and **Media**, plus tabs such as **Users**, **Store**, **Tags** or **Newsletter** where the role and
   the extensions allow. It has no developer toolbar and no **Layouts** tab on an item; the modules behind the
   hidden tabs (setup, layouts, roles, sections, object states, workflows and more) answer "not found" there. Object
   states and role assignments are set in the admin.
3. **What stays the same** (3 minutes). The dashboard, the tree, the item pages, the edit form, the upload page,
   versions, search and bookmarks work as in the rest of this course. A sign-in to the Editor does not sign you in to
   the administration, and the other way round.

### Common questions

**Which one should I use?** The one your administrator gives you. The Editor exists so that editors see less to
worry about.

**Is my draft from the administration visible in the Editor?** Yes. Both work on the same content; your drafts are in
**My drafts** in both.

**Why does the Editor say "not found" for a page my colleague opens in the admin?** That module is blocked in the
Editor on purpose. Use the admin, if your role allows it.

### Pitfalls

- **No Editor on the training installation.** Installations made before the editor siteaccess existed do not have
  one. Then show the screenshots in [Logging into the Editor](../../../logging-into-the-editor.md) and skip the
  exercise's sign-in steps.
- **Two sign-ins.** Learners are surprised that they must sign in again. That is by design: a sign-in belongs to one
  part of the installation.

## Hands-on exercise

Use the training installation's Editor address, which the trainer writes on the board.

1. Open the Editor's address in a new browser tab. Read the line under the title of the sign-in page.
2. Sign in with your training account.
3. Write down the tabs the Editor shows in its header.
4. Open **My drafts** and **My bookmarks** from the left column: your bookmarks from lesson 8.2 are there.
5. Open **Content structure** and find your course folder. Open *My first article*.
6. Write down the tabs of the article's page in the Editor and compare them with the administration's (switch to the
   other browser tab).
7. Press **Edit** on *My first article*, change one word, and press **Send for publishing**.
8. In the administration's browser tab, reload the article: the change is there.
9. Sign out of the Editor with **Logout**. Check that you are still signed in to the administration in the other tab.

## Expected result

- The sign-in page named the Editor (for example *Administration of Editor*).
- The learner's list of tabs is shorter than the administration's (for example **Dashboard**, **Content structure**,
  **Media**).
- The change published in the Editor appeared in the administration at once.
- Signing out of the Editor did not sign the learner out of the administration.
- The trainer checks by asking a learner to name one tab the administration has and the Editor does not.

## Quiz

1. What are the three siteaccesses of a new installation?
2. True or false: the Editor works on a copy of the content.
3. Name two things the administration has that the Editor does not.
4. You sign out of the Editor. Are you still signed in to the administration?
5. Where are object states set when the Editor blocks them?
   A. In the Editor's dashboard B. In the admin C. On the public site D. Nowhere

<details>
<summary>Answers</summary>

1. The site, the admin and the editor
   ([The setup wizard's new look, and the editor siteaccess](../../../../features/6.0/setup-wizard-and-editor-siteaccess.md#the-three-siteaccesses)).
2. **False.** Both work on the same content.
3. Any two of: the **Layouts** tab, **Setup**, the developer toolbar, roles, sections, object states, workflows.
4. Yes. A sign-in belongs to one part of the installation.
5. **B.**

</details>

## Further reading

- [Logging into the Editor](../../../logging-into-the-editor.md): the Editor's sign-in.
- [The setup wizard's new look, and the editor siteaccess](../../../../features/6.0/setup-wizard-and-editor-siteaccess.md):
  what the Editor shows and blocks.

## Related pages

- [Module 9: The editor siteaccess](README.md)
- [Editors 101: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
