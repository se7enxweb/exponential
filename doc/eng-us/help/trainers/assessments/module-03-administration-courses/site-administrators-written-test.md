---
title: "Lesson 3.1: Site administrators, written test"
audience: trainer, admin
category: training
order: 31
summary: A fourteen-question written test for the end of the Site administration course, on roles and policies, sections, caches, cronjobs, workflows, maintenance mode and the audit, with an answer key in which every answer cites a guide. 30 minutes for learners.
keywords: assessments and practical exercises, site administration, written test, roles, policies, assign with limitation, sections, caches, cronjobs, workflows, approval, maintenance mode, audit
type: help
---
# Lesson 3.1: Site administrators, written test

This test closes the Site administration course. It checks the concepts an administrator needs to keep a site safe
and working: who may do what, how content is grouped for permissions, which cache to clear, why cron matters, how an
approval works and how to take the site offline and back.

> **Note**
> This is the trainer's own assessment, not an official certification. The pages cited describe the Admin 4 design;
> check labels on your training installation.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 3: Assessing the administration courses](README.md) |
| Duration | 45 minutes (5 briefing, 30 test, 10 going through the answers) |
| Level | Intermediate |
| Audience | Learners at the end of the Site administration course |
| Prerequisites | The Site administration course; [Lesson 1.2](../module-01-designing-assessments/writing-written-tests.md) for the trainer |

## Learning objectives

Learners who pass this test have shown that they can:

- explain roles, policies, limitations and assignments, and spot dangerous policies;
- explain what a section is used for and why new sections are invisible to visitors;
- choose the right cache to clear and know where Velocity differs;
- explain why the workflow cronjob is needed for approvals;
- describe maintenance mode and the way back when the administration is unreachable.

## Trainer's notes

### How to run it

1. Hand out the questions without the key; 30 minutes.
2. Go through the answers with the cited guides on the projector.
3. Score with the rule under **Scoring**.

### Common questions

**Some features are not on our training release.** Remove the question; do not test what the installation cannot
show.

**Is the command line in scope?** Questions 8 and 13 name commands. If your course stayed in the browser, accept the
browser route as the answer.

### Pitfalls

- **Default sections.** Question 5 names the sections of a new installation. A site package can add more; the five
  are still there.
- **Velocity.** If your learners never used Velocity, drop question 9.

## The test

1. What is the difference between a role and a policy?
2. You created a role but nobody has its rights. What step is missing?
3. Why do you prefer assigning a role to a user group rather than to single users?
4. A role has a policy over the **role** module. Why does the role list mark it as a risk?
5. Name the five sections of a new installation.
6. You moved content into a new section and visitors no longer see it. Why?
7. When can a section be removed?
8. An editor's template change does not show. Which cache tag do you clear first, from **Setup > Caches** or the shell?
9. You cleared the INI caches, but a site served by Velocity still uses the old setting. What else is needed?
10. Content was approved hours ago and is still pending. Which cronjob do you check?
11. What does **Cancel...** do to a waiting workflow process? Is any content deleted?
12. Your site is in maintenance mode. What status code do visitors get, and can you still reach the administration?
13. The administration is unreachable during maintenance and nothing else works. What ends maintenance?
14. Where do you see who switched maintenance on, and who changed a section?

<details>
<summary>Answers</summary>

1. A role is a set of policies; a policy lets its users use one module or one function of a module, possibly limited
   ([Roles, policies and unactivated users](../../../../guides/roles-and-policies.md#in-short)).
2. It must be assigned to users or groups; a role does nothing until assigned
   ([Roles, policies and unactivated users](../../../../guides/roles-and-policies.md#in-short)).
3. Every member of the group, now and later, gets the role
   ([Roles, policies and unactivated users](../../../../guides/roles-and-policies.md#5-assign-the-role)).
4. Its users can change roles and so give themselves any access
   ([Roles, policies and unactivated users](../../../../guides/roles-and-policies.md#in-short)).
5. Standard, Users, Media, Setup and Design
   ([Sections](../../../../guides/sections.md#in-short)).
6. The Anonymous role may read Standard only; content in a new section is hidden from visitors until a policy allows
   it ([Sections](../../../../guides/sections.md#in-short)).
7. Only when nothing uses it: no object (drafts and archived ones included), no policy with a Section limitation
   naming it, no role assignment limited to it ([Sections](../../../../guides/sections.md#in-short)).
8. The template caches, by tag `template` (**Clear content / template / INI caches…** or
   `php bin/php/ezcache.php --clear-tag=template --allow-root-user`)
   ([Caches](../../../../guides/caches.md#5-from-a-shell)).
9. Restart Velocity, which keeps settings in memory
   ([Caches](../../../../guides/caches.md#in-short)).
10. The workflow cronjob (`workflow.php`, in the *frequent* part by default); without it approved content is never
    published ([Workflows](../../../../guides/workflows.md#in-short)).
11. The workflow stops, a pending version becomes a draft again, an approval request is closed and the process is
    removed. No content is deleted ([Workflows](../../../../guides/workflows.md#in-short)).
12. `503 Service Unavailable` with `Retry-After`; the administration under `/admin` stays reachable
    ([Maintenance mode](../../../../guides/maintenance-mode.md#in-short)).
13. Deleting the state file `var/maintenance.json`
    ([Maintenance mode](../../../../guides/maintenance-mode.md#in-short)).
14. In the audit trail: maintenance as `system.maintenance.change`, sections as `content.section.change`
    ([Maintenance mode](../../../../guides/maintenance-mode.md#in-short),
    [Sections](../../../../guides/sections.md#5-creating-a-section)).

</details>

## Scoring

- One point per question; question 5 needs all five names for the point (half a point for four).
- Pass: 10 of 14 points or more, with questions 4 and 10 correct: one is about security, the other is the most common
  "my content is stuck" fault.
- Give feedback as in [Lesson 1.6](../module-01-designing-assessments/giving-feedback-on-results.md).

## Further reading

- [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md)
- [Sections](../../../../guides/sections.md) and [Caches](../../../../guides/caches.md)
- [Workflows](../../../../guides/workflows.md) and [Maintenance mode](../../../../guides/maintenance-mode.md)

## Related pages

- [Lesson 3.2: Site administrators, practical task](site-administrators-practical-task.md)
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
