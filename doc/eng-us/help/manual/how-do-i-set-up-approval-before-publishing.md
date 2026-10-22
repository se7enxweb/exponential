---
title: How do I set up approval before publishing?
audience: admin
category: manual
order: 630
summary: Create a workflow with the Approve event, choose the sections, versions, approvers and excluded groups, connect it to Before publishing content under Triggers, give approvers the collaboration module and check that the workflow cronjob runs.
keywords: approval, approve event, ezapprove, four-eyes principle, workflow, trigger, before publishing content, approvers, collaboration, workflow cronjob, pending
context: workflow/edit
type: help
---
# How do I set up approval before publishing?

With an approval workflow, a version that an editor sends for publishing waits as pending until an approver accepts
it. Approvers find the request under **Dashboard** > **Collaboration**. A new installation has no such workflow.

## Who can do this

An administrator, or a role with `workflow/*` and `trigger/*`, plus `role/*` to give the approvers their policy.

## Steps

1. Click **Setup** in the top menu and choose **Workflows**.
2. Open a workflow group, for example "Standard", and click **New workflow**. Name it, for example "Approval".
3. Choose **Event / Approve** in the event list and click **Add event**.
4. In **Affected sections**, choose the sections whose content needs approval, or **All sections**.
5. In **Affected versions**, choose **All versions**, **Publishing new object** or **Updating existing object**.
6. Add the approvers with **Add users** under **Users who approve content**, or with **Add groups** under **Groups who
   approve content**.
7. Optionally add groups whose members publish without approval under **Excluded user groups**, then click **OK**.
8. Choose **Triggers** in the Setup menu, choose the workflow in the row **Before publishing content** and click
   **Apply changes**.
9. Give the approvers' role a policy for the module **collaboration** (all functions), so that they can open the
   requests ([How do I give a group edit rights on one section only?](how-do-i-give-a-group-edit-rights-on-one-section.md)
   shows how to add a policy).

Then test it: log in as an editor who is not an approver, publish an article in an affected section, and check that
it appears in **My pending items** and, for the approver, in **Collaboration**.

## What can go wrong

- **Content is published without approval.** The editor is an approver, or in an excluded group, or the content is
  outside the affected sections, languages or versions. Content created by an approver never waits for their own
  approval.
- **Approved content does not go live.** After **Approve**, the workflow cronjob (`workflow.php`, frequent part)
  finishes the publishing. If it does not run, the version stays pending. **Setup** > **Workflow processes** says
  whether it is scheduled.
- **The approver does not see Collaboration.** Their role lacks the `collaboration` module.
- **Approvers get no e-mail.** Each approver chooses the approval notification under **Dashboard** > **My
  notification settings**, and the notification cronjob must run
  ([How do I choose which notifications I get?](how-do-i-choose-which-notifications-i-get.md)).
- **You also want publishing on a date.** A trigger runs one workflow. Put **Approve** and **Wait until date** in the
  same workflow ([How do I publish content at a later time?](how-do-i-publish-content-at-a-later-time.md)).

## Related pages

- [Workflows](../../guides/workflows.md): section 3 is a complete worked example, with sample data to try it
- [How do I send content for approval?](how-do-i-send-content-for-approval.md)
- [How do I approve or reject content?](how-do-i-approve-or-reject-content.md)
- [Where do I see my items that wait for approval?](where-do-i-see-my-pending-items.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
