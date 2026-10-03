# Kickstarter: install a whole site from one file

This page is for administrators who install the same site more than once, or on several servers. The setup wizard
asks for the database, the languages, the site package, the administrator and more. The Kickstarter takes all of those
answers from one file, `kickstart.ini`, so the same installation can be repeated with one command. Added on 12 July
2026 (`34c17cdbbe`). Full reference: [Kickstarter CLI](../../bc/6.0/kickstartercli.md); this page is the short version.

## Install in three steps

```bash
# 1. write kickstart.ini from the template (asks questions; --yes accepts defaults)
php bin/php/console exp:kickstarter ini

# 2. check the answers and the remote packages without changing anything
php bin/php/console exp:kickstarter run --dry-run

# 3. install (needs --force because it can drop the database)
php bin/php/console exp:kickstarter run --force
```

See the steps before you run them:

```bash
php bin/php/kickstarter.php run --list-steps
```

`bin/php/kickstarter.php` is the same program without the console.

## Commands and options

Checked with `./console help exp:kickstarter` on 6.0.15:

| Command | Options |
|---|---|
| `ini` | `--defaults` (copy `kickstart.ini-dist`), `--yes` |
| `run` | `--start-step`, `--stop-step`, `--dry-run`, `--list-steps`, `--force` |
| `help` | |

Every run is logged to `var/log/kickstart.log` with passwords masked. `EXP_KICKSTART_LOG=0` turns the log off.

## Things to know

- **`kickstart.ini` is an example, not a default.** A `kickstart.ini` in the root applied to every web setup and would
  have conflicted with a clean install. On 16 and 19 July (`d0530e1deb`, `f9df1f6854`) the file became
  `kickstart.ini--example`, and the template `kickstart.ini-dist` generates yours. Keep your own `kickstart.ini` out of
  version control: it holds the database password.
- **Remote packages can be tested.** `--dry-run` downloads and checks the packages named by the site type into a
  package repository named `dryrun` (`kernel/setup/steps/ezstep_site_types.php`) and stops before `CreateSites`, so the
  database is not written.
- **One progress line.** The scripts use `expScriptStatus` (renamed from `eZPMStatus`), so the web setup and the
  console report progress the same way.
- **Local packages first.** When a package exists in the installation, the site type step uses it instead of
  downloading it (15 August, `28d414f422`).
- **Siteaccess names.** Whatever access type you choose (hostname, port or URL), the siteaccesses are named `site` and
  `admin` (15 August, `67e2abf9a3`). Earlier runs produced names such as `<identifier>_user` that did not exist.
- **More settings**, such as the editor siteaccess, were added later; see the
  [setup wizard page](setup-wizard-and-editor-siteaccess.md).

## Related pages

- [Install in one command](install-in-one-command.md): needs no `kickstart.ini`
- [Clean install defaults](clean-install-defaults.md)
- [ezpm package manager CLI](ezpm-package-manager-cli.md): installs single packages
- [A package installer that survives big packages](package-installer-batching.md)
- [Look inside a package, compare it with your site, import single items](package-compare-and-import.md)
- [Default extension distribution](default-extension-distribution.md)
- [Static cache generator](static-cache-generator.md)
- [Installer logs and seed data](../../specifications/6.0/installer-logs-and-seed-data.md)
- [Behaviour changes of July and August 2026](../../bc/6.0/behaviour-changes-2026-07-08.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- History: [June 2026, second half](../../history/2026/2026-06b.md), [July 2026](../../history/2026/2026-07.md), [August 2026](../../history/2026/2026-08.md)
