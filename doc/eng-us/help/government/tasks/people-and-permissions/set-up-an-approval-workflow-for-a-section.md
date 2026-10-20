---
title: Set up an approval workflow for a section
audience: government, admin
category: public-sector
order: 130
summary: Step by step - create a workflow with an Approve event for one section, connect it to Before publishing content, make sure the workflow cronjob runs, and test it with a non-approver.
keywords: approval workflow, set up approval, Approve event, section, trigger, Before publishing content, workflow cronjob, workflow.php, Setup Workflows, Setup Triggers
type: help
---
# Set up an approval workflow for a section

This task is for administrators. At the end, every version published in one section waits until a member of an
approvers' group approves it. It follows the worked example of
[Workflows, section 3](../../../../guides/workflows.md#3-worked-example-approval-before-publishing).

## Contents
1. [Before you start](#before-you-start)
2. [Create the workflow](#create-the-workflow)
3. [Connect the trigger](#connect-the-trigger)
4. [Make sure the cronjob runs](#make-sure-the-cronjob-runs)
5. [Test it](#test-it)
6. [Related pages](#related-pages)

## Before you start

- An account with the `workflow` and `trigger` modules and `setup/managecronjobs` (administrators have them).
- The approvers' user group with the approver role ([Give an approver rights](give-an-approver-rights.md)).
- The section exists and holds the content.
- To practice first: `./console exp:collaborationsampledata --dry-run --allow-root-user` shows what the sample
  approvals would create; `--remove` takes them away again.

## Create the workflow

1. **Setup > Workflows** (`/workflow/grouplist`). Open a workflow group or create one with **New workflow group**.
2. **New workflow**, name it, for example "Approval - <section name>".
3. Choose **Event / Approve** in the list and press **Add event**.
4. Fill in the event:
   - **Affected sections**: the section.
   - **Affected languages**: *All languages*.
   - **Affected versions**: *All versions*.
   - **Groups who approve content**: the approvers' group.
   - **Excluded user groups**: leave empty unless you have a documented bypass group.
5. Press **OK**.

## Connect the trigger

1. **Setup > Triggers** (`/trigger/list`).
2. In the row **Before publishing content**, choose the workflow and press **Apply changes**.
3. If another workflow is already set there, do not replace it blindly: combine both with a Multiplexer event.

## Make sure the cronjob runs

Open **Setup > Workflow processes**. The status bar must say the workflow cronjob (`workflow.php`) is scheduled, for
example in the *Frequent* part every 5 minutes. If it says **not scheduled in the crontab**, install the crontab line
**Setup > Cronjobs** proposes ([Check that cronjobs run](../operations/check-that-cronjobs-run.md)).

## Test it

1. As an editor who is not an approver, publish a test page in the section: it does not go live; **My pending
   items** shows it waiting.
2. **Setup > Workflow processes** lists one process under **Before publishing content**.
3. As an approver, **My account > Collaboration**, open the request, **Approve**.
4. Run the *Frequent* part now (**Setup > Cronjobs**, **Run part**) or wait: the page is published and the process is
   gone.
5. Remove the test page.

Change record: workflow name, event settings, trigger, test result and date.

## Related pages
- [Setting up the Approve event](../../operations/editorial-workflows/setting-up-the-approve-event.md)
- [Approval workflows overview](../../operations/editorial-workflows/approval-workflows-overview.md)
- [Workflow troubleshooting](../../operations/editorial-workflows/workflow-troubleshooting.md)
- [Workflows guide](../../../../guides/workflows.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
