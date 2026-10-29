---
title: Approval workflows
audience: businesses, admin, project-manager
category: business-solutions
order: 60
summary: How an organization makes sure texts are checked before staff, partners or the public see them - the Approve workflow event, the collaboration inbox, the workflow cronjob, review stages with object states, and how to choose between them.
keywords: approval, approval workflow, Approve event, four-eyes principle, review, collaboration, workflow cronjob, workflow processes, object states, review states, staging section, pending, intranet governance
type: help
---
# Approval workflows

This page is for managers who need a second pair of eyes on what is published: HR policies, management
announcements, prices on a partner portal. Exponential offers two built-in ways to do it, and a simple third one. The
page explains what each feels like for the people involved and links to the step-by-step guides.

## Contents

1. [Three ways to approve](#three-ways-to-approve)
2. [Approval before publishing](#approval-before-publishing)
3. [Review stages with object states](#review-stages-with-object-states)
4. [A staging section](#a-staging-section)
5. [Choosing](#choosing)
6. [What must be running](#what-must-be-running)
7. [Proof of who approved what](#proof-of-who-approved-what)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## Three ways to approve

| Way | In one sentence | Guide |
|---|---|---|
| Approval before publishing | publishing waits until an approver says yes in the collaboration inbox | [Workflows, 3](../../../guides/workflows.md#3-worked-example-approval-before-publishing) |
| Review stages | content is published but only readable to the public once its state is Approved | [Object states, 9](../../../guides/object-states.md#9-a-worked-example-a-review-workflow) |
| Staging section | pages are prepared in a section readers cannot see, then released in one step | [Sections, 13.3](../../../guides/sections.md#133-staging-content-before-it-goes-public) |

## Approval before publishing

A **workflow** is a list of steps; a **trigger** connects it to an operation. "Before publishing content, run the
approval workflow" is the classic case
([Workflows, In short](../../../guides/workflows.md#in-short)).

What the people see:

1. An editor publishes an article. It is not published; the version waits for approval ("pending").
2. An approver opens **My account > Collaboration**, looks at the preview and presses **Approve**, or **Deny** with a
   comment for the author.
3. On the next run of the workflow cronjob the approved article is published. A denied version becomes a draft for
   the author again.

What the *Approve* event can narrow down
([Workflows, 3.2](../../../guides/workflows.md#32-set-up-the-approve-event)):

| Field | Business meaning |
|---|---|
| Affected sections | only content in these areas needs approval (for example HR and Management) |
| Affected languages | only some translations need approval |
| Affected versions | only new objects, only updates, or both |
| Users / Groups who approve content | the approvers |
| Excluded user groups | people who publish without approval |

An approver never needs their own content approved. Approvers are told by mail about items that wait for them when
they tick the collaboration setting **Approval** on their notification page
([Notifications: the user's guide, Collaboration notification](../../../features/6.0/notifications.md#collaboration-notification)).

**Setup > Workflow processes** (`/workflow/processlist`) lists every process that waits, in words, with a status bar
that says whether the workflow cronjob is scheduled and when it runs next. **Cancel...** stops a process after a
confirmation; no content is deleted ([Workflows, 7](../../../guides/workflows.md#7-cancelling-and-cleaning-up-processes-safely)).

A sample approval setup can be created and removed again with `./console exp:collaborationsampledata --apply`, which helps
when training approvers ([Workflows, 3.6](../../../guides/workflows.md#36-the-sample-approvals)).

## Review stages with object states

Object states are labels every object carries, one per state group. Roles make them matter. The guide's worked
example creates a group `review` with Draft, In review and Approved, and:

- lets visitors (or staff) read only content in the state Approved;
- lets authors edit their own drafts and send them to In review, nothing else;
- lets reviewers send texts back to Draft or approve them.

| Who | Does | State | Visible to readers |
|---|---|---|---|
| Author | creates and publishes | Draft | no |
| Author | sets In review | In review | no; the author can no longer edit |
| Reviewer | sends back | Draft | no |
| Reviewer | approves | Approved | yes |

Set the existing public content to Approved **first**, or it disappears from readers when the group is created
([Object states, 9](../../../guides/object-states.md#9-a-worked-example-a-review-workflow)).

## A staging section

The simplest way: a section `Staging` that readers' roles do not include. Editors create pages in their final place
and assign them to Staging; when ready, the top item is assigned to the reading section and everything below becomes
readable at once ([Sections, 13.3](../../../guides/sections.md#133-staging-content-before-it-goes-public)). There is
no recorded approval step, so it suits launches more than day-to-day governance.

## Choosing

| Need | Best fit |
|---|---|
| Nothing may appear before an approver has seen it | approval before publishing |
| A formal record of approve and deny in an inbox | approval before publishing |
| Many authors, a small review team, back-and-forth on drafts | review stages |
| Prepare a whole new area and release it on one day | staging section |
| Only some areas need approval | approval, with Affected sections |
| Approval by department | approval with approver groups per section, or review stages limited by section |

## What must be running

- **The workflow cronjob** (`workflow.php`, in the `frequent` part by default). Without it, approved content is never
  published ([Workflows, 6](../../../guides/workflows.md#6-the-workflow-cronjob-how-processes-resume)).
- **Mail**, so approvers learn that something waits.
- **Approvers who are reachable.** When an approver leaves, clean up as described in
  [Workflows, 10.2](../../../guides/workflows.md#102-clean-up-after-a-removed-approver).

## Proof of who approved what

The audit trail records content changes, including sections and states, in a tamper-evident log
([The audit trail](../../../features/6.0/audit-trail.md)). Combined with the versions page of each object
([Versions of an object](../../../guides/content-history.md)), the organization can show who changed a text and when.

## Checklist

- [ ] Decided which areas need approval and which way fits.
- [ ] Approver groups named, with a deputy for holidays.
- [ ] Workflow cronjob scheduled and visible in the status bar.
- [ ] Approvers have ticked the Approval collaboration setting.
- [ ] One test article approved and one denied before going live.
- [ ] A procedure for when an approver leaves.

## Related pages

- [Workflows](../../../guides/workflows.md)
- [Object states](../../../guides/object-states.md)
- [Drafts and pending versions](../../../guides/drafts-and-pending.md)
- [Registration and account approval](../extranets/registration-and-account-approval.md)
- [Preview before you publish](../reliability/preview-before-you-publish.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
