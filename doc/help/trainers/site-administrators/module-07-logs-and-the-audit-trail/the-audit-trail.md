---
title: "Lesson 7.4: The audit trail"
audience: trainer, admin
category: training
order: 74
summary: Learners explain what the audit trail records and why it is tamper-evident, read the Audit dashboard and console, find their own sign-in, and check the chain with exp:audit status and verify. 45 minutes.
keywords: site administration, audit trail, audit dashboard, audit console, exp:audit, verify, hash chain, channels, access.session.login, audit/read, audit/manage, tamper-evident
type: help
---
# Lesson 7.4: The audit trail

The audit trail answers "who did what, and when?" for sign-ins and refusals, content changes, settings writes, cache
clears, deploys and orders. Every record carries the hash of the record before it, so an edited or cut record is
noticed. It is on by default and needs no setup, apart from a running cronjob. This lesson teaches administrators to
read it and to prove it is intact.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 7: Logs and the audit trail](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Site administrators, security officers |
| Prerequisites | [Lesson 7.2](the-log-viewer.md); [Lesson 6.1](../module-06-cronjobs-workflows-and-notifications/cronjob-parts-and-the-crontab.md); an administrator account and a shell |

## Learning objectives

After this lesson, learners can:

- name the five channels of the audit trail and give an example event of each;
- explain what makes the record tamper-evident;
- open the **Audit dashboard** and the **Audit console** and filter by channel, event name and login;
- find their own sign-in as an `access.session.login` event;
- check the chain with `exp:audit status` and `exp:audit verify`.

## Trainer's notes

### How to present it

Sources: [The audit trail](../../../../features/6.0/audit-trail.md) and
[Security and audit, section 3](../../../../guides/security-and-audit.md#3-read-the-audit-trail-5-minutes).

1. **Why** (3 minutes). Answer "who removed this?", notice trouble early through alert rules, prove the record is
   intact, and respect privacy: addresses are truncated, sessions hashed, and passwords, tokens and secrets are never
   recorded.
2. **Channels** (3 minutes). `content` (publish, move, remove, sections, states ...), `access` (sign-in, failed
   sign-in, refused view, role assignment), `system` (settings writes, cache clears, cronjob runs, deploys),
   `commerce` (orders, VAT, never card data) and `read` (sampled views and searches, only when switched on).
3. **The hash chain** (2 minutes). Each record carries the hash of the one before. Daily files are closed, archived
   and listed in signed manifests. `verify` tells whether anything was edited or cut.
4. **Where to look** (4 minutes). On the **Dashboard**, the **Audit trail** block or the left column entry opens the
   audit. Show the **Audit dashboard** (Health, Today and 7 days, Security, Alerts, Activity, Operations) and the
   **Audit console** with its filters **Channel**, **Event name**, **Login**, **Result**, **From**, **To** and
   **Search**, and the **Filter** button.
5. **The cronjob** (2 minutes). The `audit` part, in `frequent`, indexes events, delivers sinks, evaluates alert rules
   and once a day rotates, verifies and archives.

### Common questions

**Who may read the audit?** Users with `audit/read`; `audit/manage` adds archives, settings and **Verify now**. Only
the Administrator role holds them on a new installation. A refused attempt is itself recorded as
`access.permission.refused`.

**What happens when the disk is full?** With the default `OnWriteFailure=continue` the request goes on and the
failure is written to `error.log` as `AUDIT-UNWRITTEN`. With `refuse`, security-relevant actions are refused until
the log can be written.

**Must I back anything up?** Yes: `settings/override/audit.ini.append.php` holds the signing and pseudonym keys.
Module 9 includes it in the backup.

### Pitfalls

- **Reading the trainer's own trail on a shared site.** Learners would see each other's logins and the trainer's. On
  one installation per learner this is harmless; on a shared site, show the console filtered to one training login.
- **Expecting node views.** Reads are only recorded when the `read` channel is switched on.
- **Glob patterns.** `exp:audit` accepts `*`, a prefix ending in `.*` or a whole name; anything else is refused with
  exit code 2.

## Hands-on exercise

Use your own training installation, in the browser and in a terminal at the installation root.

1. Check the state of the audit:

   ```bash
   php bin/php/console exp:audit status
   ```

2. In the browser, sign out of the administration and sign in again.
3. On the **Dashboard**, open **Audit trail**. Read the **Audit dashboard**: the health of the channels and the
   figures of today and the last 7 days.
4. Open the **Audit console**. Set **Channel** to `access` and press **Filter**. Find your sign-in of step 2, an
   event named `access.session.login`.
5. Open the event and read its fields: time, login, address (truncated) and result.
6. In the console, filter by **Login** with your own login and read what else you did today.
7. Change the title of one training article and publish it. Filter the console by **Channel** `content` and find the
   publish event.
8. Show the newest sign-ins from the shell:

   ```bash
   php bin/php/console exp:audit tail --channel=access --name='access.session.*' --lines=3
   ```

9. Check that no chain is broken:

   ```bash
   php bin/php/console exp:audit verify
   ```

10. List who receives alert mail (nothing is sent): `php bin/php/console exp:audit alerts recipients`.

## Expected result

- Step 1 prints `Audit: enabled` and one line per channel with *Chain intact*.
- Step 4 lists the sign-in of step 2 as the newest `access.session.login`.
- Step 7 lists a content publish event with the object and the training login.
- Step 9 prints `INTACT` for each channel and exits with code 0.
- The trainer checks by asking a learner to show the event of step 5 on the projector, with its address truncated.

## Quiz

1. Which channel records a settings write?
   A. `content` B. `access` C. `system` D. `commerce`
2. What makes the audit record tamper-evident?
3. True or false: the audit records the passwords typed at a failed sign-in.
4. Which cronjob part must run for alerts and daily archives to work?
5. Which file must be backed up so archived audit records can still be verified?

<details>
<summary>Answers</summary>

1. **C.** ([The audit trail, What is recorded](../../../../features/6.0/audit-trail.md#what-is-recorded)).
2. Every record carries the hash of the one before it, and daily archives are listed in signed manifests;
   `exp:audit verify` detects an edit or a cut.
3. **False.** Passwords, tokens and secrets are never recorded; a failed sign-in for an unknown account is recorded
   hashed only.
4. The `frequent` part, which contains the `audit` part's script.
5. `settings/override/audit.ini.append.php`, which holds the signing and pseudonym keys.

</details>

## Further reading

- [The audit trail](../../../../features/6.0/audit-trail.md): the short reference.
- [Security and audit](../../../../guides/security-and-audit.md): a hands-on security walk-through including the
  audit.
- [The audit guide](../../../../bc/6.0/audit.md): the event catalogue and every setting.
- [The audit trail for hosts](../../../hosting/security/audit-trail.md): sinks and retention on a server.

## Related pages

- [Module 7: Logs and the audit trail](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
