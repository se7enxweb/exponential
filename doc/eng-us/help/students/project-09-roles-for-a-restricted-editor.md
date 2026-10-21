---
title: "Project 9: roles for a restricted editor"
audience: student
category: learning
order: 33
summary: Study project 9 - create a user group and a test editor, build a role whose policies let the group edit only the club subtree, assign it with a limitation, sign in as the editor to prove what is allowed and what is refused, read the role as sentences, and hand in an access plan with evidence, graded with a rubric.
keywords: study project, roles, policies, limitations, user group, assign, subtree, section, editor, access control, least privilege, content/edit, content/create, learner
type: help
---
# Project 9: roles for a restricted editor

Real sites never give every editor the administrator account. In this project you give a group of club editors the
right to work on their own part of the site, and nothing else, and you prove it. The steps follow
[Roles, policies and unactivated users](../../guides/roles-and-policies.md).

## Contents

1. [At a glance](#at-a-glance)
2. [Goal](#goal)
3. [Before you start](#before-you-start)
4. [Tasks](#tasks)
5. [Hand in](#hand-in)
6. [Rubric](#rubric)
7. [Stretch goals](#stretch-goals)

## At a glance

| | |
|---|---|
| Level | Intermediate |
| Time | About 2 hours |
| Paths | Administrator |
| Prerequisites | [project 2](project-02-a-small-site-structure.md); two browsers, or a private window, to be signed in twice |

## Goal

A user group "Club editors" with one test user, a role that lets the group create and edit content only inside the
"Student club" subtree, an assignment, and proof from the test user's side of what works and what is refused.

## Before you start

Read [Roles, policies and unactivated users](../../guides/roles-and-policies.md), sections "In short" and 1 to 5. Know:

- A **role** is a set of **policies**; it does nothing until it is **assigned** to users or groups.
- A user has every policy of every role assigned to them or to one of their groups.
- Every change in the role editor is made in a draft; nothing changes until you press **Save**.
- Prefer assigning roles to groups.

## Tasks

1. **Write an access plan** first: what may a club editor do (sign in to the administration, read content, create and
   edit in the club subtree, upload images for it), and what not (setup, users, other parts of the tree).
2. **Create the group and the user.** In **Users**, create a group "Club editors" and a user in it with an address at
   `example.invalid` and a password of at least 10 characters.
3. **Read existing roles.** Open **Users > Roles and policies**. Look at **Editor** (if your site package has it) and
   **Anonymous**; read their policies as sentences ([section 2](../../guides/roles-and-policies.md#2-read-one-role)).
4. **Build the role.** **New role**, name "Club editors". Add policies with the wizard (module, function,
   limitations), for example `content/read`, `content/create` and `content/edit` with a **Subtree** limitation, and
   the policy that lets the user sign in to the administration's siteaccess. Press **Save**
   ([section 3](../../guides/roles-and-policies.md#3-create-a-role-with-limited-policies)).
   Avoid any policy over every module, which gives full access, and any policy over the role module, which lets users
   give themselves any access ([In short](../../guides/roles-and-policies.md#in-short)).
5. **Assign it** to the group, or with **Assign with limitation** to the subtree
   ([section 5](../../guides/roles-and-policies.md#5-assign-the-role)).
6. **Test as the editor** in a private window: sign in, create an article in the club folder, edit another, then try to
   create something outside the subtree and to open **Setup**. Note each result and message.
7. **Fix and test again** until the plan and the results match. Each fix is a change to the role, never giving the
   test user the Administrator role.
8. **Read the role page** with its figures and its policies as sentences, and compare it with your plan.

## Hand in

- The access plan, and the final role as the role page shows it (policies as sentences).
- Screenshots from the editor's side: a successful edit inside the subtree, and a refused action outside it.
- A short report: which policy you added last and why, and what "least privilege" means for this group.

## Rubric

Score the four common criteria of [Grading your own work](grading-your-own-work.md), and these checks (8):

1. The plan was written before the role.
2. The role is assigned to a group, not to the individual user only.
3. No policy over every module and none over the role module.
4. Create and edit are limited to the club subtree.
5. The test user can work inside the subtree, shown with a screenshot.
6. The test user is refused outside it, shown with the message.
7. The test user's address is at `example.invalid`.
8. The report explains the difference between a role and its assignment.

## Stretch goals

- Limit the role further by **Class** (Article and Image only) and test again.
- Add a translator who may edit only one language ([Content languages, section 7](../../guides/content-languages.md#7-translating-content-in-the-editor)).
- Give the editors the `editor` siteaccess instead of `admin`: it is the administration for content only, without
  setup, design and developer tools ([Installing Exponential 6.0](../../INSTALL.md#1-what-you-get-and-the-quick-start)).
- Turn on two-step sign-in for the test user ([How do I turn on two-step sign-in?](../manual/how-do-i-turn-on-two-step-sign-in.md)).

## Related pages

- [Study projects: overview](study-projects-overview.md)
- [Roles, policies and unactivated users](../../guides/roles-and-policies.md)
- [Sharing one study site with a class](sharing-one-study-site-with-a-class.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
