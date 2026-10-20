---
title: "Lesson 1.1: What Exponential is, for an editor"
audience: trainer, editor
category: training
order: 11
summary: Learners can explain content items, the content tree, drafts, publishing and roles in their own words before they touch the administration. 30 minutes.
keywords: editors 101, content item, object, content tree, draft, publish, version, role, administration, site, siteaccess, beginner
type: help
---
# Lesson 1.1: What Exponential is, for an editor

New editors learn faster when they first have a picture of what they are working on. This lesson gives them that
picture in five ideas: content items, the tree, drafts, publishing and roles. Nothing is clicked yet; the exercise is
a short look at a site and a sketch on paper.

> **Note**
> Use a training installation for every exercise of this course, never a live site. Lesson 1.2 explains how the
> trainer prepares one.

## At a glance

| | |
|---|---|
| Course | [Editors 101](../README.md) |
| Module | [Module 1: Signing in and the tour](README.md) |
| Duration | 30 minutes (20 presenting, 10 exercise) |
| Level | Beginner |
| Audience | New editors, authors, communication staff, anyone who will write for a site built with Exponential |
| Prerequisites | None. A browser for the exercise. |

## Learning objectives

After this lesson, learners can:

- explain that everything on the site (a page, an article, an image, a file, a user) is a **content item**;
- describe the **content tree** and say why an item's place in it matters;
- explain the difference between a **draft** and the **published** version;
- name the two places they work with: the **administration** (or the **Editor**) and the **site**;
- say what a **role** decides for them.

## Trainer's notes

### How to present it

1. **Content items** (5 minutes). Exponential is the content management system behind the site. Everything on the
   site is a content item, also called an object: a folder, an article, an image, a file, even a user account. Each
   item has a **type** (in the administration: *Folder*, *Article*, *Image* ...), and the type decides which fields
   the item has. An *Article* has a title, an intro, a body and an image; a *Folder* has a name, a summary and a
   description. Source: [Getting started](../../../getting-started.md#what-exponential-is).
2. **The tree** (5 minutes). Items are arranged in a tree, like folders on a computer: a folder holds articles, an
   article can hold images. Draw it on the board:

   ```text
   Websites (the top of the content)
     News (Folder)
       First article (Article)
       Second article (Article)
     About us (Folder)
   Media
     Images
     Files
   ```

   Where an item sits decides where it appears on the site and, usually, its address. Point out that **Media** is a
   part of the tree of its own, kept for images and files.
3. **Drafts and publishing** (5 minutes). A change is never visible at once. Pressing **Edit** makes a **draft**, a
   copy only its creator sees; the site keeps showing the published version until the draft is **published**. The
   earlier version is kept as an archived version, so an editor can always go back. Source:
   [Publishing, versions and the trash](../../../publishing-and-versions.md#drafts-and-versions).
4. **Two places** (3 minutes). Editors work in the **administration**, or on many installations in the **Editor**,
   a simpler interface with only what writing and publishing need. Visitors see the **site**. Both are parts of the
   same installation (each a *siteaccess*) and work on the same content.
5. **Roles** (2 minutes). What an editor may open, create and change is decided by the **role** of their account,
   usually *Editor*. An editor sees fewer tabs and buttons than an administrator. Nothing is wrong when a button is
   missing; it is the role.

Keep it short: the next lessons show all of this on screen.

### Common questions

**Is a page the same as an article?** On the site, a "page" is whatever a visitor sees at an address. In the
administration that is usually one content item (an article, a folder) shown with the site's design. Some sites
also arrange pages with Exponential Layouts; that is a course of its own.

**Can visitors see my draft?** No. A draft is visible only to its creator until it is published.
[Publishing, versions and the trash](../../../publishing-and-versions.md#drafts-and-versions) lists the states
draft, pending, published and archived.

**What happens to the old text when I publish?** It becomes an archived version and stays available under
**Manage versions** (module 6), until the site's limit of versions per item is reached.

**Why do I have a different screen than my colleague?** Different roles, a different administration design, or a
different interface language. Lesson 1.3 shows where those are chosen.

### Pitfalls

- **Too much theory.** Beginners do not need classes, siteaccesses or policies yet. Use the words "type" and
  "part of the installation" and move on.
- **"Saving" versus "publishing".** Learners from word processors expect a save button. Stress early that storing a
  draft and publishing are two different things, and that only publishing changes the site.
- **The old product name.** Some learners know the system from earlier jobs under its former name. Exponential
  continues that software, whose original creator discontinued it after almost 18 years; the ideas they know (the
  tree, drafts, versions) still hold.

## Hands-on exercise

Use the public site of the training installation the trainer prepared (or any site built with Exponential that the
trainer names). No account is needed.

1. Open the site's front page in your browser.
2. Write down three things you see on the front page that you think are separate content items (for example a
   news article, an image, a menu entry).
3. Follow the main menu to a section of the site and note its address in the browser's address bar.
4. Open one article in that section and note its address too.
5. Compare the two addresses: does the article's address start with the section's? Write down what this tells you
   about where the article sits in the tree.
6. On paper, draw the part of the tree you visited: the section as a folder, the article below it.
7. Mark on your drawing which item you would open to change the article's title.

## Expected result

- Each learner has a small tree sketch with a section (folder) and at least one article below it.
- Most sites give an article an address below its section's, so learners conclude that the article is a sub item
  of the section. Where the site does not (some sites use short addresses), the trainer explains that addresses can
  be set by the site and that lesson 2.1 shows the real tree.
- The trainer collects two or three sketches and compares them on the board with the real tree in lesson 2.1.

![The sub items of a site's front page: its sections, each a folder that holds the articles below it](../../../images/trainers/e101-site-tree.webp)

## Quiz

1. Which of these is a content item in Exponential?
   A. Only articles B. Only pages with text C. Articles, folders, images, files and users D. Only what is in the
   Media library
2. You press **Edit** on an article and change its title. When do visitors see the new title?
3. What decides which tabs and buttons you see in the administration?
   A. Your browser B. Your role C. The time of day D. The size of the site
4. True or false: the Editor and the administration work on different copies of the content.
5. What happens to the previously published text when a new version is published?

<details>
<summary>Answers</summary>

1. **C.** Everything on the site is a content item ([Getting started](../../../getting-started.md#what-exponential-is)).
2. Only after the draft is published; until then the site shows the published version.
3. **B.** The role of your account.
4. **False.** Both are parts of the same installation and work on the same content
   ([Logging into the Editor](../../../logging-into-the-editor.md)).
5. It becomes an archived version and can be brought back later
   ([Publishing, versions and the trash](../../../publishing-and-versions.md#drafts-and-versions)).

</details>

## Further reading

- [Getting started](../../../getting-started.md): the ten-minute introduction this lesson is built on.
- [The content model and editing content](../../../../guides/content-model-and-editing.md): how types (classes),
  fields and items fit together, for learners who want more.
- [Publishing, versions and the trash](../../../publishing-and-versions.md): the states of a version.

## Related pages

- [Module 1: Signing in and the tour](README.md)
- [Editors 101: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
