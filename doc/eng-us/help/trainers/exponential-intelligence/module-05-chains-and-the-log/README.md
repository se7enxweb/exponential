---
title: "Module 5: Chains and the log"
audience: trainer, editor, admin
category: training
order: 50
summary: The fifth module of the Exponential Intelligence course. Learners run an AI chain to its human checkpoint and read the log of their requests and versions. 1 hour 30 minutes.
keywords: exponential intelligence, module 5, ai chains, checkpoint, job queue, log, requests, versions written, csv
type: help
---
# Module 5: Chains and the log

Two tools for working at a distance from the text: chains, which write, review and revise in the background and stop
for the editor; and the log, which records everything the assistant did.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Duration | 1 hour 30 minutes |
| Lessons | 2 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 5.1: AI chains and the human checkpoint](ai-chains.md) | 60 minutes | Run a chain, ask for changes at the checkpoint, approve |
| [Lesson 5.2: Reading the log](reading-the-log.md) | 30 minutes | Read turns, filter, switch views, export CSV |

## Preparing this module

- The job queue (expqueue) running on the training installation, with a worker or its cron part every minute; run one
  chain yourself before the class and time it.
- The learners' role needs `expai/chain` (limited to the article chain if you like).
- Chains make several requests per run; check the training limits and budget.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 5.1 | 60 |
| Lesson 5.2 | 30 |
| **Total** | **90** |

## Related pages

- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
