# Exponential Console: `bin/php/console`

`bin/php/console` is one entry point for every command line script of Exponential 6.0.15, modelled on Symfony's
`bin/console`. Read this page if you run scripts by hand or from deploy and cron jobs: you no longer need to remember
paths under `bin/php/`, `bin/shell/` or inside extensions.

## In short

| | |
|---|---|
| What changed | New dispatcher `bin/php/console`. It finds every script at run time (no configuration) and runs it as `<namespace>:<name>`. Since June 2026 it also runs cronjob parts (`cron:<part>`) and shows the crontab (`crontab:list`, `crontab:edit`). |
| Who is affected | Nobody is forced to change. Every script keeps its path; `php bin/php/ezcache.php ...` works as before. |
| How to check | `php bin/php/console --version` |
| How to fix | Nothing to fix. Use `php bin/php/console list` to find a command. |

## Start in one minute

Run every command from the Exponential root directory, the one that holds `index.php`, `autoload.php` and `bin/`.
You need PHP 8.0 or later on your `$PATH` (`php -v`); the `exp:velocity` commands need 8.1, as Velocity does. Some sub-scripts also need `curl` and `wget`; the console itself
does not.

```bash
ls index.php                      # confirms you are in the right directory
php bin/php/console               # list every command (same as "list")
php bin/php/console --version     # print the console version
```

Expected start of the listing:

```
════════════════════════════════════════════════════════════════════
  Exponential Console  1.0.0  (Exponential CMS 6.x)
  With great power comes great responsibility.
════════════════════════════════════════════════════════════════════

exp   — bin/php/*.php  (Exponential PHP scripts)
──────────────────────────────────────────────────
  exp:ezcache          Exponential Cache Handler
  exp:preload          Exponential CMS — site preloader & cache warmer
  exp:updatesearchindex  Exponential search index updater.
  ...
```

## Common commands

```bash
# Clear all caches
php bin/php/console exp:ezcache --clear-all

# Everything Setup > Cache does, with --dry-run and PASS/FAIL (see cache-console.md)
php bin/php/console exp:cache --help
php bin/php/console exp:cache clear --tag=ini --dry-run

# Regenerate the autoload arrays after adding an extension or a class
php bin/php/console exp:ezpgenerateautoloads

# Rebuild the search index
php bin/php/console exp:updatesearchindex

# Warm the caches of the default siteaccess, or of one siteaccess
php bin/php/console exp:preload
php bin/php/console exp:preload --siteaccess=sevenx_site_user

# Fix directory and file permissions after a deployment
php bin/php/console bin:modfix

# Run the session garbage collector
php bin/php/console exp:ezsessiongc

# Measure the site: page timings, two servers back to back, the kernel in-process (see benchmark.md)
php bin/php/console exp:benchmark
php bin/php/console exp:benchmark --compare=https://www.example.com,https://www.example.com:8080
php bin/php/console exp:benchmark kernel --save=var/benchmark/base.json
```

The benchmark command is described in [Benchmark](../../features/6.0/benchmark.md).

Before a script runs, the console prints a short notice to **stderr**, so piped output stays clean:

```
  ▶  running exp:ezcache  →  bin/php/ezcache.php
```

## Find a command

```bash
php bin/php/console list              # every command
php bin/php/console list exp          # bin/php/*.php
php bin/php/console list shell        # bin/shell/*.sh
php bin/php/console list bin          # bin/*.sh and bin/*.php
php bin/php/console list cron         # cronjob parts
php bin/php/console list ext:hcaptcha # the commands of one extension
```

If you mistype a name, the console suggests the closest match:

```
$ php bin/php/console exp:ezcach

  Unknown command: exp:ezcach
  Did you mean:    exp:ezcache?
```

## Help for a command

All three forms print the usage block of the script itself:

```bash
php bin/php/console help exp:preload
php bin/php/console --help exp:preload
php bin/php/console exp:preload --help
```

## Command names

| Namespace | Runs | Example |
|---|---|---|
| `exp:<name>` | `bin/php/<name>.php` | `exp:ezcache` |
| `shell:<name>` | `bin/shell/<name>.sh` | `shell:phpcheck` |
| `bin:<name>` | `bin/<name>.sh` or `bin/<name>.php` | `bin:modfix` |
| `ext:<ext>:<name>` | `extension/<ext>/bin/php/<name>.php` or `extension/<ext>/bin/<name>.sh` | `ext:hcaptcha:install` |
| `ext:<ext>:sh:<name>` | `extension/<ext>/bin/shell/<name>.sh` | `ext:myext:sh:setup` |
| `cron:<part>` | the cronjob part `<part>` through `runcronjobs.php` | `cron:frequent` |
| `crontab:list`, `crontab:edit` | the system crontab of the current user | |

Without a namespace, the console tries `exp:`, then `shell:`, then `bin:`. These two are the same:

```bash
php bin/php/console ezcache --clear-all
php bin/php/console exp:ezcache --clear-all
```

## Options

Everything after the command name goes to the script unchanged:

```bash
php bin/php/console exp:preload --siteaccess=sevenx_site_user
php bin/php/console exp:cleanupversions --dry-run
php bin/php/console exp:updatesearchindex --siteaccess=sevenx_site_user --verbose
```

These flags belong to the console and are not passed on:

| Flag | Effect |
|---|---|
| `--version`, `-V` | print the console version and exit |
| `--help`, `-h` (on its own) | show the command listing |
| `--quiet`, `-q` | no dispatch notice on stderr |

## Cronjob parts and the crontab (added June 2026)

Commit `a2ef4efa50`.

A cronjob part is a `[CronjobPart-<name>]` block with one or more `Scripts[]=` entries, in `settings/cronjob.ini` or
in an extension's `settings/cronjob.ini.append.php`. The console reads the parts from those INI files, not from file
names, and runs them through `runcronjobs.php`:

```bash
php bin/php/console list cron
php bin/php/console cron:frequent --allow-root-user
php bin/php/console cron:infrequent -s site_admin    # siteaccess flags are passed on
```

The parts of a stock installation are `infrequent`, `frequent`, `contentjobs`, `audit`, `unlock`,
`cluster_maintenance`, `cleanuprss` and `cache_cleanup`. `list cron` describes each part with the `@description` tags
of the scripts it runs; every core script under `cronjobs/` has one.

To see that the parts are scheduled:

```bash
php bin/php/console crontab:list      # print the system crontab of the current user
php bin/php/console crontab:edit      # open it in $EDITOR
```

The browser view of the same information is the [cronjobs console](../../features/6.0/cronjobs-console.md).

## Extension commands

Scripts in these places are found automatically:

```
extension/<extname>/bin/php/<name>.php      → ext:<extname>:<name>
extension/<extname>/bin/shell/<name>.sh     → ext:<extname>:sh:<name>
extension/<extname>/bin/<name>.sh           → ext:<extname>:<name>
```

```bash
php bin/php/console list ext:hcaptcha
php bin/php/console ext:hcaptcha:install --siteaccess=sevenx_site_user
```

## Describe your own script

When `list` shows `(no description)`, add a `@description` tag near the top of the file. Both tags are read
automatically; there is no registration step.

PHP script, in the opening docblock:

```php
<?php
/**
 * File containing my-script.php
 *
 * @description One-line summary shown in console list
 * @long-description Longer explanation for console help output.
 */
```

Shell script, on the line after the shebang:

```bash
#!/bin/bash
# @description One-line summary shown in console list
# @long-description Longer explanation for console help output.
```

A cronjob script uses the same tag, for example `@description Remove expired baskets of anonymous visitors`.

## Troubleshooting

| Message | Cause and fix |
|---|---|
| `PHP Fatal error: ... autoload.php` | You are not in the Exponential root directory. `cd /path/to/your/exponential-root` and run the command again. |
| `Running scripts as root may be dangerous` | The script runs as `root`. Add `--allow-root-user` to confirm, for example `php bin/php/console exp:ezcache --clear-all --allow-root-user`. |
| `Unknown command: exp:myscript` | The file is not where the namespace expects it. Check that `bin/php/myscript.php` exists and is readable, then run `php bin/php/console list`. |
| No colours | The terminal does not support ANSI codes, or output is redirected; colours are switched off automatically. |

## How it works

- The console does not call `eZScript::startup()` or `eZScript::initialize()` for itself. Each script owns its own
  life cycle, including the database connection.
- Scripts run with the same PHP binary as the console.
- The exit code of the script is returned unchanged, so the console is safe in shell pipelines and CI jobs.

## Related pages

- [Cache console (`exp:cache`)](cache-console.md)
- [Commands, cronjob parts and module views as classes](cli_cronjob_view_abstractions.md)
- [Cronjobs console](../../features/6.0/cronjobs-console.md)
- [June 2026, first half (1 to 15 June)](../../history/2026/2026-06a.md)
- [Operating a site](../../guides/operating-a-site.md)
