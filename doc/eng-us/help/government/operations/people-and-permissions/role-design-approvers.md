---
title: "Role design: approvers"
audience: government, admin, editor
category: public-sector
order: 80
summary: What an approver in a public body needs in Exponential to review and approve content held by an approval workflow - membership of the approvers' group, the collaboration inbox, versionread - and how to keep approval separate from authoring.
keywords: approver, approval, role design, collaboration, versionread, Approve event, approvers group, reviewer, sign-off, four eyes, two-person rule, editorial approval
type: help
---
# Role design: approvers

This page is for administrators who give senior editors, managers or legal reviewers the right to approve content
before it is published. In Exponential an approver is made of two things: being named in the **Approve** event of a
workflow, and having the rights to read and decide the request. The facts come from
[Workflows](../../../../guides/workflows.md), [My drafts and my pending items](../../../../guides/drafts-and-pending.md)
and the module definitions in `kernel/`.

## Contents
1. [How someone becomes an approver](#how-someone-becomes-an-approver)
2. [The role "Approver"](#the-role-approver)
3. [Keeping approval separate from authoring](#keeping-approval-separate-from-authoring)
4. [Setting it up](#setting-it-up)
5. [Checking it](#checking-it)
6. [Related pages](#related-pages)

## How someone becomes an approver

The **Approve** event of a workflow names its approvers in two fields: **Users who approve content** and **Groups
who approve content** ([Workflows, section 3.2](../../../../guides/workflows.md#32-set-up-the-approve-event)). Name a
group, for example "Approvers - Department of Example", never individuals: a new approver is then added by putting
them in the group, and a leaver stops approving when removed from it.

When an editor sends a version for publishing, the event creates a collaboration item, and the approvers find it in
their collaboration inbox (**My account > Collaboration**, `/collaboration/view/summary`) and on **My pending items**
(`/content/pendinglist`), filtered as "waiting for your approval". They press **Approve** or **Deny**, with a comment
for the author; the workflow cronjob then publishes or returns the version
([Workflows, section 3.5](../../../../guides/workflows.md#35-try-it)).

## The role "Approver"

| Module | Function | Limitations | Why |
|---|---|---|---|
| user | login | SiteAccess: the siteaccess where approvers work | sign in |
| collaboration | (whole module) | none | the inbox and the approval page; the collaboration module defines no separate functions, so its pages are checked against a policy over the whole module (`kernel/collaboration/module.php`, `eZUser::hasAccessToView()`) |
| content | read | the area they approve (Section or Subtree) | read the content |
| content | versionread | the same area | read the pending version; a version waiting for your approval is listed only with this right ([Drafts and pending](../../../../guides/drafts-and-pending.md#problems)) |
| content | diff | the same area | compare the pending version with the published one |
| content | dashboard, pendinglist | none | the dashboard and **My pending items** |

An approver does **not** need `content/edit` to approve. Give it only if approvers are also expected to correct texts
themselves, and then decide whether their own edits need someone else's approval (next section).

## Keeping approval separate from authoring

- **An approver's own content is let through.** "When the publishing user is one of the approvers, the event lets the
  version through" ([Workflows, section 3.2](../../../../guides/workflows.md#32-set-up-the-approve-event)). If your
  rules say nobody approves their own work, approvers must not author in the areas they approve, or a second workflow
  path must cover them. See [Two-person rule for publishing](../editorial-workflows/two-person-rule-for-publishing.md).
- **Excluded user groups** in the Approve event publish without approval. Keep that list short and documented; it is
  how an emergency bypass is built ([Emergency publishing bypass](../editorial-workflows/emergency-publishing-bypass.md)).
- **Different areas, different approvers:** one workflow per area is not needed; the Approve event's **Affected
  sections** narrow which content needs approval. For approvers per department, use separate workflows chained with
  a Multiplexer event, or a section per department.

## Setting it up

1. Create the user group "Approvers - <area>" under Users.
2. Create the role "Approver" with the policies above (**Users > Roles and policies > New role**), and assign it to
   the group, with **Assign with limitation: Subtree** if approvers are per department.
3. In **Setup > Workflows**, open the approval workflow's Approve event and add the group under **Groups who approve
   content** ([Set up an approval workflow for a section](../../tasks/people-and-permissions/set-up-an-approval-workflow-for-a-section.md)).
4. Make sure the workflow cronjob runs: **Setup > Workflow processes** says in its status bar whether `workflow.php`
   is scheduled.

## Checking it

- Publish as an editor in the affected area: the version waits for approval.
- Sign in as an approver: **My account > Collaboration** lists the request; **My pending items** shows it under
  "waiting for your approval".
- After **Approve** and the next workflow cronjob run, the content is published; the process is gone from
  **Setup > Workflow processes**.

## Related pages
- [Give an approver rights](../../tasks/people-and-permissions/give-an-approver-rights.md)
- [Approval workflows overview](../editorial-workflows/approval-workflows-overview.md)
- [Collaboration inbox](../editorial-workflows/collaboration-inbox.md)
- [Separation of duties](separation-of-duties.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
