---
title: Approval before publishing
audience: government, admin, editor
category: public-sector
order: 100
summary: How to make every transparency publication wait for a second person's approval in Exponential: the Approve workflow event, the trigger, the cronjob, and how approvers and authors see pending items.
keywords: approval, approve, workflow, trigger, pending, four eyes, second check, collaboration, workflow cronjob, Before publishing content, sign-off
type: help
---
# Approval before publishing

This page is for administrators who set up a four-eyes rule for transparency publications, and for the editors and
approvers who work with it. Exponential holds a version as *pending* until an approver decides, with the workflow
event *Approve*. The full reference is [Workflows](../../../guides/workflows.md).

## Contents

1. [How it works](#how-it-works)
2. [Setting it up](#setting-it-up)
3. [For authors](#for-authors)
4. [For approvers](#for-approvers)
5. [What is recorded](#what-is-recorded)
6. [Problems](#problems)

## How it works

1. An editor presses **Send for publishing**.
2. The trigger *Before publishing content* starts the approval workflow.
3. The *Approve* event creates a collaboration item for the approvers; the version is pending.
4. An approver presses **Approve** or **Deny**.
5. The workflow cronjob runs again; an approved version is published, a denied one goes back to the author as a draft.

## Setting it up

1. **Setup > Workflows**: open a workflow group, **New workflow**, name it ("Transparency approval").
2. Choose **Event / Approve** and press **Add event**. Set:
   - **Affected sections**: the transparency section;
   - **Affected versions**: *All versions* (new and changed items);
   - **Groups who approve content**: the approvers' user group;
   - **Excluded user groups**: none, unless a group may publish directly.
3. **OK**.
4. **Setup > Triggers**: row **Before publishing content**, choose the workflow, **Apply changes**. One workflow per
   trigger; to combine with an existing one use a **Multiplexer** event.
5. Check that the `frequent` cronjob group runs: **Setup > Workflow processes** shows in its status bar whether the
   workflow cronjob is scheduled. Without it, approved content is never published.

An approver's own content is let through without approval, so name at least two approvers.

## For authors

- **My pending items** on the dashboard lists your versions waiting for approval and what holds them
  ([Drafts and pending items](../../../guides/drafts-and-pending.md)).
- A denied version comes back as a draft; edit and send again.

## For approvers

- **Collaboration** on the dashboard (`/collaboration/view/summary`) lists the requests.
- Open the request, read the version (preview), add a comment if your process wants one, press **Approve** or **Deny**.
- A version waiting for your approval is listed only if you may read it (`content/versionread`).

## What is recorded

The version history keeps who created the version; the audit trail records the publish event in the `content`
channel ([Audit trail](../../../features/6.0/audit-trail.md)). Keep in mind that the approval decision itself lives in the
collaboration item; check in the audit console on your installation which events it shows before relying on it as
the record of approval.

## Problems

| Problem | Fix |
|---|---|
| Approved items are not published | The workflow cronjob does not run; schedule the part with `workflow.php` |
| Nothing waits for approval | The content is in another section than **Affected sections**, or the author is in an excluded group or is an approver |
| A process must be stopped | **Setup > Workflow processes**, **Cancel...**: the pending version becomes a draft again; no content is deleted |

## Related pages

- [Workflows](../../../guides/workflows.md)
- [Drafts and pending items](../../../guides/drafts-and-pending.md)
- [Who may publish datasets](../open-data/who-may-publish-datasets.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
