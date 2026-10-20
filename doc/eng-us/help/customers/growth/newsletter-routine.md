---
title: Newsletter routine
audience: customer, editor, businesses
category: customer-success
order: 3
summary: The routine of every newsletter issue and the monthly look at the lists on an Exponential site with cjw_newsletter - write, preview, test group, approval, scheduled send, archive - with who does each step and a checklist to copy.
keywords: newsletter routine, edition, test group, preview, approval, send, recurring send, bounces, suppression list, monthly review, cjw_newsletter
type: help
---
# Newsletter routine

This page is for the writers, approvers and senders of a newsletter, and for the owner who wants it to run reliably.
It turns [Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md) into a routine
you can copy. The setup and decisions are on [Newsletters](newsletters.md).

## Contents

1. [Every issue](#every-issue)
2. [The day of the send](#the-day-of-the-send)
3. [When something should not go out](#when-something-should-not-go-out)
4. [Every month](#every-month)
5. [Checklist to copy](#checklist-to-copy)

## Every issue

From [Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md):

| Step | Who | Where |
|---|---|---|
| 1. Create the edition with its articles | writer | content tree, below the list |
| 2. Choose the skin | writer | send form of the edition |
| 3. Preview as a subscriber and send to a test group | writer | `newsletter/preview_as/...`, test send form |
| 4. Ask for approval (if the list requires it) | writer | `newsletter/approval/<edition>/<version>` |
| 5. Approve or reject | approver | collaboration inbox or the approval page |
| 6. Schedule the send | sender | **Send** on the edition |
| 7. Watch the send and check the archive | sender | dashboard `/newsletter/index`, `newsletter/archive` |

Notes that save trouble:

- **Preview as a subscriber** shows the subject, language, interests, HTML and text part as one subscriber would get
  them; nothing is sent or stored.
- **Test groups** hold at most 25 addresses by default; test mails start with `[Test]` and are never counted in the
  statistics.
- Approval is per **version**: a change after approval needs approval again. What the approver saw is what goes out.
- **Article pools** can offer the newest articles of a folder; only content anonymous visitors may read is offered,
  because a newsletter is public.

## The day of the send

- [ ] Subject, preheader and sender name are right.
- [ ] Every link works and points to the public site.
- [ ] Images have alternative texts; the mail reads well without images.
- [ ] The test group received it and someone read it on a phone.
- [ ] The edition is approved (if required) and scheduled.
- [ ] Someone is at work during and after the send, not on the way home
      ([Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md)).

The first three come from the website creator's checklist
([Sending newsletters safely](../../creators/website-creators/sending-newsletters.md)).

## When something should not go out

Stop first, explain later. The page [Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md)
describes how to abort a send that should not go out. Then:

1. Tell the owner and the list owner what went out to whom, as far as the archive shows.
2. Decide whether a correction mail is needed; do not send it in a hurry.
3. Write one line in the routine about what to check next time.

## Every month

| Look at | Why | Read |
|---|---|---|
| The newsletter dashboard's list of problems | transport, cron and configuration issues | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |
| Bounces, blacklist and suppression list | a clean list keeps delivery healthy | [Bounces and list hygiene](../../businesses/newsletters/bounces-and-list-hygiene.md) |
| Subscriptions and unsubscriptions | is the list growing, and why do people leave? | [Lists and subscriptions](../../businesses/newsletters/lists-and-subscriptions.md) |
| Statistics, where subscribers consented | which articles were opened | [Opt-in and consent](../../businesses/newsletters/opt-in-and-consent.md) |
| Recurring sends | ran as expected; runs missed while cron was down are not made up | [Writing and sending editions](../../businesses/newsletters/writing-and-sending-editions.md) |

## Checklist to copy

```text
EDITION: ______________________   List: ______________   Send date: __________
Writer: ______________   Approver: ______________   Sender: ______________
[ ] articles placed          [ ] skin chosen            [ ] previewed as a subscriber
[ ] test group received it   [ ] read on a phone        [ ] links checked
[ ] alternative texts        [ ] approved (version __)  [ ] scheduled for ______
[ ] send watched             [ ] archive checked        Notes: ____________________
```

## Related pages

- [Newsletters](newsletters.md)
- [Newsletter checklist](../../businesses/newsletters/newsletter-checklist.md)
- [Content calendar](../day-to-day/content-calendar.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
