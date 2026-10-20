---
title: Two-person rule for publishing
audience: government, admin, project-manager
category: public-sector
order: 60
summary: How to make sure no single person can publish content on a public website alone - an approval workflow on every public section and version, approvers kept out of authoring, a small documented bypass group and audit evidence - and the gaps a process has to cover.
keywords: two-person rule, four-eyes principle, dual control, approval, sign-off, self-approval, Approve event, excluded user groups, separation of duties, editorial control, audit
type: help
---
# Two-person rule for publishing

This page is for managers and administrators whose policy says that every publication is seen by a second person
(the "four-eyes" principle). It shows how close Exponential gets to that rule out of the box, which settings matter,
and where a written process has to close the gap. Sources: [Workflows](../../../../guides/workflows.md) and
[Publish without notification](../../../../features/6.0/publish-without-notification.md).

## Contents
1. [Why a publish right is not enough](#why-a-publish-right-is-not-enough)
2. [The configuration](#the-configuration)
3. [The approver's own content](#the-approvers-own-content)
4. [Operations outside publishing](#operations-outside-publishing)
5. [Evidence for an auditor](#evidence-for-an-auditor)
6. [Related pages](#related-pages)

## Why a publish right is not enough

In the edit form, whether a user may publish a draft is decided by `content/edit`
([Publish without notification](../../../../features/6.0/publish-without-notification.md)): whoever may edit can press
the publish button. The control is therefore the **approval workflow** on the trigger **Before publishing content**,
which holds every version until an approver decides.

## The configuration

| Setting | Value for a two-person rule |
|---|---|
| Trigger | **Before publishing content** runs the approval workflow |
| Approve event: Affected sections | every section visitors can read |
| Affected languages | *All languages* |
| Affected versions | *All versions* (a change is a publication too) |
| Groups who approve content | approvers' groups, never one person |
| Excluded user groups | empty, or one emergency group ([Emergency publishing bypass](emergency-publishing-bypass.md)) |
| Workflow cronjob | scheduled; checked on **Setup > Workflow processes** |

## The approver's own content

The Approve event lets a version through when the publishing user is one of its approvers
([Workflows, section 3.2](../../../../guides/workflows.md#32-set-up-the-approve-event)). For a strict two-person rule
choose one of these:

| Option | How | Cost |
|---|---|---|
| Approvers do not author | approvers' role has no `content/edit` in the areas they approve | approvers send corrections back as comments |
| Cross-approval | the approvers of area A are authors only in area B, and the other way round; two workflows or one Approve event per area with **Affected sections** | needs at least two areas and two groups |
| Second Approve event | a second Approve event in the same workflow with a different approvers' group (for example the web team); an approver of the first group is not an approver of the second, so their content still stops there | every publication needs two decisions |

Test the chosen option with a member of each group, because the result depends on group membership.

## Operations outside publishing

The approval covers publishing. Other actions change what the public sees without publishing:

| Action | Right | Control |
|---|---|---|
| Remove, move | `content/remove`, `content/move` | keep them with the web team; removed items go to the trash and can be restored |
| Hide | `content/hide` | same |
| Section or state change | `section/assign`, `state/assign` | limit with `NewSection`, `NewState` |

These actions are in the audit trail's `content` channel, so a second person can review them after the fact.

## Evidence for an auditor

- The approval itself: the collaboration item and the process, while it runs; the version history afterwards shows
  who created each version ([Version history and accountability](version-history-and-accountability.md)).
- The audit trail's `content` channel records publishing, removals, moves, sections and states.
- The trigger list and the workflow's page document the configuration; keep a dated copy in your change record.

Exponential does not produce a single "approved by" report per page; if your auditors need one, combine the version
history with the audit export ([Export audit records for an auditor](../../tasks/operations/export-audit-records-for-an-auditor.md)).

## Related pages
- [Separation of duties](../people-and-permissions/separation-of-duties.md)
- [Role design: approvers](../people-and-permissions/role-design-approvers.md)
- [Setting up the Approve event](setting-up-the-approve-event.md)
- [Legal review step](legal-review-step.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
