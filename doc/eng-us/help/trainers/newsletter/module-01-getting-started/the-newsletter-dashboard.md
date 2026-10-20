---
title: "Lesson 1.3: The newsletter dashboard"
audience: trainer, editor, admin
category: training
order: 13
summary: Learners read the newsletter dashboard section by section, find the lists, the last sends and the problems, and know which buttons start work in the background. 30 minutes.
keywords: newsletters with cjw newsletter, newsletter dashboard, newsletter/index, problems and hints, send now, count only, last sends, ext:cjw_newsletter:status
type: help
---
# Lesson 1.3: The newsletter dashboard

The start page of the newsletter area is a dashboard. It answers the questions people ask every day: how many
subscribers, what was sent, did cron run, and is anything wrong. This lesson teaches learners to read it before they
act, a habit that prevents most newsletter accidents.

> **Note**
> This lesson describes the admin4 design of Exponential 6.0.15 with `cjw_newsletter` 4.2. Other designs show the
> same information with a different layout.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 1: Getting started](README.md) |
| Duration | 30 minutes (15 presenting, 15 exercise) |
| Level | Beginner |
| Audience | Editors and administrators |
| Prerequisites | [Lesson 1.2](preparing-a-safe-training-installation.md): the training installation writes mail to files |

## Learning objectives

After this lesson, learners can:

- open the dashboard and name its main sections;
- read the **Lists** table (approved, waiting and all subscribers per list);
- find the last sends and their status;
- explain the hints under **Problems and hints** and what each asks them to do;
- say which buttons start work in the background and why they are not pressed in class without the trainer.

## Trainer's notes

### How to present it

Source: [Dashboard, commands and background runs](../../../../features/6.0/extensions/cjw_newsletter.md#dashboard-commands-and-background-runs-4117)
and [Problems the dashboard reports](../../../../specifications/6.0/cjw_newsletter.md#problems-the-dashboard-reports).

1. **Getting there** (2 minutes). **Newsletter** in the top menu, or the **Newsletter** tile on the main dashboard.
   The left menu of the area has **Dashboard**, **Users**, **Blacklists**, **Bounces**, **Mail accounts**,
   **Imports**, **Subscription form** and **INI Settings**.
2. **Top to bottom** (8 minutes). Walk the sections on the projector: **Subscribers**, **Sending**, **Bounces**,
   **Run now**, then the blocks of the 4.2 areas (**Deliverability**, **Editorial**, **Skins, languages and
   interests**, **Statistics**, **Imports and migration**), then **Problems and hints**, **Lists**, **Last sends**,
   **Newsletter systems** and **Last actions**. Only name the 4.2 blocks; modules 2 to 6 come back to them.
3. **Run now** (3 minutes). **Send now** creates the queue and sends what is due; **Count only** reports what a run
   would do without changing anything; **Process the mail accounts** reads the bounce accounts. Each starts a
   background run and shows its progress on the page. In class, only **Count only** is pressed, and only by the
   trainer, until module 4.
4. **Problems and hints** (2 minutes). Each problem names its cure. Examples from the specification: tables missing,
   newsletter folder missing, mails written to files, default sender address, orphans (with a **Remove them**
   button), stuck sends.

### Common questions

**Is there a command line version of the dashboard?** Yes: `php bin/php/console ext:cjw_newsletter:status` prints
what the dashboard shows ([specification](../../../../specifications/6.0/cjw_newsletter.md#console-commands)).

**What is "Waiting" in the Lists table?** Subscriptions that are not yet approved or confirmed, for example people
who have not clicked the confirmation link.

**Why does the dashboard say mails are written to files?** Because we set it up that way in lesson 1.2. On a live
site this hint means nothing is reaching subscribers.

**Who may see the dashboard?** Users whose role grants the policy function `newsletter/index`. The 4.2 blocks need
their own functions, for example `newsletter/deliverability` and `newsletter/editorial`.

### Pitfalls

- **Pressing Remove them.** It removes orphaned subscriptions for real. Let learners read the hint, not press it.
- **Send now in a shared installation.** If one learner presses it, the queues of every learner are processed. On a
  file transport that is harmless, but it confuses the later exercises. Agree the rule before the exercise.
- **Old data.** A shared training site may show sends from earlier courses. Tell learners which list is theirs.

## Hands-on exercise

Use the shared training installation (file transport, see lesson 1.2) and your admin training account. You only read.

1. Open **Newsletter** and make sure **Dashboard** is selected in the left menu.
2. Under **Lists**, write down each list name with its **Approved**, **Waiting** and **All** numbers.
3. Under **Last sends**, note the **Status**, **Mails** and **Failed** of the newest send, if there is one.
4. Under **Newsletter systems**, note how many systems the installation has.
5. Read **Problems and hints**. For each line, write in one sentence what it asks you to do.
6. Find the **Run now** section and name its three buttons. Do not press them.
7. Open a terminal on your own training installation (if you have one) and run:

   ```bash
   php bin/php/console ext:cjw_newsletter:status
   ```

8. Compare the command's output with what you wrote down from the dashboard.

## Expected result

- Each learner has a small table of the lists and their numbers, and a sentence per hint.
- Learners with a terminal see the same lists, sends and problems in the status command's output.
- No background run was started (the **Last actions** section shows nothing new).
- The trainer checks by asking each pair to explain one hint and its cure.

![The newsletter dashboard: subscribers, sending, bounces, Run now and deliverability](../../../images/trainers/nl-dashboard.webp)

## Quiz

1. Which button shows what a queue run would do without changing anything?
   A. **Send now** B. **Count only** C. **Process the mail accounts** D. **Remove them**
2. Name the console command that prints the dashboard's information.
3. The dashboard reports "Stuck sends". What do you check first?
4. What does the **Waiting** column of the Lists table count?
5. Why is the hint "mails are written to files" good news in this course and bad news on a live site?

<details>
<summary>Answers</summary>

1. **B.** **Count only** starts a dry run of the queue ([feature page](../../../../features/6.0/extensions/cjw_newsletter.md#dashboard-commands-and-background-runs-4117)).
2. `ext:cjw_newsletter:status` ([specification](../../../../specifications/6.0/cjw_newsletter.md#console-commands)).
3. That cron runs the part `cjw_newsletter` ([specification](../../../../specifications/6.0/cjw_newsletter.md#problems-the-dashboard-reports)).
4. Subscriptions that are not yet confirmed or approved.
5. In class nothing may leave the machine; on a live site it means no subscriber receives anything
   ([specification](../../../../specifications/6.0/cjw_newsletter.md#problems-the-dashboard-reports)).

</details>

## Further reading

- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md#dashboard-commands-and-background-runs-4117):
  the dashboard and background runs.
- [Specification: the cjw_newsletter extension](../../../../specifications/6.0/cjw_newsletter.md#problems-the-dashboard-reports):
  every problem the dashboard reports.
- [Roles and policies](../../../../guides/roles-and-policies.md): granting the newsletter policy functions.

## Related pages

- [Module 1: Getting started](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
