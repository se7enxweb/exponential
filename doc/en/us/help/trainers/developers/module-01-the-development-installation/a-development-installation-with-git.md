---
title: "Lesson 1.2: A development installation with Git"
audience: trainer, developer
category: training
order: 12
summary: Learners get the kernel and its extensions as Git clones, install a site on SQLite with exp:install, start a web server with Velocity and sign in. 60 minutes.
keywords: developers course, git clone, composer install, preferred-install source, exp:install, sqlite, velocity, development installation, my-dev branch
type: help
---
# Lesson 1.2: A development installation with Git

Developers work on an installation of their own where they can break things. In this lesson every learner builds
one: the kernel and every extension as Git clones, a database in one SQLite file, and a web server started with
one command. Everything later in the course runs on this installation.

> **Note**
> The clone and `composer install` download several hundred megabytes. In a classroom with a slow connection,
> prepare a copy of a finished clone on each machine beforehand and start at step 5.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 1: The development installation](README.md) |
| Duration | 60 minutes (15 presenting, 45 exercise) |
| Level | Beginner |
| Audience | PHP developers new to Exponential |
| Prerequisites | [Lesson 1.1](the-developers-map.md); PHP, Composer 2 and Git 2.28 or later on the learner's machine |

## Learning objectives

After this lesson, learners can:

- configure Composer to install the `se7enxweb/*` packages as Git clones;
- clone the kernel, start a branch of their own and run `composer install`;
- install a site with `php bin/php/console exp:install` and find the generated administrator password;
- start the built-in web server with `exp:velocity start` and sign in to the administration;
- explain why the development installation never shares settings or data with a live site.

## Trainer's notes

### How to present it

Sources: [Develop with Git](../../../develop-with-git.md),
[Setting up a development installation](../../../extension-development/part-1-from-user-to-developer/setting-up-a-development-installation.md)
and [7. The console install](../../../../install/07-console-install.md).

1. **Why clones** (4 minutes). A release archive has the files of one version; a clone has the history.
   `git log`, `git blame` and `git bisect` answer "why is this line here" and "which change broke it", and a
   branch keeps your own changes. Show the table in [Develop with Git](../../../develop-with-git.md#why-git-clones-and-not-archives).
2. **The one Composer setting** (2 minutes). `composer config --global 'preferred-install.se7enxweb/*' source`
   makes every `se7enxweb/*` package a clone and everything else a fast archive.
3. **`exp:install`** (5 minutes). With no options it installs the `sevenx_multisite` site package on SQLite in
   `eng-US` and ends with a summary: site address, administration login, user `admin` and a generated password,
   also written to `var/log/initial-admin-password` (readable by its owner only).
4. **Serving** (4 minutes). `settings/velocity.ini` ships with `[ServerSettings] Engine=php`, PHP's built-in
   server with the router `bin/php/velocity-router.php`; `[PHPServerSettings] Port=8087`.

### Common questions

**"HEAD detached" in an extension: is something broken?** No. Composer checks out the exact release tag that
`composer.json` asks for. Start a branch before changing anything.

**Do I need a GitHub account?** Only to send changes back (lesson 13.3). Installing needs none.

**Can I use MySQL instead of SQLite?** Yes: `exp:install` takes database options, listed in
[7.3 Options](../../../../install/07-console-install.md#73-options). SQLite needs no database server, which keeps a
classroom simple.

**Why `./console`?** Many developers make a link, `ln -s bin/php/console console`. It is not part of the
distributed files; this course writes `php bin/php/console` in full.

### Pitfalls

- **Running everything as root.** Commands refuse to run as root unless `--allow-root-user` is given. Let learners
  work as their own user.
- **`exp:install` over an existing installation.** It refuses without `--force`, and its default
  `--db-action=remove` empties the database it is given. Never point it at a database that matters.
- **A busy port.** If port 8087 is taken, the start fails; the trainer changes `Port` in a settings override, or
  stops the other server.

## Hands-on exercise

Each learner works on their own machine (or their own folder on a shared training server).

1. Tell Composer to prefer clones for the project's packages:

   ```bash
   composer config --global 'preferred-install.se7enxweb/*' source
   composer config --global preferred-install
   ```

2. Clone the kernel and start your branch:

   ```bash
   git clone https://github.com/se7enxweb/exponential.git exponential
   cd exponential
   git switch -c my-dev
   ```

3. Install the packages: `composer install`.
4. Check that the extensions are clones: `ls -d extension/*/.git | wc -l`.
5. Look at the configuration without writing anything:

   ```bash
   php bin/php/console exp:install --print --url=http://127.0.0.1:8087 --title="Developer course"
   ```

6. Install: `php bin/php/console exp:install --url=http://127.0.0.1:8087 --title="Developer course"`.
7. Copy the password from the summary to a safe place.
8. Start the web server: `php bin/php/console exp:velocity start`.
9. Open the administration login address from the summary and sign in as `admin`.
10. Change the administrator password in the administration, then remove `var/log/initial-admin-password`.
11. Run `git status` in the installation root and read which files the installation created.

## Expected result

- Step 4 prints a number greater than zero.
- Step 6 ends with the box "Exponential is installed", listing the site, admin and editor addresses.
- Step 9 shows the administration dashboard.
- Step 11 lists files such as `settings/override/` as untracked: the installation's own settings, never to be
  pushed to a public repository because they hold host-specific values.
- The trainer checks by asking each learner to show the dashboard and the output of `git branch` (`* my-dev`).

## Quiz

1. What does `composer config --global 'preferred-install.se7enxweb/*' source` change?
2. Which database does `exp:install` use when no option says otherwise?
   A. MySQL B. PostgreSQL C. SQLite D. MongoDB
3. Where is the generated administrator password written besides the summary?
4. True or false: `exp:install` installs over an existing installation without asking.
5. Why must `settings/override/` never be copied from the live site to the development installation?

<details>
<summary>Answers</summary>

1. Composer installs every `se7enxweb/*` package as a Git clone and everything else as an archive.
2. **C.** SQLite, in one file below `var/storage/sqlite3/`.
3. `var/log/initial-admin-password`, readable by its owner only; delete it after changing the password.
4. **False.** It refuses without `--force`.
5. It holds database passwords and host-specific values; each machine keeps its own.

</details>

## Further reading

- [Develop with Git](../../../develop-with-git.md): clones, branches, keeping up to date.
- [Setting up a development installation](../../../extension-development/part-1-from-user-to-developer/setting-up-a-development-installation.md):
  the same steps with debug settings.
- [7. The console install](../../../../install/07-console-install.md): every option of `exp:install`.
- [8. Serving the site](../../../../install/08-serving-the-site.md): Velocity, Apache, nginx and FrankenPHP.

## Related pages

- [Module 1: The development installation](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
