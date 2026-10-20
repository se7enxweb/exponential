---
title: Setting up the Approve event
audience: government, admin
category: public-sector
order: 30
summary: Every field of the Approve event (affected sections, languages and versions, approving users and groups, excluded groups) with the choice a public body usually makes for each, and the trigger and cronjob that make it work.
keywords: Approve event, approval, workflow event, affected sections, affected languages, affected versions, approvers, groups who approve content, excluded user groups, trigger, Before publishing content, workflow.php
type: help
---
# Setting up the Approve event

This page is for administrators who build the approval workflow. It goes through the fields of the **Approve** event
as [Workflows, section 3](../../../../guides/workflows.md#3-worked-example-approval-before-publishing) describes them
and gives the usual public-sector choice for each.

## Contents
1. [Create the workflow](#create-the-workflow)
2. [The fields](#the-fields)
3. [Connect the trigger](#connect-the-trigger)
4. [Check the cronjob](#check-the-cronjob)
5. [Try it and record it](#try-it-and-record-it)
6. [Related pages](#related-pages)

## Create the workflow

1. **Setup > Workflows**. Open a workflow group (for example *Standard*) or create one with **New workflow group**.
2. **New workflow**; name it for its purpose, for example "Approval before publishing - public site".
3. In the event list choose **Event / Approve** and press **Add event**.

## The fields

| Field | What it does | Usual choice |
|---|---|---|
| Affected sections | only content in these sections needs approval; content whose own section is not listed also counts when the parent of one of its locations is in a listed section | the sections visitors read (Standard, and any public section of your own); not Users or Media unless media files are published content |
| Affected languages | only versions whose initial language is listed | *All languages*, unless one language team has its own workflow ([Multilingual approvals](multilingual-approvals.md)) |
| Affected versions | *All versions*, only *Publishing new object*, or only *Updating existing object* | *All versions*: a change to a published page is also a publication |
| Users who approve content | individual approvers | leave empty; use groups |
| Groups who approve content | every user in these groups is an approver | one approvers' group per area |
| Excluded user groups | users in these groups publish without approval | the smallest group possible, documented ([Emergency publishing bypass](emergency-publishing-bypass.md)) |

Press **OK** to store the workflow. The event lets through content outside its fields at once, and lets through
content published by one of its approvers
([Workflows, section 3.2](../../../../guides/workflows.md#32-set-up-the-approve-event)).

## Connect the trigger

**Setup > Triggers** lists the operations that can start a workflow. In the row **Before publishing content**
(`content/publish`, before), choose your workflow and press **Apply changes**. *Before* holds publishing back until
the workflow is done; *after* would run it once the content is already public, which is useless for approval.

Only one workflow per trigger: if another workflow already runs before publishing (for example a notification
workflow from an extension), build a workflow with a **Multiplexer** event that runs both.

## Check the cronjob

**Setup > Workflow processes** shows in its status bar whether `workflow.php` is scheduled, for example "Resumed by
the workflow cronjob (workflow.php) in the Frequent part: Every 5 minutes". If it says **not scheduled in the
crontab**, install the line **Setup > Cronjobs** suggests ([Check that cronjobs run](../../tasks/operations/check-that-cronjobs-run.md)).

## Try it and record it

1. As an editor who is not an approver, publish an article in an affected section: it waits.
2. **Setup > Workflow processes** shows one process under **Before publishing content**.
3. As an approver, open **My account > Collaboration**, open the request, **Approve**.
4. Run the *Frequent* part from **Setup > Cronjobs**, or wait: the article is published.

For your change record, note the workflow's name, the trigger, every field value and the date; the trigger list and
the workflow's page show them. Changes to workflows are configuration changes: route them through your change process
([Change management and advisory boards](../upgrades-and-change/change-management-and-advisory-boards.md)).

## Related pages
- [Approval workflows overview](approval-workflows-overview.md)
- [Role design: approvers](../people-and-permissions/role-design-approvers.md)
- [Workflow troubleshooting](workflow-troubleshooting.md)
- [Set up an approval workflow for a section](../../tasks/people-and-permissions/set-up-an-approval-workflow-for-a-section.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
