---
title: "Lesson 1.5: Running an assessment session"
audience: trainer
category: training
order: 15
summary: Run the assessment on the day, prepare identical training installations, brief learners on the rules, keep time, collect evidence safely, handle a broken installation without penalizing the learner, and clean up test accounts and data afterwards. 40 minutes.
keywords: assessments and practical exercises, assessment day, lab preparation, golden copy, reset, evidence, time keeping, broken installation, retake, cleanup, file transport
type: help
---
# Lesson 1.5: Running an assessment session

A well-written test can still be unfair on the day: one learner's installation is broken, another's clock started
late, a third sends a real mail by accident. This lesson is the checklist for the session itself.

> **Note**
> Practical tasks always run on training installations, never on a live site. Set the mail transport to files on
> every training installation before the session ([Mail: the file transport](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending)).

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 1: Designing assessments](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Trainers who run assessments |
| Prerequisites | [Lesson 1.3](building-scoring-rubrics.md), [Resetting the lab](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md) |

## Learning objectives

After this lesson, learners can:

- prepare identical training installations from one golden copy or one install command;
- brief learners on the rules, the time and what may be open;
- collect evidence for the rubric without collecting personal data;
- decide fairly what happens when an installation breaks during the task;
- clean up after the session.

## Trainer's notes

### How to present it

1. **The day before** (4 minutes).
   - Build every learner's installation the same way, with the same release and site package, as in
     [One installation per learner](../../train-the-trainer/module-02-building-the-lab/one-install-per-learner.md),
     or reset them from a golden copy ([Resetting the lab](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md)).
   - Set `[MailSettings] Transport=file` in `settings/override/site.ini.append.php`; a mail is then written to
     `var/log/mail/` instead of being sent. For newsletter tasks also set every `TransportMethod*` of
     `cjw_newsletter.ini` to `file`; the shipped `TransportMethodPreview` is `sendmail`
     ([Newsletters (cjw_newsletter)](../../../../install/10-after-installing.md#newsletters-cjw_newsletter)).
   - Do not install a crontab on training machines; learners run cron parts by hand when a task asks for it.
   - Create the learner accounts with addresses at `example.invalid`.
   - Do the task yourself on one installation and time it.
2. **The briefing** (3 minutes). Read the first line of the sheet ("the trainer's own assessment, not an official
   certification"), the time, what may be open (the help, notes), whether learners may talk, and how to call the
   trainer.
3. **During** (4 minutes). Write the start time on each learner's sheet. Walk around, do not help with the task, but
   do help with the room: a frozen browser, a lost password of the training account.
4. **When an installation breaks** (2 minutes). If the fault is in the lab, not in the learner's work, stop the
   learner's clock, reset the installation, restore their progress where possible, and give the lost time back. If the
   learner broke it as part of the task, that is evidence for the rubric.
5. **After** (2 minutes). Score with the rubric while the installations are still there. Then stop the servers,
   remove learner accounts on shared sites and delete lab directories
   ([Security and privacy in class](../../train-the-trainer/module-04-delivering/security-and-privacy-in-class.md)).

### Common questions

**May learners use their own laptops?** Yes, if each runs the same installation; otherwise use the lab machines.
Different setups make different tasks.

**What counts as evidence?** What the rubric's Evidence column names: an item's page, a file in `var/log/mail/`, a
command's output. Take screenshots only with the learner's permission and only of the training installation.

**How do retakes work?** As written in the rules: usually a new practical task of the same kind on a fresh
installation, on another day.

### Pitfalls

- **One installation for the whole group.** Learners overwrite each other's work. Use one installation per learner, or
  one folder and one account per learner with roles that keep them apart.
- **The crontab of a copied site.** A restored copy of a real site with its cron running sends real mail. Never use
  a copy of a live site with real addresses for an assessment.
- **Scoring days later.** The installation has been reset and the evidence is gone.

## Hands-on exercise

Prepare an assessment session on your own training installation.

1. Check the mail transport of the installation:

   ```bash
   ./console exp:ini get site.ini/MailSettings/Transport --allow-root-user
   ```

   If it is not `file`, set it in `settings/override/site.ini.append.php` and clear the INI cache.
2. Create one learner account at `example.invalid` in the group your task needs.
3. Write the briefing text: issuer line, time, what may be open, how to call you.
4. Make a golden copy of the prepared state as described in
   [Resetting the lab](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md).
5. Do the task yourself and time it.
6. Break the installation on purpose (for example stop the server) and reset it from the golden copy. Time the reset.
7. Write the rule you will apply when a learner's installation breaks.

## Expected result

- The mail transport is `file`, checked by the command.
- A briefing text, a golden copy, the time of the task and the time of a reset.
- A written rule for broken installations that gives the lost time back when the fault is the lab's.
- The trainer asks one learner to read their briefing aloud.

## Quiz

1. Which mail setting do you check on every training installation before a session?
2. Why do you not install a crontab on training machines?
3. A learner's installation stops answering because the lab machine ran out of disk. What do you do?
4. When do you score, and why then?
5. What do you remove after the session?

<details>
<summary>Answers</summary>

1. `[MailSettings] Transport`, which should be `file`
   ([10.4](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending)); for newsletter
   tasks also the `TransportMethod*` keys of `cjw_newsletter.ini`.
2. Cron runs notifications, newsletters and jobs on its own; on a copy with real addresses it sends real mail
   ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md#a-restore-test-on-another-machine)).
3. Stop the learner's clock, reset the installation, restore the progress if possible and give the lost time back.
4. Right after the task, while the installations and the evidence still exist.
5. Learner accounts on shared sites, lab directories and running servers
   ([Security and privacy in class](../../train-the-trainer/module-04-delivering/security-and-privacy-in-class.md)).

</details>

## Further reading

- [Lab quick card](../../lab-quick-card.md): the commands for building, serving and resetting a lab.
- [Resetting the lab](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md): golden copies and fresh
  installs.
- [Troubleshooting in class](../../train-the-trainer/module-04-delivering/troubleshooting-in-class.md): when something
  breaks during the session.

## Related pages

- [Module 1: Designing assessments](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
