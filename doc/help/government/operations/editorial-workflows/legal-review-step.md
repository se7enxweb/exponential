---
title: Legal review step
audience: government, admin, editor
category: public-sector
order: 70
summary: Two ways to add a legal or compliance review to publishing in Exponential - a second approvers' group in the workflow, or an object state "Legal cleared" that roles respect - with the trade-offs and what neither of them does.
keywords: legal review, compliance review, legal clearance, counsel, sign-off, approval, object state, Approve event, second approver, regulated content, notices, public announcements
type: help
---
# Legal review step

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

This page is for web managers whose organization requires legal or compliance review of some content before
publication: notices, regulations, procurement announcements, statements. Exponential has no legal-review feature
as such; the review is built from an **approval workflow** or an **object state**. Sources:
[Workflows](../../../../guides/workflows.md) and [Object states](../../../../guides/object-states.md).

## Contents
1. [Choosing the mechanism](#choosing-the-mechanism)
2. [Option A: legal reviewers as approvers](#option-a-legal-reviewers-as-approvers)
3. [Option B: a "Legal" state group](#option-b-a-legal-state-group)
4. [Deciding which content needs review](#deciding-which-content-needs-review)
5. [What it does not do](#what-it-does-not-do)
6. [Related pages](#related-pages)

## Choosing the mechanism

| | Option A: workflow | Option B: state |
|---|---|---|
| Holds publication | yes, the version waits | only if visitors' read policy requires the state |
| Reviewer works in | the collaboration inbox | the object's **Details** tab, Content state |
| Survives later edits | no, each version is reviewed again | yes, the state stays until someone changes it |
| Best for | every publication in a legal area | one clearance per document, edited later for typos |

## Option A: legal reviewers as approvers

1. Create a user group "Legal reviewers" and give it the approver role
   ([Role design: approvers](../people-and-permissions/role-design-approvers.md)).
2. Put the legal content in its own section (for example "Notices") or decide by section already used.
3. In the approval workflow, add a second **Approve** event with **Affected sections** = the legal section(s) and
   **Groups who approve content** = Legal reviewers. Content then needs the editorial approval and the legal one.
4. Or, if legal review replaces editorial review there, use one Approve event per area.

The approver's own content is let through by the event, so legal reviewers should not author in the area they clear.

## Option B: a "Legal" state group

Following the review example of [Object states, section 9](../../../../guides/object-states.md#9-a-worked-example-a-review-workflow):

1. **Setup > States**: create the group `legal` with the states Not needed (first, so the default), Pending legal,
   Cleared.
2. Anonymous role: add `StateGroup_legal = Not needed, Cleared` to its `content/read` policy, so pending content
   is invisible to visitors.
3. Editors: `state/assign` with `NewState = legal/pending_legal` for the legal section.
4. Legal reviewers: `state/assign` with `NewState = legal/cleared, legal/pending_legal`.
5. Every change of state is recorded as `content.object.state` in the audit trail, with the states before and after.

Set existing public content to Not needed (or Cleared) before limiting the Anonymous role.

## Deciding which content needs review

Write it down: which sections or classes, which kinds of change (new items only, or every change), how quickly the
reviewers answer, and what happens when they are absent. Use Affected sections, Affected versions or a Class
limitation to express it; avoid "editors decide case by case", which cannot be checked later.

## What it does not do

- It does not check the text: reviewers do.
- It does not keep a separate legal file; the record is the collaboration item, the version history and the audit
  trail. Export what your records rules require ([Version history and accountability](version-history-and-accountability.md)).
- It does not stop removal or hiding of content; those are separate rights.

## Related pages
- [Two-person rule for publishing](two-person-rule-for-publishing.md)
- [Object states in permissions](../people-and-permissions/object-states-in-permissions.md)
- [Set up a review state group](../../tasks/people-and-permissions/set-up-a-review-state-group.md)
- [Setting up the Approve event](setting-up-the-approve-event.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
