---
title: "Lesson 2.1: Newsletter systems and the tree"
audience: trainer, editor, admin
category: training
order: 21
summary: Learners find the newsletter root, explain RootFolderNodeId and the CJW Newsletter section, and create a newsletter system of their own. 45 minutes.
keywords: newsletters with cjw newsletter, newsletter system, cjw_newsletter_system, RootFolderNodeId, newsletter root, section, CJW Newsletter, content tree
type: help
---
# Lesson 2.1: Newsletter systems and the tree

Every newsletter lives in the content tree, below one root folder that the settings name. A *newsletter system*
groups lists that belong together, for example all newsletters of one brand or one department. In this lesson
learners find the root, understand how the administration knows where it is, and create a system of their own.

> **Note**
> Use a training installation. On a shared training site, each learner creates a system with their own name in it,
> so nobody edits someone else's work.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 2: Systems, lists and editions](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Editors and administrators |
| Prerequisites | [Module 1](../module-01-getting-started/README.md); an admin training account |

## Learning objectives

After this lesson, learners can:

- find the newsletter root and the systems below it from the newsletter area's left menu;
- explain what `[NewsletterSettings] RootFolderNodeId` does and read its value in the settings viewer;
- explain why the newsletter tree has a section of its own;
- create a newsletter system with **Title**, **Short title** and **Short description**.

## Trainer's notes

### How to present it

Sources: [The parts](../../../../features/6.0/extensions/cjw_newsletter.md#the-parts),
[Setting it up](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#setting-it-up) and
[The newsletter tree of a new installation](../../../../features/6.0/extensions/cjw_newsletter.md#the-newsletter-tree-of-a-new-installation).

1. **The root** (4 minutes). `cjw_newsletter.ini [NewsletterSettings] RootFolderNodeId` names the node whose
   children are the newsletter systems. The shipped value is `1`, so every installation sets its own in
   `settings/override/cjw_newsletter.ini.append.php`. The installer class of the extension can create a root
   (`cjw_newsletter_root`), a system and a list, and write this setting; administrators read about it on the
   feature page.
2. **The section** (3 minutes). The newsletter tree is placed in the section "CJW Newsletter" with the navigation
   part of the newsletter. That is why opening a newsletter item keeps the newsletter's left menu instead of the
   content menu. See [Sections](../../../../guides/sections.md).
3. **The class** (3 minutes). `CJW Newsletter System` has three fields: **Title** (required), **Short title** and
   **Short description**. Its name in lists is the short title when it is filled in, else the title.
4. **When to make a second system** (5 minutes). One system per brand, language market or department that has its
   own lists. Most sites need one. Discuss: what would your organization need?

### Common questions

**Can I move an existing folder under the root and use it as a system?** No. Systems, lists and editions are their
own classes; a plain folder is not a newsletter system.

**What happens if `RootFolderNodeId` is wrong?** The dashboard reports "Newsletter folder missing"
([problems the dashboard reports](../../../../specifications/6.0/cjw_newsletter.md#problems-the-dashboard-reports)).

**Can editors without admin rights work here?** Yes, if their role allows editing in the newsletter section and the
newsletter policy functions they need. Lesson 3.2 and [Roles and policies](../../../../guides/roles-and-policies.md)
cover it.

### Pitfalls

- **Creating the system in the wrong place.** A system created below an ordinary folder is not found by the
  newsletter area. Check the path before creating.
- **Removing a system.** It removes its lists and editions too (they go to the trash). In class, nobody removes
  anything that is not their own.
- **Section not set.** If the root was created by hand and the section was forgotten, newsletter items show the
  content menu. That is a setup fault for the trainer, not the learner.

## Hands-on exercise

Use the training installation and your admin training account. Replace `<name>` with your first name.

1. Open **Newsletter** and press **INI Settings** in the left menu; find `[NewsletterSettings]` and
   note the value of `RootFolderNodeId`.
2. Press **Newsletter system** in the left menu. Look at the breadcrumb at the top: note the name of the root above
   the system.
3. Open the **Details** tab of the system and find its node ID.
4. Go up to the newsletter root (use the breadcrumb). Compare its node ID with the value from step 1.
5. On the root's page, under **Sub items**, press **Create new** and choose **CJW Newsletter System**.
6. In **Title**, type `Training system <name>`. In **Short title**, type `TS <name>`.
7. In **Short description**, write one sentence: who this system is for.
8. Press **Send for publishing**.
9. Open your new system. Check that the left menu still shows the newsletter entries (**Dashboard**, **Users** and
   the others).
10. Open the **Locations** tab and note the path of your system.

## Expected result

- The learner's system `TS <name>` is published directly below the newsletter root.
- The value of `RootFolderNodeId` matches the node ID of the root.
- The newsletter left menu stays visible on the new system's page, which shows the section works.
- The trainer checks by opening the root and counting one new system per learner.

![The newsletter settings page](../../../images/trainers/nl-settings.webp)

## Quiz

1. What does `[NewsletterSettings] RootFolderNodeId` name?
2. Which field of a newsletter system is required?
   A. **Short title** B. **Title** C. **Short description** D. none
3. Why does the newsletter tree have a section of its own?
4. The dashboard says "Newsletter folder missing". What are two ways to fix it?
5. True or false: any folder below the root works as a newsletter system.

<details>
<summary>Answers</summary>

1. The node whose children are the newsletter systems ([feature page](../../../../features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
2. **B.** **Title** is the only required field of `CJW Newsletter System`.
3. So that newsletter items use the newsletter's navigation part and left menu ([Sections](../../../../guides/sections.md)).
4. Create the tree (the extension's installer class can do it) or set `RootFolderNodeId` to the right node
   ([specification](../../../../specifications/6.0/cjw_newsletter.md#problems-the-dashboard-reports)).
5. **False.** A system must be of the class `CJW Newsletter System`.

</details>

## Further reading

- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md#the-newsletter-tree-of-a-new-installation):
  how a new installation gets its newsletter tree.
- [Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#setting-it-up):
  the setup steps for a site.
- [Sections](../../../../guides/sections.md): sections and navigation parts.

## Related pages

- [Module 2: Systems, lists and editions](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
