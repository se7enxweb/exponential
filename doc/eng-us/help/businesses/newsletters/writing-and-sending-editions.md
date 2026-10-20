---
title: Writing and sending editions
audience: businesses, editor, sales-marketing
category: business-solutions
order: 50
summary: The routine of one newsletter issue in cjw_newsletter - writing the edition as content, choosing a skin, previewing as a subscriber, test groups, approval, scheduling the send, recurring sends, A/B subject tests and stopping a send that should not go out.
keywords: newsletter edition, write newsletter, test send, test group, approval, send newsletter, recurring send, article pool, skin, A/B subject test, abort send, archive
type: help
---
# Writing and sending editions

This page is for the people who produce each issue: editors, marketing staff, and the manager who signs it off. It
describes the routine of one edition from first draft to archive, in the order you do it, and links to the technical
pages for whoever sets up the parts behind it.

## Contents

1. [The routine at a glance](#the-routine-at-a-glance)
2. [Writing the edition](#writing-the-edition)
3. [Choosing the look](#choosing-the-look)
4. [Testing before anyone else sees it](#testing-before-anyone-else-sees-it)
5. [Approval before sending](#approval-before-sending)
6. [Sending](#sending)
7. [Recurring sends](#recurring-sends)
8. [A/B subject tests](#ab-subject-tests)
9. [When something should not go out](#when-something-should-not-go-out)

## The routine at a glance

| Step | Who | Where |
|---|---|---|
| 1. Create the edition with its articles | Writer | Content tree, below the list |
| 2. Choose the skin | Writer | Send form of the edition |
| 3. Preview as a subscriber and send to a test group | Writer | `newsletter/preview_as/...`, test send form |
| 4. Ask for approval (if the list requires it) | Writer | `newsletter/approval/<edition>/<version>` |
| 5. Approve or reject | Approver | Collaboration inbox or the approval page |
| 6. Schedule the send | Sender | **Send** on the edition |
| 7. Watch the send and check the archive | Sender | Dashboard `/newsletter/index`, `newsletter/archive` |

## Writing the edition

An edition (class `cjw_newsletter_edition`) is created below its list and holds articles (`cjw_newsletter_article`).
"It is content, so it has drafts and preview"
([Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md#writing-and-sending-an-edition)).

Release 4.2.0 adds **article pools** and an article picker: on the send page of an edition, "Pick articles" offers
content from the pool of the list, for example the newest articles of your news folder. Only content the anonymous
user may read is offered for auto-fill, "because a newsletter is public"
([editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md)).

Good practice for writers, from the website creator's checklist
([Sending newsletters safely](../../creators/website-creators/sending-newsletters.md#before-every-send)):

- [ ] Subject, preheader and sender name right.
- [ ] Every link works and points to the public site.
- [ ] Images have alternative texts; the mail reads well without images.

## Choosing the look

The look of a mail is its **skin**. Release 4.2.0 ships four (`default`, `company`, `news`, `shop`); the three new ones
are 600px table layouts with every style inline and no web fonts, "so they read the same in Outlook, Gmail and Apple
Mail, and with images blocked". A list can limit which skins may be used, and a send may choose another allowed skin
([rendering.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/rendering.md)). A designer can write a
skin of your own in your design (`design:newsletter/skin/<name>`,
[cjw_newsletter, Settings you will use](../../../features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).

## Testing before anyone else sees it

- **Preview as a subscriber** (`newsletter/preview_as/...`) shows the subject, language, interests, HTML and text part
  as one chosen subscriber would get them. "Nothing is sent or stored" (rendering.md).
- **Test groups** (Newsletter > Test groups) are named sets of addresses, at most 25 by default
  (`MaxTestGroupSize`). Every test mail has a subject starting with `[Test]` and the header `X-Cjwnl-Test: 1`, and by
  default each address gets its own mail so testers do not see each other's addresses. Test mails are never counted
  in the statistics ([deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md)).

> **Warning**
> Test mails use the preview transport, `TransportMethodPreview`. In the shipped `cjw_newsletter.ini` that one is
> `sendmail`, with the comment "the extension default really sends". During a rehearsal set it to `file` as well, or
> test sends leave the server ([Newsletter mail delivery](newsletter-mail-delivery.md)).

## Approval before sending

Approval is switched on per list ("Approval: yes"). Then ([editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md)):

1. The writer asks for approval on `newsletter/approval/<edition>/<version>`, with a message.
2. The request appears in the **collaboration inbox** of the approvers: the users of `[ApprovalSettings]
   ApproverUserIds[]` and the members of the groups `ApproverGroupIds[]` (by default 12, the Administrator users of a
   standard installation), never the person who asked.
3. A user with the policy `newsletter/approve` approves or rejects it, with a comment. Nobody decides their own request
   unless `AllowSelfApproval=enabled`.
4. The send form refuses a version that is not approved, and the queue holds every send of such a version.

Approval is per **version**: change the edition after approval and it needs approval again. That is the point: what
the manager approved is what goes out. For approval of ordinary web content, see
[Workflows](../../../guides/workflows.md).

## Sending

1. **Send** schedules the edition ([Sending newsletters safely](../../creators/website-creators/sending-newsletters.md#how-a-send-runs)).
2. The cron part `cjw_newsletter_mailqueue_create` builds the queue: one entry per subscriber.
3. `cjw_newsletter_mailqueue_process` sends what waits.
4. The edition is archived (`newsletter/archive`).

Since 4.1.17 the dashboard's **Send now** starts the work in the background and shows its progress, "so a slow mail
server no longer holds the request"
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#dashboard-commands-and-background-runs-4117)).

> **Tip**
> Choose a send time that suits your readers, and avoid sending just before you leave the office: if something is
> wrong you want to be there to stop it.

## Recurring sends

Recurring sends (Newsletter > Recurring sends) run on chosen weekdays, weekly or monthly, at a time of day in a time
zone. In mode **latest** a schedule sends the newest edition of the list that was never sent; in mode **copy** it
copies a template edition and can fill it from the article pool with what was published since the last send. With
**skip if empty** (the default) nothing is sent when there is nothing new. **Try** says what a run would do now and
changes nothing. Runs missed while cron was down are not made up
([editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md)).

If the list requires approval, a scheduled send waits for it like any other.

## A/B subject tests

The send form can test two or more subjects on a sample of the list (default 10 percent per variant), wait (default 4
hours) and send the rest with the winner. A test needs tracking to be switched on
([statistics.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/statistics.md)), so it is only available
if you have decided to use statistics ([Opt-in and consent](opt-in-and-consent.md#statistics-only-with-consent)).

## When something should not go out

| Situation | Do |
|---|---|
| The wrong edition is scheduled | Abort it with `newsletter/send_abort` before cron processes the queue |
| A send is running and must stop | Abort it; mails already handed to the mail server cannot be recalled |
| The whole sending must pause | Pause the transport on Newsletter > Rate limits and batches (applies even when throttling is off) |
| Nothing is sent | Check that cron runs for the right siteaccess and that the transport is not `file` |

Sources: [Sending newsletters safely](../../creators/website-creators/sending-newsletters.md#when-something-goes-wrong)
and [deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md).

## Related pages

- [Newsletters for businesses](README.md)
- [Newsletter mail delivery](newsletter-mail-delivery.md)
- [Bounces and list hygiene](bounces-and-list-hygiene.md)
- [Sending newsletters safely](../../creators/website-creators/sending-newsletters.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
