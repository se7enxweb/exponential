---
title: "Lesson 6.2: Installing a multisite with exp:install"
audience: trainer, admin, developer
category: training
order: 62
summary: Learners install a fresh site with host matching in one command, check it first with --print and --dry-run, read the summary and the password file, and test the three siteaccesses with curl. 55 minutes.
keywords: multisite and site packages, exp:install, bin/php/install.php, --print, --dry-run, --access=host, --host, --admin-host, --editor-host, --url, --package, --db-action, EXP_INSTALL_DB_PASSWORD, initial-admin-password, exit status, summary
type: help
---
# Lesson 6.2: Installing a multisite with exp:install

`exp:install` installs Exponential with one command and no file to write first. Every option has a default, so the
command alone installs the default site package on SQLite. In this lesson learners give it the options a multisite
needs, host names for the site, the administration and the Editor, check them before anything is written, and read
what the command reports.

> **Note**
> Run this lesson in a fresh code directory, never in the installation of modules 1 to 5 and never on a live site.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 6: Installing sites with the kickstarter and exp:install](README.md) |
| Duration | 55 minutes (15 presenting, 40 exercise; the install itself takes a few minutes) |
| Level | Intermediate |
| Audience | Administrators, developers and hosts |
| Prerequisites | [Lesson 6.1](one-engine-three-front-ends.md); a fresh directory with the code ([Train the trainer, Lesson 2.2](../../train-the-trainer/module-02-building-the-lab/getting-the-code-for-a-class.md)) |

## Learning objectives

After this lesson, learners can:

- install with `php bin/php/console exp:install` and the options for host matching;
- check the configuration with `--print` and the whole run with `--dry-run`;
- read the summary, find the generated password and remove the password file after the first login;
- explain the guard against overwriting an installation, `--force` and `--db-action`.

## Trainer's notes

### How to present it

Source: [7. The console install: exp:install](../../../../install/07-console-install.md).

1. **What it does** (4 minutes). It checks every option before anything is written, chooses the administrator
   password, builds the eleven sections of a `kickstart.ini`, puts it in place (an existing one is moved aside), runs
   the kickstarter, saves the configuration masked as `var/log/exp-install-<date>.ini`, deletes the temporary file and
   prints a summary.
2. **The multisite options** (5 minutes). `--access=host` with `--host` and `--admin-host` (both required, and
   different); the Editor gets `edit.<host without www.>` unless `--editor-host` says otherwise. `--url` is the
   address every siteaccess's links are built from: always set it. `--package` chooses the site package
   (`sevenx_multisite` by default; `sevenx_multisite_clean` without demo content).
3. **Checks before the run** (3 minutes). `--print` shows the configuration, passwords masked, and writes nothing;
   `--dry-run` checks the database and fetches the packages into a temporary repository, then stops: "Dry-run
   completed: database and remote packages verified. Stopped after SiteDetails, nothing written."
4. **The password and the guard** (3 minutes). Without `--password`, a 24-character password is generated, shown in
   the summary and written to `var/log/initial-admin-password` (mode 0600). Over an existing installation the command
   refuses unless `--force`; the default `--db-action=remove` then empties the database. For database servers, pass
   the password in `EXP_INSTALL_DB_PASSWORD`, not as an option visible in the process list.

### Common questions

**Why `--url` on a laptop?** Without it every address points to `http://localhost`, which is right only when the site
is opened on the same machine.

**Does `exp:install` start a web server?** No. The addresses in the summary work once something serves the
installation root, for example Velocity as in Train the trainer lesson 2.4.

**Can it use MySQL or PostgreSQL?** Yes: `--db=mysql` or `--db=pgsql` with `--db-host`, `--db-name` and `--db-user`;
the database must exist first. SQLite needs nothing.

**What is the exit status good for?** `0` means installed (or a passed dry run, `--print`, `--help`); `1` anything
else, so a script can test `$?`.

### Pitfalls

- **Running as root.** Files created by root cannot be changed by the web server later. Run as the user that serves
  the site.
- **A value with a space.** `--db mysql` is refused: every value needs `=`.
- **Short options.** `-f` is refused; the command knows long options only.
- **Losing the password.** It is shown once. It is also in `var/log/initial-admin-password` until you delete it.

## Hands-on exercise

In a fresh code directory, for example `multisite-course`, as the user that will serve the site.

1. Print the configuration for host matching:

   ```bash
   php bin/php/console exp:install --print --access=host \
       --host=www.club.localhost --admin-host=admin.club.localhost \
       --url=http://www.club.localhost:8087 --title="Club" --email=webmaster@example.invalid
   ```

2. In the output, find `Access`, `AccessHostname`, `AdminAccessHostname`, `EditorAccessHostname`, `Site_package`
   and `DatabaseAction`.
3. Try a mistake: run the same command with `--admin-host=www.club.localhost`. Read the message and `echo $?`.
4. Run the same command as in step 1 with `--dry-run` instead of `--print`. Wait for the "Dry-run completed" line.
5. Install: the same command without `--print` or `--dry-run`. Note the time it takes.
6. Read the summary: the site, the admin login, the editor address, the password and its note, the siteaccesses.
7. Run the install command of step 5 a second time and read the refusal. Nothing was touched.
8. Serve the directory on port 8087 as in Train the trainer lesson 2.4, then test the three hosts:

   ```bash
   curl -sI -H 'Host: www.club.localhost' http://127.0.0.1:8087/ | head -1
   curl -sI -H 'Host: admin.club.localhost' http://127.0.0.1:8087/ | head -1
   curl -sI -H 'Host: edit.club.localhost' http://127.0.0.1:8087/ | head -1
   ```

9. Read the host map the install wrote:
   `php bin/php/console exp:ini get 'site.ini/SiteAccessSettings/HostMatchMapItems[]'`.
10. Sign in to the administration (browser or `curl`), change the administrator password, then delete
    `var/log/initial-admin-password`.

## Expected result

- Step 2: `Access=hostname` in `[site_access]`, the three host names, the editor at
  `edit.club.localhost`, `Site_package=sevenx_multisite`.
- Step 3 stops with "--host and --admin-host must differ" and exit status 1.
- Step 5 ends with the summary block "Exponential is installed".
- Step 7: "This directory already holds an installation ... Re-run with --force ..." and exit status 1.
- Step 8: the public host answers `200` (or a redirect to the start page), the administration and the Editor answer
  with their sign-in pages or a redirect to them.
- Step 9 lists the three hosts with `site`, `admin` and `editor`.
- The trainer checks step 6 and step 8 on one learner's screen.

## Quiz

1. Which two options does `--access=host` require?
2. What does `--print` do that `--dry-run` does not, and the other way round?
3. Where is a generated administrator password written, and what do you do with the file?
4. You run `exp:install` again in the same directory. What happens?
   A. It reinstalls B. It refuses without `--force` C. It upgrades D. It asks a question
5. Why pass a database password in `EXP_INSTALL_DB_PASSWORD`?

<details>
<summary>Answers</summary>

1. `--host` and `--admin-host`, and they must differ.
2. `--print` shows the configuration without touching the database or the network; `--dry-run` also connects to the
   database and fetches the packages, then stops before anything is written
   ([7.6](../../../../install/07-console-install.md#76-dry-run-and-print)).
3. In `var/log/initial-admin-password` (mode 0600); change the password after the first login and delete the file.
4. **B.**
5. An option is visible to every user of the machine in the process list and stays in the shell history.

</details>

## Further reading

- [7. The console install: exp:install](../../../../install/07-console-install.md).
- [Installing Exponential in one command](../../../../features/6.0/install-in-one-command.md).
- [exp:install for hosts](../../../hosting/provisioning/exp-install.md).
- [8. Serving the site](../../../../install/08-serving-the-site.md) and
  [10. After installing](../../../../install/10-after-installing.md).

## Related pages

- [Module 6: Installing sites with the kickstarter and exp:install](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
