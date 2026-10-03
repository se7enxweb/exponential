# Kickstarter: install a whole site from one file

The setup wizard asks for the database, the languages, the site package, the
administrator and more. The Kickstarter answers all of those from a file,
`kickstart.ini`, so the same installation can be repeated on any server with one
command. It was added on 12 July 2026 (`34c17cdbbe`) and documented the same day
in [Kickstarter CLI](../../bc/6.0/kickstartercli.md), the full reference. This page
is the short version.

```bash
# 1. write kickstart.ini from the template (asks questions; --yes accepts defaults)
php bin/php/console exp:kickstarter ini

# 2. check the answers and the remote packages without changing anything
php bin/php/console exp:kickstarter run --dry-run

# 3. install (needs --force because it can drop the database)
php bin/php/console exp:kickstarter run --force
```

`bin/php/kickstarter.php` is the same program without the console. Checked with
`./console help exp:kickstarter` on 6.0.15: commands `ini`, `run`, `help`; run options
`--start-step`, `--stop-step`, `--dry-run`, `--list-steps` and `--force`; ini options `--defaults`
(copy `kickstart.ini-dist`) and `--yes`. Every run is logged to `var/log/kickstart.log` with passwords masked
(`EXP_KICKSTART_LOG=0` turns the log off). Try `php bin/php/kickstarter.php run --list-steps` to see the steps.

## What July added around it

- **`kickstart.ini` is an example, not a default.** A `kickstart.ini` in the root
  applied to every web setup and would have conflicted with a clean install. On 16 and 19 July
  (`d0530e1deb`, `f9df1f6854`) the file became `kickstart.ini--example`, and the
  template `kickstart.ini-dist` generates yours. Keep your own `kickstart.ini`
  out of version control; it holds the database password.
- **Remote packages can be tested.** `--dry-run` downloads and checks the packages
  named by the site type into a package repository named `dryrun` (`kernel/setup/steps/ezstep_site_types.php`) and stops before `CreateSites`, so the database is not written.
- **A shared progress line.** The scripts use `expScriptStatus` (renamed from
  `eZPMStatus`, so the web setup and the console report progress the same way).
- **Local packages first.** When a package exists in the installation the site type
  step uses it instead of downloading it (15 August, `28d414f422`).
- **Site access names.** Whatever access type you choose (hostname, port or URL),
  the siteaccesses are named `site` and `admin` (15 August, `67e2abf9a3`). Earlier
  runs produced names such as `<identifier>_user` that did not exist.
- **More settings** (such as the editor siteaccess) were added later, see the
  [setup wizard page](setup-wizard-and-editor-siteaccess.md).

## Related

- [Clean install defaults](clean-install-defaults.md)
- [ezpm](ezpm-package-manager-cli.md) installs single packages; [Install in one command](install-in-one-command.md) needs no `kickstart.ini`.
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md); [Behaviour changes of July and August 2026](../../bc/6.0/behaviour-changes-2026-07-08.md)
- Month pages: [July 2026](../../history/2026/2026-07.md), [August 2026](../../history/2026/2026-08.md).
