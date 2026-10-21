---
title: "Approving guestbook entries with a workflow"
audience: builder, admin
category: books
order: 160
summary: Stopping spam and offensive text in the guestbook: an Approve workflow limited to the Guestbook section, connected to the publish trigger, the approver's inbox, and the cronjob that carries the approval out.
keywords: workflow, approve, trigger, content publish before, collaboration, approval, cronjob, runcronjobs, guestbook, section, Building book
type: help
---
# Approving guestbook entries with a workflow

The guestbook as built so far lets anyone publish anything on the club's site. Sometimes that is acceptable; for most public
sites it is not. The built-in workflow system can hold each entry until an administrator approves it. The book calls the
workflow system huge and uses a small piece of it here; so will this page. The long version is
[Workflows: approvals, triggers and the processes that wait](../../guides/workflows.md), and the ideas are in
[workflows and triggers](../book-learning/workflows-and-triggers.md).

## Tell the visitors

Change the entry page of the guestbook to say what will happen: "Your entry will not be visible right away. All content on
this site is approved by the administrator; look back in a few hours or days." Visitors who are not told think the site lost
their text.

## Create the workflow

1. **Setup > Workflows**, open the group **Standard**, **New workflow**.
2. Name it **Guestbook entry approval**.
3. Choose the event **Event / Approve** and press **Add event**.
4. In the event: the **Affected sections** is the **Guestbook** section (so no other content is held), the approver is the
   administrator user (or a group of club officers), and **Excluded user groups** holds the administrators, so that their
   own entries go through at once. The book's fields were "Editor", "Section" and "Users without approval"; they are today's
   *Users who approve content*, *Affected sections* and *Excluded user groups*.
5. Store the workflow.

## Connect it to a trigger

A workflow does nothing until a trigger starts it. **Setup > Triggers**, the row **Before publishing content**
(`content/publish`, before): choose *Guestbook entry approval* and press **Apply changes**. *Before* holds the publication
until the workflow is done.

## Try it

Add an entry as a visitor (a private window). It does not appear in the list: the object waits, unpublished, for approval.
**Setup > Workflow processes** lists it. The administrator opens **My account > Collaboration**, finds "a new guestbook entry
awaits your approval" (the book's "Personal > Collaboration"), looks at the preview and presses **Approve** (or **Deny**).

## The cronjob

After **Approve** the entry still does not appear. Workflows and notifications are carried out by the cronjob, not by a
web request: `runcronjobs.php` does it, and must run regularly ([Installing, then and now](../book-learning/installing-then-and-now.md),
[Cronjobs](../../guides/cronjobs.md)). The book runs it by hand with `php runcronjobs.php`. In Exponential 6 the workflow
runs in the **Frequent** part, so the command is

```bash
php runcronjobs.php -q -s tscm frequent
```

(or press the run button of the part on the **Cronjobs** page). Then the entry is published. If the crontab does not
have the line, the approved entries wait for ever; the **Workflow processes** page says when it is not scheduled.

Next: [the links page](the-links-page.md).

## Source and license

Adapted from the guestbook and approval chapters of of the tutorial *Building an eZ publish site* (written for eZ publish 3.2, 2003), originally by
ez.no and released again under the GNU Free Documentation License by 7x in 2024,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x and Graham Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later
version, with no Invariant Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rewritten for Exponential 6: current workflow screens, the cronjob command, form tokens, and a spam warning.
