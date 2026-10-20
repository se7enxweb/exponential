---
title: "Lesson 6.3: Cost, limits and fair use"
audience: trainer, editor, admin
category: training
order: 63
summary: Learners read the estimated cost of their use, explain the site's limits and monthly budget, and use the assistant economically, with the right model, short conversations and plans for bulk work. 30 minutes.
keywords: exponential intelligence, cost, estimated cost, tokens, limits, budget, price class, cheaper model, local model, lanes, batch, new conversation, fair use
type: help
---
# Lesson 6.3: Cost, limits and fair use

Each request to a hosted model is billed to the site's account. The assistant estimates every cost, the site can set
limits and a monthly budget, and editors can save a lot with a few habits. This short lesson covers all three.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 6: Responsible use](README.md) |
| Duration | 30 minutes (15 presenting, 15 exercise) |
| Level | Intermediate |
| Audience | Every user of the assistant; administrators |
| Prerequisites | [Lesson 6.2](reviewing-ai-output.md) |

## Learning objectives

After this lesson, learners can:

- read the estimated cost of a click (defaults line), a turn (record) and a period (dashboard);
- explain usage limits and the monthly budget, and what happens when one is reached;
- choose a cheaper model where it does the job, using the model list's price classes;
- name three habits that save cost.

## Trainer's notes

### How to present it

Sources: [expai: For administrators](../../../../features/6.0/extensions/expai.md#for-administrators) and the usage and
budgets pages under **EI: Help** (`extension/expai/doc/usage-tracking.md`, `extension/expai/doc/budgets.md`).

1. **Estimates everywhere** (3 minutes). The edit form's defaults line (cost per click), each turn's record, the
   dashboard's **Estimated cost**, **EI: Log**. Estimates come from a price table; the provider's invoice is
   authoritative. A local model costs no fees.
2. **Limits and budget** (4 minutes). The dashboard's **Limits** show a bar for each limit set (per user, day, month,
   site, provider), with when it resets, or *No limits configured.* **Budget this month** shows the monthly site budget
   for queued work, if set, and what was spent and reserved. When a limit is reached, requests stop with a message
   until it resets.
3. **Habits** (5 minutes):
   - choose the cheaper model for routine work (the model list shows price classes such as $ to $$$);
   - start a **New conversation** for a new topic; long conversations make every request larger;
   - write clear briefs, so fewer rounds are needed;
   - use Vector's plan for bulk work: its estimate shows the cost before anything runs;
   - for large background work, administrators can use cheaper batch lanes.
4. **Fair use** (3 minutes). Training accounts share the site's budget with real editors; keep experiments small.

### Common questions

**Why is the cost "< $0.01" or with "≈"?** Very small amounts, and amounts priced with fallback prices, are marked.

**Who sets limits?** Administrators, in the extension's settings (module 7).

**Is the cheapest model always fine?** For short rewrites often yes; for long texts with structure, a stronger model
may need fewer corrections. Compare on your own content.

### Pitfalls

- **Repeated clicking** of the same field action until the text "feels right". Edit by hand instead.
- **Huge scopes** in Vector without reading the estimate.
- **Forgotten queued work.** Chains and alt text jobs run in the background and cost too.

## Hands-on exercise

Use the training installation and your training account.

1. Open the dashboard page **Exponential Intelligence**. Write down your estimated cost and requests for **7d**.
2. Read **Limits** and **Budget this month**: which limits apply to you?
3. In **Start writing**, read the model list and its price classes. Write down the cheapest model that may write drafts
   (not marked "reads only").
4. Open an article with **Edit** and read the defaults line's cost per click. Change your model where the site lets you
   choose one, and read it again.
5. Open **EI: Log** and find your most expensive turn of this course. What made it expensive (many steps, a long
   conversation, a strong model)?
6. Write your three cost-saving habits on a card.

## Expected result

- The learner's notes have their period cost, the limits that apply, the cheapest writing model and their most
  expensive turn with a reason.
- Each card lists three habits.
- The trainer checks two cards.

![Budgets: the monthly budget and what was spent this month](../../../images/trainers/ei-budgets.webp)

## Quiz

1. Where do you see the cost of one click in the edit form?
2. What happens when a usage limit is reached?
3. Name three habits that save cost.
4. True or false: the estimated cost is exactly what the provider bills.
5. What does Vector show before bulk work runs?
   A. Nothing B. A plan with a cost estimate C. The invoice D. The provider's terms

<details>
<summary>Answers</summary>

1. In the defaults line above the fields.
2. Requests stop with a message until the limit resets.
3. Any three of: a cheaper model for routine work; new conversations for new topics; clear briefs; plans for bulk work;
   batch lanes for large background work; editing by hand instead of repeated clicks.
4. **False.** It is an estimate; the invoice is authoritative.
5. **B.**

</details>

## Further reading

- [expai: For administrators](../../../../features/6.0/extensions/expai.md#for-administrators)
- The usage tracking, budgets and lanes pages under **EI: Help**.

## Related pages

- [Module 6: Responsible use](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
