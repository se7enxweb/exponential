---
title: "Lesson 5.2: Reading the log"
audience: trainer, editor, admin
category: training
order: 52
summary: Learners open EI: Log, read the requests grouped into turns, filter by date, action, model, provider, region and errors, switch to Versions written, and export the filtered requests as CSV. 30 minutes.
keywords: exponential intelligence, log, ei log, requests and usage, versions written, turns, filter, action, model, provider, region, errors only, export csv, cost, tokens
type: help
---
# Lesson 5.2: Reading the log

Every request the assistant makes is recorded: who asked, which model answered, how long it took, what it cost and
which versions it wrote. **EI: Log** is where editors check their own use and administrators the site's. This lesson
reads it.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 5: Chains and the log](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Editors; administrators with `expai/log` |
| Prerequisites | [Lesson 5.1](ai-chains.md): the learner has made requests in this course |

## Learning objectives

After this lesson, learners can:

- open **EI: Log** and switch between **Requests and usage** and **Versions written**;
- open a turn and its requests;
- filter by **From**, **To**, **Action**, **Model**, **Provider**, **Region** and **Errors only**, and **Reset**;
- export the filtered requests with **Export CSV**;
- say what the log never records.

## Trainer's notes

### How to present it

Sources: the dashboard and usage pages under **EI: Help** (`extension/expai/doc/dashboard.md`,
`extension/expai/doc/usage-tracking.md`).

1. **Two views** (2 minutes). **Requests and usage** and **Versions written**. Editors see their own; with
   `expai/log`, everyone's (the page says *Everyone's activity.*), with **User ID** and **Node ID** filters.
2. **Turns** (3 minutes). Requests are grouped into turns: one panel message, one field action, one Fill the fields,
   with every request it led to. A turn opens to its requests and tool calls. Above them, totals, per day and (with
   `expai/log`) per user.
3. **Filters** (3 minutes). **Action** (`panel`, `fill`, `field:write`, `field:rewrite`, `field:shorten`,
   `field:translate`, `field:suggest`, and for administrators `probe` and `test`), **Model**, **Provider**, **Region**,
   **Errors only**; **Filter** and **Reset**. **Export CSV** gives the filtered requests without request texts, with
   user ids only.
4. **Privacy** (2 minutes). Prompts, answers and keys are not part of the usage records by default; the audit trail
   never records them.

### Common questions

**Why do some requests have no price?** The model has no price in the site's table; the page says how many and links
to them.

**Can I see what I typed?** The draft log keeps a short summary of the request for a time; full requests are logged
only where an administrator switched it on.

**Who sees my requests?** You, and holders of `expai/log`.

### Pitfalls

- **Exporting personal data.** The CSV carries user ids; handle it as personal data.
- **Reading one request as the cost of a turn.** A turn can have several requests; open it.

## Hands-on exercise

Use the training installation and your training account.

1. Open **EI: Log** in the Dashboard tab's left column.
2. Read the totals at the top. Write down your requests, tokens and estimated cost for this course so far.
3. Open your newest turn and read its requests: model, time, tokens.
4. Set **Action** to `field:shorten` (or another action you used) and press **Filter**. Then **Reset**.
5. Tick **Errors only** and press **Filter**. Are there any? Read one if so.
6. Switch to **Versions written** and find the drafts and versions the assistant wrote for you.
7. Press **Export CSV** with a date filter for today, and open the file in a spreadsheet. Check that it has no request
   texts.

## Expected result

- The learner's notes have their totals and one turn's details.
- The CSV opened and contained requests without request texts.
- The trainer checks by asking a learner to find a failed request (or explain why there is none).

## Quiz

1. Name the two views of **EI: Log**.
2. What is a turn?
3. Which policy shows everyone's requests?
   A. `expai/write` B. `expai/log` C. `expai/summary` D. `expai/vector`
4. What does **Export CSV** leave out?
5. True or false: the audit trail records prompts and answers.

<details>
<summary>Answers</summary>

1. **Requests and usage** and **Versions written**.
2. One panel message, field action or Fill the fields, with every request it led to.
3. **B.**
4. The request texts; users appear as ids only.
5. **False.** Prompts, answers and keys are never recorded there
   ([expai](../../../../features/6.0/extensions/expai.md#for-administrators)).

</details>

## Further reading

- [expai: For administrators](../../../../features/6.0/extensions/expai.md#for-administrators): usage, cost and audit.
- The usage tracking and audit trail pages under **EI: Help**.

## Related pages

- [Module 5: Chains and the log](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
