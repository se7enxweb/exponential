# Exponential Console — `bin/php/console`

**Introduced:** Exponential CMS 6.0.15  
**Location:** `bin/php/console`  
**Type:** PHP CLI script — command dispatcher

---

## What is it?

`console` is a single entry-point for every command-line script that ships with
Exponential CMS, modeled on Symfony's `bin/console`.

Instead of memorizing dozens of paths under `bin/php/`, `bin/shell/`, or deep
inside extensions, you type one thing:

```
php bin/php/console <command> [options...]
```

`console` auto-discovers every script on the system at runtime — **no
configuration required**. When a new script appears in a supported location it
shows up in the listing automatically.

---

## Prerequisites

You must run all commands from the **Exponential CMS root directory** (the
folder that contains `index.php`, `autoload.php`, `bin/`, etc.).

```bash
# Confirm you are in the right place — you should see index.php
ls index.php
```

You need:
- **PHP 7.4 or 8.x** on your `$PATH` (type `php -v` to check)
- **curl** and **wget** on your `$PATH` (needed by specific sub-scripts, not by
  `console` itself)

---

## Quick-start

```bash
# Show all available commands
php bin/php/console

# Same — list is the default view
php bin/php/console list

# Check the console version
php bin/php/console --version
```

---

## Listing commands

```bash
# List every command on the system
php bin/php/console list

# List only Exponential PHP scripts  (bin/php/*.php)
php bin/php/console list exp

# List only shell scripts  (bin/shell/*.sh)
php bin/php/console list shell

# List only root bin executables  (bin/*.sh, bin/*.php)
php bin/php/console list bin

# List commands provided by a specific extension
php bin/php/console list ext:hcaptcha
```

Sample output:

```
════════════════════════════════════════════════════════════════════
  Exponential Console  1.0.0  (eZ Publish 4 / Exponential CMS 6.x)
  With great power comes great responsibility.
════════════════════════════════════════════════════════════════════

exp   — bin/php/*.php  (Exponential PHP scripts)
──────────────────────────────────────────────────
  exp:ezcache          Exponential Cache Handler
  exp:preload          Exponential CMS — site preloader & cache warmer
  exp:updatesearchindex  Exponential search index updater.
  ...
```

---

## Getting help for a command

Every sub-script that supports `--help` can be reached through the console:

```bash
# Option A — the "help" sub-command
php bin/php/console help exp:preload

# Option B — --help before the command name
php bin/php/console --help exp:preload

# Option C — --help after the command name (passed through directly)
php bin/php/console exp:preload --help
```

All three produce the same output: the full usage block printed by the script
itself.

---

## Running a command

```bash
php bin/php/console <namespace>:<name> [options...]
```

Before the script runs, the console prints a brief dispatch notice to
**stderr** so it never corrupts piped output:

```
  ▶  running exp:ezcache  →  bin/php/ezcache.php
```

### Common commands — copy and run

```bash
# Clear all caches (safe, very common operation)
php bin/php/console exp:ezcache --clear-all

# Regenerate PHP autoload arrays after adding a new extension or class
php bin/php/console exp:ezpgenerateautoloads

# Rebuild the full-text search index
php bin/php/console exp:updatesearchindex

# Warm site caches for the default siteaccess
php bin/php/console exp:preload

# Warm site caches for a specific siteaccess
php bin/php/console exp:preload --siteaccess=sevenx_site_user

# Fix directory and file permissions after a deployment
php bin/php/console bin:modfix

# Run the session garbage collector
php bin/php/console exp:ezsessiongc
```

---

## Command namespaces

| Namespace | What it maps to | Example |
|---|---|---|
| `exp:<name>` | `bin/php/<name>.php` | `exp:ezcache` |
| `shell:<name>` | `bin/shell/<name>.sh` | `shell:phpcheck` |
| `bin:<name>` | `bin/<name>.sh` or `bin/<name>.php` | `bin:modfix` |
| `ext:<ext>:<name>` | `extension/<ext>/bin/php/<name>.php` | `ext:hcaptcha:install` |
| `ext:<ext>:sh:<name>` | `extension/<ext>/bin/shell/<name>.sh` | `ext:myext:sh:setup` |

### Bare-name shortcut

If you leave out the namespace, the console will try `exp:`, then `shell:`,
then `bin:` automatically:

```bash
# These two are identical
php bin/php/console ezcache --clear-all
php bin/php/console exp:ezcache --clear-all
```

---

## Passing arguments and options to sub-scripts

Everything after the command name is passed verbatim to the sub-script:

```bash
# Pass --siteaccess to preload
php bin/php/console exp:preload --siteaccess=sevenx_site_user

# Dry-run a cleanup to see what would be deleted (no actual changes)
php bin/php/console exp:cleanupversions --dry-run

# Pass multiple flags
php bin/php/console exp:updatesearchindex --siteaccess=sevenx_site_user --verbose
```

---

## Console-level flags

These flags are consumed by `console` itself and are **not** forwarded to
sub-scripts:

| Flag | Effect |
|---|---|
| `--version` or `-V` | Print the console version and exit |
| `--help` or `-h` (bare) | Show the command listing |
| `--quiet` or `-q` | Suppress the dispatch notice on stderr |

---

## "Did you mean?" suggestions

If you mistype a command name the console will suggest the closest match:

```
$ php bin/php/console exp:ezcach

  Unknown command: exp:ezcach
  Did you mean:    exp:ezcache?
```

---

## Extension commands

Any extension that ships scripts in one of the following locations is
discovered automatically:

```
extension/<extname>/bin/php/<name>.php      → ext:<extname>:<name>
extension/<extname>/bin/shell/<name>.sh     → ext:<extname>:sh:<name>
extension/<extname>/bin/<name>.sh           → ext:<extname>:<name>
```

```bash
# List all commands from the hcaptcha extension
php bin/php/console list ext:hcaptcha

# Run an extension install script
php bin/php/console ext:hcaptcha:install --siteaccess=sevenx_site_user
```

---

## Adding a description to your own script

When `console list` shows `(no description)` next to a script, add a
`@description` comment near the top of the file.

**PHP script** — inside the opening docblock:

```php
<?php
/**
 * File containing my-script.php
 *
 * @description One-line summary shown in console list
 * @long-description Longer explanation for console help output.
 */
```

**Shell script** — on the second line (after the shebang):

```bash
#!/bin/bash
# @description One-line summary shown in console list
# @long-description Longer explanation for console help output.
```

Both tags are picked up automatically — no registration step needed.

---

## Troubleshooting

**`PHP Fatal error: ... autoload.php` — cannot find bootstrap**  
You are not running the command from the Exponential root directory. `cd` there
first:

```bash
cd /path/to/your/exponential-root
php bin/php/console list
```

**`Running scripts as root may be dangerous`**  
Exponential's eZScript component warns when a script is run as `root`. To
acknowledge and continue:

```bash
php bin/php/console exp:ezcache --clear-all --allow-root-user
```

**`Unknown command: exp:myscript`**  
The script was not found in the expected location. Check that the file exists at
`bin/php/myscript.php` and is readable. Run `php bin/php/console list` to see
what was discovered.

**Colors not showing**  
Colors require a terminal that supports ANSI escape codes. If running inside a
script or redirecting output, colors are suppressed automatically.

---

## Technical notes

- The console **does not** call `eZScript::startup()` or `eZScript::initialize()`
  for itself. Each sub-script owns its own lifecycle, including the optional
  database connection.
- All sub-scripts are executed by the same PHP binary that runs the console
  (`which php`).
- Exit codes from sub-scripts are propagated to the caller unchanged, making the
  console safe to use in shell pipelines and CI/CD scripts.
