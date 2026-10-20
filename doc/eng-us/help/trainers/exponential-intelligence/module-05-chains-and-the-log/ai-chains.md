---
title: "Lesson 5.1: AI chains and the human checkpoint"
audience: trainer, editor
category: training
order: 51
summary: Learners start an AI chain from a brief, follow its steps (write, review, revise, SEO and QA), and decide at the human checkpoint with Approve and continue, Ask for changes or Stop the run. 60 minutes.
keywords: exponential intelligence, chains, ai chain, write with a chain, start the chain, review, revise, seo, checkpoint, approve and continue, ask for changes, stop the run, job queue, run page
type: help
---
# Lesson 5.1: AI chains and the human checkpoint

An **AI chain** writes in steps instead of one answer: a model writes, another reviews the text against a score and a
checklist, the first revises until the review passes, a third prepares title and metadata and checks the links, and
then an editor decides before anything else happens. This lesson runs one chain per learner and practices the
checkpoint.

## At a glance

| | |
|---|---|
| Course | [Exponential Intelligence](../README.md) |
| Module | [Module 5: Chains and the log](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Editors with the `expai/chain` policy |
| Prerequisites | [Module 2](../module-02-the-intelligence-tab/README.md); the job queue running on the training installation |

## Learning objectives

After this lesson, learners can:

- start a chain from the Intelligence tab (**Write with a chain**) or from **Start writing** (**Use chain**, **Items**,
  **Start the chain**);
- follow a run's steps, scores, issues and cost on its run page;
- decide at the checkpoint: **Approve and continue**, **Ask for changes** (with a note) or **Stop the run**;
- explain that a chain's result is one draft, never a published page.

## Trainer's notes

### How to present it

Sources: the **Chains** page and the chains documentation under **EI: Help** (`extension/expai/doc/chains.md`).

1. **The idea** (3 minutes). Read the **Chains** page's text: *A chain writes in steps: a model writes, another reviews
   and the text is revised until it passes, a third prepares the title and summary, then an editor approves before
   anything else happens. Every step is a queued job with its cost; the result is a draft.*
2. **The shipped article chain** (4 minutes). Draw it:

   ```text
   write -> review --fail--> revise -> review ... (at most 3 reviews)
                |pass
                v
          SEO and QA -> checkpoint (editor) -> translate (hand-off) -> done
   ```

   The draft is written at the checkpoint, below the chosen location, as the user who started the run.
3. **Starting** (3 minutes). In the Intelligence tab, the section "Write with a chain": choose the chain, the number of
   items, the brief, **Start the chain**. On the dashboard's **Start writing**: **Use chain**, **Items**, **Start the
   chain**. Chains need the job queue; steps run in the background.
4. **The checkpoint** (5 minutes). The run page shows state, cost, tokens, time, the draft, every step with model,
   verdict, score, issues and checklist, the metadata and the link check. Three buttons: **Approve and continue** (the
   draft is read back, so your edits in the edit form are kept), **Ask for changes** (a note is required; the run
   revises and reviews again and returns), **Stop the run** (queued jobs are canceled; the draft stays a draft).
5. **Escalation** (2 minutes). After three failed reviews the run goes on and the checkpoint shows the reasons.

### Common questions

**Is a chain better than a single request?** For longer texts with a quality bar, the review step catches problems;
it costs more requests.

**Who may see my run?** You, holders of `expai/log`, and users of the chain who may edit the run's item.

**What does the translate step do with a new draft?** It is skipped for an unpublished draft; translations follow
after publishing through translation sync.

### Pitfalls

- **No job queue.** Without a running queue the run never starts its steps. Check before the class.
- **Approving unread.** The checkpoint exists for reading; open the draft first.
- **Many items.** Each item is one run with all its steps; start with one.

## Hands-on exercise

Use the training installation, a role with `expai/chain`, and your course folder.

1. Open your course folder's **Intelligence** tab and find "Write with a chain" (or use **Start writing** on the
   dashboard page with **Use chain**).
2. Choose the article chain, **Items**: 1, and the brief: `An article for beginners on composting in a small garden,
   about 200 words, practical steps, no brand names.`
3. Press **Start the chain**. The run page opens.
4. Follow the steps as they finish (refresh or wait). Write down the review score and any issues.
5. When the run waits at the checkpoint, open the draft from the run page and read it.
6. Press **Ask for changes** with the note `Add one sentence on what not to compost.` Wait until the run is back at the
   checkpoint and read the draft again.
7. Press **Approve and continue**.
8. Open **EI: Chains** and find your run in the list; read its final state and cost.
9. Publish the draft from **My drafts**, or discard it.

## Expected result

- One chain run per learner reached the checkpoint, went through one round of changes, and was approved.
- The draft below the learner's folder contains the requested sentence; it is published or discarded by the learner.
- The trainer checks by opening one learner's run page on the projector.

## Quiz

1. Name the steps of the shipped article chain in order.
2. When is the chain's draft written?
3. What does **Ask for changes** require?
   A. Nothing B. A note C. A new brief D. A new chain
4. What happens to the draft when you press **Stop the run**?
5. What does a chain need on the server to run its steps?

<details>
<summary>Answers</summary>

1. Write, review (revise and review again on failure, at most three reviews), SEO and QA, checkpoint, translate.
2. At the checkpoint.
3. **B.**
4. It stays a draft; queued jobs are canceled.
5. The job queue (the expqueue extension with a worker or its cron part).

</details>

## Further reading

- The chains and job queue pages under **EI: Help**; [expqueue](../../../../features/6.0/extensions/expqueue.md).

## Related pages

- [Module 5: Chains and the log](README.md)
- [Exponential Intelligence: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
