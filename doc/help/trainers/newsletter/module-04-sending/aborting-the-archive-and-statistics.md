---
title: "Lesson 4.4: Aborting a send, the archive and statistics"
audience: trainer, editor, admin
category: training
order: 44
summary: Learners abort a scheduled send before the queue runs, find what was sent in the archive, and read a send's statistics, knowing that tracking is off unless switched on and needs consent per person. 45 minutes.
keywords: newsletters with cjw newsletter, abort send, send_abort, archive, statistics, tracking, consent, opens, clicks, A/B subject test, report
type: help
---
# Lesson 4.4: Aborting a send, the archive and statistics

Mistakes happen: the wrong edition, the wrong list, a typo in the subject. If the queue has not processed a send yet,
it can be aborted. After a send, the archive shows exactly what went out, and the statistics show what happened to
it, within the limits that privacy sets. This lesson covers all three.

> **Note**
> This lesson schedules a send in order to abort it. All transports write files and all subscribers are at
> `example.invalid` (lesson 1.2). Tracking stays off in class.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 4: Sending](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Editors and administrators |
| Prerequisites | [Lesson 4.2](scheduling-and-sending-an-edition.md): at least one finished send |

## Learning objectives

After this lesson, learners can:

- abort a scheduled send with **Abort send out process** before the queue processes it;
- explain why an abort cannot call back mails that were already sent;
- find a past send in the archive and on the dashboard's **Last sends**;
- explain the three tracking modes and why per-person counting needs consent;
- read a send's report page.

## Trainer's notes

### How to present it

Sources: [When something goes wrong](../../../creators/website-creators/sending-newsletters.md#when-something-goes-wrong),
the statistics guide of the extension (linked from the [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420))
and [E-mail preferences](../../../../features/6.0/mail-preferences.md).

1. **Aborting** (5 minutes). On an edition with a send that is not finished, **Newsletter processing info** offers
   the abort. The page `newsletter/send_abort` shows the send's statistics and asks whether you really want to abort
   the send out process. Mails already sent stay sent; the rest are not sent.
2. **The archive** (4 minutes). Every send is kept with the version and skin it was rendered with. `newsletter/archive`
   shows a sent edition; the edition's page shows **Statistics** (**Emails count**, **Emails sent**,
   **Emails opened**, **Emails not sent**, **Emails bounced**).
3. **Tracking and consent** (7 minutes). Three modes: off (the default), anonymous totals, and per person with
   consent. Nothing is counted per person without the person's consent in the e-mail preference category
   "Newsletter statistics", off by default. The site switch `[TrackingSettings] Tracking` is disabled as shipped:
   then nothing is counted at all. Test sends and SMS sends are never tracked.
4. **The report** (4 minutes). **All statistics** on the dashboard leads to the reports. A report shows opens and
   clicks over time, clicks per link, an A/B subject test if there was one, and CSV exports.

### Common questions

**Can I abort a send that is half done?** Yes; the remaining mails are not sent. The ones already sent cannot be
called back.

**Why are there no opens on our training sends?** Tracking is off, and on the training installation nobody opens the
files in a mail program connected to the site.

**What is an A/B subject test?** A send with two subjects, each to a sample of the list; after a waiting time the
subject with the better open or click rate goes to the rest. It needs tracking.

**How long are per-person rows kept?** The dashboard of a 4.2 installation states the retention (for example twelve
months, then only totals). Check yours.

### Pitfalls

- **Pressing Send now before aborting.** Agree that nobody starts the queue during this exercise.
- **Expecting statistics from files.** File transports produce no opens or clicks; show a report from a prepared
  installation if you want to show numbers.
- **Promising tracking to editors.** Tracking is a data protection decision of the organization, not of the editor.

## Hands-on exercise

Use the training installation. Nobody presses **Send now** during steps 1 to 6. Replace `<name>` with your first name.

1. Copy your edition `Tips for week 1` (**Copy**) and rename the copy `Tips for week 3`.
2. Press **Send Newsletter** on the copy, choose a send time ten minutes from now, tracking **Off**, and press
   **Send Newsletter**; confirm.
3. On the edition's page, read **Newsletter processing info**: the send waits.
4. Use the abort offered there. On the page, read the statistics and the question, then press
   **Abort send out process**.
5. Reload the edition's page and read the **Cronjob status**.
6. Check the outbox: no new files for `Tips for week 3`.

   ```bash
   ls -lt var/log/mail | head -5
   ```

7. On the dashboard, find your sends under **Last sends** and compare their **Status**.
8. Open the edition `Tips for week 2` from lesson 4.2 and read its **Statistics**.
9. On the dashboard, press **All statistics** and open the report of a send (the trainer's prepared send, if yours has
   no tracking). Read **Opens and clicks over time** and **Clicks per link**.
10. Discuss in pairs: would your organization switch tracking on, and in which mode?

## Expected result

- The send of `Tips for week 3` is aborted; no mail file was written for it.
- **Last sends** shows one finished send (`Tips for week 2`) and one aborted send.
- Learners can explain the three tracking modes and why consent is needed for per-person counting.
- The trainer checks the dashboard's **Last sends** on the projector.

## Quiz

1. When can a send still be aborted completely?
2. What does an abort do to mails that were already sent?
   A. calls them back B. nothing; they stay sent C. marks them bounced D. deletes them from the archive
3. What is the default tracking mode of a list?
4. What must a person have done before their opens are counted per person?
5. Which sends are never tracked?

<details>
<summary>Answers</summary>

1. Before the queue processes it ([When something goes wrong](../../../creators/website-creators/sending-newsletters.md#when-something-goes-wrong)).
2. **B.** A newsletter cannot be called back ([Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md)).
3. Off (statistics guide, linked from the [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
4. Switched on the e-mail preference category "Newsletter statistics" (statistics guide; [E-mail preferences](../../../../features/6.0/mail-preferences.md)).
5. Test sends and SMS sends (statistics guide).

</details>

## Further reading

- [Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md#when-something-goes-wrong):
  what to do when something goes wrong.
- [E-mail preferences](../../../../features/6.0/mail-preferences.md): categories and consent.
- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420):
  the statistics guide.

## Related pages

- [Module 4: Sending](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
