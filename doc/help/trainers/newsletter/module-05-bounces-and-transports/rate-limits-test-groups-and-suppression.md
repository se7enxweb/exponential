---
title: "Lesson 5.3: Rate limits, test groups and the suppression list"
audience: trainer, admin
category: training
order: 53
summary: Administrators read the rate limit settings and page, create a test group of example.invalid addresses for their list, and explain the suppression import and soft bounce retries. 45 minutes.
keywords: newsletters with cjw newsletter, deliverability, rate limits, batches, throttle, pause, test groups, suppression list, suppression import, soft bounce retry, ThrottleSettings, TestSendSettings
type: help
---
# Lesson 5.3: Rate limits, test groups and the suppression list

Large sends need care: mail providers slow down or refuse a sender who sends too much too fast, reviewers need a
fixed group of test addresses, and addresses known to be bad should never be tried. The deliverability tools of
CJW Newsletter 4.2 cover these three needs. This lesson shows where they are and how to use them safely.

> **Note**
> The test group of this lesson holds only addresses at `example.invalid`, and test sends go to files because
> `TransportMethodPreview=file` on the training installation (lesson 1.2).

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 5: Bounces and transports](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Advanced |
| Audience | Administrators |
| Prerequisites | [Lesson 5.1](mail-accounts-and-bounces.md); [Lesson 4.1](../module-04-sending/previews-and-test-sends.md) |

## Learning objectives

After this lesson, learners can:

- explain batches, per-minute and per-hour limits, pauses and resumed sends;
- read the **Rate limits and batches** page;
- create a test group for a list and use it in a test send;
- explain the suppression import and when soft bounces are retried.

## Trainer's notes

### How to present it

Source: the deliverability guide of the extension (linked from the
[feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)); the policy function
`newsletter/deliverability` opens all its pages.

1. **Rate limits** (6 minutes). With `[ThrottleSettings] Throttle=enabled`, a send goes out in batches of at most
   `BatchSize` mails, and within the limits per minute and per hour of its transport (for example
   `MaxPerMinute[smtp]`, `MaxPerHour[smtp]`). When a limit is reached the send stops and the next queue run resumes
   it. `PauseBetweenBatches` spaces batches without any run waiting. Shipped: `Throttle=disabled`, `BatchSize=200`.
2. **The page** (3 minutes). **Rate limits and batches** (dashboard, **Deliverability** block): **Transports** with
   **Sent** and **State**, **Last batches**, and **Pause** for some minutes. A paused transport sends nothing, even
   with throttling off; the dashboard lists the pause as a warning.
3. **Test groups** (4 minutes). **Test groups**, then **New test group**: a named set of addresses for one list or
   every list, at most `MaxTestGroupSize` (25). The test form of an edition offers the groups of its list, added to
   the typed addresses.
4. **Suppression import** (4 minutes). **Import into the suppression list** takes a CSV of addresses known to be bad
   (for example from an earlier provider) into the kernel's suppression list, which the newsletter respects.
5. **Soft bounce retries** (3 minutes). `SoftBounceMaxRetries` (2), `SoftBounceRetryDelay` (3600 seconds, doubled for
   each further retry) and `SoftBounceRetryMaxAge` (3 days).

### Common questions

**What limits should we set?** Ask your mail provider; they publish or tell you their limits.

**Does throttling apply to the file transport?** Limits are per transport, `file` included, so you can rehearse them
on a training installation.

**Who sees the test group addresses?** Each address gets its own test mail by default (`OneMailPerAddress=enabled`),
so testers do not see each other.

### Pitfalls

- **Pausing a transport and forgetting it.** Nothing goes out until the pause ends; the dashboard warns.
- **A real address in a test group.** Test groups persist; a real address stays there for every later test. Check.
- **Importing a real suppression file into a training installation.** It is personal data; use made-up addresses.

## Hands-on exercise

Use the training installation. Replace `<name>` with your first name.

1. In **INI Settings**, read `[ThrottleSettings]` (`Throttle`, `BatchSize`, `PauseBetweenBatches`) and
   `[TestSendSettings]` (`MaxTestGroupSize`, `SubjectPrefix`, `OneMailPerAddress`). Check `TransportMethodPreview=file`.
2. On the dashboard, open **Rate limits and batches**. Read the **Transports** table and **Last batches**. Do not press
   **Pause**.
3. Back on the dashboard, open **Test groups** and press **New test group**.
4. Name it `Reviewers <name>`, choose your list, and add three addresses: `rev1-<name>@example.invalid`,
   `rev2-<name>@example.invalid`, `rev3-<name>@example.invalid`. Save.
5. Open your edition `Tips for week 3` (not sent; you aborted it in lesson 4.4) or make a new copy, and find the test
   form. Choose your test group.
6. Press **Send Test Newsletter**.
7. List the outbox and count the new test files:

   ```bash
   ls -lt var/log/mail | head -6
   ```

8. Open **Import into the suppression list** and read the form. Do not import.
9. With your neighbor, decide limits for a list of 20,000 subscribers whose provider allows 120 mails per minute.
   How long does the send take?

## Expected result

- The test group `Reviewers <name>` exists for the learner's list with three addresses at `example.invalid`.
- The test send wrote one file per address (the list's test receiver plus the three reviewers), each with `[Test]` in
  the subject.
- No transport was paused; no suppression was imported.
- The pair's estimate: 20,000 / 120 is about 167 minutes, almost three hours, plus any hourly limit.
- The trainer checks the test group list and one outbox listing.

![The sending rate limits per transport](../../../images/trainers/nl-throttle.webp)

## Quiz

1. What happens to a send when its transport's per-hour limit is reached?
2. What is the largest test group with the shipped settings?
   A. 10 B. 25 C. 100 D. unlimited
3. True or false: a paused transport still sends when `Throttle=disabled`.
4. What does the suppression import do?
5. After a soft bounce, how long until the first retry with the shipped settings?

<details>
<summary>Answers</summary>

1. It stops and is resumed by the next queue run (deliverability guide, linked from the
   [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
2. **B.** `MaxTestGroupSize=25`.
3. **False.** A paused transport sends nothing, even with throttling off.
4. It loads a CSV of addresses into the kernel's suppression list, which every newsletter send respects
   ([E-mail preferences: setting them up and running them](../../../../guides/mail-preferences-administrator.md)).
5. One hour (`SoftBounceRetryDelay=3600`).

</details>

## Further reading

- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420):
  the deliverability guide.
- [E-mail preferences: setting them up and running them](../../../../guides/mail-preferences-administrator.md): the
  suppression list.
- [Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md#when-something-goes-wrong):
  many bounces and spam reports.

## Related pages

- [Module 5: Bounces and transports](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
