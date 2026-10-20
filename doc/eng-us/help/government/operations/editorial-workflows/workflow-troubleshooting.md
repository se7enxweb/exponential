---
title: Workflow troubleshooting
audience: government, admin, editor
category: public-sector
order: 150
summary: What to do when approved content is not published, an approval waits for an approver who left, "Send for publishing" seems to do nothing, or processes fail - with the Setup > Workflow processes page, the cronjob commands and a safe way to cancel.
keywords: workflow troubleshooting, approval stuck, not published, pending, workflow processes, cancel process, workflow cronjob, workflow.php, failed process, approver left, Send for publishing
type: help
---
# Workflow troubleshooting

This page is for administrators and web managers when the approval process does not move. It condenses the
troubleshooting of [Workflows, section 9](../../../../guides/workflows.md#9-troubleshooting) into the cases public
bodies meet most, with the public-sector angle: tell the author and the requester what happened, and keep the record.

## Contents
1. [First look](#first-look)
2. [Approved but not published](#approved-but-not-published)
3. [Waiting for an approver who is gone](#waiting-for-an-approver-who-is-gone)
4. [Send for publishing did nothing](#send-for-publishing-did-nothing)
5. [Failed and stuck processes](#failed-and-stuck-processes)
6. [Cancelling safely](#cancelling-safely)
7. [Related pages](#related-pages)

## First look

Open **Setup > Workflow processes** (`/workflow/processlist`) and type the content's name in **Find a process**. Each
card says in one grey line what the process waits for, with **Open the approval request** for approvals. The status
bar says whether the workflow cronjob is scheduled. The overview counts waiting, failed and stopped processes and
shows the oldest waiting one. If the list is empty, look under **Stopped** and **All**.

## Approved but not published

Cause: the workflow cronjob (`workflow.php`, frequent part) did not run since the decision.

1. Status bar says **not scheduled in the crontab**: install the line from **Setup > Cronjobs**
   ([Check that cronjobs run](../../tasks/operations/check-that-cronjobs-run.md)).
2. Run it now: **Setup > Cronjobs**, the *Frequent* card, **Run part**; or on the shell:

   ```bash
   php runcronjobs.php -s <siteaccess> --script=workflow.php
   ```

3. Reload the process list: the process is gone and the content is published.

## Waiting for an approver who is gone

A request already made keeps its participants. If nobody in it can decide any more:

1. Fix the approvers first: add people to the approvers' group, or name a group in the Approve event.
2. Cancel the waiting processes (below); their versions become drafts again.
3. Tell the authors to send them for publishing again; the new requests go to the current approvers.

## Send for publishing did nothing

A failed or cancelled process with the same key (same object, version, workflow and user) is removed by the first
attempt, which stops the operation once; the second attempt works. Cancel failed processes from the list first to
avoid it ([Workflows, section 4.4](../../../../guides/workflows.md#44-publishing-the-same-version-again)).

## Failed and stuck processes

| Card says | Do |
|---|---|
| Failed | read the technical details for the event's answer; cancel the process |
| Marked as running, for hours | the run was cut off; cancel; look in `var/log/error.log` for the run that died |
| Trigger not recorded | cannot be resumed; cancel, then publish the content again |
| Step "None" | the workflow was deleted or never reached; cancel |
| Waiting for its parent workflow | a Multiplexer child; cancel the children together with the parent |

## Cancelling safely

**Cancel...** on a card, or tick several and **Cancel selected processes...**; a confirmation says what will happen.
Confirmed: a pending version becomes a draft again, the approval request leaves the inbox, the process is removed.
Nothing is published and no content is deleted; the way back is publishing the draft again. Cancelling does not
undo what earlier events did (a mail already sent) ([Workflows, section 7](../../../../guides/workflows.md#7-cancelling-and-cleaning-up-processes-safely)).

There is no console command to list or cancel processes; the page is the tool. For a read-only look at the table,
the guide gives a SQL query against `ezworkflow_process`.

## Related pages
- [Approval workflows overview](approval-workflows-overview.md)
- [Cronjob monitoring](../monitoring/cronjob-monitoring.md)
- [Collaboration inbox](collaboration-inbox.md)
- [Workflows guide](../../../../guides/workflows.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
