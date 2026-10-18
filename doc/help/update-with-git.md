---
title: "Update with Git: stay in sync with GitHub"
audience: user, developer, admin
category: development
order: 6
summary: Get Exponential's updates as they are pushed to GitHub, then update Composer to match, on the schedule you choose (hourly, daily, weekly, monthly, or only at each stable release), with the commands to see what changed, the safety steps, and an automatic update you can switch on.
keywords: update, upgrade, git, pull, fetch, merge, composer update, prefer-source, release, tag, main, cron, systemd, timer, schedule, daily, hourly, weekly, monthly, automatic, stay in sync
type: help
---
# Update with Git: stay in sync with GitHub

Once your site is made of Git clones ([Develop with Git](develop-with-git.md)), updating is two moves: bring the
kernel up to date with Git, then let Composer bring every extension up to date to match. This page shows both, for
the schedule that suits you, how to see what changed, how to stay safe, and how to let the computer do it for you.

Every command here was run on a fresh installation first: a kernel clone on a branch `my-site` made from release
`v6.0.13`, updated to the release `v6.0.14` and then to the development line `main`.

## Choose how closely you follow

| You want | Follow | Good for |
|---|---|---|
| every change as soon as it is pushed | `main`, the development line: check **hourly** or **daily** | development machines, staging sites, people who test and contribute |
| changes in batches | `main`, checked **weekly** or **monthly** | sites that change often but want quiet weeks |
| only finished, tested versions | the newest **release tag** (`v6.0.14`, ...) | live sites |

Following `main` gets fixes the day they land; a release gets them later, after they have been tested together.
Release tags are on the history of `main`, so you can start with releases and move to `main` later (or the other way
round, once the next release has caught up).

## The update, step by step

Run these in the root of the installation, as the user that owns the files (not as root).

### 1. See what is waiting

```bash
git status
git fetch origin --tags
git log --oneline HEAD..origin/main
git tag -l 'v6.*' --sort=version:refname | tail -3
composer outdated 'se7enxweb/*'
```

`git status` should report no changes of your own that are not committed. `git log HEAD..origin/main` lists the
commits on GitHub that your site does not have yet; the tag list shows the newest releases, the newest last (always
sort by version: by name, `v6.0.10` comes before `v6.0.8`). `composer outdated` names the extensions with a newer
release than the one installed.

### 2. Bring the kernel up to date

Following the development line:

```bash
git merge origin/main
```

Following releases only, to the newest release tag:

```bash
git merge "$(git tag -l 'v6.*' --sort=version:refname | tail -1)"
```

`git merge` keeps your own commits on `my-site` and adds the new ones. If your branch has no commits of its own, the
merge simply moves forward. When one of your commits and an update change the same lines, Git stops with
`CONFLICT` and leaves the choice to you; `git merge --abort` puts everything back as it was.

If you work on a clone without a branch of your own (`git status` says `HEAD detached at v6.0.14`), switch to the new
release instead:

```bash
git switch --detach "$(git tag -l 'v6.*' --sort=version:refname | tail -1)"
```

Start a branch (`git switch -c my-site`) before you change anything.

### 3. Bring the extensions up to date

```bash
composer update
```

`composer.json` in the kernel names the extensions and their versions, and it changes as the kernel moves on: on the
way from `v6.0.14` to `main` it added 39 packages and updated 11. `composer update` reads the new `composer.json`,
clones the new extensions (each with its history, thanks to the setting of
[Develop with Git](develop-with-git.md#step-1-tell-composer-to-prefer-clones)), moves the existing clones to their
new release tags, and regenerates the class maps at the end. Use `composer update`, not `composer install`: the
lock file is your own (it is not part of the kernel's repository), so after the kernel moved it is out of date.

**About `--prefer-source`.** Composer chooses between a clone and an archive when it **first installs** a package.
An update keeps that choice: a clone stays a clone, an archive stays an archive, and `composer update --prefer-source`
does not turn archives into clones. With the `preferred-install` setting, every new `se7enxweb/*` package arrives as
a clone anyway. To turn an existing archive into a clone, use `composer reinstall <package>`
([Develop with Git](develop-with-git.md#the-extensions)).

Composer never overwrites your work in a clone. When an extension has changes you did not commit, or commits on a
branch that exists only on your machine, it stops with a message instead; [When Composer
refuses](develop-with-git.md#when-composer-refuses) says what to do. Fix the cause and run `composer update` again
to finish.

### 4. Finish

```bash
php bin/php/ezcache.php --clear-all
```

On a site served by Exponential Velocity, `php bin/php/console exp:velocity deploy` does everything a code change
needs instead (class maps, caches, PHP-FPM, a restart of Velocity, in the right order); see
[Velocity engines](../bc/6.0/velocity-engines.md). A release can also need a **database update**: read
[Upgrading](../install/11-upgrading.md#step-2-apply-the-database-update) for the release you moved to, before the
site goes back to work.

### 5. See what changed

```bash
git log --oneline ORIG_HEAD..HEAD
git -C extension/eztags log --oneline -5
```

After a merge, `ORIG_HEAD` is where you were before it, so the first command lists every commit the update brought.
Composer's own output lists each extension it moved (`Upgrading se7enxweb/eztags (v2.4.20 => v2.4.21)`), and the
second command shows the latest changes of one of them.

In the administration:

- **Help** > **Recently updated** on the help dashboard lists the documentation pages that changed most recently,
  with their commit message.
- With the [git_manager](../features/6.0/extensions/git_manager.md) extension, its **git dashboard** (**Setup**)
  shows where the installation stands against GitHub, the commits it is missing, and a commit log you can filter by
  author and date.
- **Setup** > **Upgrade check** compares every file with the release's checksums and the database with the
  schema; see [The upgrade check](../guides/upgrade-check.md).

## Stay safe

- **Back up first**: the database and the folder, or a [git_manager](../features/6.0/extensions/git_manager.md)
  backup ("DB + Files caption").
- **Try it on a copy first.** Run the update on a staging copy of the site, look around, run the
  [upgrade check](../guides/upgrade-check.md), and only then update the live site, ideally to the same commit
  (`git merge <the commit you tested>`).
- **Read before a release**: the [changelog](../changelogs/6.0/6.0.15.md) of the version you move to, and
  [Upgrading](../install/11-upgrading.md) for its database and settings steps.
- **Going back**: `git reset --hard ORIG_HEAD` right after a merge returns the kernel to where it was, and
  `composer update` then moves the extensions back with it (from `main` back to `v6.0.14` it removed the 39 packages
  `main` had added and moved 11 back). Restore the database from your backup if a database
  update already ran.

## Let it update itself

When you have done the update by hand a few times and trust it, a small script can do it on your schedule. Test it by
hand on a staging copy first, and keep the live site on releases. An automatic update never runs database updates,
and it stops instead of guessing when something needs a person.

Save this as `/usr/local/bin/exp-update` and make it executable (`chmod +x /usr/local/bin/exp-update`):

```sh
#!/bin/sh
# exp-update: bring an Exponential installation made of Git clones up to date.
#   exp-update release   the newest release tag of the 6.x line (v6.*)
#   exp-update main      the development line, origin/main
# Stops without changing anything when the kernel has uncommitted changes or the merge needs a hand.
set -eu
SITE=${EXP_SITE:-/var/www/exponential}
MODE=${1:-release}
cd "$SITE"
if [ -n "$(git status --porcelain --untracked-files=no)" ]; then
    echo "exp-update: the kernel has uncommitted changes; commit or stash them first" >&2
    exit 1
fi
git fetch --quiet origin --tags
if [ "$MODE" = main ]; then
    TARGET=origin/main
else
    TARGET=$(git tag -l 'v6.*' --sort=version:refname | tail -1)
fi
if git merge-base --is-ancestor "$TARGET" HEAD; then
    echo "exp-update: already up to date with $TARGET"
    exit 0
fi
if ! git merge --no-edit "$TARGET"; then
    git merge --abort
    echo "exp-update: merging $TARGET needs a hand; nothing was changed" >&2
    exit 1
fi
composer update --no-interaction --no-progress
php bin/php/ezcache.php --clear-all
echo "exp-update: now at $(git describe --tags --always)"
```

Set `EXP_SITE` to your installation's folder. What it does, as tested:

| Situation | What `exp-update` does |
|---|---|
| nothing new | prints `already up to date with v6.0.14` (or `origin/main`) and stops |
| something new | merges it, runs `composer update` and clears the caches, then prints `now at ...` |
| the kernel has uncommitted changes | changes nothing and exits with an error |
| a merge conflict with your own commits | aborts the merge, changes nothing and exits with an error |
| Composer refuses an extension (local changes, unpushed commits) | stops with Composer's message; run `composer update` by hand once the cause is fixed |

On a site served by Velocity, replace the `ezcache.php` line with `php bin/php/console exp:velocity deploy`.

### With cron

Edit the crontab of the user that owns the files (`crontab -e`) and add **one** of these lines:

```text
# every hour, the development line
5 * * * *   EXP_SITE=/var/www/exponential /usr/local/bin/exp-update main >> $HOME/exp-update.log 2>&1
# every day at 04:15, the development line
15 4 * * *  EXP_SITE=/var/www/exponential /usr/local/bin/exp-update main >> $HOME/exp-update.log 2>&1
# every Monday at 04:15, the development line
15 4 * * 1  EXP_SITE=/var/www/exponential /usr/local/bin/exp-update main >> $HOME/exp-update.log 2>&1
# on the first of every month at 04:15, the development line
15 4 1 * *  EXP_SITE=/var/www/exponential /usr/local/bin/exp-update main >> $HOME/exp-update.log 2>&1
# every day at 04:15, but only when a new release has been published
15 4 * * *  EXP_SITE=/var/www/exponential /usr/local/bin/exp-update release >> $HOME/exp-update.log 2>&1
```

Checking daily in `release` mode updates only on the day a new release appears; on every other day it prints
`already up to date`.

### With a systemd timer

Two files in `/etc/systemd/system/`. `exp-update.service`:

```ini
[Unit]
Description=Update the Exponential installation from GitHub
Wants=network-online.target
After=network-online.target

[Service]
Type=oneshot
User=www-data
Environment=EXP_SITE=/var/www/exponential
ExecStart=/usr/local/bin/exp-update release
```

and `exp-update.timer`:

```ini
[Unit]
Description=Update the Exponential installation from GitHub, daily

[Timer]
OnCalendar=daily
RandomizedDelaySec=30min
Persistent=true

[Install]
WantedBy=timers.target
```

Use the user that owns your files in `User=`, and `main` in `ExecStart=` to follow the development line.
`OnCalendar=` takes `hourly`, `daily`, `weekly` (Mondays), `monthly` (the first of the month) or a time such as
`*-*-* 04:15:00`; `systemd-analyze calendar weekly` shows when it would run. Then:

```bash
systemctl daemon-reload
systemctl enable --now exp-update.timer
systemctl list-timers exp-update.timer
journalctl -u exp-update.service
```

The last two show when it runs next and what it said last time.

## Where to go next

- [Develop with Git](develop-with-git.md): installing as clones, working on a branch, sending fixes back.
- [Contribute extensions back](contribute-extensions-back.md): publish your own extensions so everyone can use them, and what the GPL requires when you distribute one.
- [Upgrading](../install/11-upgrading.md): the database and settings steps of each release.
- [The upgrade check](../guides/upgrade-check.md) and [git_manager](../features/6.0/extensions/git_manager.md).
- The project on GitHub: [se7enxweb/exponential](https://github.com/se7enxweb/exponential) and its
  [releases](https://github.com/se7enxweb/exponential/releases).
