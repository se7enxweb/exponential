---
title: How do I publish content at a later time?
audience: admin, editor
category: manual
order: 625
summary: Give the content class a date and time field, create a workflow with the Wait until date event pointing to that field, connect it to Before publishing content and let the workflow cronjob run; editors then set the date and send for publishing as usual.
keywords: scheduled publishing, publish later, delayed publishing, wait until date, ezwaituntildate, date and time, pending, workflow, trigger, cronjob, unpublish date, hide date
context: workflow/edit
type: help
---
# How do I publish content at a later time?

Exponential can hold a version back until a date and time stored in the object itself. The version is sent for
publishing as usual, waits with the status pending, and the workflow cronjob publishes it once the time has come.
This works for new objects and for new versions of published ones: the published version stays on the site until
the new one replaces it. Nothing of this is set up in a new installation.

## Who can do this

- Setting it up: an administrator, or a role with `class/*`, `workflow/*` and `trigger/*`.
- Using it: every editor who may edit the class.

## Steps

1. Under **Setup** > **Classes**, edit the class, for example Article, and add an attribute of the type **Date and
   time** with the identifier `publish_date` ([How do I add an attribute to a content class?](how-do-i-add-an-attribute-to-a-content-class.md)).
   Leave **Required** off, so that content without a date is published at once.
2. Under **Setup** > **Workflows**, open a workflow group and click **New workflow**. Name it, for example
   "Publish on date".
3. Choose **Event / Wait until date** in the event list and click **Add event**.
4. In the event, choose the class in **Class**, the date attribute in **Attribute**, and click **Select attribute**.
   The pair is listed under **Class/attribute combinations**. Repeat for every class that has such a field.
5. Tick **Modify the objects' publishing dates** if the published date of the object should be the planned date, not
   the date the editor sent it. Click **OK**.
6. Under **Setup** > **Triggers**, choose the workflow in the row **Before publishing content** and click
   **Apply changes**.
7. Under **Setup** > **Workflow processes**, read the status bar: it must say that the workflow cronjob is scheduled.
8. Test it: edit an article, set the date a few minutes ahead and click **Send for publishing**.
   The version is listed in **My pending items** and is published at the next run of the workflow cronjob after
   that time.

## What can go wrong

- **The content is published at once.** The date is empty or in the past, the class/attribute pair is missing from
  the event, or the trigger is not set.
- **The time has passed but nothing is published.** The workflow cronjob (`workflow.php`, in the *frequent* part of
  the cronjobs) is not running. Publication happens at its next run, so a five-minute schedule means up to five
  minutes of delay.
- **The editor wants to change a waiting version.** A pending version cannot be edited. Open the version history,
  make a new draft from it, change and send that; the old pending version is replaced when the new one is published
  ([How do I go back to an earlier version?](how-do-i-go-back-to-an-earlier-version.md)).
- **Approval is needed as well.** A trigger takes one workflow. Put both events, **Approve** and **Wait until date**,
  in that one workflow, or use a **Multiplexer** event that starts several workflows.
- **Two versions wait at the same time.** Each one is published when its own time comes, so the older version can
  replace a newer one. Keep one waiting version per object.

**Note:** Taking content off the site at a time works differently, without a workflow. Two cronjobs in the default
part do it: `unpublish.php` moves objects to the trash when their attribute `unpublish_date` has passed, for the
classes and subtrees in `content.ini [UnpublishSettings]`; `hide.php` hides nodes whose date attribute has passed,
as listed in `content.ini [HideSettings] RootNodeList[]` and `HideDateAttributeList[<class identifier>]=<attribute>`.
Front page blocks that change on a timetable belong to the optional ezflow extension.

## Related pages

- [Workflows](../../guides/workflows.md): events, triggers, processes and the cronjob in detail
- [Where do I see my items that wait for approval?](where-do-i-see-my-pending-items.md)
- [How do I set up approval before publishing?](how-do-i-set-up-approval-before-publishing.md)
- [Cronjobs](../../guides/cronjobs.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
