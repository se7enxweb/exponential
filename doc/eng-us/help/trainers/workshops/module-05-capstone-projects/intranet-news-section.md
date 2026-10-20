---
title: "Project 5.2: An intranet news section"
audience: trainer, admin, editor
category: training
order: 52
summary: A capstone project brief for administrators, an intranet news section with its own section, a newsroom group, a limited editor role, an approval path and a test of every account, built on a training installation in four hours, with a rubric.
keywords: capstone, project brief, intranet, news section, administrators, roles, sections, user groups, access control, rubric
type: help
---
# Project 5.2: An intranet news section

Administrators prove their skills by setting up access correctly and testing it. This brief fits the end of the
[administrator bootcamp](../module-03-three-day-bootcamps/administrator-bootcamp.md) or the
[administrators day](../module-02-one-day-agendas/administrators-day.md) (shortened).

## At a glance

| | |
|---|---|
| Course | [Workshops and bootcamps](../README.md) |
| Module | [Module 5: Capstone projects](README.md) |
| Duration | 4 hours |
| Level | Intermediate |
| Audience | Site administrators, alone or in pairs |
| Prerequisites | Users, roles, policies and sections ([Roles and policies](../../../../guides/roles-and-policies.md), [Sections](../../../../guides/sections.md)) |

## Learning objectives

Learners show that they can:

- create a section and assign it to a subtree;
- create user groups and users with test addresses;
- create roles with limited policies and assign them correctly;
- test each account and document the setup.

## The brief

> The invented "Northfield Council" intranet needs a News section. Three newsroom writers may create and edit news
> only in News. One news editor may also publish and remove there. Everyone signed in may read news. Nobody in the
> newsroom may change users, roles or settings.

| Part | What to build |
|---|---|
| Section | "Intranet news", assigned to the folder News |
| Groups | "Newsroom writers", "Newsroom editors" |
| Users | Three writers, one editor, all at `example.invalid` |
| Roles | "News writer" (create and edit in the News subtree or section), "News editor" (also remove), assigned to the groups |
| Documentation | A one-page access note: who may do what, where |

## Trainer's notes

- **Read roles as sentences** on the role page; the page states each policy in words, which makes checking easier.
- **Test with the accounts**, not by reading: sign in as a writer and try to edit outside News.
- **Never on a live site.** A training installation per learner.

## Hands-on exercise

1. Create the folder News (if missing) and the section "Intranet news"; assign the section to News.
2. Create the two groups and the four users.
3. Create the two roles with limited policies; assign each to its group.
4. Sign in as a writer: create a news item in News; try to edit an item elsewhere; try to open the Setup area.
5. Sign in as the editor: remove a news item and restore it from the trash.
6. Write the access note.

## Expected result

Rubric (each criterion 0, 1 or 2 points):

| Criterion | 0 | 1 | 2 |
|---|---|---|---|
| Section | Missing | Created, not assigned | Assigned to News |
| Groups and users | Missing | Created, some real addresses | Complete, all at `example.invalid` |
| Writer role | Too wide or missing | Works, limitation unclear | Limited to News, tested |
| Editor role | Missing | Works | Limited to News, remove tested and restored |
| No escalation | Newsroom can change roles or settings | | Newsroom cannot reach roles, users or settings |
| Access note | Missing | Incomplete | Who, what, where, for each group |

A pass is 8 of 12 points; the rubric is the trainer's own and claims no official status.

## Quiz

1. Why must the newsroom roles not include policies over the `role` module?
2. What is the difference between limiting by section and by subtree?
3. How do you check the role is limited correctly?

<details>
<summary>Answers</summary>

1. A policy over the role module lets users change roles and so give themselves any access
   ([Roles and policies](../../../../guides/roles-and-policies.md#in-short)).
2. A section is a label that can be on content anywhere; a subtree is a place in the tree with everything below it.
3. Sign in as the users and try actions inside and outside the limitation.

</details>

## Further reading

- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md)
- [Sections](../../../../guides/sections.md)
- [Security and audit](../../../../guides/security-and-audit.md)

## Related pages

- [Module 5: Capstone projects](README.md)
- [Workshops and bootcamps: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
