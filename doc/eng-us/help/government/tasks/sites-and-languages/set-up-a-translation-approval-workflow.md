---
title: Set up a translation approval workflow
audience: government, admin
category: public-sector
order: 380
summary: Task for administrators: have translations into one language approved by that language's reviewers before they are published, with an Approve event limited to Affected languages, connected to Before publishing content.
keywords: translation approval, Approve event, Affected languages, workflow, trigger, reviewers, Before publishing content, multiplexer
type: help
---
# Set up a translation approval workflow

This task is for administrators. Background:
[Translation workflow and approval](../../languages/translation-workflow-and-approval.md) and
[Workflows](../../../../guides/workflows.md).

## Before you start

- A user group of reviewers for the language.
- Whether another workflow already uses the trigger **Before publishing content**.

## Steps

1. **Setup > Workflows**: open a group, **New workflow** "French translation approval".
2. Choose **Event / Approve**, **Add event**; set **Affected languages** to French, **Affected sections** as needed,
   **Groups who approve content** to the French reviewers; **OK**.
3. **Setup > Triggers**: row **Before publishing content**, choose the workflow, **Apply changes**. If a workflow is
   already there, combine both in one workflow with a **Multiplexer** event and connect that one.
4. Check that the `frequent` cronjob group runs (**Setup > Workflow processes** shows it in its status bar).

## Check

A translator's French version waits as pending; a French reviewer finds it in **Collaboration**, presses **Approve**;
after the next workflow cronjob run it is published. English versions publish without waiting.

## Problems

| Problem | Fix |
|---|---|
| Approved versions are not published | the workflow cronjob does not run |
| Nothing waits | the version's language is not in **Affected languages**, or the translator is an approver |

## Related pages

- [Approval before publishing](../../transparency-and-foi/approval-before-publishing.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
