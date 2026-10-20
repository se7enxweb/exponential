---
title: "Lesson 6.3: The kickstarter and kickstart.ini"
audience: trainer, admin, developer
category: training
order: 63
summary: Learners write kickstart.ini with exp:kickstarter ini --yes, review the values marked REVIEW, set port matching for three siteaccesses, run a dry run and an install, and read kickstart.log and setup.log. 55 minutes.
keywords: multisite and site packages, kickstarter, exp:kickstarter, kickstart.ini, ini --yes, run --dry-run, run --force, --list-steps, REVIEW, Continue=true, site_details, site_access, DatabaseAction, kickstart.log, setup.log, maintenance mode
type: help
---
# Lesson 6.3: The kickstarter and kickstart.ini

The kickstarter installs from one file, `kickstart.ini`, that can be reviewed, kept and run again. That makes it the
method for installations that must be repeated exactly: staging copies, test systems, training labs. Learners write
the file, review it, set port matching for the site, the administration and the Editor, and install from it.

> **Note**
> `kickstart.ini` holds passwords in clear text. Keep it at mode 0600, never commit it, and move it out of the
> installation root after the install.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 6: Installing sites with the kickstarter and exp:install](README.md) |
| Duration | 55 minutes (15 presenting, 40 exercise) |
| Level | Advanced |
| Audience | Administrators, developers and hosts |
| Prerequisites | [Lesson 6.2](installing-with-exp-install.md); another fresh code directory |

## Learning objectives

After this lesson, learners can:

- write `kickstart.ini` with `exp:kickstarter ini --yes` and review the values marked `# REVIEW`;
- explain the sections, `Continue=true` and the keys of `[site_access]` and `[site_details]`;
- check a file with `run --list-steps` and `run --dry-run`, and install with `run --force`;
- read `var/log/kickstart.log` and `var/log/setup.log`, and handle the maintenance page after a failure.

## Trainer's notes

### How to present it

Source: [6. The kickstarter](../../../../install/06-kickstarter.md).

1. **Two subcommands** (3 minutes). `ini` writes or edits `kickstart.ini` (interactively, or with `--yes` from
   built-in defaults, or with `--defaults` from `kickstart.ini-dist`); `run` installs from it. Both are also
   `php bin/php/kickstarter.php`.
2. **The file** (5 minutes). One section per step; the command line needs every section from `[email_settings]` to
   `[site_admin]`, each with `Continue=true`. No whitespace before a key, and a comment after a value becomes part of
   the value. `--yes` writes `Type=sqlite3`, `Site_package=sevenx_multisite`, `Access=url`, `DatabaseAction=ignore`
   and an empty `URL`; the header lists the values to review (both `Database` keys, `DatabaseAction`, `URL`, the
   administrator's `Email` and `Password`).
3. **Access** (3 minutes). `[site_access] Access` is `url`, `port` or `hostname`, written as `MatchOrder=uri`,
   `port` or `host`. `[site_details]` holds the values for each type: `Access`, `AdminAccess`, `EditorAccess` for
   paths; `AccessPort`, `AdminAccessPort`, `EditorAccessPort` for ports; the `...Hostname` keys for hosts. Give all
   three of a type to be sure.
4. **Running** (4 minutes). `run --list-steps` prints the step table; `run --dry-run` runs `DatabaseChoice` to
   `SiteDetails`, imports the package into a temporary repository and writes nothing; `run --force` installs.
   Without `--force` a run that includes `CreateSites` stops. While it works the site shows the maintenance page; after
   a failed step it stays in maintenance mode until the next successful run or `php bin/php/maintenance.php off`.

### Common questions

**What does `DatabaseAction` do?** `remove` empties the database first, `ignore` adds to what is there (right for a
new, empty database), `skip` inserts nothing and only rewrites the settings. Lesson 6.4 is about it.

**Why is `URL` empty after `--yes`?** The defaults guess nothing about your address; empty means `http://localhost`.
Always set it.

**Can I start at a later step?** `--start-step` is for diagnosis only; a start that needs results of earlier steps is
refused, with the earliest step that works.

**I edited the file and see old values.** `ini` and `run` delete the cached copies `var/cache/ini/kickstart-*.php`
themselves; the browser wizard does not.

### Pitfalls

- **`--from-installed`.** It fills the database keys from an existing installation: the file then points at that
  installation's database. Do not use it in class.
- **An indented key.** It is not read, and the step fails for a missing value.
- **The administrator password.** An empty, short or well-known password is never installed; the run generates one,
  prints it once and writes `var/log/initial-admin-password`.

## Hands-on exercise

In a second fresh code directory, for example `kickstart-course`, as the user that will serve the site.

1. Write the file from the built-in defaults:
   `php bin/php/console exp:kickstarter ini --yes`. Check the mode: `ls -l kickstart.ini` shows `-rw-------`.
2. Read the header and every `# REVIEW` line.
3. Edit `kickstart.ini` in a text editor:
   - `[site_access]`: `Access=port`;
   - `[site_details]`: `Title=Kickstart course`, `URL=http://127.0.0.1:9000`, `AccessPort=9000`,
     `AdminAccessPort=9001`, `EditorAccessPort=9002`;
   - `[site_admin]`: `Email=webmaster@example.invalid`, `Password` left empty.
4. Show the steps: `php bin/php/console exp:kickstarter run --list-steps`.
5. Run without `--force`: `php bin/php/console exp:kickstarter run`. Read the message and the exit status.
6. Dry run: `php bin/php/console exp:kickstarter run --dry-run`. Wait for "Dry-run completed".
7. Install: `php bin/php/console exp:kickstarter run --force`. Note "Maintenance mode on" at the start and the summary
   at the end, with the generated password.
8. Read the port settings the run wrote:
   `php bin/php/console exp:ini list site.ini/PortAccessSettings global` and
   `php bin/php/console exp:ini get site.ini/SiteAccessSettings/MatchOrder`.
9. Read the logs: `tail -n 40 var/log/setup.log` and `grep -c '\*\*\*\*\*\*\*\*' var/log/kickstart.log` (the masked
   passwords).
10. Move `kickstart.ini` out of the installation root to a safe place (or delete it, if your trainer says so).

## Expected result

- Step 1: a `kickstart.ini` with mode 0600 and a header listing the values to review.
- Step 5 stops with "The CreateSites step will modify the database and site settings." and "Re-run with --force ...",
  exit status 1.
- Step 7 prints the summary with the site package, the title and the URLs, and the generated password once.
- Step 8 shows `9000`, `9001` and `9002` mapped to the site, the administration and the Editor, and
  `MatchOrder=port` (`uri;port` when the `exp_adminui` extension is present, for its `/adminui` path).
- Step 9: `setup.log` ends with the result of the run; `kickstart.log` holds the passwords only masked.
- The trainer checks steps 7 and 8 on one screen.

## Quiz

1. Which subcommand writes `kickstart.ini`, and which installs from it?
2. What must every section from `[email_settings]` to `[site_admin]` contain for the command line?
3. What does `run` say without `--force` when the steps include `CreateSites`?
   A. Nothing; it installs B. It stops and asks for `--force` C. It runs a dry run D. It opens the editor
4. How is `Access=port` written into the settings?
5. Why keep `kickstart.ini` at mode 0600 and out of the installation root after the install?

<details>
<summary>Answers</summary>

1. `ini` writes it, `run` installs from it.
2. `Continue=true` ([6.3](../../../../install/06-kickstarter.md#63-how-each-step-behaves-on-the-command-line)).
3. **B.**
4. As `MatchOrder=port`, with the ports in `[PortAccessSettings]`.
5. It holds passwords in clear text, and a browser wizard started later would read it.

</details>

## Further reading

- [6. The kickstarter](../../../../install/06-kickstarter.md): every key, example files, CI and containers.
- [Kickstarter: install a whole site from one file](../../../../features/6.0/kickstarter-cli.md).
- [The kickstarter for hosts](../../../hosting/provisioning/kickstarter.md).
- [Maintenance mode](../../../../features/6.0/maintenance-mode.md).

## Related pages

- [Module 6: Installing sites with the kickstarter and exp:install](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
