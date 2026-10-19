---
title: "Lesson 6.2: A backup policy and retention"
audience: trainer, hosting, admin
category: training
order: 62
summary: Learners write a backup policy for a hosting plan from the rules in the sources (frequency, generations, location, encryption, restore tests), align it with the retention Exponential keeps itself, and describe it to customers without over-promising. 35 minutes.
keywords: hosting and operations, backup policy, schedule, retention, generations, off-site, encryption, point-in-time recovery, restore test, audit retention, LiveDays, ArchiveDays, personal data in backups
type: help
---
# Lesson 6.2: A backup policy and retention

Commands make a backup; a policy makes a backup service. This lesson is a short workshop: learners turn the rules
the documentation gives into a policy table for a hosting plan, line it up with the retention Exponential applies to
its own records, and practise answering "how far back can you go?" from that table.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 6: Backups and recovery](README.md) |
| Duration | 35 minutes (10 presenting, 25 workshop) |
| Level | Intermediate |
| Audience | Product managers, system administrators and support leads |
| Prerequisites | [Lesson 6.1](what-to-back-up.md) |

## Learning objectives

After this lesson, learners can:

- name the rules the sources set as the floor of any policy;
- make the decisions of a policy: frequency, consistency, generations, location, encryption, point-in-time recovery,
  who may restore, restore tests;
- list when a backup is taken outside the schedule (before an upgrade, a package install, an engine change, a
  restore);
- compare the policy with the retention settings inside Exponential;
- explain why personal data erased from the site can remain in older backups, and that this is a legal question.

## Trainer's notes

### How to present it

Source: [Backup schedules and retention](../../../hosting/operations/backup-schedules-and-retention.md).

1. **The floor** (3 minutes). A backup is the database and the files at the same moment; store backups away from the
   server and keep several generations; back up before every upgrade; test a restore on another machine at least once
   and after every major upgrade; weekly: a backup from the last seven days exists and has been restored once.
2. **The decisions** (3 minutes). Show the decisions table. The documentation gives no prices and no fixed figures of
   its own; every value is the host's choice.
3. **Retention inside Exponential** (2 minutes). The audit trail keeps live files 90 days and archives 730 days by
   default; the e-mail consent log of people who are gone 1095 days; archives of `404.log` and `security.log` are
   pruned after 30 and 90 days. A backup keeps them longer.
4. **Erased data** (2 minutes). Copies in older backups keep erased personal data until they expire. The page is not
   legal advice; the host and each customer take their own legal counsel.

### Common questions

**Does PostgreSQL give point-in-time recovery?** Its own replication and point-in-time recovery, set up from the
PostgreSQL documentation, if a plan promises recovery between two dumps.

**Can a customer restore from a panel?** A host may offer it; a customer restore must never overwrite another
customer's data.

**What should the plan description say?** The policy table with the host's values, so support answers from the same
table the customer saw.

### Pitfalls

- **Inventing industry figures.** Keep to the rules in the sources and the values the group decides; mark them as the
  group's own.
- **Turning the workshop into legal advice.** Name the question, do not answer it.
- **Policies without restore tests.** Every group's table must have a restore-test row.

## Hands-on exercise

Work in groups of two or three on paper or in a shared document.

1. Copy the decisions table from [Backup schedules and retention](../../../hosting/operations/backup-schedules-and-retention.md#the-decisions-of-a-backup-policy)
   and add a third column, "Our plan".
2. Choose an imagined plan: small business sites on one shared machine, SQLite, about five editors per site.
3. Fill in frequency, consistency, generations and location.
4. Fill in encryption, point-in-time recovery and who may restore.
5. Fill in restore tests: how often and on which machine.
6. Add a row "Backups outside the schedule" and list the four occasions from the page.
7. Compare your generations with the shipped retention of the audit trail (90 and 730 days) and write one sentence on
   what that means for a restore one year back.
8. Write the two-sentence answer support gives to "How far back can you restore our site?".
9. Present your table to the group in two minutes.

## Expected result

- Each group has a complete table, with a value in every row of "Our plan".
- The restore-test row names a frequency and another machine.
- The "outside the schedule" row lists an upgrade, a package install, an engine change and a restore.
- The support answer matches the generations in the table.
- The trainer checks each table for the rules of step 1 of the trainer's notes.

## Quiz

1. Name two rules that form the floor of every backup policy.
2. Which occasion does not call for a backup outside the schedule?
   A. an upgrade B. a change of database engine C. reading the logs D. a restore
3. The audit trail keeps archives for 730 days by default. Your backups go back 30 days. What does that mean?
4. Why can an erased person's data still exist after the erasure?
5. Does the documentation say how many generations a host must keep?

<details>
<summary>Answers</summary>

1. Any two of: database and files at the same moment; away from the server with several generations; before every
   upgrade; a restore tested on another machine; weekly, a recent backup restored at least once.
2. **C.** The four occasions are an upgrade, a package install, an engine change and a restore.
3. The site itself keeps audit archives longer than the backups; a restore can only go back 30 days, but the archives
   on the live site reach further.
4. Older backups keep it until they expire ([Backup schedules and retention](../../../hosting/operations/backup-schedules-and-retention.md#backups-and-erased-personal-data)).
5. No. How many and for how long is the host's product decision.

</details>

## Further reading

- [Backup schedules and retention](../../../hosting/operations/backup-schedules-and-retention.md)
- [Log and data retention](../../../hosting/security/log-and-data-retention.md)
- [Compliance and GDPR](../../../hosting/security/compliance-and-gdpr.md)
- [SLA options](../../../hosting/business/sla-options.md): describing services in a plan.

## Related pages

- [Module 6: Backups and recovery](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
