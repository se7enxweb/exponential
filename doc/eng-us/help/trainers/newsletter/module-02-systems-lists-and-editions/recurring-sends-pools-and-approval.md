---
title: "Lesson 2.4: Recurring sends, article pools and approval"
audience: trainer, editor, admin
category: training
order: 24
summary: Learners read the editorial tools of CJW Newsletter 4.2 (recurring sends, article pools, the approval of editions), fill in a recurring send form, and follow an approval request through the collaboration inbox. 45 minutes.
keywords: newsletters with cjw newsletter, recurring sends, schedule, article pool, article picker, approval, collaboration inbox, newsletter/editorial, newsletter/approve
type: help
---
# Lesson 2.4: Recurring sends, article pools and approval

Many newsletters go out on a rhythm, often with the latest articles of the site, and some organizations want a
second person to approve every issue. CJW Newsletter 4.2 has three editorial tools for this: recurring sends,
article pools and the approval of editions. This lesson shows what each does and when to use it.

> **Note**
> A recurring send sends by itself when cron runs. On the training installation every transport writes files
> (lesson 1.2), and every subscriber is at `example.invalid`; still, set new recurring sends to **paused** in class.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 2: Systems, lists and editions](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Editors who plan issues, administrators who set up approvals |
| Prerequisites | [Lesson 2.3](writing-an-edition.md): the learner has a list with two editions |

## Learning objectives

After this lesson, learners can:

- explain the two modes of a recurring send ("latest" and "copy") and when each fits;
- fill in the **New recurring send** form and save it paused;
- explain what an article pool provides to a recurring send and to the article picker;
- describe the approval flow from the request to the decision in the collaboration inbox.

## Trainer's notes

### How to present it

Source: the editorial guide of the extension (`extension/cjw_newsletter/doc/editorial.md`, linked from the
[feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).

1. **Recurring sends** (8 minutes). Open **Recurring sends** from the dashboard's **Editorial** block. A recurring
   send belongs to a list and runs on chosen weekdays, weekly on one weekday, or monthly on a day. Two modes:
   *Send the latest edition of the list that was not sent yet*, or *Copy a template edition and send the copy*,
   optionally filled from the article pool with what was published since the last send. With *Skip the run when
   there is nothing new* nothing goes out on a quiet week. Runs that cron missed are not made up.
2. **Article pools** (4 minutes). **Article pools** says which classes and filters supply articles. With no pool
   stored, the settings `[ArticlePoolSettings]` are used. Only content the anonymous user may read is taken,
   because a newsletter is public.
3. **Approval** (6 minutes). Switched on per list (**Approval:** on the list page). The send form then refuses an
   edition version that is not approved. The editor asks on the approval page; the request appears in the
   **Collaboration inbox** of the approvers, who press **Approve** or **Reject** with a **Comment**. Nobody approves
   their own request unless the settings allow it, and a new version needs a new approval.
4. **Permissions** (2 minutes). The pages need the policy function `newsletter/editorial`; deciding needs
   `newsletter/approve`.

### Common questions

**What time zone does a recurring send use?** Its own, else `[ScheduleSettings] DefaultTimezone`, else PHP's.

**What happens on the 31st in a short month?** A monthly run on a day the month does not have runs on its last day.

**Who are the approvers?** The users and user groups named in `[ApprovalSettings]`; by default the group with ID 12
(the administrators of a standard installation). Check your installation's settings.

**Can I test a recurring send without sending?** The **Recurring sends** list has **Try**, which runs it as a trial.
Read the editorial guide for what it reports before you use it in class.

### Pitfalls

- **Saving a schedule active.** On a shared training site, an active schedule sends with the next cron run. Choose
  **paused** before **Save**.
- **Approval on a list in use.** Switching approval on blocks sends that are waiting. Do it on the learner's own list.
- **Self-approval.** A learner who asks and then tries to approve their own request is refused; pair learners.

## Hands-on exercise

Use the training installation (all transports write files, subscribers at `example.invalid`). Work in pairs, A and B.

1. Open **Newsletter**, find the **Editorial** block on the dashboard and press **Recurring sends**.
2. Read the existing rows: **What**, **When**, **Runs**. Do not press **Run now** or **Try**.
3. Press **New recurring send** (on the dashboard's **Editorial** block).
4. **List**: your list `Weekly tips <name>`. Choose **Copy a template edition and send the copy**, and as
   **Template edition** your `Tips for week 1`.
5. Tick **Skip the run when there is nothing new (it is logged)**. Leave **Condition** at **None**.
6. Choose **Weekly on** and a weekday, a **Time**, and leave **Time zone** at the default.
7. Choose **paused** and press **Save**.
8. Open **Article pools** and read the columns **For**, **Classes** and **Filters** of the pools listed.
9. Trainer demonstration (on the trainer's own list with approval switched on): open the approval page of an
   edition, show **Your decision**, **Comment**, **Approve** and **Reject**, and the **History**.
10. Pair work: A explains the approval flow to B in four steps; B explains the two modes of a recurring send to A.

## Expected result

- Each learner's recurring send is listed under **Recurring sends**, paused, with the chosen weekday and time.
- Nothing was sent: no new row under **Last sends** on the dashboard.
- Each pair can explain the approval flow and the two schedule modes.
- The trainer checks the **Recurring sends** list for any schedule that is not paused and pauses it.

![The article pool: articles waiting to go into the next recurring edition](../../../images/trainers/nl-pool.webp)

## Quiz

1. What are the two modes of a recurring send?
2. A monthly send is set for day 31. When does it run in April?
   A. 1 May B. 30 April C. it is skipped D. 31 March
3. Which policy function is needed to approve an edition?
4. True or false: an approval covers every later version of the edition.
5. Why does an article pool only take content the anonymous user may read?

<details>
<summary>Answers</summary>

1. Send the latest edition of the list that was not sent yet, or copy a template edition and send the copy
   (editorial guide, linked from the [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
2. **B.** A day beyond the end of a month means its last day (editorial guide).
3. `newsletter/approve` (editorial guide; see also [Roles and policies](../../../../guides/roles-and-policies.md)).
4. **False.** The approval is per version; a new version needs its own approval.
5. A newsletter is public; it must not carry content that visitors could not read on the site.

</details>

## Further reading

- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420):
  the 4.2 areas and their guides.
- [Roles and policies](../../../../guides/roles-and-policies.md): granting `newsletter/editorial` and
  `newsletter/approve`.
- [Cronjobs](../../../../guides/cronjobs.md): recurring sends run with the queue part of cron.

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
