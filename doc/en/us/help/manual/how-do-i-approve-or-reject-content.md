---
title: How do I approve or reject content?
audience: editor, admin
category: manual
order: 315
summary: Open Dashboard > Collaboration, open the item Waiting for approval, check it with Preview, add a comment if needed and click Approve or Deny.
keywords: approve, deny, reject, approval, approver, collaboration, waiting for approval, workflow, four-eyes principle
context: collaboration/view
type: help
---
# How do I approve or reject content?

When you are an approver, the versions that wait for you are in **Collaboration**. Approving publishes the version;
denying sends it back to the author as a draft.

## Who can do this

Users or groups named as approvers in the site's approval workflow. Only an approver of the item can decide.

## Steps

1. Open **Dashboard** in the top menu and choose **Collaboration**.
   The items that wait are marked **Waiting for approval**.
2. Click the item.
   It shows who sent it, when, and the participants.
3. Click **Preview** to read the version as visitors would see it.
4. If you want to tell the author something, write it and click **Add Comment**.
5. Click **Approve** to publish it, or **Deny** to send it back. Confirm the question.
   The item is closed. The author sees your decision and comment.

![An approval item in Collaboration with Preview, the comment box and the buttons Approve and Deny](images/collaboration-approval.webp)

## What can go wrong

- **"Only an approver can approve or deny this item."** You take part in the item but are not its approver.
- **You approved, but the content is not published yet.** Publishing continues when the workflow cronjob runs, usually
  within minutes.
- **"This approval is closed: comments can no longer be added."** Someone already decided. Write to the author
  directly.

## Related pages

- [How do I send content for approval?](how-do-i-send-content-for-approval.md)
- [Approvals and review](../customers/day-to-day/approvals-and-review.md)
- [Workflows](../../guides/workflows.md)
- [User manual](README.md)
