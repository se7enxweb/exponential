---
title: "Lesson 2.7: A shared training server or one installation per learner"
audience: trainer, admin
category: training
order: 27
summary: Decide between one installation per learner and one shared training site, and set up a shared site so learners cannot disturb each other, one folder per learner and a role assigned with a subtree limitation. 45 minutes.
keywords: shared training server, lab design, subtree limitation, assign with limitation, role, folder per learner, isolation, classroom
type: help
---
# Lesson 2.7: A shared training server or one installation per learner

Not every class can install software: some laptops are locked, some courses are online with a browser only. Then a
shared training site is the answer, and its risk is that learners change each other's work. This lesson weighs the
two designs and builds the shared one with the access control Exponential already has.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 2: Building the training lab](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Trainers who run browser-only or online courses |
| Prerequisites | [Lesson 2.5](learner-accounts-and-roles.md) |

## Learning objectives

After this lesson, learners can:

- list the advantages and limits of each lab design;
- create one folder per learner on a shared site;
- create a role for learners and assign it with a subtree limitation;
- check that a learner cannot edit another learner's folder.

## Trainer's notes

### How to present it

1. Compare the designs (5 minutes):

   | | One installation per learner | One shared site |
   |---|---|---|
   | Learners can be administrators | yes | no (one site, one configuration) |
   | Needs software on each machine | yes | no, a browser |
   | Learners can disturb each other | no | only where roles allow |
   | Good for | developer and administrator courses | editor courses, online courses |

2. Explain the access design for a shared site (5 minutes): one folder per learner; a role that allows editing;
   **Assign with limitation**, *Subtree*, to each learner's folder.
3. Show the role page with an assignment limited to a subtree: the role page says *Only in the subtree ...*
   (5 minutes).

### Common questions

**Can I copy the Editor role instead of building one?** Yes: **Copy** on the role list makes "Copy of Editor" with
the same policies and no assignments. Rename it, for example "Course learners".

**Does a limited learner still see the whole tree?** They can read what their read policies allow. The limitation
controls where the role applies, so editing and creating are limited to their folder.

### Pitfalls

- **Assigning the role without limitation.** The learner may then edit everywhere the role allows. Always choose
  **Assign with limitation**.
- **Learners in the Editors group as well.** If that group's role applies site-wide, the limitation does not help.
  Put learners in a group of their own.
- **Real content on the training site.** A shared training site holds only invented content.

## Hands-on exercise

On a training installation, as administrator:

1. Under **Users**, create a user group **Course learners** (in the **Users** group: **Create new**, **User group**,
   name it, **Send for publishing**).
2. Move `learner01` from lesson 2.5 into it, or create a new user `learner03` there with an address at
   `example.invalid`.
3. In **Content structure**, create a folder **Training** and inside it a folder **Learner 03**.
4. Open **Users > Roles and policies**, select the **Editor** role and press **Copy**, then **Make the copy**. Rename
   the copy `Course learners` in the editor and press **Save**.
5. On the new role's page, press **Assign with limitation**, choose *Subtree*, pick the folder **Learner 03**, then
   choose the user `learner03`.
6. Sign in as `learner03`. Try to edit an article outside **Learner 03**, then create an article inside it.

## Expected result

- The role page of **Course learners** lists `learner03` with the limitation *Only in the subtree* of the learner's
  folder.
- Signed in as `learner03`, editing is offered inside **Learner 03** and refused or not offered elsewhere.
- The trainer documents the design (group, folders, role, limitation) on the lab sheet.

## Quiz

1. Name one advantage of one installation per learner and one of a shared site.
2. Which button applies a role only in one subtree?
3. What does **Copy** on a role create?
4. Why should learners on a shared site be in their own group?

<details>
<summary>Answers</summary>

1. Per learner: they can be administrators and cannot disturb each other. Shared: only a browser is needed.
2. **Assign with limitation**, then *Subtree* ([Roles and policies, 5](../../../../guides/roles-and-policies.md#5-assign-the-role)).
3. "Copy of <name>" with the same policies and no assignments.
4. So that no other role assigned to a shared group gives them site-wide editing.

</details>

## Further reading

- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md)
- [Sections](../../../../guides/sections.md): the other way to limit access, by section.
- [Hands-on workshop outline](../../../advocates/hands-on-workshop-outline.md): why each participant should have their
  own copy where possible.

## Related pages

- [Module 2: Building the training lab](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
