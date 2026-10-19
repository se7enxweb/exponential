---
title: The collaboration inbox
audience: government, editor
category: public-sector
order: 40
summary: Where approvers find approval requests (My account > Collaboration and My pending items), how they approve or deny with a comment, what the author sees, and the rights an approver needs to open the inbox at all.
keywords: collaboration, inbox, approval request, approve, deny, comment, approver, My pending items, collaboration/view/summary, sign-off, editorial review
type: help
---
# The collaboration inbox

This page is for approvers and the editors whose content they approve. It explains where an approval request appears,
how to decide it and what happens next. It is based on [Workflows](../../../../guides/workflows.md) and
[My drafts and my pending items](../../../../guides/drafts-and-pending.md).

## Contents
1. [Where requests appear](#where-requests-appear)
2. [Deciding a request](#deciding-a-request)
3. [What the author sees](#what-the-author-sees)
4. [Rights needed](#rights-needed)
5. [Good practice](#good-practice)
6. [Related pages](#related-pages)

## Where requests appear

| Page | Address | Shows |
|---|---|---|
| **My account > Collaboration** | `/collaboration/view/summary` | the approval requests you take part in; "Nothing to handle yet" when there are none |
| **My pending items** | `/content/pendinglist` | versions sent by you, and versions waiting for your approval, with a link **Open the approval** |
| **Setup > Workflow processes** | `/workflow/processlist` | (administrators) every waiting process, with **Open the approval request** |

A version waiting for your approval is listed on My pending items only when you may read it with
`content/versionread` ([Drafts and pending](../../../../guides/drafts-and-pending.md#2-my-pending-items)).

## Deciding a request

1. Open the request from the inbox or from **Open the approval** on My pending items.
2. Read the preview of the pending version. Compare it with the published version if it is a change: the versions
   page of the object offers **Compare** to anyone with `content/diff`
   ([Versions of an object](../../../../guides/content-history.md#2-the-actions-of-a-version)).
3. Press **Approve**, or **Deny** and write a comment for the author saying what to change.
4. The decision takes effect at the next run of the workflow cronjob: approved versions are published, denied
   versions go back to the author as drafts ([Workflows, section 3.5](../../../../guides/workflows.md#35-try-it)).

## What the author sees

- After **Send for publishing**, the version waits; **My pending items** lists it under "sent by you" with the state
  of its approval and who approves.
- After **Deny**, the version is a draft again under **My drafts**; the comment is with the approval.
- After **Approve** and the cronjob, the content is published and leaves the pending list.

## Rights needed

The collaboration module defines no separate policy functions (`kernel/collaboration/module.php`), so its pages are
checked against a policy over the whole module. An editor role without it gets "The requested page could not be
displayed" on the inbox. Approvers therefore need a role with the `collaboration` module, as on
[Role design: approvers](../people-and-permissions/role-design-approvers.md).

## Good practice

- **Decide within an agreed time**, for example two working days; a waiting process older than a day is marked amber
  on Setup > Workflow processes, which helps a web manager chase approvals.
- **Write denials as instructions** ("Replace the table with a list; add the contact address"), so the author can
  act without a meeting.
- **Absence:** approval goes to groups, so a group with two or more approvers keeps working during leave.
- **A leaver among the approvers**: requests already made keep their participants; cancel and resend them as
  [Workflow troubleshooting](workflow-troubleshooting.md) describes.

## Related pages
- [Drafts and pending items](drafts-and-pending-items.md)
- [Approval workflows overview](approval-workflows-overview.md)
- [Give an approver rights](../../tasks/people-and-permissions/give-an-approver-rights.md)
- [Editors 101 course](../../../trainers/editors-101/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
