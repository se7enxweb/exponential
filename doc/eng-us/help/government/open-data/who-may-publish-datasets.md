---
title: Who may publish datasets
audience: government, admin, project-manager
category: public-sector
order: 150
summary: How to set up roles, sections and an approval step so that only the data team creates datasets, a data steward approves them before they go public, and every publication is recorded.
keywords: roles, policies, data team, data steward, approval, workflow, section, subtree limitation, content/create, publish rights, open data governance
type: help
---
# Who may publish datasets

This page is for administrators and project managers who set up the publishing rights of an open data program.
Exponential controls rights with roles and policies, groups content in sections, and can hold a version for
approval with a workflow. The building blocks come from [Roles and policies](../../../guides/roles-and-policies.md),
[Sections](../../../guides/sections.md) and [Workflows](../../../guides/workflows.md).

## Contents

1. [Typical people and their rights](#typical-people-and-their-rights)
2. [A section for open data](#a-section-for-open-data)
3. [The Data publisher role](#the-data-publisher-role)
4. [An approval step](#an-approval-step)
5. [Recording who did what](#recording-who-did-what)
6. [Checklist](#checklist)

## Typical people and their rights

| Person (function) | May | Must not |
|---|---|---|
| Data publisher | create and edit datasets and distributions in the catalog | change classes, roles or other parts of the site |
| Data steward | approve or reject datasets before publication | (optionally) edit content |
| Web editor | link datasets from news and pages | create datasets |
| Administrator | classes, roles, feeds, API settings | publish data without approval |

Name functions, not people, in your documentation; assign people to user groups.

## A section for open data

1. **Setup > Sections**: create a section "Open data" (identifier `open_data`).
2. Assign the catalog folder to it with **Assign content**; everything below it gets the section, and new datasets
   take the section of their parent.
3. Make sure the Anonymous role may read the section, or visitors will not see the catalog
   ([Visitor permissions](../../creators/website-creators/visitor-permissions.md)).

## The Data publisher role

In **Users > Roles and policies** (`/role/list`) create a role "Data publisher" with:

| Module / function | Limitation |
|---|---|
| `content/read` | Section: Open data, Standard |
| `content/create` | Class: dataset, distribution, file; Section: Open data |
| `content/edit` | Section: Open data |
| `content/versionread` | Section: Open data |

Assign it to a user group "Data team". Do not give `content/remove`: datasets are withdrawn, not removed (see
"Versioning and updating datasets"). Add `content/translate` with a Language limitation if translators work on
dataset descriptions.

## An approval step

To have every new or changed dataset approved before it is public:

1. **Setup > Workflows**: open a workflow group, press **New workflow**, name it "Data approval", choose
   **Event / Approve** and press **Add event**. In the event set **Affected sections** to Open data and **Groups who
   approve content** to "Data stewards", then **OK**.
2. **Setup > Triggers**: in the row **Before publishing content** choose "Data approval" and press **Apply changes**.
   One workflow per trigger: if another workflow already uses it, combine them with a **Multiplexer** event.
3. Make sure the `frequent` cronjob group runs: it contains the workflow cronjob, and without it approved content is
   never published ([Workflows](../../../guides/workflows.md)).

A publisher's version then waits as **pending**; the steward finds it under **Collaboration** on the dashboard and
presses **Approve** or **Deny**.

## Recording who did what

- The version history shows who created each version ([Content history](../../../guides/content-history.md)).
- The audit trail records publish, remove, section and state changes in the `content` channel, and role
  assignments in the `access` channel; it is on by default ([Audit trail](../../../features/6.0/audit-trail.md)).

## Checklist

- [ ] One section for the catalog, readable by Anonymous.
- [ ] Data publishers can create only dataset classes, only in that section.
- [ ] No one but administrators can remove datasets.
- [ ] Approval workflow on, cronjob running, stewards named.
- [ ] Roles reviewed once a year; leavers removed from the Data team group.

## Related pages

- [Roles and policies](../../../guides/roles-and-policies.md)
- [Sections and object states for site builders](../../creators/website-creators/sections-and-object-states.md)
- [Drafts and pending items](../../../guides/drafts-and-pending.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
