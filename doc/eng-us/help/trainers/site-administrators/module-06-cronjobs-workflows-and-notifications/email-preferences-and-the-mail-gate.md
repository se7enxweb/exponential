---
title: "Lesson 6.5: E-mail preferences and the mail gate"
audience: trainer, admin
category: training
order: 65
summary: Learners read the E-mail preferences status page, fill in the sender details on a training site, check what the mail gate would decide for test addresses without sending, and use the suppression list and consent log. 35 minutes.
keywords: site administration, e-mail preferences, mail gate, exp:mail:status, exp:mail:gate, suppression list, consent log, categories, unsubscribe, site secret, mailpreferences
type: help
---
# Lesson 6.5: E-mail preferences and the mail gate

Every mail Exponential sends passes the *mail gate*. Optional mail (content notifications, newsletters, offers) goes
only to people who turned it on, never to an address on the suppression list, and always with a footer that says who
sends it and how to stop it. Essential mail (security, orders) always goes. This lesson shows administrators how to set
up and check the gate, and how to answer a person's request, without sending any mail in class.

> **Note**
> Never test by sending from the web pages: turning on a newsletter, "send me a link" and an address change send
> real mail through the site's transport. Use the console commands below, which write to files.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 6: Cronjobs, workflows and notifications](README.md) |
| Duration | 35 minutes (15 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators, people who answer data protection requests |
| Prerequisites | [Lesson 6.4](running-notifications.md); an administrator account and a shell on the training installation |

## Learning objectives

After this lesson, learners can:

- explain the difference between optional and essential mail, and what the gate does with each;
- read **Setup > E-mail preferences** and `exp:mail:status`, and fix a missing sender detail;
- test what the gate would decide for an address with `exp:mail:gate`, writing mail to files only;
- add, check and lift an address on the suppression list;
- say why the site secret must be backed up with the installation.

## Trainer's notes

### How to present it

Source: [E-mail preferences: setting them up and running them](../../../../guides/mail-preferences-administrator.md),
sections 1 to 10. What visitors see: [E-mail preferences](../../../../features/6.0/mail-preferences.md).

1. **The gate in one paragraph** (3 minutes). Every mail passes `eZMailTransport::send()`. A mail with a category is
   checked against the person's preferences, the suppression list and a pending double opt-in. Mail without a
   category is sent as before and counted, so you can find the code that sends it.
2. **The status page** (4 minutes). **Setup**, then **E-mail preferences** (policy
   `mailpreferences/administrate`). Show **Sender details** with **Organization name** and **Postal address**,
   **Settings in use**, and the links to **Categories**, **Suppression list**, **Consent log** and **A user**.
3. **The site secret** (2 minutes). Links in mail are signed with a secret generated into
   `settings/override/mailpreferences.ini.append.php`. A new secret breaks every link already sent and makes the
   suppression list stop matching. Back it up with the installation (Module 9).
4. **Requests** (4 minutes). "What do you have about me?", "stop sending", "delete my data": the guide's table maps
   each to a page or a command. The consent log keeps the proof that mail stopped.
5. **Retention** (2 minutes). The `mailpreferences` part, in the `infrequent` group, removes old consent rows and
   expired confirmations.

### Common questions

**Can I turn a category on by default?** Never for an optional category: it would send mail to people who did not
ask for it.

**Why can I not read the suppression list?** Only a hash of each address is stored. You find an entry by typing the
address.

**Who sees the consent log export?** Users with the policy `mailpreferences/export`. The CSV holds addresses and IP
addresses: store it like any personal data and delete it when the request is answered.

### Pitfalls

- **Real addresses in class.** Use only addresses at `example.invalid`; the commands mask addresses unless you add
  `--addresses`.
- **Editing `settings/mailpreferences.ini`.** Settings go into an override, never into the shipped file. Use
  `exp:ini set ... --dry-run` first.
- **Changes do not show on Velocity.** A settings change needs the INI cache cleared and, for Velocity, a restart of
  its workers, as any settings change.

## Hands-on exercise

Use your own training installation, in a terminal at the installation root.

1. In the administration, open **Setup**, then **E-mail preferences**. Read any problems listed and **Settings in
   use**.
2. Run the console status and compare:

   ```bash
   php bin/php/console exp:mail:status
   ```

3. Preview setting the organization name in an override, without writing:

   ```bash
   php bin/php/console exp:ini set mailpreferences.ini/FooterSettings/OrganisationName "Training Organization" global --dry-run
   ```

4. On the status page, fill in **Organization name** (`Training Organization`) and **Postal address**
   (`Example Street 1`), and press **Save the sender details**. Reload: the footer warning has gone.
5. Ask the gate what it would decide for two test addresses, writing the mail to files:

   ```bash
   php bin/php/console exp:mail:gate --category=content --to=reader@example.invalid,other@example.invalid --write --mail-file-dir=var/tmp/gate-mail
   ```

6. Open the written `.mail` file in `var/tmp/gate-mail` and find the footer and the `List-Unsubscribe` header.
7. Put one test address on the suppression list and check it:

   ```bash
   php bin/php/console exp:mail:suppression add --email=other@example.invalid --reason=legal --note="Training"
   php bin/php/console exp:mail:suppression check --email=other@example.invalid
   ```

8. Run step 5 again and compare the decision for `other@example.invalid`.
9. Open **Setup > E-mail preferences > Suppression list**, use **Check an address** for the same address, then lift
   it from the console: `php bin/php/console exp:mail:suppression lift --email=other@example.invalid`.
10. Preview the retention cleanup: `php bin/php/console exp:mail:consent cleanup --dry-run`.

## Expected result

- Step 2 lists five tables as `ok`, the gate, the site secret and the categories.
- Step 3 printed a diff of the override file and *nothing written*.
- Step 5 printed one decision per address and *Written: 1 file(s)*, ending with `PASS`.
- Step 8: the suppressed address is now blocked for the optional category.
- Step 9: the entry is found, then lifted.
- The trainer checks the `.mail` file of one learner on the projector: the footer names the training organization.

![E-mail preferences: the mail categories and the mail gate](../../../images/trainers/sa-mail-prefs.webp)

## Quiz

1. Which kind of mail still goes to an address on the suppression list?
   A. Newsletters B. Content notifications C. Essential mail such as security mail D. None at all
2. What happens to the links in mail already sent when the site secret is replaced?
3. Which command shows what the gate would decide, without contacting a mail server?
4. Why must optional categories never be on by default?
5. Where do sender details belong: in `settings/mailpreferences.ini` or in an override?

<details>
<summary>Answers</summary>

1. **C.** Essential mail always goes; optional mail is blocked
   ([E-mail preferences, section 7](../../../../guides/mail-preferences-administrator.md#7-the-suppression-list)).
2. They stop working, and every suppression entry stops matching
   ([section 2.4](../../../../guides/mail-preferences-administrator.md#24-the-site-secret)).
3. `exp:mail:gate`; with `--write` and `--mail-file-dir` it writes the mail to files.
4. It would send mail to people who did not ask for it, which data protection and anti-spam rules forbid.
5. In an override (the status page, or `exp:ini set ... global`), never in the shipped file.

</details>

## Further reading

- [E-mail preferences: setting them up and running them](../../../../guides/mail-preferences-administrator.md): the
  full administrator's guide with a checklist.
- [E-mail preferences for developers](../../../../guides/mail-preferences-developer.md): how code sends mail through
  the gate.
- [Mail, consent and bounces for hosts](../../../hosting/operations/mail-consent-and-bounces.md): the server side.

## Related pages

- [Module 6: Cronjobs, workflows and notifications](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
