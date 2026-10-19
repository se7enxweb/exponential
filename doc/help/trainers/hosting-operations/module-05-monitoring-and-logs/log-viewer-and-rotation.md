---
title: "Lesson 5.2: The log viewer and log rotation"
audience: trainer, hosting, admin
category: training
order: 52
summary: Learners read and follow logs in Setup, Log files and with exp:logs, find the lines of one failed request by its error reference, check log health, and explain how rotation works safely with two writers. 45 minutes.
keywords: hosting and operations, log viewer, Setup Log files, exp:logs, error reference, ERR-, error digest, log health, masking, setup/logs, unmasklogs, rotation, LogFileMaxSize, prune, logrotate
type: help
---
# Lesson 5.2: The log viewer and log rotation

Logs answer most support questions, if staff can read them quickly and safely. This lesson teaches the log viewer in
the administration and its command-line twin, the error reference that leads from an error page to its log lines,
the masking that protects personal data, and the rotation that keeps logs from filling a disk.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 5: Monitoring and logs](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Hosting support staff and system administrators |
| Prerequisites | [Lesson 5.1](monitoring-and-health-checks.md); an administrator account on the learner's training installation |

## Learning objectives

After this lesson, learners can:

- open, follow and search a log in **Setup**, **Log files**, and copy a permalink for a ticket;
- find the lines of one request from an error reference such as `ERR-...`, in the browser and with `exp:logs`;
- read **Log health** and the **Error digest**;
- explain what is masked by default and which policies allow reading, managing and unmasking logs;
- explain how the kernel rotates, compresses and prunes logs, and when the system's logrotate is used instead.

## Trainer's notes

### How to present it

Source: [Log viewer and rotation](../../../hosting/operations/log-viewer-and-rotation.md),
[The log viewer](../../../../features/6.0/log-viewer.md) and
[Log rotation and pruning](../../../../features/6.0/log-rotation.md).

1. **The viewer** (5 minutes). **Setup**, **Log files** shows every log of the installation (`error.log`,
   `warning.log`, `404.log`, `security.log`, cronjob and extension logs, rotations). On the admin4 design the log tools
   are **Logs**, **Error digest**, **Log health** and **Manage logs**; a log view has **Follow**, **Copy permalink**,
   **Show unmasked** and **Download (gzip)**. Permalinks carry no query string and are safe in a ticket.
2. **Privacy** (3 minutes). E-mail addresses, IP addresses, passwords, tokens, keys, session ids and cookies are
   masked; the values of secret settings are never shown. Policies: `setup/logs`, `setup/managelogs`,
   `setup/unmasklogs`. The viewer is read-only by default; every view and action is audited.
3. **One request** (4 minutes). An error page shows a reference `ERR-...`; the same reference is in `error.log`.
   Search for it and read **Same request**. Not found? It rotated away, or another node served it.
4. **Rotation** (5 minutes). When a log outgrows `[FileSettings] LogFileMaxSize` (2 MB), the next write rotates it;
   `LogFileRotateCount` (3) archives are kept. Rotation is safe with two writers (Velocity as root and PHP-FPM as the
   site's user): it locks, checks the size again and keeps owner, group and mode. `exp:logs --prune` removes archives
   beyond the limits.
5. **The shell** (3 minutes). `./console exp:logs --list`, `--tail`, `--follow`, `--search`, `--reference` use the
   same settings and masking.

### Common questions

**Can a customer's administrator read logs?** Give `setup/logs` to a role of theirs; keep `setup/unmasklogs` and
`setup/managelogs` with support staff. Each installation shows only its own files.

**Can I see Apache's logs here?** Yes, if you name them in `logviewer.ini [LogViewerSettings]`; they are off by
default, never guessed, and never rotated by Exponential.

**What does Log health warn about?** Disk used by logs against `[HealthSettings] DiskBudget` (256 MB, a warning from
80 %), the fastest growing logs, files the web server cannot write and mixed owners, which stop rotation.

**Where are Velocity's logs?** Under `var/vc/qbix/log/`, rotated by date by the server itself.

### Pitfalls

- **Unmasking on a projector.** Never press **Show unmasked** in class; masked output is the point.
- **Rotating or emptying logs on a shared site.** These actions ship disabled; leave them so in class.
- **Looking on the wrong node.** Each server keeps its own `var/log`.

## Hands-on exercise

Use your own training installation, signed in to its administration as an administrator, and a shell as the site's
user.

1. Open **Setup**, then **Log files**. Note which log opens first and its size.
2. Switch **Follow** on and, in another tab, open a page of the site that does not exist (for example
   `/this-page-does-not-exist`). Open `404.log` in the viewer and find the line.
3. Press **Copy permalink** and paste the address into a text file; note that it has no query string.
4. Open **Error digest** and note the groups it shows for the last 24 hours.
5. Open **Log health** and note the disk used against the budget.
6. In the shell, list the logs: `php bin/php/console exp:logs --list`.
7. Show the last 20 entries of `error.log`: `php bin/php/console exp:logs --tail=var/log/error.log --lines=20`.
8. If an entry carries a reference (`ERR-...`), look it up: `php bin/php/console exp:logs --reference=<reference>`.
9. Show each log's archives and limits: `php bin/php/console exp:logs --archives`.
10. Preview a prune without removing anything: `php bin/php/console exp:logs --prune --dry-run`.
11. Read the rotation settings: `php bin/php/console exp:ini get site.ini/FileSettings/LogFileMaxSize` and the same for
    `LogFileRotateCount`.

## Expected result

- Step 2: the request appears in `404.log` with a `reason=` field and a masked client address.
- Step 3: the permalink has the form `/setup/logs/view/<log>` without a query string.
- Step 5 shows a percentage of the budget.
- Step 10 lists what would be removed and removes nothing.
- Step 11 prints `2097152` and `3` unless overridden.
- The trainer checks by asking each learner for the `reason=` of their step 2 line.

## Quiz

1. A customer sends a screenshot with `ERR-0123456789`. Where do you start?
2. Which policy shows e-mail and IP addresses as written?
   A. `setup/logs` B. `setup/managelogs` C. `setup/unmasklogs` D. `setup/system_info`
3. Why can rotation not leave a log that one of two writers cannot write?
4. A log line cannot be found although the customer saw the error. Name two reasons.
5. Who rotates web server logs that are shown in the viewer?

<details>
<summary>Answers</summary>

1. Search for the reference in **Setup**, **Log files**, or run `exp:logs --reference=ERR-0123456789`; read
   **Same request**.
2. **C.** For that viewer only; masking is on by default.
3. Rotation takes a lock, checks the size again and creates the new log with the owner, group and mode of the old one
   ([Log viewer and rotation](../../../hosting/operations/log-viewer-and-rotation.md#how-the-kernel-rotates-its-logs)).
4. The log rotated away, or another node served the request.
5. The program that writes them; Exponential never rotates or prunes them.

</details>

## Further reading

- [Log viewer and rotation](../../../hosting/operations/log-viewer-and-rotation.md): the host's guide.
- [The log viewer](../../../../features/6.0/log-viewer.md): every view and setting.
- [Log rotation and pruning](../../../../features/6.0/log-rotation.md): rotation, compression and pruning.
- [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md): from error page to log.

## Related pages

- [Module 5: Monitoring and logs](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
