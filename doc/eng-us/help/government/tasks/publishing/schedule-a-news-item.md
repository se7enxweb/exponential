---
title: Schedule a news item
audience: government, editor, admin
category: public-sector
order: 80
summary: For editors and the administrator who sets it up - publish a news item or press release at a set time with Exponential's Wait until date workflow event and the workflow cronjob, what the editor does, how to check it, and the honest limits.
keywords: schedule, scheduled publishing, embargo, publish later, Wait until date, workflow, trigger, workflow cronjob, pending, publish date, release time
type: help
---
# Schedule a news item

This page is for editors who need an item to appear at a set time (an embargoed press release, a news item for a
launch), and for the administrator who sets it up. Exponential schedules publication with the *Wait until date*
workflow event: on publishing, it holds the version until a date attribute of the object is reached; the workflow
cronjob then publishes it ([Workflows](../../../../guides/workflows.md),
`kernel/classes/workflowtypes/event/ezwaituntildate/`).

## Contents

1. [Before you start](#before-you-start)
2. [Steps for the editor](#steps-for-the-editor)
3. [One-time setup for the administrator](#one-time-setup-for-the-administrator)
4. [Check the result](#check-the-result)
5. [Pitfalls](#pitfalls)

## Before you start

- Your class has a Date and time attribute for the release time (for example **Release date and time**), and the
  administrator connected it to a *Wait until date* event (setup below).
- The frequent cronjob part runs often enough (every few minutes is typical).

## Steps for the editor

1. Create the item as usual.
2. Set **Release date and time** to the moment it should appear.
3. **Send for publishing**. The version is not published yet: it is *pending*.
4. Find it in **My pending items** (`/content/pendinglist`): it is held by the workflow.

A pending version cannot be edited. To change the time, ask the
administrator to cancel the waiting process in **Setup > Workflow processes** (**Cancel...** turns the version back into a
draft), then edit it and send it again.

## One-time setup for the administrator

1. **Setup > Workflows**, a workflow group, **New workflow**; name it, choose **Event / Wait until date**, **Add event**.
2. In the event, **Class/attribute combinations**: choose the class and its release date attribute; **Update
   attributes**; **OK**.
3. **Setup > Triggers**: in the row for publishing content, before, choose the workflow; **Apply changes**. One workflow
   per trigger: if an approval workflow is already there, put both events in one workflow, or use a *Multiplexer*.
4. Check **Setup > Workflow processes**: the status bar names the cronjob part that resumes waiting processes and says
   whether the crontab runs it.

## Check the result

- [ ] Right after sending, the item is not visible to signed-out visitors.
- [ ] **Setup > Workflow processes** lists a waiting process for it.
- [ ] After the release time and the next frequent cronjob run, the item is published and visible.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Release attribute empty or in the past | the event lets the version through at once |
| Expecting the exact second | it appears at the first cronjob run after the time |
| The cronjob does not run | the item waits for ever; check the status bar |
| An embargoed text is already in the database | limit who may read pending versions (`content/versionread`) |
| No scheduling set up on your site | the honest alternative: prepare the item as a draft (**Store draft and exit**) and publish it by hand at the time |

## Related pages

- [Publishing tasks](README.md)
- [Publish a press release](publish-a-press-release.md)
- [Press releases: embargo](../../building/news-and-alerts/press-releases.md#embargo)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
