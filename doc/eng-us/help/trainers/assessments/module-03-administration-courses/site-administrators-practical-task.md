---
title: "Lesson 3.2: Site administrators, practical task"
audience: trainer, admin
category: training
order: 32
summary: A 60-minute practical task for the end of the Site administration course, create a section and a limited role, assign it to a group, prove the limit with a test user, clear one cache group, run a cronjob part and take the training site offline and back, scored with a six-criterion rubric. 85 minutes with scoring.
keywords: assessments and practical exercises, site administration, practical task, rubric, new section, new role, new policy, assign with limitation, test user, example.invalid, caches, cronjobs, maintenance mode
type: help
---
# Lesson 3.2: Site administrators, practical task

A real administration job in one hour: a new team needs its own area of the site and a role that lets it edit there
and nowhere else. The learner sets it up, proves it works with a test account, and does three routine jobs: a cache,
a cronjob and a maintenance window.

> **Note**
> This is the trainer's own assessment, not an official certification. Each learner works on their own training
> installation as an administrator. Test users get addresses at `example.invalid`; the mail transport is `file`.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 3: Assessing the administration courses](README.md) |
| Duration | 85 minutes (5 briefing, 60 task, 20 scoring) |
| Level | Intermediate |
| Audience | Learners at the end of the Site administration course |
| Prerequisites | [Lesson 3.1](site-administrators-written-test.md); an Administrator account on the learner's own training installation |

## Learning objectives

Learners who pass this task have shown that they can:

- create a section and assign content to it;
- create a role with limited policies and assign it to a group;
- prove a permission with a separate test account;
- clear one cache group deliberately;
- run a cronjob part from the administration;
- take a site offline with a message and bring it back.

## Trainer's notes

### How to run it

1. **Before**: one training installation per learner, installed fresh, with the mail transport set to `file`. The
   learner signs in as the installation's administrator.
2. **Briefing**: hand out the steps and the rubric. 60 minutes. The guides may be open.
3. **Scoring**: on each installation, read **Setup > Sections**, the role page, the test user's group, the cronjob
   page's recent runs and the maintenance page's recent changes (the latest eight switches are listed).

### Common questions

**A learner locks themselves out with maintenance mode.** Maintenance keeps the administration reachable when it is
switched on from the page. If a learner is locked out anyway, `php bin/php/maintenance.php off` brings it back. Score
the step, then give the time back if the fault was not theirs.

**May learners use the shell instead of the browser?** Yes, where the result is the same.

### Pitfalls

- **Testing with the administrator account.** Administrators are not limited by sections; the test needs the test
  user.
- **Forgetting the role draft.** Nothing changes until **Save**. A learner who closed the editor without saving has no
  role.

## Hands-on exercise

The task, as handed to learners. Use your own training installation, signed in as administrator.

1. In **Setup > Sections**, press **New section**. Name it `Press area`; keep the identifier it proposes and the
   navigation part **Content structure**. Press **Create section**.
2. Create a **Folder** named `Press` below the content root and assign it, with everything below it, to the section
   `Press area` (**Assign content** on the section's page, or the folder's details).
3. Create a user group `Press team` below the users.
4. In **Roles and policies**, press **New role**, name it `Press editors`, and add policies with **New policy**:
   `content` `read` limited to the section `Press area`; `content` `create` and `content` `edit` limited to the section
   `Press area`. Press **Save**.
5. On the role page, press **Assign** and choose the group `Press team`.
6. Create a user `press01` in `Press team` with the address `press01@example.invalid` and a password you write down.
7. In a private browser window, sign in as `press01`. Create an article inside `Press`. Then try to edit an item
   outside `Press`. Write down what you see. Sign out.
8. In **Setup > Caches**, clear the group **Templates** only. Note the notice that follows.
9. In **Setup > Cronjobs**, run the part that holds `workflow.php` (the *frequent* part by default) with **Run part**
   and watch the **Output**.
10. In **Setup > Maintenance**, take the site offline with the message `Training maintenance, back in 15 minutes`, an
    expected duration of 15 minutes, and yourself allowed in; tick the confirmation and press **Take the site
    offline**. Open the public site in the private window: note what it shows. Then press **Bring the site back
    online**.

## Expected result

- **Setup > Sections** lists `Press area` with objects; the folder `Press` shows that section in its details.
- The role `Press editors` has three policies limited to `Press area` and is assigned to `Press team`.
- `press01` created an article in `Press` and could not edit outside it.
- The cache notice named the Templates group; the cronjob page lists the run; the maintenance page's recent changes
  show one switch off line and one on line, by the learner.
- The trainer checks each page in the administration and asks the learner to explain step 7.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Section | None | Created, no content assigned | Content assigned, not the whole subtree | Section created, folder and subtree assigned | **Setup > Sections**, the folder's details |
| Role and policies | No role saved | Role saved, policies unlimited | Limited, one function missing | read, create, edit limited to the section | The role page |
| Assignment | Not assigned | Assigned to a single user | Assigned to the group, test user elsewhere | Group assigned, test user in it | Role page, the group |
| Proof with test user (must) | No test | Tested with the administrator | Tested, result not explained | Tested with `press01`, both results explained | The learner's note, the article |
| Routine jobs | None | One of cache, cronjob | Both, one not what was asked | Templates group only, the right part run | Cache notice, cronjob recent runs |
| Maintenance | Not done | On, not back online | Done, message or duration missing | Message, duration, own access, back online | Maintenance recent changes |

**Pass**: 12 of 18 points or more and **Proof with test user** at 2 or 3. Without testing, nobody knows whether a role
works.

## Quiz

Debrief questions to ask after scoring.

1. Why could the administrator account not prove the role?
2. Visitors cannot see `Press`. Is that correct here, and what would you add if they should?
3. Why clear only the Templates group, rather than all caches?
4. What would have happened to approvals if the *frequent* part never ran?

<details>
<summary>Answers</summary>

1. Administrators' policies are not limited by section, so everything is allowed
   ([Sections](../../../../guides/sections.md#5-creating-a-section)).
2. Yes for a press area in progress; for visitors, a policy for the Anonymous role to read the section
   ([Sections](../../../../guides/sections.md#in-short)).
3. Clearing everything costs time while caches rebuild; clear what the change needs
   ([Caches](../../../../guides/caches.md)).
4. Approved content would never be published ([Workflows](../../../../guides/workflows.md#in-short)).

</details>

## Further reading

- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md#3-create-a-role-with-limited-policies):
  the policy wizard.
- [Sections](../../../../guides/sections.md#5-creating-a-section): the section form.
- [Cronjobs](../../../../guides/cronjobs.md) and [Maintenance mode](../../../../guides/maintenance-mode.md).

## Related pages

- [Lesson 3.1: Site administrators, written test](site-administrators-written-test.md)
- [Module 3: Assessing the administration courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
