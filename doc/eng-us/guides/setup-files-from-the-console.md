# Setting up .htaccess and config.php from the console

This guide is for the person who installs or runs an Exponential site: an administrator, a hosting operator, or a
developer who scripts installations. Two files at the root of every installation are copied from templates that ship
with it, and one of them is edited afterwards:

| Template that ships | File the site uses | What it is for |
|---|---|---|
| `.htaccess_root` | `.htaccess` | The rewrite and protection rules for Apache and LiteSpeed |
| `.htaccess_root_static` | `.htaccess` | The same rules plus the ones that serve the static cache ahead of the CMS |
| `config.php-RECOMMENDED` | `config.php` | The few settings that must be known before the INI files are read: file modes, INI checks, cluster storage, log rotation |

Until now that meant `cp -va .htaccess_root .htaccess`, `cp -va config.php-RECOMMENDED config.php` and then an editor
to take the `//` off the lines you want. The console does all of it for you, safely:

```bash
./console exp:setup:status                          # which template each file comes from
./console exp:setup:htaccess --apply                # .htaccess_root -> .htaccess
./console exp:setup:htaccess --static --apply       # .htaccess_root_static -> .htaccess
./console exp:setup:config --apply                  # config.php-RECOMMENDED -> config.php
./console exp:config:list                           # every setting, its value, the proposed one, what it does
./console exp:config:get EZP_INI_FILEMTIME_CHECK    # one value, for a person or a script
./console exp:config:set EZP_INI_FILEMTIME_CHECK false --apply
./console exp:config:unset EZP_INI_FILEMTIME_CHECK --apply
```

Time: 10 minutes. You need shell access to the installation directory (the folder that holds `index.php` and
`console`). Run the commands as the user that owns the installation. Run as root, every command needs
`--allow-root-user`, as every Exponential command does; the examples below leave it out.

Every result shown on this page was produced by running the command; the installation directory is shown as
`/var/www/example` and its owner as `www-data:www-data`, the user and group of the example installation.

## 1. The rules every command follows

These hold for all seven commands, so you can rely on them in scripts:

- **Nothing is written without `--apply`.** Without it a command shows what it would do and exits 0 (or 1 when it
  would refuse). `--dry-run` is accepted and means the same as leaving `--apply` out; giving both is a usage error,
  exit code 2.
- **An existing file is never replaced silently.** `exp:setup:htaccess` and `exp:setup:config` show the difference
  between the file and the template and refuse (exit 1) unless you add `--force`.
- **Every write is backed up first.** The file as it was goes to `var/backup/setup/<time>/` (copies) or
  `var/backup/config/<time>/` (edits of `config.php`), with its mode, and with its owner and group when the command
  runs as root. The backup directories get mode `0700`, because `config.php` can hold passwords; `var/backup/` is not
  served by the shipped `.htaccess`.
- **A write is atomic.** The new content goes to a temporary file in the same directory, which gets the right mode
  and owner, and is then renamed over the old file. The web server never reads half a file.
- **`config.php` is checked before it is written.** The new file must pass `php -l` and must read back with the value
  you asked for; if not, nothing is changed.
- **Passwords are never printed by accident.** The value of a setting whose name contains `PASS`, `SECRET`, `KEY`,
  `TOKEN`, `CREDENTIAL` or `PWD` is shown as `********`, in values and in diffs, unless you add `--show-secrets`.
- **Exit codes:** 0 done or nothing to do, 1 refused or failed (nothing changed), 2 usage error.
- **Output ends with `PASS:` or `FAIL:`**, so a script or a person sees the result on the last line.

## 2. See where you stand

```bash
./console exp:setup:status
```

On a fresh checkout:

```text
Installation: /var/www/example
  .htaccess   missing: ./console exp:setup:htaccess --apply copies .htaccess_root (with --static: .htaccess_root_static)
  config.php  missing: ./console exp:setup:config --apply copies config.php-RECOMMENDED
PASS: shown
```

Later it says which template `.htaccess` is a copy of (`.htaccess_root`, `.htaccess_root_static`, or neither: changed
by hand or from an older release), and whether `config.php` is an untouched copy, how many settings it sets, or that
it does not compile.

## 3. Put .htaccess into place

Look first:

```bash
./console exp:setup:htaccess
```

```text
Installation: /var/www/example
Would copy .htaccess_root to .htaccess (mode 0644, owner www-data:www-data)
PASS: dry run, nothing changed; --apply copies
```

Then copy. `-v` prints the copy the way `cp -v` does:

```bash
./console exp:setup:htaccess --apply -v
```

```text
Installation: /var/www/example
'.htaccess_root' -> '.htaccess'
PASS: .htaccess_root copied to .htaccess (mode 0644, owner www-data:www-data)
Apache and LiteSpeed read .htaccess on the next request; nginx, Caddy and IIS never read it, and Velocity has the rules of .htaccess_root built in.
```

Running it again changes nothing:

```text
Installation: /var/www/example
PASS: .htaccess is already a copy of .htaccess_root; nothing to do
```

Which web servers read the file:

| Server | Reads `.htaccess`? | What to do instead |
|---|---|---|
| Apache with PHP-FPM or mod_php | yes, with `AllowOverride All` | nothing: `exp:setup:htaccess --apply` is enough |
| LiteSpeed Enterprise | yes, like Apache | nothing more |
| OpenLiteSpeed | only its rewrite rules, only with `autoLoadHtaccess` on, and only after a restart | put the rules in the virtual host: [LiteSpeed](../examples/web-servers/litespeed.md) |
| Exponential Velocity | no, it has the rules of `.htaccess_root` built in | nothing: copy it anyway if Apache may serve the same tree |
| nginx, Caddy, IIS | no | use the example configurations in [the web server examples](../examples/web-servers/README.md) |

### Switching to the static cache rules

When you generate a static cache (Setup > Cache > Static content cache), `.htaccess_root_static` serves those pages
before the CMS starts. Its rules are those of `.htaccess_root` plus the offload rules, so switching is a copy of the
other file. Because `.htaccess` now exists and differs, the command shows the difference and refuses:

```bash
./console exp:setup:htaccess --static
```

```text
Installation: /var/www/example
.htaccess differs from .htaccess_root_static (- what .htaccess has, + what .htaccess_root_static has):
  --- .htaccess
  +++ .htaccess_root_static
  @@ -1,5 +1,27 @@
   # Copy this file to a new file called .htaccess in your Exponential root
  -# to secure your installation by turning on .htaccess based virtual host mode.
  +# to secure your installation by turning on .htaccess based virtual host mode
  +# AND serve the static cache ahead of the CMS.
  ...
  ... 73 more lines of the diff (12 lines removed, 64 added in all); -v shows the whole diff
FAIL: .htaccess exists and differs from .htaccess_root_static; nothing was changed. --force --apply replaces it (the current file is backed up to var/backup/setup/<time>/ first)
```

The diff is cut after 40 lines; `-v` shows all of it. Read it: if your `.htaccess` has rules of your own (a redirect,
an IP block), they are on the `-` lines and will be gone after the copy. Then replace it:

```bash
./console exp:setup:htaccess --static --force           # what would happen
./console exp:setup:htaccess --static --force --apply -v
```

```text
'.htaccess' -> 'var/backup/setup/20261029-130242/.htaccess'
'.htaccess_root_static' -> '.htaccess'
Backup: var/backup/setup/20261029-130242/.htaccess
PASS: .htaccess replaced with .htaccess_root_static (mode 0644, owner www-data:www-data)
```

To go back, run `./console exp:setup:htaccess --force --apply` (the plain rules), or copy the backup back by hand.

## 4. Put config.php into place

```bash
./console exp:setup:config --apply
```

```text
Installation: /var/www/example
PASS: config.php-RECOMMENDED copied to config.php (mode 0644, owner www-data:www-data)
Next: ./console exp:config:list shows its settings, ./console exp:config:set <NAME> <value> sets one. A persistent server (Velocity, FrankenPHP worker) reads config.php again after a restart.
```

A copy of `config.php-RECOMMENDED` sets nothing: every setting in it is a comment. It is the documented starting
point, and the commands below use those comments as the list of known settings.

When `config.php` already exists and differs, the command shows the diff, says how many settings it sets, and
refuses without `--force`, exactly as for `.htaccess`. Passwords in the diff are masked:

```text
config.php differs from config.php-RECOMMENDED (- what config.php has, + what config.php-RECOMMENDED has):
  ...
  -define( 'CLUSTER_STORAGE_PASS', '********' );
  +// define( 'CLUSTER_STORAGE_PASS', '********' );
  ...
  (config.php sets 3 setting(s); ./console exp:config:list --only-set shows them)
FAIL: config.php exists and differs from config.php-RECOMMENDED; nothing was changed. --force --apply replaces it (the current file is backed up to var/backup/setup/<time>/ first)
```

Replacing a working `config.php` with the template drops every setting it had. To start again on purpose, use
`--force --apply`; the old file is in `var/backup/setup/<time>/config.php`.

## 5. Read the settings

```bash
./console exp:config:list
```

lists every setting `config.php-RECOMMENDED` documents, grouped by its section, with the value `config.php` has, the
value the template proposes and the first sentence of its description. Settings that `config.php` defines but the
template does not document come last. A part of the output:

```text
config.php: 0 setting(s) set, 30 documented in config.php-RECOMMENDED

INI FILES OPTIMIZATIONS
  EZP_INI_FILEMTIME_CHECK              (not set)
      RECOMMENDED false - This new setting controls whether eZINI should check if the settings have been updated...

FILE PERMISSIONS
  EZP_INI_FILE_PERMISSION              (not set)
      RECOMMENDED 0644 - Set EZP_INI_FILE_PERMISSION constant to the permissions you want the INI cache files (v...
  EZP_DIR_MODE_MAX                     (not set)
      RECOMMENDED 0750 - Set EZP_DIR_MODE_MAX and EZP_FILE_MODE_MAX to the widest modes any directory and any fi...

CLUSTERING
  CLUSTER_STORAGE_HOST                 (not set)
      RECOMMENDED 'localhost' - Cluster storage host.
  CLUSTER_STORAGE_PASS                 (not set)
      RECOMMENDED '********' - Cluster storage password
  ...

(not set): the built-in default applies. RECOMMENDED: the value config.php-RECOMMENDED proposes, an optimized value or an example. ./console exp:config:get <NAME> --format=json shows the whole description.
```

`(not set)` means the built-in default applies. `RECOMMENDED` is what the template proposes: for some settings the
optimized value, for others (the cluster settings) an example to replace. Options:

| Option | What it does |
|---|---|
| `--only-set` | Only the settings `config.php` sets (a quick review of a server) |
| `--format=json` | Everything as JSON: name, state, type, value, raw literal, line, secret, documented, recommended, section, summary and the whole description |
| `--show-secrets` | Show the values of passwords and keys |

One value:

```bash
./console exp:config:get EZP_INI_FILEMTIME_CHECK
```

```text
false
```

The value is printed alone on standard output, so a script can use it:
`check=$(./console exp:config:get EZP_INI_FILEMTIME_CHECK)`. Strings come without quotes, modes with their leading
`0` (`0640`). A setting that is not set prints a message on standard error and exits 1:

```text
EZP_INI_FILEMTIME_CHECK is not set in config.php (config.php-RECOMMENDED proposes false)
```

A password stays hidden unless asked for:

```bash
./console exp:config:get CLUSTER_STORAGE_PASS                  # ********
./console exp:config:get CLUSTER_STORAGE_PASS --show-secrets
```

`--format=json` gives the whole entry, with the whole description from the template:

```text
{
    "name": "EZP_FILE_MODE_MAX",
    "state": "set",
    "type": "octal",
    "value": 416,
    "raw": "0640",
    "text": "0640",
    "line": 112,
    "secret": false,
    "documented": true,
    "recommended": "0640",
    "recommended_type": "octal",
    "section": "FILE PERMISSIONS",
    ...
}
```

`value` is the PHP value (an octal mode is the number 416), `raw` the literal as written in the file, `text` the
value as `exp:config:get` prints it. `state` is `set`, `not set`, or `conditional` for a `define()` inside an `if`,
which applies only when its condition is true and which the commands never change.

## 6. Set a value

```bash
./console exp:config:set EZP_INI_FILEMTIME_CHECK false
```

```text
EZP_INI_FILEMTIME_CHECK = false (bool): take the comment off the documented line 57
  --- config.php
  +++ config.php
  @@ -54,7 +54,7 @@
       when a new INI file override is added to the structure. Note that new files
       are not affected, but this is an edge situation.
   */
  -//define( 'EZP_INI_FILEMTIME_CHECK', false );
  +define( 'EZP_INI_FILEMTIME_CHECK', false );
   
   
   /*
PASS: dry run, config.php not changed; --apply writes it (backup to var/backup/config/<time>/ first)
```

and with `--apply`:

```text
Backup: var/backup/config/20261029-130302/config.php
PASS: config.php written and checked with php -l (mode 0644, owner www-data:www-data)
A persistent server (Velocity, FrankenPHP worker) reads config.php again after a restart; PHP-FPM once OPcache sees the change (a reload of PHP-FPM when opcache.validate_timestamps=0).
```

Where the value goes, in this order:

1. **A `define()` of the name that config.php already runs**: only its value changes, in place. Comments, spacing and
   everything else on the line stay.
2. **The documented line**, `//define( 'NAME', ... );` as `config.php-RECOMMENDED` ships it: the comment is taken
   off and the value written there, so the setting stays next to its documentation.
3. **Otherwise** the `define()` is added between two marker lines, which the first such addition creates before the
   first statement of the file that is not a `define()`:

   ```php
   // BEGIN settings set by ./console exp:config:set
   define( 'MY_SITE_FLAG', true );
   // END settings set by ./console exp:config:set
   ```

The commands only ever change a `define( 'NAME', value );` statement. The file is read as PHP tokens, never run and
never edited with a search and replace, so a `define()` in a comment or in a string is never mistaken for one.

### Types

A value is written with a type, as PHP sees it:

| Type | Written as | Example |
|---|---|---|
| `bool` | `true`, `false` (also accepted with `--type=bool`: 1/0, yes/no, on/off, enabled/disabled) | `exp:config:set CLUSTER_ENABLE_DEBUG false` |
| `int` | `3306` | `exp:config:set CLUSTER_STORAGE_PORT 3307` |
| `octal` | `0640`, for modes; `640` and `0o640` are accepted | `exp:config:set EZP_DIR_MODE_MAX 750` writes `0750` |
| `float` | `1.5` | |
| `string` | `'text'`, quotes and backslashes escaped | `exp:config:set CLUSTER_STORAGE_HOST db1.example.com` |
| `null` | `null` | |

Without `--type` the type comes from the value `config.php` has, else from the value the template documents, so
`EZP_DIR_MODE_MAX 750` becomes the octal `0750` and `CLUSTER_STORAGE_PASS 12345` stays a string. `true`, `false` and
`null` are always the PHP values, because several settings take `false` in place of a number or a text
(`CLUSTER_EXPIRY_TIMEOUT false` turns the header off). A value that does not fit is refused:

```text
FAIL: CLUSTER_STORAGE_PORT: "abc" is not an integer (the documented value is of type int; give --type=<type> to write another type)
FAIL: EZP_INI_FILEMTIME_CHECK: "site.ini" is not true or false (the documented value is of type bool; give --type=<type> to write another type)
```

`EZP_INI_FILEMTIME_CHECK` may also name one INI file that is still checked. Say so with `--type`:

```bash
./console exp:config:set EZP_INI_FILEMTIME_CHECK site.ini --type=string --apply
```

```text
EZP_INI_FILEMTIME_CHECK = 'site.ini' (string): change the value on line 57
  -define( 'EZP_INI_FILEMTIME_CHECK', false );
  +define( 'EZP_INI_FILEMTIME_CHECK', 'site.ini' );
```

To write the word `false` as text, use `--type=string`. A value that starts with `-` would be read as an option; give
it with `--value=`: `exp:config:set CUSTOM_LOG_ROTATE_FILES --value=-1 --type=int`.

### Passwords and other secrets

```bash
./console exp:config:set CLUSTER_STORAGE_PASS 'S3cret-example' --apply
```

```text
CLUSTER_STORAGE_PASS = '********' (string): take the comment off the documented line 201
  -// define( 'CLUSTER_STORAGE_PASS', '********' );
  +define( 'CLUSTER_STORAGE_PASS', '********' );
Backup: var/backup/config/20261029-130326/config.php
PASS: config.php written and checked with php -l (mode 0644, owner www-data:www-data)
WARN: config.php holds a password or secret and every user of the server can read it (mode 0644): chmod 0640 config.php, with the web server's group
```

The value never appears in the output. Mind your shell history, though: a password typed on the command line is in
it. Read it from a file or a variable instead, for example `--value="$(cat /root/cluster-pass)"`, or set
`HISTCONTROL=ignorespace` and start the line with a space. Follow the warning and narrow the mode of `config.php`;
see [Security](#10-security) below.

### Names that are not documented

```text
FAIL: MY_SITE_FLAG is not documented in config.php-RECOMMENDED and not set in config.php; check the name (./console exp:config:list), or give --allow-unknown to add it anyway
```

The check catches typing mistakes (`EZP_INI_FILEMTIM_CHECK` would otherwise be written and silently ignored). A
constant of your own, read by your extension, is added with `--allow-unknown`; it goes into the marker block. A name
that `config.php` already defines can always be changed.

## 7. Remove a value

```bash
./console exp:config:unset EZP_DIR_MODE_MAX --apply
```

```text
EZP_DIR_MODE_MAX: comment out the define() on line 111 (the built-in default applies again)
  -define( 'EZP_DIR_MODE_MAX', 0750 );
  +//define( 'EZP_DIR_MODE_MAX', 0750 );
Backup: var/backup/config/20261029-130349/config.php
PASS: config.php written and checked with php -l (mode 0644, owner www-data:www-data)
```

A documented setting is commented out where it is, so `exp:config:set` finds the line again next time. A setting in
the marker block is removed; when the last one goes, the two marker lines go too. A name that is not set is reported
with `PASS: ... nothing to do`.

## 8. Owners, modes and backups in detail

| Situation | Mode of the written file | Owner and group |
|---|---|---|
| New file (no `.htaccess` or `config.php` yet) | the template's mode, as `cp -a` | run as root: the template's owner and group, or the installation directory's when the template belongs to root; run as another user: that user |
| Existing file replaced (`--force`) or edited (`exp:config:set`, `unset`) | the file's own mode | the file's own owner and group |
| `--mode=0640` (setup commands) | that mode; it must let the owner read and write | unchanged |
| `--owner=user` or `--owner=user:group` (setup commands) | unchanged | that user and group; only root can give a file to another user |

The modification time is the time of the write, not the template's: a replaced `config.php` must look changed to
OPcache. Backups keep the content, the mode and (as root) the owner of the file as it was:

```text
drwxr-xr-x www-data:www-data var
drwx------ www-data:www-data var/backup
drwx------ www-data:www-data var/backup/config
drwx------ www-data:www-data var/backup/config/<time>
-rw-r--r-- www-data:www-data var/backup/config/<time>/config.php
drwx------ www-data:www-data var/backup/setup
drwx------ www-data:www-data var/backup/setup/<time>
-rw-r--r-- www-data:www-data var/backup/setup/<time>/.htaccess
```

Backups are never deleted by the commands. Remove old ones yourself when you no longer need them.

Running as root is the usual case in an installer or a deploy script. The command then gives the new file to the
owner of the template, so the site's PHP can still read it, and never leaves a root-owned `config.php` behind. Give
`--owner` when the installation belongs to someone else than its templates, for example after an unpack as root:
`./console exp:setup:config --apply --owner=www-data:www-data --allow-root-user`.

## 9. When something goes wrong

**`FAIL: ... exists and differs ...`** You asked for a copy over a file that has changes. Read the diff (`-v` for all of
it). Keep your changes: leave the file, or copy them into the new one by hand after `--force --apply`, from the backup.

**`FAIL: config.php does not compile (syntax error, ... on line 3); nothing was read or changed.`** Every
`exp:config:*` command refuses a `config.php` with a syntax error, because nothing in it can be trusted to be what it
looks like. The site is down too: PHP stops on the parse error in every request. The console itself still runs and
warns at the start:

```text
Warning: config.php does not compile: syntax error, unexpected identifier "define" on line 3. Commands other than exp:setup:* and exp:config:* stop on it;
./console exp:setup:status shows it, ./console exp:setup:config --force --apply replaces it (backup first).
```

Fix the line it names by hand, copy back the last backup from `var/backup/config/` or `var/backup/setup/`, or start
again from the template with `./console exp:setup:config --force --apply` (the broken file is backed up first).

**`FAIL: cannot write a temporary file in /var/www/example: nothing was changed`** The user running the command cannot
write to the installation directory. Run it as the owner of the installation (`sudo -u <owner> ./console ...`) or as
root with `--allow-root-user`.

**`FAIL: --owner=www-data: only root can give a file to another user`** Leave `--owner` off, or run as root.

**`FAIL: ... is defined inside a condition or an expression on line N; change it by hand`** The `define()` is part of an
`if`, of `defined( ... ) || define( ... )`, or similar. Such a line decides itself when it applies; the commands leave
it to you.

**`FAIL: ... is defined more than once (lines 12, 40)`** PHP uses the first and warns about the second. Remove one by
hand, then use the commands again.

**The change has no effect.** A persistent server reads `config.php` once per worker: restart Velocity
(`./console exp:velocity restart`) or the FrankenPHP worker. With PHP-FPM and `opcache.validate_timestamps=0`, reload
PHP-FPM. Check the value with `./console exp:config:get <NAME>`.

**The site answers 500 after a new `.htaccess`.** The server lacks a module the rules use (`mod_rewrite`, or
`mod_headers` for the SVG rules), or `AllowOverride` does not allow them. Put the backup back
(`var/backup/setup/<time>/.htaccess`) and read the Apache error log.

## 10. Security

- `config.php` may hold the cluster database password. Never commit it to version control: add `/config.php` to the
  `.gitignore` of your project (the repository of Exponential does not track a `config.php`, only the template), and
  narrow its mode: `chmod 0640 config.php`, with the group of the web server. The commands keep the mode you set on
  every later edit.
- Values of names with `PASS`, `SECRET`, `KEY`, `TOKEN`, `CREDENTIAL` or `PWD` are masked in every output and diff;
  `--show-secrets` shows them, so do not paste such output into a ticket.
- Backups in `var/backup/` hold earlier copies of `config.php`, passwords included, in directories of mode `0700`.
  Include `var/backup/` in what you protect and clean it up.
- The shipped `.htaccess` answers 404 for every dot file and for anything below `var/` that is not a public asset, so
  neither `.htaccess` itself nor a backup is served. nginx and other servers need the same rules in their
  configuration; see [the web server examples](../examples/web-servers/README.md).
- [Security and audit](security-and-audit.md) and [Security hardening](../help/security-hardening.md) cover the rest.

## 11. In scripts, containers and CI

The commands are made to be run unattended: they ask nothing, print `PASS:` or `FAIL:` last and exit 0, 1 or 2.

```bash
#!/usr/bin/env bash
# provision.sh: put the files into place and set what this server needs
set -euo pipefail
cd /var/www/example
./console exp:setup:htaccess --apply
./console exp:setup:config --apply
./console exp:config:set EZP_INI_FILEMTIME_CHECK false --apply
./console exp:config:set EZP_DIR_MODE_MAX 0750 --apply
./console exp:config:set EZP_FILE_MODE_MAX 0640 --apply
./console exp:config:list --only-set
```

Run it twice and the second run changes nothing: each command says `nothing to do` and exits 0. In a container image,
run the same lines in the build step (as root, with `--allow-root-user --owner=www-data:www-data`), or at start from
environment variables:

```bash
./console exp:config:set CLUSTER_STORAGE_HOST "$CLUSTER_DB_HOST" --apply --allow-root-user
./console exp:config:set CLUSTER_STORAGE_PASS --value="$CLUSTER_DB_PASS" --apply --allow-root-user
```

In CI, compare a server with what you expect without changing it: `./console exp:config:list --only-set --format=json`
gives a stable document to diff, and `./console exp:setup:htaccess` exits 1 when `.htaccess` has drifted from the
template.

## 12. After an upgrade

A new release can change `.htaccess_root`, `.htaccess_root_static` and `config.php-RECOMMENDED`. Your copies stay as
they are. After the upgrade:

```bash
./console exp:setup:status                 # is .htaccess still a copy of a template?
./console exp:setup:htaccess               # the diff between your .htaccess and the new template
./console exp:setup:htaccess --force --apply    # take the new rules (backup first), when the diff holds nothing of yours
./console exp:config:list                  # settings new in config.php-RECOMMENDED show up as (not set)
```

`exp:config:list` reads the new template, so a setting the release adds appears with its description and proposed
value; set the ones you want with `exp:config:set`. Settings you had keep their values. See [Upgrading](upgrading.md).

## Reference

| Command | Alias of | Options |
|---|---|---|
| `exp:setup:status` | `exp:setupfiles status` | `--root` |
| `exp:setup:htaccess` | `exp:setupfiles htaccess` | `--static`, `--force`, `--apply`, `--dry-run`, `--mode`, `--owner`, `-v`, `--root` |
| `exp:setup:config` | `exp:setupfiles config` | `--force`, `--apply`, `--dry-run`, `--mode`, `--owner`, `--show-secrets`, `-v`, `--root` |
| `exp:config:list` | `exp:configphp list` | `--only-set`, `--format=text\|json`, `--show-secrets`, `--root`, `--file`, `--recommended` |
| `exp:config:get <NAME>` | `exp:configphp get` | `--format=text\|json`, `--show-secrets`, `--root`, `--file` |
| `exp:config:set <NAME> <value>` | `exp:configphp set` | `--type`, `--value`, `--allow-unknown`, `--apply`, `--dry-run`, `--show-secrets`, `-v`, `--root`, `--file` |
| `exp:config:unset <NAME>` | `exp:configphp unset` | `--apply`, `--dry-run`, `--show-secrets`, `-v`, `--root`, `--file` |

`--root=<dir>` works on another installation than the one the console belongs to; `--file` and `--recommended` name
other files than `<root>/config.php` and `<root>/config.php-RECOMMENDED`. Without the console the same commands are
`php bin/php/setupfiles.php <htaccess|config|status>` and `php bin/php/configphp.php <list|get|set|unset>`.

The generated pages list every option with its help text: [exp:setupfiles](../examples/commands/setupfiles.md) and
[exp:configphp](../examples/commands/configphp.md). The feature in short:
[Console commands for .htaccess and config.php](../features/6.0/setup-files-and-config-php-commands.md).

## Related

- [Installation: getting the code](../install/03-getting-the-code.md) and [Serving the site](../install/08-serving-the-site.md)
- [Deploying](deploying.md) and [Operating a site](operating-a-site.md)
- [Changing INI settings from the command line: exp:ini](../features/6.0/exp-ini-command.md), the tool for everything in `settings/`
- [Environment variables](environment-variables.md) and `config.env.php`, for what differs per machine
- [File modes](../bc/6.0/file-modes.md), for `EZP_DIR_MODE_MAX` and `EZP_FILE_MODE_MAX`
- [The static cache](../features/6.0/static-cache-generator.md), for `.htaccess_root_static`
