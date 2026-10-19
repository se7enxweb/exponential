---
title: "Lesson 7.2: The log viewer"
audience: trainer, admin
category: training
order: 72
summary: Learners open Setup > Log files, follow a log live, search it by text, level and time, share a masked permalink, and read the Error digest and Log health pages. 45 minutes.
keywords: site administration, log viewer, Setup Log files, follow, search, permalink, masking, Show unmasked, Error digest, Log health, Manage logs, setup/logs
type: help
---
# Lesson 7.2: The log viewer

**Setup > Log files** reads every log of the installation from the browser: it follows a file as it is written,
searches it and its rotations, groups the lines of one request, and masks personal data by default. It also has two
summary pages, **Error digest** and **Log health**, that tell an administrator in a minute what is wrong. This lesson
makes the viewer the daily tool, with the command line from lesson 7.1 as the fallback.

> **Note**
> On a live site, take screenshots and reports with masking on. The exercises use your own training installation.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 7: Logs and the audit trail](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Site administrators, support staff |
| Prerequisites | [Lesson 7.1](the-log-files.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- open a log from the navigator of **Setup > Log files** and follow it live with **Pause** and **Newest**;
- search one file, all kernel logs or every file, by text, level and time;
- copy a permalink of a view and explain why it is safe to share;
- read the **Error digest** and **Log health** pages and say what each warns about;
- name the three policies of the viewer and who holds them on a new installation.

## Trainer's notes

### How to present it

Source: [The log viewer](../../../../features/6.0/log-viewer.md). The page described is the one of the admin4 design.

1. **The way in** (2 minutes). **Setup**, then **Log files** in the left column. The page opens on `error.log` at
   its end. Above the lines the tabs **Logs**, **Error digest**, **Log health** and **Manage logs**.
2. **The navigator** (3 minutes). Files are grouped **Kernel**, **Site**, **Extensions**, **Web server** and
   **Rotated archives**, each with size, last write and lines a minute. Web server logs appear only when
   `logviewer.ini` names them.
3. **Reading and following** (3 minutes). Each entry shows time, level (colored, with a label and an icon), source,
   siteaccess, masked client and request. Entries of the same request share a marker. Following uses short requests:
   **Pause** stops, **Newest** jumps to the end.
4. **Searching** (3 minutes). Search in **This file**, **All kernel logs** or **All log files**, with **Include
   rotated files**; filter by levels, time range, **Siteaccess**, **Request address contains**, **Client IP starts
   with** and **Error reference**. A long search continues with **More results**.
5. **Privacy and sharing** (2 minutes). Addresses, IPs, tokens and session ids are masked by default; secret
   settings are never shown. **Copy permalink** gives an address without a query string that always opens masked.
6. **Digest and health** (2 minutes). **Error digest** groups the distinct errors and warnings of the last 24 hours,
   most frequent first. **Log health** shows disk use, the fastest growing logs, logs over their limit and files the
   web server cannot write.

### Common questions

**Who may use the viewer?** `setup/logs` to read, `setup/managelogs` to rotate, empty or remove archives (each only
when switched on in `logviewer.ini [ActionSettings]`), and `setup/unmasklogs` for **Show unmasked**. A new
installation gives all three to the Administrator role only.

**Can I rotate or empty a log from the page?** Only when the action is switched on; by default the viewer is read
only, apart from downloads. Every view and action is audited.

**Why can a search not find an address I know is in the log?** A search finds only what you are shown. With masking
on, search for the masked form (`203.0.`), or switch **Show unmasked** on if you hold `setup/unmasklogs`.

### Pitfalls

- **Show unmasked on the projector.** Never in class on real data; the audit records that the file was read
  unmasked.
- **Expecting live lines with nothing happening.** Following slows down while nothing arrives. Open a page of the
  training site in another tab to make lines appear.
- **Mixed owners.** If **Log health** warns that logs have mixed owners (some written by root), rotation and new
  lines can stop. Fix ownership on the server; lesson 7.1 explained why.

## Hands-on exercise

Use your own training installation, signed in to the administration as an administrator.

1. Open **Setup**, then **Log files**. Read the name of the open file at the top.
2. In the navigator, open `404.log` (in the **Kernel** group). Note the address in your browser: it is the log's
   permalink.
3. In a second tab, open two addresses of your training site that do not exist. Back in the viewer, watch the new
   lines arrive; press **Pause**, then **Newest**.
4. Open `error.log` again. In the search form, choose **All kernel logs**, tick **Include rotated files** and search
   for the word `not found` (or a word your trainer gives you).
5. Narrow the search to the levels **Error** and **Warning** and a time range covering the last hour.
6. Press **Copy permalink** and paste the address into a new tab. The same search opens.
7. Open **Error digest**. Switch the window between **1 h**, **24 h** and **168 h** and read the most frequent
   message.
8. Open **Log health**. Read **Disk use**, **Fastest growing** and **Not writable by the web server**.
9. Open **Manage logs** and read the limits that apply to `debug.log`. Do not press any action button.
10. In a terminal, compare with the command line: `php bin/php/console exp:logs --search="not found" --scope=kernel --limit=5`.

## Expected result

- Step 3: the new 404 lines appear without reloading the page, each with the masked client address.
- Step 6 opens the same filtered view from the permalink alone.
- Step 7 shows groups of messages with counts and a last-seen time; numbers and ids in messages are set aside and
  shown as `#` or `?`.
- Step 8 shows a disk use figure against the budget (256 MB by default) and, ideally, no file in **Not writable by
  the web server**.
- The trainer checks by asking a learner to share their permalink from step 6 in the class chat; it opens masked for
  everyone with `setup/logs`.

## Quiz

1. Which policy is needed for **Show unmasked**?
   A. `setup/logs` B. `setup/managelogs` C. `setup/unmasklogs` D. `audit/read`
2. What does **Error digest** show?
3. A colleague needs to see the lines you found. What do you send?
4. True or false: on a new installation, **Rotate now** is available on every log.
5. Which page warns that some logs were written by root and cannot be written by the web server?

<details>
<summary>Answers</summary>

1. **C.** ([The log viewer, Who may use it](../../../../features/6.0/log-viewer.md#who-may-use-it)).
2. The distinct errors and warnings of the last 24 hours (or another window), grouped and counted, most frequent first
   ([Error digest](../../../../features/6.0/log-viewer.md#error-digest)).
3. The permalink from **Copy permalink**; it opens the same view, always masked.
4. **False.** Rotate, empty and remove are off until `logviewer.ini [ActionSettings]` switches them on.
5. **Log health** ([Log health](../../../../features/6.0/log-viewer.md#log-health)).

</details>

## Further reading

- [The log viewer](../../../../features/6.0/log-viewer.md): every area, setting and keyboard shortcut.
- [Log rotation and pruning](../../../../features/6.0/log-rotation.md): what **Manage logs** does.
- [Finding an error by its reference](../../../../guides/finding-an-error-by-its-reference.md): the next lesson's
  guide.

## Related pages

- [Module 7: Logs and the audit trail](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
