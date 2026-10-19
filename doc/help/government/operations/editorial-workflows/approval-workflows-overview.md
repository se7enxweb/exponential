---
title: Approval workflows overview
audience: government, editor, admin, project-manager
category: public-sector
order: 20
summary: How approval before publishing works in Exponential - workflows, the Approve event, triggers, the collaboration inbox, pending versions and the workflow cronjob - explained for public bodies that need a documented sign-off before content goes live.
keywords: approval, approval workflow, sign-off, publishing approval, workflow, Approve event, trigger, Before publishing content, collaboration, pending, workflow cronjob, editorial process, governance
type: help
---
# Approval workflows overview

This page is for web managers, editors-in-chief and administrators who must make sure content is signed off before
it reaches the public. In Exponential the sign-off is a **workflow** with an **Approve** event, connected to the
publishing operation by a **trigger**. The facts come from [Workflows](../../../../guides/workflows.md); this page
explains the moving parts and the decisions a public body makes.

## Contents
1. [The moving parts](#the-moving-parts)
2. [What happens to one article](#what-happens-to-one-article)
3. [Decisions to make](#decisions-to-make)
4. [Prerequisites people forget](#prerequisites-people-forget)
5. [What approvals do not cover](#what-approvals-do-not-cover)
6. [Related pages](#related-pages)

## The moving parts

| Part | What it is | Where |
|---|---|---|
| Workflow | an ordered list of events, in a workflow group | **Setup > Workflows** (`/workflow/grouplist`) |
| Approve event | a step that holds a version until an approver decides | the workflow's edit page |
| Trigger | connects a workflow to an operation, before or after it | **Setup > Triggers** (`/trigger/list`), row **Before publishing content** |
| Collaboration item | the approval request approvers decide | **My account > Collaboration** (`/collaboration/view/summary`) |
| Pending version | the version waiting for the decision | **My pending items** (`/content/pendinglist`) |
| Workflow process | one running approval for one version | **Setup > Workflow processes** (`/workflow/processlist`) |
| Workflow cronjob | `workflow.php`, in the *frequent* part by default; publishes approved versions | **Setup > Cronjobs** |

## What happens to one article

1. An editor presses **Send for publishing**. The version becomes *pending*.
2. The trigger **Before publishing content** starts the approval workflow; the Approve event creates a collaboration
   item for the approvers and the process waits.
3. An approver opens the request, reads the preview and presses **Approve** or **Deny** (with a comment).
4. At the next run of the workflow cronjob: approved versions are published; denied versions go back to the author as
   drafts ([Workflows, section 1](../../../../guides/workflows.md#1-workflows-events-triggers-and-processes)).

The editor follows it on **My pending items**, which says what holds the version and links to the approval.

## Decisions to make

| Question | Where it is set | Typical public-sector answer |
|---|---|---|
| Which content needs approval? | Approve event: **Affected sections**, **Affected languages**, **Affected versions** | the public sections, all languages, all versions |
| Who approves? | **Groups who approve content** (prefer groups to named users) | an approvers' group per area |
| Who may publish without approval? | **Excluded user groups** | a small, documented group; see [Emergency publishing bypass](emergency-publishing-bypass.md) |
| New pages only, or every change? | **Affected versions**: all, new objects, updates | all versions, if a typo fix must also be checked; otherwise new objects only |
| One approver or two? | one Approve event, or two in sequence | see [Two-person rule](two-person-rule-for-publishing.md) |
| Separate legal or language review? | an extra Approve event, a second group, or an object state | see [Legal review step](legal-review-step.md), [Multilingual approvals](multilingual-approvals.md) |

Setting up the event field by field: [Setting up the Approve event](setting-up-the-approve-event.md).

## Prerequisites people forget

- **The workflow cronjob must run.** Without it, approved content is never published. The status bar of **Setup >
  Workflow processes** says whether `workflow.php` is scheduled
  ([Workflows, section 3.4](../../../../guides/workflows.md#34-make-sure-the-workflow-cronjob-runs)).
- **Approvers need the rights to decide**: the collaboration module and `content/versionread`
  ([Role design: approvers](../people-and-permissions/role-design-approvers.md)).
- **One workflow per trigger.** To run several workflows on publishing, use a *Multiplexer* event in the triggered
  workflow.
- **Test with sample data**: `./console exp:collaborationsampledata --dry-run --allow-root-user` shows what it would
  create; `--remove` takes it away again ([Workflows, section 3.6](../../../../guides/workflows.md#36-the-sample-approvals)).

## What approvals do not cover

- **Removing, moving and hiding** content are other operations; the trigger list shows which operations can start a
  workflow. Limit those rights by role instead.
- **The approver's own content** is let through by the Approve event.
- **Excluded user groups** publish directly.
- An approval checks a version, not later changes: the next version needs its own approval (with **Affected
  versions** set to all).

## Related pages
- [Setting up the Approve event](setting-up-the-approve-event.md)
- [Collaboration inbox](collaboration-inbox.md)
- [Set up an approval workflow for a section](../../tasks/people-and-permissions/set-up-an-approval-workflow-for-a-section.md)
- [Workflows guide](../../../../guides/workflows.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
