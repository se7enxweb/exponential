---
title: "Lesson 1.2: A tour of the Setup area"
audience: trainer, admin
category: training
order: 12
summary: Learners find their way around the Setup area of the administration, name what each group of the Setup menu is for, and find any Setup page in under a minute. 35 minutes.
keywords: site administration, setup, setup menu, left menu, cache management, system information, ini settings, sections, states, cronjobs, log files, admin4
type: help
---
# Lesson 1.2: A tour of the Setup area

Almost everything a site administrator does in the browser starts at **Setup** in the top menu. The Setup page
groups about forty pages by purpose and gives each a one-line description. This lesson walks through the groups so
learners know where to look before later modules open the pages one by one.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 1: Getting oriented as a site administrator](README.md) |
| Duration | 35 minutes (15 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | New site administrators |
| Prerequisites | [Lesson 1.1](what-a-site-administrator-does.md): a running training installation and an administrator account |

## Learning objectives

After this lesson, learners can:

- open the Setup page and name its groups;
- find a Setup page from the left menu or from the Setup page's descriptions;
- tell which pages only read and which can change the site;
- bookmark a Setup page they will use often.

## Trainer's notes

### How to present it

Source: [A tour of the administration](../../../admin-tour.md). This lesson describes the admin4 design; other
administration designs show the same pages in a different frame.

1. **The Setup page** (4 minutes). Press **Setup** in the top menu. The page **Setup** opens with the line
   "Configure the installation: content model, site and URLs, workflows, users and the system." Each group lists its
   pages with a short description; some show a count (for example the number of classes, languages or sections).
2. **The groups** (8 minutes). Go through them on the projector:

   | Group | Pages (examples) | Used in this course |
   |---|---|---|
   | **Content model** | **Classes**, **Languages**, **Interface languages**, **Sections**, **States**, **Content finder**, **Broken relations** | Module 4 |
   | **Site and URLs** | **Preload Sites**, **RSS**, **Packages**, **Search statistics**, **Link management**, **URL translator**, **URL wildcards** | Modules 5 and later |
   | **Workflows** | **Triggers**, **Workflows**, **Workflow processes** | Module 6 |
   | **Users and messages** | **Audit**, **E-mail preferences**, **Notification**, **Sessions**, **Check user accounts** | Module 2 and later |
   | **System** | **Cache management**, **Maintenance**, **Cronjobs**, **Log files**, **Extensions**, **Ini settings**, **Site data**, **System information**, **Upgrade check** | most modules |

   Installations with more extensions show more groups, for example **AI** and **Updates**.
3. **The left menu** (2 minutes). On every Setup page the left column repeats the same groups as a menu, with
   **Overview** at the top. The current page is highlighted.
4. **Look or change** (1 minute). Pages such as **System information**, **Site data** and **Log files** only read.
   **Cache management**, **Maintenance**, **Ini settings** and **Cronjobs** can change how the site works for
   everyone. On a live site, look before you press anything.

### Common questions

**Why do I not see Setup at all?** The account lacks the policies for the `setup` module. Setup pages need their own
policies, for example `setup/system_info` for **System information**. Module 3 explains policies.

**Why does my Setup page show other groups than the trainer's?** Groups and pages come from the installed
extensions. A page an extension adds appears only when that extension is active.

**Where is the user list?** Under **Users** in the top menu, not under Setup. Only **Check user accounts** and
**Sessions** are in Setup.

### Pitfalls

- **Clicking through Cache management.** Learners curious about every button may clear caches; on a training
  installation that is harmless, on a live site it slows the site down. Say so before the exercise.
- **Lost in the left column.** If the left column is hidden, the **Left column** button in the header brings it back.

## Hands-on exercise

On your training installation, signed in as an administrator:

1. Press **Setup** in the top menu and read the descriptions of the group **System**.
2. Open **System information** from the left menu. Note the version of Exponential it shows.
3. Return with **Overview** at the top of the left menu.
4. Find, without the trainer's help, the page that lists **who is signed in** and open it.
5. Find the page that lists the **sections** and note how many there are.
6. Find the page that shows **what visitors searched for**.
7. Find the page where **INI settings** are read. Do not change anything.
8. Open **Log files** and note which log files exist.
9. Bookmark **System information** with **Add to bookmarks** in the right column.
10. Write down for each of the pages from steps 2 to 8: does it only read, or can it change the site?

## Expected result

- Step 4 leads to **Sessions**, step 5 to **Sections**, step 6 to **Search statistics**, step 7 to **Ini settings**.
- The bookmark appears in the right column under **Bookmarks**.
- The list from step 10 marks **System information**, **Search statistics** and **Log files** as reading, and
  **Sessions**, **Sections** and **Ini settings** as able to change things (ending sessions, editing sections,
  saving settings).
- The trainer checks by calling out a task ("where do I see the cronjobs?") and timing the answers.

## Quiz

1. Which Setup group holds **Cache management** and **Cronjobs**?
   A. **Content model** B. **Site and URLs** C. **Users and messages** D. **System**
2. In which group are **Sections** and **States**?
3. Name two Setup pages that only read.
4. Why can two administrators see different groups on their Setup page?
5. Where is the list of users, if not in Setup?

<details>
<summary>Answers</summary>

1. **D.** The group **System** (admin4 design).
2. **Content model.**
3. For example **System information**, **Site data**, **Search statistics**
   ([System information](../../../../guides/system-information.md)).
4. Extensions add pages and groups, and policies decide which pages an account may open.
5. Under **Users** in the top menu.

</details>

## Further reading

- [A tour of the administration](../../../admin-tour.md): the header, the left and right columns, the dashboard.
- [Operating a site](../../../../guides/operating-a-site.md): what the System pages are used for day to day.
- [The admin4 design](../../../../features/6.0/admin4-design.md): the design this course describes.

## Related pages

- [Module 1: Getting oriented as a site administrator](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
