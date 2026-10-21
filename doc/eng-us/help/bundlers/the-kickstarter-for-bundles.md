---
title: The kickstarter for bundles
audience: bundler, hosting, developer
category: partnership
order: 210
summary: How a bundle uses the kickstarter - kickstart.ini in the installation root, the ini and run commands with their real options, the sections of the file, DatabaseAction, the registration report a bundle should leave off, the generated password, re-runs and clean-up.
keywords: kickstarter, kickstart.ini, kickstart.ini-dist, kickstart.ini--example, exp:kickstarter, kickstarter.php, ini, run, --yes, --defaults, --from-installed, --dry-run, --list-steps, --start-step, --stop-step, --force, DatabaseAction, registration, Send
type: help
---
# The kickstarter for bundles

This page is for bundlers who install from a reviewed configuration file. The kickstarter "runs the step classes in
one PHP process on the command line and reads every answer from `kickstart.ini`"
([The kickstarter for hosts](../hosting/provisioning/kickstarter.md#what-it-is)). The full reference is
[Chapter 6: The kickstarter](../../install/06-kickstarter.md).

## Contents

1. [Where the file lives](#where-the-file-lives)
2. [Commands and options](#commands-and-options)
3. [The sections of kickstart.ini](#the-sections-of-kickstartini)
4. [DatabaseAction](#databaseaction)
5. [Settings a bundle should set on purpose](#settings-a-bundle-should-set-on-purpose)
6. [Re-runs and clean-up](#re-runs-and-clean-up)

## Where the file lives

`kickstart.ini` belongs in the **root of the installation**, next to `index.php`. The repository ships two templates
there, not a live file:

| File | Role |
|---|---|
| `kickstart.ini-dist` | the template `exp:kickstarter ini --defaults` copies; "Copy this file as kickstart.ini to the root of your Exponential installation" (its own header) |
| `kickstart.ini--example` | an example, renamed in July 2026 so that it does not apply to every web setup ([Kickstarter](../../features/6.0/kickstarter-cli.md#things-to-know)) |

Never ship a filled `kickstart.ini` inside a bundle: "it holds the database password", and "a browser wizard started
later would read it" ([6.10](../../install/06-kickstarter.md#610-re-running-and-resuming)).

## Commands and options

Checked with the command's help on 6.0.15 (`kernel/private/classes/commands/kickstarter.php`):

| Command | Options |
|---|---|
| `php bin/php/console exp:kickstarter ini` | `--yes` (built-in defaults), `--defaults` (copy `kickstart.ini-dist`), `--from-installed` (take the database server, name and user, not the password, of an installed siteaccess) |
| `php bin/php/console exp:kickstarter run` | `--dry-run`, `--list-steps`, `--start-step`, `--stop-step`, `--force` |

`bin/php/kickstarter.php` is the same program without the console. `--force` "is required whenever the range of steps
includes `CreateSites`". A call without a command shows the help and exits `0`, "so a script that forgot the subcommand
does not fail; check that your scripts name `run`" ([The kickstarter for hosts](../hosting/provisioning/kickstarter.md#the-commands)).

## The sections of kickstart.ini

The blocks listed in `kickstart.ini-dist`: `email_settings`, `database_choice`, `database_init`, `language_options`,
`site_types`, `site_access`, `site_details`, `site_admin`, `security`, `registration`. Each has `Continue=true` to use
the values without asking. The keys are on [6.4.2 Sections and keys](../../install/06-kickstarter.md#642-sections-and-keys).

| Section | Keys a bundle usually sets |
|---|---|
| `[database_choice]` | `Type` (`sqlite3` default, `mysqli`, `pgsql`, ...) |
| `[database_init]` | `Server`, `Port`, `Database`, `User`, `Password`, `Socket` |
| `[language_options]` | `Primary` (for example `eng-US`), `Languages[]` |
| `[site_types]` | `Site_package` |
| `[site_access]` | `Access` (`url`, `port`, `hostname`) |
| `[site_details]` | `Title`, `URL`, `Database`, `DatabaseAction`, the access values |
| `[site_admin]` | `FirstName`, `LastName`, `Email`, `Password` (leave empty to generate) |

## DatabaseAction

> **Warning**
> "`DatabaseAction=remove` destroys data. It empties the database named in `[site_details] Database` and installs
> into it ... `--force` is the only confirmation" ([6.5](../../install/06-kickstarter.md#65-databaseaction-read-this-before-you-run)).

"A value that is missing or misspelled behaves like `ignore`": write it explicitly in every template you ship
([The kickstarter for hosts](../hosting/provisioning/kickstarter.md#databaseaction)).

## Settings a bundle should set on purpose

| Setting | Why |
|---|---|
| `[site_admin] Password=` empty | the installer generates a random 20-character password and writes it to `var/log/initial-admin-password` ([The default administrator password](../default-administrator-password.md#the-kickstarter-kickstartini)) |
| `[site_admin] Email` | a real address of the recipient, so password resets reach someone |
| `[site_details] URL` | "Always set it"; the command-line default is `http://localhost` |
| `[registration] Send=false` | `true` "sends a registration report about the installation (system, PHP and database details) through the configured mail transport"; a bundle must not do that without the recipient's consent |
| `[email_settings]` | the transport the recipient's site will use; leave mail unconfigured rather than pointing at your own server |

## Re-runs and clean-up

- "The same `kickstart.ini` with `DatabaseAction=remove` and `run --force` replaces the database and the settings
  again. Use it to reset a test or training site." That is how a classroom bundle resets.
- `DatabaseAction=skip` with `run --force` "rewrite[s] `settings/siteaccess/` and `settings/override/` from the file
  and leave[s] the database alone".
- After the install: "Move `kickstart.ini` out of the installation root, or delete it", and deliver and remove
  `var/log/initial-admin-password`.
- Logs to keep from a failed run: `var/log/kickstart.log` (passwords masked; `EXP_KICKSTART_LOG=0` turns it off) and
  `var/log/setup.log`.

## Related pages

- [kickstart.ini templates](kickstart-ini-templates.md)
- [The kickstarter for hosts](../hosting/provisioning/kickstarter.md)
- [Kickstarter: install a whole site from one file](../../features/6.0/kickstarter-cli.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
