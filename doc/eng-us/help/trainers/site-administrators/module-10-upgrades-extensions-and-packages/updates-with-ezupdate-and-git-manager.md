---
title: "Lesson 10.3: Updates with ezupdate and git_manager"
audience: trainer, admin
category: training
order: 103
summary: Learners use the two administration tools for updating an installation without a shell, ezupdate (Setup > Updates and packages) and git_manager (Setup > Git Manager), with checks and dry runs that change nothing, and decide which tool fits which installation. 45 minutes.
keywords: site administration, ezupdate, Updates and packages, Check for updates, Preview (dry run), Install updates, AllowUpdate, Composer, git_manager, Git Manager, Fetch now, upstream, update/ezupdate, update/manage
type: help
---
# Lesson 10.3: Updates with ezupdate and git_manager

Not every administrator has a shell. Two extensions bring updating into the administration: **ezupdate** runs
Composer (check for updates, dry run, guarded update, package search), and **git_manager** shows where a git checkout
stands against its remote and pulls or switches branches. Both are built around checks that change nothing first.
This lesson teaches them with those checks only.

> **Note**
> Both tools can rewrite the code of the installation. In class, use only the actions this lesson names, which change
> nothing, and only on your own training installation.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 10: Upgrades, extensions and packages](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate to advanced |
| Audience | Site administrators, hosting support staff |
| Prerequisites | [Lesson 10.1](planning-and-running-an-upgrade.md); an administrator account; for the full exercise, a training installation with `ezupdate` and, if it is a git checkout, `git_manager` active |

## Learning objectives

After this lesson, learners can:

- explain what ezupdate and git_manager each do, and which installation each one fits;
- check for updates and preview an update with ezupdate without changing anything;
- explain why updating and installing are off by default and which policies a role needs;
- read the upstream card of git_manager: up to date, behind, ahead or diverged;
- name the safety rules both tools follow (backup first, dry run, never a forced push).

## Trainer's notes

### How to present it

Sources: [ezupdate: updates and packages in the admin](../../../../features/6.0/extensions/ezupdate.md),
[git_manager: deploy and back up from the admin](../../../../features/6.0/extensions/git_manager.md) and
[Automated updates with ezupdate for hosts](../../../hosting/operations/automated-updates-with-ezupdate.md).

1. **Two kinds of installation** (3 minutes). An installation managed with Composer (`composer.json`,
   `composer.lock`, `vendor/`) is updated through Composer: ezupdate. An installation that is a git checkout is
   updated through git: git_manager. The extension list (**Setup > Extensions**) marks git checkouts.
2. **ezupdate** (6 minutes). **Setup > Updates and packages** (`/update/dashboard`). The **Overview** shows where
   Composer is, whether updating and installing are allowed, **Check for updates** (`composer outdated`, changes
   nothing), **Preview (dry run)**, **Install updates** after a backup confirmation, and **Recent runs**. Updates
   run in the background, one at a time, with Composer's output followed live. Afterwards the autoloads are
   regenerated and caches cleared.
3. **Off by default** (2 minutes). `ezupdate.ini [UpdateSettings] AllowUpdate` and `AllowInstall` are `disabled`
   until someone switches them on (the page has **Switch on updates**). Policies: `update/ezupdate` to look, check and
   dry run; `update/manage` to change the installation.
4. **git_manager** (6 minutes). **Setup > Git Manager** (`/git_manager/dashboard`). The upstream card shows the
   remote, the branch, the last fetch and a badge (**up to date**, **behind**, **ahead**, **diverged**), with the
   missing commits. **Fetch now** runs `git fetch --prune` and nothing else. Push is never forced. Every fetch is
   audited.
5. **The rules** (3 minutes). Backup first (Module 9), dry run before the real run, read the output, verify as in
   lesson 10.1.

### Common questions

**Is ezupdate part of every installation?** It is an extension (`se7enxweb/ezupdate`), activated in
`ActiveExtensions`. Check **Setup** on your training installation: the entry **Updates and packages** is there only
when it is active.

**Can ezupdate install new extensions?** Yes, through **Find packages** and `composer require`, when `AllowInstall`
is enabled. A dry run first.

**What does "diverged" mean in git_manager?** The installation has commits the remote lacks and the remote has commits
the installation lacks. Do not pull blindly; find out who committed on the server.

### Pitfalls

- **Switching on updates in class.** Leave `AllowUpdate` disabled; the dry run teaches everything that matters.
- **A training installation that is neither.** One built from an archive is neither a git checkout nor fully managed
  by Composer in the same way. Show the tools on the projector installation instead.
- **Running Composer as root.** Files created by root in `vendor/` and `var/` can break the site for the web server's
  user.

## Hands-on exercise

Use your own training installation if it has the extensions; otherwise follow on the trainer's projector
installation. Every step below only reads.

1. Open **Setup** and check whether **Updates and packages** and **Git Manager** are in the left column. Note which
   tools your installation has.
2. Open **Setup > Extensions** and find whether the installation's extensions are marked as git checkouts.
3. Open **Setup > Updates and packages**. Read **Summary** and **Composer**: where Composer is, which PHP runs it,
   whether updates are allowed.
4. Press **Check for updates**. Read the list of outdated packages, if any.
5. Press **Preview (dry run)**. Follow the output and read the Kind column: which packages *Can be installed* and which
   are *Blocked by composer.json*.
6. Open **Recent runs** and find your dry run with its result.
7. Read the shell equivalent and, if you have a shell, run it:

   ```bash
   php extension/ezupdate/bin/php/ezupdate.php status
   php extension/ezupdate/bin/php/ezupdate.php outdated
   ```

8. If the installation is a git checkout with git_manager: open **Setup > Git Manager** and read the upstream card:
   remote, branch, last fetch and badge. Press **Fetch now** and read the badge again.
9. Write down, for your training installation, which tool you would use for a real update and why.

## Expected result

- Step 4 lists outdated packages or says the installation is up to date; nothing changed.
- Step 5 shows Composer's dry run output, with installable and blocked packages; nothing changed.
- Step 8 shows a badge and, after **Fetch now**, the time of the last fetch updated; no code changed.
- The trainer checks the answer of step 9 with two learners: Composer-managed installation, ezupdate; git checkout,
  git_manager; a shell remains the fallback for both.

## Quiz

1. Which button of ezupdate changes nothing?
   A. **Install updates** B. **Check for updates** C. **Switch on updates** D. **Run again** of a real run
2. Why can a fresh installation not update from the administration at once?
3. Which policy lets a role look, check and dry run, but not change the installation?
4. What does **Fetch now** in git_manager do?
5. The upstream badge says **diverged**. What do you do before pulling?

<details>
<summary>Answers</summary>

1. **B.** It runs `composer outdated`
   ([ezupdate, How an update goes](../../../../features/6.0/extensions/ezupdate.md#how-an-update-goes)).
2. `AllowUpdate` and `AllowInstall` are disabled until an administrator switches them on.
3. `update/ezupdate`; `update/manage` is needed to change the installation.
4. `git fetch --prune` on the remote, and nothing else: no pull, merge, reset or checkout.
5. Find out which commits exist only on the installation (who committed on the server, and why) before anything else.

</details>

## Further reading

- [ezupdate: updates and packages in the admin](../../../../features/6.0/extensions/ezupdate.md).
- [git_manager: deploy and back up from the admin](../../../../features/6.0/extensions/git_manager.md).
- [Update with git](../../../update-with-git.md): updating a git checkout from a shell.
- [Automated updates with ezupdate for hosts](../../../hosting/operations/automated-updates-with-ezupdate.md).

## Related pages

- [Module 10: Upgrades, extensions and packages](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
