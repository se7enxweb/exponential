---
title: Preview before you publish
audience: businesses, editor, project-manager, admin
category: business-solutions
order: 190
summary: How drafts, versions, preview, approval workflows and the trash keep editorial mistakes away from customers, and how a business sets rules for who publishes what, so that content changes are as safe as code changes.
keywords: preview, draft, versions, approval, workflow, pending, publish, rollback, manage versions, compare versions, trash, version history, editorial safety
type: help
---
# Preview before you publish

This page is for managers who are responsible for what customers see on the site, and for editors and administrators
who set up the rules. Reliability is not only about servers: a wrong price, a broken landing page or a page published
too early also fails customers. Exponential's content model was built to make that hard. This page explains how, and
which rules a business should set. The editor's guide is
[Publishing, versions and the trash](../../publishing-and-versions.md).

## Contents

1. [Content mistakes are outages too](#content-mistakes-are-outages-too)
2. [Drafts: work that customers cannot see](#drafts-work-that-customers-cannot-see)
3. [Preview](#preview)
4. [Approval before publishing](#approval-before-publishing)
5. [Going back](#going-back)
6. [Large changes in the background](#large-changes-in-the-background)
7. [Rules for the business](#rules-for-the-business)
8. [Checklist](#checklist)

## Content mistakes are outages too

For a customer, a checkout page with a wrong price or a campaign page with broken links is as bad as a server error.
These mistakes are often harder to notice, because the monitoring stays green. The protection is a publishing process
in which nothing reaches customers before someone has looked at it, and every change can be undone.

## Drafts: work that customers cannot see

"Every save of an item is a **version**. When you press **Edit**, a new version is made as a **draft**: only you see
it, and the site keeps showing the published version until you publish"
([Publishing, versions and the trash](../../publishing-and-versions.md#drafts-and-versions)).

| State | What it means |
|---|---|
| **Draft** | Being written; only its creator sees and edits it |
| **Pending** | Sent for publishing, but a workflow holds it, usually an approval |
| **Published** | The version the site shows; one at a time |
| **Archived** | An earlier published version, kept so you can go back |

The draft is saved automatically every few minutes while an editor works, "so a closed browser tab costs little"
([Editing content](../../editing-content.md)). **Store draft and exit** keeps a draft for later; **My drafts** on the
dashboard lists the drafts an editor has not published ([My drafts and my pending items](../../../guides/drafts-and-pending.md)).

## Preview

The editor's left column offers **Preview**, to "see the draft as it will look"
([Editing content](../../editing-content.md)). The preview shows the version in the site's design, and lets you
choose the translation, location and siteaccess, so a multilingual or multi-brand site can check each one.

Where a business also needs to see a draft as it will be printed or exported (for example a PDF layout of a circular
letter), the preview can offer other view modes listed in `content.ini [VersionView] ViewModes[]`
([Version preview in other view modes](../../../features/6.0/version-preview-view-modes.md)). Seeing a version needs
the `content/versionread` policy.

## Approval before publishing

For content that must be checked by a second person (prices, legal texts, announcements), set up an approval
workflow ([Workflows, worked example: approval before publishing](../../../guides/workflows.md#3-worked-example-approval-before-publishing)):

- A **trigger** connects a workflow to an operation: "before publishing content, run the approval workflow".
- When an editor sends a draft for publishing, the version becomes **pending** until an approver decides.
- The approver sees the request with a preview, and approves or rejects it.
- The **workflow cronjob** (`workflow.php`, in the `frequent` part by default) then publishes the approved version.
  "Without that cronjob, approved content is never published" ([Workflows, in short](../../../guides/workflows.md#in-short)).

**Setup > Workflow processes** shows every waiting process in words, and a status bar says whether the workflow cronjob
is scheduled. Cancelling a process turns a pending version back into a draft; "No content is deleted" (same source).

> **Tip**
> Approval does not have to cover the whole site. Use it for the sections where a mistake costs the most, and let
> trusted editors publish elsewhere directly. Sections and roles decide who goes through which workflow; see
> [Departments, roles and sections](../intranets/departments-roles-and-sections.md).

## Going back

Every published change can be undone without a restore from backup
([Publishing, versions and the trash](../../publishing-and-versions.md#go-back-to-an-earlier-version)):

1. On the item, press **Manage versions**.
2. **Compare** two versions to see the differences field by field.
3. Choose the earlier version and press **Make a new draft**; check it and publish it as usual.

Removed items go to the **trash** and can be restored from there
([Publishing, versions and the trash, the trash](../../publishing-and-versions.md#the-trash)). The full guide to the
versions page is [Versions of an object](../../../guides/content-history.md).

How many versions are kept is a setting: `content.ini [VersionManagement] DefaultVersionHistoryLimit`, shipped as 45
per item; when the limit is reached the oldest archived version is removed to make room
(`settings/content.ini`; [Publishing, versions and the trash](../../publishing-and-versions.md#drafts-and-versions)).
If your business must keep a longer history of some content, ask your administrator to raise the limit for those
classes.

The audit trail records who published, moved, removed or restored what, so "who changed this?" always has an answer
([Audit trail for operations](audit-trail-for-operations.md)).

## Large changes in the background

Removing, copying or moving a large subtree used to run inside one web request and could fail on time or memory
limits. From 6.0.15 the administration offers to run it as a **content job** that works in batches, takes a
checkpoint after each batch and resumes where it stopped. The confirmation page shows what the operation touches
before you start, and suggests the background for 50 nodes or more
([Content jobs](../../../features/6.0/content-jobs.md)). See
[Cronjobs and background jobs](cronjobs-and-background-jobs.md).

## Rules for the business

Write down and share:

- **Who may publish where**: which roles publish directly, which go through approval.
- **What must be previewed**: at least landing pages, prices, legal texts, and anything linked from a newsletter or a
  campaign.
- **Who checks the second language or brand** on a multilingual or multi-brand site.
- **When large changes happen**: large removals, moves and imports outside peak hours, ideally as content jobs.
- **How mistakes are reported and undone**: editors know **Manage versions** and the trash.
- **Before a peak**: campaign content is published and checked days ahead ([Peak traffic planning](peak-traffic-planning.md)).

## Checklist

- [ ] Editors know the difference between draft, pending and published.
- [ ] Important content is previewed in every language and siteaccess it appears in.
- [ ] Sections where mistakes are costly go through an approval workflow, and the workflow cronjob runs.
- [ ] Editors know how to make a new draft from an earlier version, and how to use the trash.
- [ ] The version history limit fits your business's needs.
- [ ] Large removals and moves run as content jobs, outside peak hours.

## Related pages

- [Publishing, versions and the trash](../../publishing-and-versions.md)
- [Approval workflows for intranets](../intranets/approval-workflows.md)
- [Workflows](../../../guides/workflows.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
