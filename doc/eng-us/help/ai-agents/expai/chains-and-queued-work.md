---
title: Chains and queued work
audience: ai-agents, editor, admin, developer
category: ai-agents
order: 30
summary: Multi-step AI work in Exponential Intelligence through the job queue - AI chains (write, review, revise, SEO and QA, a human checkpoint, then translate), what runs in the background, who it runs as, lanes and batches for cheaper bulk work, and the cron parts it needs.
keywords: AI chains, multi-step, agentic workflow, job queue, expqueue, checkpoint, human in the loop, Approve and continue, Ask for changes, background work, lanes, batch, cron parts, expai/chain
type: help
---
# Chains and queued work

This page is for editors and administrators who use Exponential Intelligence for work that takes longer than one
request: multi-step writing, many items at once, work that runs while nobody watches. That work runs through the job
queue of the extension `expqueue`, as the user who started it.

## Contents

1. [What runs in the background](#what-runs-in-the-background)
2. [AI chains](#ai-chains)
3. [The human checkpoint](#the-human-checkpoint)
4. [Lanes and batches](#lanes-and-batches)
5. [What the queue needs](#what-the-queue-needs)

## What runs in the background

| Work | Started from | Ends in |
|---|---|---|
| AI chains | The Intelligence tab ("Write with a chain") or the dashboard ("Start writing") | One draft per item, after a human checkpoint |
| Translation sync | A workflow event after publishing, or the Translations section | Drafts of translations ([Translation help](translation-help.md)) |
| Missing alt text report | `expai/alttext` | Drafts with suggested alt text ([Alt text for images](alt-text-for-images.md)) |
| Vector background runs and approved plans | A Vector thread | Proposals or drafts ([Vector and staged review](vector-and-staged-review.md)) |
| Model test and load | Setup > EI: Models | A result on that page |

Queued work runs **as the user who queued it**, with that user's permissions; there is no service account.

## AI chains

A chain writes content in steps instead of in one answer
([extension documentation: chains](https://github.com/se7enxweb/expai/blob/main/doc/chains.md)). The shipped chain
`article`:

```
write -> review --fail--> revise -> review ... (at most 3 reviews)
             |pass
             v
       SEO and QA -> checkpoint (editor) -> translate (hand-off) -> done
```

1. **write**: the brief, the location and the class's fields go to a strong model; the answer is the fields.
2. **review**: a second model scores the text and checks it against a checklist.
3. **revise**: on a failed review, the first model gets the issues and writes again. After three failed reviews the
   run goes on and the editor sees the reasons at the checkpoint.
4. **SEO and QA**: a small model prepares a short title, meta title, meta description and keywords; every link is
   checked offline.
5. **checkpoint**: the draft is written now, labeled Exponential Intelligence, and the run waits for an editor.
6. **translate**: after approval, the translation is handed to the translation sync.

Each step is a queued job with its own model, lane, tokens and cost. The models of the shipped chain are a
configuration choice of the extension; administrators define their own chains in the settings. Chains need the policy
`expai/chain` with `expai/write`.

## The human checkpoint

At the checkpoint, the run page (`expai/chain/(run)/<id>`) shows every step with its model, verdict, score, issues
and cost, the meta data, the link check and the draft. An editor who may act on the run has three buttons:

- **Approve and continue**: the draft is read back, so your changes in the edit form are kept, and the run goes on.
- **Ask for changes** (with a note): the note goes to the revise step as a high-severity issue; the review runs again
  and the run comes back to the checkpoint. The same draft is updated.
- **Stop the run**: queued jobs are canceled; the draft stays a draft.

Nothing a chain writes is published by the chain.

## Lanes and batches

A **lane** is a way of sending a request: standard, flex (slower, cheaper), batch (answered within hours, typically
half the price) or fast (a premium). The panel and field actions always use the standard lane. Queued requests take
the lane the lane policy chooses. The extension never uses a lane whose price it would have to guess: such lanes ship
disabled ([Cost control](../governance/cost-control.md#lanes-and-batches)).

## What the queue needs

- The extension `expqueue` is active, and its cron part runs every minute
  (`php runcronjobs.php -q -s <admin siteaccess> expqueue`), or a worker runs.
- The cron part `expai` runs every five minutes (`php runcronjobs.php -q -s <admin siteaccess> expai`): it resumes
  held jobs that fit the budget again, and prunes old data.
- A budget for queued work, so nothing runs up a bill unattended ([Cost control](../governance/cost-control.md)).

## Related pages

- [Queues with expqueue](../../creators/app-creators/queues-with-expqueue.md)
- [Background work patterns](../../creators/app-creators/background-work-patterns.md)
- [Vector and staged review](vector-and-staged-review.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
