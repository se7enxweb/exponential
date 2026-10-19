---
title: "Lesson 10.2: The upgrade check"
audience: trainer, admin
category: training
order: 102
summary: Learners run the file and database consistency checks of Setup > Upgrade check, read each kind of finding, change one file on purpose to see it reported, and get the same answer from a shell with checkmanifest.php. 35 minutes.
keywords: site administration, upgrade check, Setup Upgrade check, systemupgrade, Check file consistency, Check database consistency, filelist.md5, Modified, Missing, checkmanifest.php, schema
type: help
---
# Lesson 10.2: The upgrade check

Before an upgrade an administrator needs to know whether anyone changed the installation's files or database by hand:
an upgrade replaces changed files, and a database that drifted from the schema can make the update fail.
**Setup > Upgrade check** answers both questions with two checks that only read. This lesson teaches the page, its
findings and the command line equivalent.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 10: Upgrades, extensions and packages](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators, support staff |
| Prerequisites | [Lesson 10.1](planning-and-running-an-upgrade.md); an administrator account and a shell |

## Learning objectives

After this lesson, learners can:

- run **Check file consistency** and **Check database consistency** and read the result line;
- tell the problems (**Modified**, **Missing**, **Unreadable**) from the notes that do not stop an upgrade;
- explain what to do with a modified file before an upgrade;
- download a report as CSV or text;
- run the same file check with `php bin/php/checkmanifest.php --all --extensions`.

## Trainer's notes

### How to present it

Source: [The upgrade check: files and database before an upgrade](../../../../guides/upgrade-check.md). The page
described is the one of the admin4 design.

1. **The way in** (1 minute). **Setup**, then **Upgrade check** (`/setup/systemupgrade`), policy `setup/setup`. The
   page reminds you to back up first.
2. **The overview** (2 minutes). The version, the number of files in `share/filelist.md5`, when the manifest last
   changed, the active extensions with a manifest of their own, and the database engine.
3. **The file check** (3 minutes). The result line: *File consistency check OK.* (green), a warning with the numbers
   of modified, missing and unreadable files (amber), or a red line when it cannot run. Then the figures, the
   manifests read, a search, and one group per state with **What it means and what to do**.
4. **The database check** (2 minutes). Findings grouped by table, each with the SQL the engine would need. The SQL is
   text to read; nothing is run. Differences the engine writes no SQL for are engine notes.
5. **What to do** (2 minutes). A modified file is overwritten by an upgrade: move the change into an extension or an
   override first, or make sure it is part of the new release.

### Common questions

**Does the check change anything?** No. No file is written and no SQL is run.

**Why do I see files I changed on purpose?** The check lists every file that differs from the release. Expected; the
point is to know them before the upgrade.

**Are extensions checked?** Those with their own `share/filelist.md5`, and the ones shipped inside the release by the
release's manifest. Active extensions with neither are named as **not checked**.

### Pitfalls

- **A development checkout.** A training installation built from a branch between releases can show many modified
  files. Explain it rather than "fixing" them.
- **Forgetting to undo the exercise.** Step 5 changes a file; step 8 restores it.
- **Running the shell check from the wrong directory.** The paths in the list are relative to the document root.

## Hands-on exercise

Use your own training installation, in the browser and in a terminal at the installation root.

1. Open **Setup > Upgrade check**. Read **Overview**: version, files in the manifest, database engine.
2. Press **Check file consistency**. Read the result line and the figures.
3. Open **What the file check reads** and find which extensions are checked and which are **not checked**.
4. Press **Check database consistency**. Read the result and any engine notes.
5. Change one listed file on purpose: copy `index.php` to `index.php.orig`, then add one empty line at the end of
   `index.php` (`echo >> index.php`).
6. Press **Check file consistency** again. Find `index.php` under **Modified** and read **What it means and what to
   do**.
7. Run the same check from the shell:

   ```bash
   php bin/php/checkmanifest.php --all --extensions
   ```

8. Restore the file: `mv index.php.orig index.php`, and run the check once more.
9. Press **Download text** and open the report.

## Expected result

- Step 2 shows a result line and figures; on a release installation ideally *File consistency check OK.*
- Step 6 lists `index.php` as **Modified**, with the first characters of the listed and current checksums.
- Step 7 reports the same file.
- After step 8 the file is no longer listed.
- The trainer checks that every learner's `index.php` was restored (step 8) and the site still answers.

![The upgrade check: file and database consistency](../../../images/trainers/sa-upgrade-check.webp)

## Quiz

1. Which finding stops a safe upgrade?
   A. **Not listed** B. **Malformed lines** C. **Modified** D. **Out of order**
2. Does **Check database consistency** run the SQL it shows?
3. What happens to a file you changed in place when you upgrade?
4. Which file holds the checksums of a release?
5. Which command gives the file findings from a shell?

<details>
<summary>Answers</summary>

1. **C.** Modified, missing and unreadable files are problems; the others are notes
   ([The upgrade check, In short](../../../../guides/upgrade-check.md#in-short)).
2. No. Both checks only read; the SQL is text for you to read.
3. The upgrade replaces it, and your change is lost unless you moved it into an extension or an override first.
4. `share/filelist.md5` (and an extension's own `share/filelist.md5`).
5. `php bin/php/checkmanifest.php --all --extensions`.

</details>

## Further reading

- [The upgrade check](../../../../guides/upgrade-check.md): every part of the page and each finding.
- [File consistency check and the release file list](../../../../features/6.0/file-consistency-check.md).
- [Quality checks](../../../../features/6.0/quality-checks.md).

## Related pages

- [Module 10: Upgrades, extensions and packages](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
