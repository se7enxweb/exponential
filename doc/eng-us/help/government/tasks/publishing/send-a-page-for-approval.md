---
title: Send a page for approval
audience: government, editor
category: public-sector
order: 240
summary: For editors in sections with an approval workflow - send a page for publishing, see it wait in My pending items, read the approver's comments, and handle a denied version.
keywords: approval, send for approval, Send for publishing, pending, My pending items, approval workflow, collaboration, denied, comments, workflow cronjob
type: help
---
# Send a page for approval

This page is for editors whose changes need approval. On such sites an *Approve* workflow event holds the version
until an approver decides; the editor presses the same **Send for publishing** button
([Workflows](../../../../guides/workflows.md), [Drafts and pending items](../../../../guides/drafts-and-pending.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [If it is denied](#if-it-is-denied)
5. [Pitfalls](#pitfalls)

## Before you start

- Know whether approval applies to you and to this part of the site (it is set per section, language, version type and
  user group). If you are in an excluded group, your change is published at once.
- Know who approves, and how long it usually takes.

## Steps

1. Edit the page and press **Send for publishing**. Instead of being published, the version becomes *pending*.
2. Open **My pending items** (`/content/pendinglist`, also on the dashboard). The card shows the state of the approval,
   who sent it, and what holds it.
3. **Open the approval** (or **Approval and comments**) shows the request and any comments.
4. Wait for the decision. After an approval, the version is published at the next run of the workflow cronjob.

## Check the result

- [ ] The version is listed in **My pending items** until it is decided.
- [ ] After approval and the next cronjob run, the page is published (check signed out).
- [ ] **Manage versions** shows it as published, and the previous one as archived.

## If it is denied

1. The approver's comment explains why; read it in the approval.
2. Make a new draft from the denied version ([Restore an earlier version](restore-an-earlier-version.md) describes
   **Make a new draft**), change what was asked, and **Send for publishing** again.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Expecting immediate publication | approvals take time, and the cronjob runs every few minutes |
| Editing the same page again while it is pending | you get an edit conflict; wait or coordinate |
| A version pending for days with no approver | ask the web team to check **Setup > Workflow processes** |
| Urgent content in an approval section | agree an urgent route in advance |

## Related pages

- [Publishing tasks](README.md)
- [Approve or reject a page](approve-or-reject-a-page.md)
- [Publishing, versions and the trash](../../../publishing-and-versions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
