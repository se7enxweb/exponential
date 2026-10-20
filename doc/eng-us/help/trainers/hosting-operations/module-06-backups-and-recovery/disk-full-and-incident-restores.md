---
title: "Lesson 6.4: A full disk and restores in an incident"
audience: trainer, hosting, admin
category: training
order: 64
summary: Learners free disk space safely with the disk-full runbook (what may be removed and what never), and make the decisions of the restore-from-backup runbook for a data loss and a compromise. 40 minutes.
keywords: hosting and operations, disk full, runbook, du, exp:logs prune, truncate, compress, clear caches, never delete storage, restore from backup, compromised site, incident decisions
type: help
---
# Lesson 6.4: A full disk and restores in an incident

Two incidents end up on the backup team's desk: a disk that ran full, and a customer who needs yesterday's site back.
Both go wrong when someone deletes or restores in a hurry. This lesson rehearses the disk-full runbook on a training
installation and the decisions of the restore runbook as a tabletop exercise.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 6: Backups and recovery](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | On-call staff and system administrators |
| Prerequisites | [Lesson 6.3](restoring-and-restore-tests.md); [Lesson 5.2](../module-05-monitoring-and-logs/log-viewer-and-rotation.md) |

## Learning objectives

After this lesson, learners can:

- find what grew on a full disk, inside and outside the installation;
- say what may be removed (caches, log archives) and what never (`var/storage`, `settings/`, the SQLite files);
- free space with the cache command and `exp:logs` instead of `rm`;
- answer the four questions of the restore runbook (which backup, how much, what is lost, where) for a data loss and
  for a compromise;
- keep the damaged state before restoring over it.

## Trainer's notes

### How to present it

Source: [Runbook: disk full](../../../hosting/runbooks/disk-full.md) and
[Runbook: restore from backup](../../../hosting/runbooks/restore-from-backup.md).

1. **Symptoms** (2 minutes). Uploads fail, publishing fails, pages show errors, logs stop growing, the database
   refuses writes.
2. **Find it** (3 minutes). `df -h`, `du -sh var/* | sort -h`, then outside the installation: backups on the same disk,
   the database server's data directory, system logs, PHP session files.
3. **Remove and never** (4 minutes). Show the table: caches through the cache command; log archives through
   `exp:logs --prune`; current logs emptied through `exp:logs --truncate`, not deleted; `var/storage`, `settings/` and
   the SQLite files never by hand. A log that grows by megabytes a minute is a symptom: find out why.
4. **Restore decisions** (6 minutes). Which backup (for a compromise, one from before the attacker's first change),
   everything or part, what is lost (tell the customer before you start), in place or first on a second machine. Keep
   the damaged state.

### Common questions

**Can I delete `var/cache` with `rm`?** Use the cache command; it knows the paths of every site.

**The SQLite `-wal` file is huge.** It shrinks at a checkpoint; `VACUUM` needs free space of its own. Read chapter 9
before running anything.

**After a compromise, is a restore enough?** No. The runbook [Compromised site](../../../hosting/runbooks/compromised-site.md)
comes first; the backup must predate the first change, and the hole must be closed.

### Pitfalls

- **Deleting current log files** breaks their permissions for the other writer. Use `--truncate`.
- **Filling a learner's disk on purpose.** Do not; read the sizes and use dry runs.
- **Treating the tabletop as theory.** Ask each group to name the exact backup file they would choose.

## Hands-on exercise

Part A uses your own training installation, as the site's user. Part B is a tabletop exercise in groups.

1. Check the file system: `df -h .`.
2. Find the largest directories in `var/`: `du -sh var/* | sort -h | tail -10`.
3. Show the log archives and their sizes: `php bin/php/console exp:logs --archives`.
4. Preview a prune: `php bin/php/console exp:logs --prune --dry-run`.
5. Show which logs grow fastest: `php bin/php/console exp:logs --list`.
6. Write down, for each directory of step 2, whether it may be cleaned and with which command, using the runbook's
   table.
7. Part B, scenario 1: an editor removed a whole section by mistake two days ago and noticed it today. Answer the four
   questions of *Decide first* in the restore runbook.
8. Part B, scenario 2: a customer's site was changed by an attacker; the first unknown change is in the audit trail
   five days ago. Answer the same four questions and name the runbook to open first.
9. Each group reads its answers to the room.

## Expected result

- Step 6: caches and log archives marked as removable with the cache command and `exp:logs`; `var/storage`,
  `settings/` and the SQLite directory marked "never".
- Step 7: the group first checks the trash and versions, then chooses the newest backup from before the removal,
  restores on a second machine and recovers the section, and tells the customer what would be lost by a full restore.
- Step 8: a backup from before the first change, the compromised-site runbook first, the damaged state kept for
  investigation.
- The trainer checks both scenarios' answers against the runbook.

## Quiz

1. Which of these may be removed on a full disk?
   A. `var/storage` B. `settings/override` C. rotated log archives D. the SQLite `-wal` file
2. Why are current logs emptied with `exp:logs --truncate` and not deleted?
3. Which backup do you restore after a compromise?
4. Why keep the damaged state before a restore?
5. Name two places outside the installation to check when a disk is full.

<details>
<summary>Answers</summary>

1. **C.** Through `exp:logs --prune` ([Runbook: disk full](../../../hosting/runbooks/disk-full.md#what-may-be-removed-and-what-never)).
2. Truncating keeps the files and their permissions; a deleted log may be created again with the wrong owner.
3. One from before the attacker's first change.
4. It may be needed to recover content published after the backup, or to investigate.
5. Any two of: backups on the same disk, the database server's data directory, system logs, PHP session files.

</details>

## Further reading

- [Runbook: disk full](../../../hosting/runbooks/disk-full.md)
- [Runbook: restore from backup](../../../hosting/runbooks/restore-from-backup.md)
- [Runbook: compromised site](../../../hosting/runbooks/compromised-site.md)
- [Content history](../../../../guides/content-history.md): versions before a restore.

## Related pages

- [Module 6: Backups and recovery](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
