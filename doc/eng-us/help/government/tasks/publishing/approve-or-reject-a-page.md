---
title: Approve or reject a page
audience: government, editor
category: public-sector
order: 250
summary: For approvers - find versions waiting for your approval, read and compare them, approve or deny with a comment in the collaboration inbox, and know when the approved version appears.
keywords: approver, approve, deny, reject, collaboration, My account Collaboration, pending items, waiting for your approval, compare, comment, workflow cronjob
type: help
---
# Approve or reject a page

This page is for approvers: heads of unit, legal reviewers, clinical reviewers, communications leads. An *Approve*
workflow event creates a collaboration item for the approvers; you decide in the collaboration inbox
([Workflows](../../../../guides/workflows.md), [Drafts and pending items](../../../../guides/drafts-and-pending.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- You are an approver (named in the event, or in a group that approves).
- You may read the versions you approve (`content/versionread`); a version waiting for you is listed only if you may
  read it.
- Notification of new requests by e-mail can be set in **My notification settings**, under collaboration.

## Steps

1. Open **My pending items** and filter **waiting for your approval**, or open **My account > Collaboration**
   (`/collaboration/view/summary`).
2. Open the request. Read the preview of the version.
3. For a change to a published page, open the versions page and use **Compare** to see exactly what changed
   ([Compare two versions](compare-two-versions.md)).
4. Decide:
   - **Approve**: the version will be published at the next workflow cronjob run;
   - **Deny**, with a comment that says what to change: the author can make a new draft.

## Check the result

- [ ] The request leaves your list of open items.
- [ ] After the next frequent cronjob run, an approved version is published (check signed out).
- [ ] The audit trail records the publication.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Approving without comparing | for changes, compare; small changes hide in long texts |
| A denial without a reason | always comment; the author needs to know what to change |
| Approving your own content | you are not asked: an approver's own content passes the event |
| Requests waiting while you are away | agree a deputy; the web team can add approvers |
| Approved, but not published after an hour | the workflow cronjob may not run; tell the web team |

## Related pages

- [Publishing tasks](README.md)
- [Send a page for approval](send-a-page-for-approval.md)
- [Versions of an object](../../../../guides/content-history.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
