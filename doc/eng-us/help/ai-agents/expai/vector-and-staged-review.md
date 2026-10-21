---
title: Vector and staged review
audience: ai-agents, editor, admin
category: ai-agents
order: 29
summary: Vector, the workspace of threads of Exponential Intelligence, for bulk and agentic work under control - a scope the thread may touch, plan mode with a cost estimate before bulk work, staged review of every change per field and part before anything is written, checkpoints to go back to, and the expai/vector limitations that bound it.
keywords: Vector, expai/vector, threads, agentic, scope, plan mode, propose_plan, staged review, direct review, Changes tab, checkpoints, restore, bulk work, parallel runs, lanes
type: help
---
# Vector and staged review

This page is for editors who want the assistant to do larger jobs (many objects, several languages) and for
administrators who decide how far it may go. Vector is the most agentic part of Exponential Intelligence: a thread
plans, works through many steps and runs in the background. It is also the most controlled.

## Contents

1. [What a thread is](#what-a-thread-is)
2. [Scope: what a thread may touch](#scope-what-a-thread-may-touch)
3. [Plan mode: a plan and an estimate first](#plan-mode-a-plan-and-an-estimate-first)
4. [Staged review: nothing written until you accept it](#staged-review-nothing-written-until-you-accept-it)
5. [Checkpoints](#checkpoints)
6. [Limits an administrator sets](#limits-an-administrator-sets)

## What a thread is

Vector (`expai/vector`, in the left menu of the Dashboard tab) is a workspace of **threads**
([extension documentation: Vector](https://github.com/se7enxweb/expai/blob/main/doc/vector.md)). For each thread you
set what it may touch, how it works (plan or write, staged or direct review), which model and lane it uses; you run
it, stop it, queue follow-ups and see what it has cost. Threads are private to their owner; approvers of a version a
thread wrote may open it read only.

Vector needs `expai/write` and `expai/vector`.

## Scope: what a thread may touch

The scope is a set of nodes, subtrees (a node and everything below it), classes and languages. It is checked against
your read access and the limitations of `expai/vector` when it is saved and again at every run. The write tools write
only inside the scope.

## Plan mode: a plan and an estimate first

**Bulk work** is more than one object or more than one language, or any subtree or class in the scope. Bulk work
starts in **plan mode** and is always **staged**:

1. The thread has the read tools and `propose_plan`, no write tools.
2. The model proposes a plan: one item per object and action (create, update, translate), with languages, fields and
   an instruction per item, plus assumptions, questions and risks.
3. Every item is checked against the scope and your permissions; refused items are shown struck through and never
   run.
4. The **Plan** tab shows the items with an estimate per item and the limits the plan touches. You can edit the
   instruction of each item, untick items, and then **Approve and run**, **Ask for changes** or **Reject**.

An approved plan can run its items side by side, each with its own progress and cost.

## Staged review: nothing written until you accept it

With **Review: Staged** (the default, and always for bulk work), the write tools do not write drafts. They stage
**proposals**, one per object, language and field. In the **Changes** tab you accept or reject each proposal, part by
part and field by field. Only the accepted parts are written into drafts, and your edit permission is checked again
at that moment.

| Review | Effect |
|---|---|
| Staged | Proposals first; you accept parts; then drafts |
| Direct | Drafts written at once (never published); one object in one language only, and only where your role allows it |

Rich text proposals are converted through the kernel's input parser when they are staged, so invalid markup is caught
before review. A value equal to the published one is not staged.

## Checkpoints

Every thread keeps **checkpoints**: the state at the start of each turn, before accepted changes are written, and
before an approved plan runs. You can **restore** a thread to one of them, which undoes what the assistant did since
then without cleaning up drafts by hand. Nothing is published either way.

## Limits an administrator sets

The policy `expai/vector` has four limitations
([Permissions for AI features](permissions-for-ai-features.md)):

| Limitation | Effect |
|---|---|
| Subtree | Every node and subtree of a scope must lie inside one of them |
| Section | Every node and subtree root of the scope must be in one of them |
| Scope | The kinds of scope entries the user may pick: node, subtree, class |
| Review | staged, direct; without direct, every thread is staged |

Example from the extension: an editor role for one site section, node work only, always reviewed:

```
module expai, function vector: Subtree /1/2/58/, Scope node, Review staged
```

Costs of background runs count against the limits and budgets like every other request
([Cost control](../governance/cost-control.md)).

## Related pages

- [Chains and queued work](chains-and-queued-work.md)
- [Drafts and human review](drafts-and-human-review.md)
- [Roles and least privilege](../governance/roles-and-least-privilege.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
