---
title: Press releases
audience: government, website-creator, project-manager, admin
category: public-sector
order: 70
summary: How a press office publishes press releases on an Exponential site - a Press release class, approval and its exceptions, an embargo with a Wait until date workflow event, press contacts as roles, a media feed, and corrections.
keywords: press release, press office, media, embargo, Wait until date, workflow, approval, press contact, RSS for media, corrections, newsroom
type: help
---
# Press releases

This page is for website creators, project managers and administrators who set up publishing for a press office.
Press releases need speed, sometimes an embargo, and careful corrections. The features named are documented in
[Workflows](../../../../guides/workflows.md), [RSS feeds](../../../../guides/rss-feeds.md) and
[Versions of an object](../../../../guides/content-history.md).

## Contents

1. [A Press release class](#a-press-release-class)
2. [Approval](#approval)
3. [Embargo](#embargo)
4. [The newsroom](#the-newsroom)
5. [Corrections](#corrections)

## A Press release class

| Attribute | Identifier | Datatype | Why |
|---|---|---|---|
| Title | `title` | Text line | |
| Release date and time | `release_date` | Date and time | the embargo time and the date shown |
| Lead | `intro` | XML block | the first paragraph |
| Body | `body` | XML block | |
| Notes to editors | `notes` | XML block | background for journalists |
| Press contact | `press_contact` | XML block | the press office's line and mailbox, not a private person's |
| Attachments | `attachments` | Object relations | photos (with alternative text and credits) and documents |
| Topics | `tags` | Tags | |

The shipped Article class works too if you do not need the embargo date and notes ([News articles](news-articles.md)).

## Approval

| Rule | Setting |
|---|---|
| Releases approved before publishing | an *Approve* event with **Affected sections** the press section, approvers the head of communications |
| The press office publishes urgent statements at once | the press office group in **Excluded user groups**, if your rules allow |
| Approvals are released by the workflow cronjob | check **Setup > Workflow processes** shows it scheduled |

## Embargo

The shipped *Wait until date* workflow event holds a version until a date attribute of the object is reached; the
workflow cronjob then lets it through:

1. **Setup > Workflows**, a group, **New workflow**; add **Event / Wait until date**.
2. In the event, choose the class Press release and the attribute Release date and time (**Class/attribute
   combinations**); **OK**.
3. Connect it to **Before publishing content** in **Setup > Triggers**. Only one workflow per trigger: if an approval
   workflow is already connected, add both events to one workflow (approve first, then wait), or use a *Multiplexer*.
4. Test: publish a release with a time ten minutes ahead; it stays pending; after that time and the next workflow
   cronjob run (the frequent part), it is published.

Without a date set, or with a past date, the event lets the version through at once. The release appears at the first
cronjob run after the time, not at the exact second; schedule the frequent part often enough. An embargoed text is in
the database before release: limit who may read pending versions (`content/versionread`).

## The newsroom

| Part | How |
|---|---|
| A press folder with the releases, newest first | sort by publication date |
| An RSS export for media | [RSS feeds](rss-feeds.md) |
| Media can follow by e-mail | notifications for account holders ([Notifications and subscriptions](notifications-and-subscriptions.md)) |
| Press images | Image objects with credits and alternative text, in a gallery |
| Contact | the press office contact point, not personal numbers |

## Corrections

1. **Edit** the release, correct, add "Corrected on <date>: <what>" at the end.
2. Publish. The earlier text stays in the version history; **Compare** shows the change.
3. For a correction media must know about, publish normally so followers are notified; for a typo,
   **Publish without notification** where enabled ([Publish without notification](../../../../features/6.0/publish-without-notification.md)).

Never remove a published press release to hide a mistake; correct it openly, unless it must be withdrawn for legal
reasons.

## Related pages

- [News and alerts](README.md)
- [News articles](news-articles.md)
- [Drafts and pending items](../../../../guides/drafts-and-pending.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
