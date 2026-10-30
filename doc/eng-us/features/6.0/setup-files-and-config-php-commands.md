# Console commands for .htaccess and config.php

For administrators, hosting operators and anyone who scripts installations. Every installation ships three templates
at its root: `.htaccess_root` and `.htaccess_root_static` (the rules for Apache and LiteSpeed, the second with the
static cache offload) and `config.php-RECOMMENDED` (the documented settings of `config.php`). Seven console commands
put them into place and read and change `config.php`, so nobody has to copy them by hand or edit PHP in an editor
on a server:

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../examples/web-servers/nginx.md), [LiteSpeed](../../examples/web-servers/litespeed.md),
> [Caddy](../../examples/web-servers/caddy.md), [IIS](../../examples/web-servers/iis.md), [Velocity](../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../help/hosting/panels/plesk.md), [cPanel](../../help/hosting/panels/cpanel.md), [DirectAdmin](../../help/hosting/panels/directadmin.md), [ISPConfig](../../help/hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md).

| Command | Does |
|---|---|
| `./console exp:setup:status` | Says which template `.htaccess` is a copy of, and whether `config.php` is a plain copy, sets settings, or does not compile |
| `./console exp:setup:htaccess [--static] [--force] --apply` | Copies `.htaccess_root` (with `--static`: `.htaccess_root_static`) to `.htaccess` |
| `./console exp:setup:config [--force] --apply` | Copies `config.php-RECOMMENDED` to `config.php` |
| `./console exp:config:list [--only-set] [--format=json]` | Every documented setting with the value `config.php` has, the proposed value and its description |
| `./console exp:config:get <NAME>` | One value, plain, for a script (exit 1 when it is not set) |
| `./console exp:config:set <NAME> <value> [--type=<t>] --apply` | Sets one value in place, takes the comment off its documented line, or adds it |
| `./console exp:config:unset <NAME> --apply` | Comments the setting out again |

## What makes them safe

- **Dry run first.** Without `--apply` each command shows what it would change; `--apply` with `--dry-run` is a usage
  error (exit 2).
- **No silent overwrite.** An existing `.htaccess` or `config.php` that differs from the template is shown as a diff
  and kept unless `--force`.
- **A backup before every write**, to `var/backup/setup/<time>/` or `var/backup/config/<time>/`, with the file's mode
  and owner, in directories of mode `0700`.
- **Atomic writes** through a temporary file in the same directory, renamed into place.
- **`cp -a` behavior for owners and modes.** A new file gets the template's mode and, run as root, the template's
  owner (never root's); an existing file keeps its own. `--mode` and `--owner=user[:group]` set them; `-v` prints
  each copy like `cp -v`.
- **`config.php` is parsed, not searched.** It is read as PHP tokens and never run; only the value of one
  `define( 'NAME', value )` changes, and comments, order and formatting stay. A `define()` inside a condition is
  reported and left alone.
- **Checked before it lands.** A new `config.php` must pass `php -l` and read back with the value asked for. A
  `config.php` that does not compile is refused, and `./console` still starts to repair it.
- **Typed values.** `bool`, `int`, `octal` (modes such as `0640`), `float`, `string`, `null`, from `--type`, the
  current value or the documented one.
- **Secrets masked.** Values of names with `PASS`, `SECRET`, `KEY`, `TOKEN`, `CREDENTIAL` or `PWD` are `********` in
  every output and diff unless `--show-secrets`; setting one in a `config.php` everyone can read prints a warning.
- **Only known names**, those `config.php-RECOMMENDED` documents, unless `--allow-unknown`, so a typing mistake is
  caught instead of written.

## Example

```bash
./console exp:setup:htaccess --apply -v
./console exp:setup:config --apply
./console exp:config:set EZP_INI_FILEMTIME_CHECK false --apply
./console exp:config:set EZP_FILE_MODE_MAX 0640 --apply
./console exp:config:list --only-set
```

```text
'.htaccess_root' -> '.htaccess'
PASS: .htaccess_root copied to .htaccess (mode 0644, owner www-data:www-data)
...
config.php: 2 setting(s) set, 30 documented in config.php-RECOMMENDED

INI FILES OPTIMIZATIONS
  EZP_INI_FILEMTIME_CHECK              false (line 57)
      RECOMMENDED false - This new setting controls whether eZINI should check if the settings have been updated...

FILE PERMISSIONS
  EZP_FILE_MODE_MAX                    0640 (line 112)
      RECOMMENDED 0640 - Set EZP_DIR_MODE_MAX and EZP_FILE_MODE_MAX to the widest modes any directory and any fi...
```

The whole walk-through, with every option, the output of each case, the backup and `--force` rules, scripts,
containers and troubleshooting: **[Setting up .htaccess and config.php from the console](../../guides/setup-files-from-the-console.md)**.

## For developers

- Code: `kernel/classes/expsetupfilecopy.php` (copy, backup, atomic write, `php -l`), `kernel/classes/expconfigphpeditor.php`
  (the token-based `define()` editor), `kernel/classes/expconfigphpcatalogue.php` (the settings documented in
  `config.php-RECOMMENDED`), the commands `kernel/private/classes/commands/setupfiles.php` and `configphp.php`, the
  entry points `bin/php/setupfiles.php` and `bin/php/configphp.php`.
- A `config.php` that does not compile would stop `autoload.php`. `bin/php/console`, `bin/php/setupfiles.php` and
  `bin/php/configphp.php` define `EXP_CONFIG_PHP_TOLERATE` first; for them alone `autoload.php` checks the file with the
  PHP parser and leaves it unread when it does not compile (`EXP_CONFIG_PHP_SKIP` holds the error). Every other entry
  point reads `config.php` as before.
- A new setting becomes known to the commands by documenting it in `config.php-RECOMMENDED` the same way as the
  others: a comment block (a heading underlined with dashes starts a section) followed by `//define( 'NAME', value );`.
- Tests: `tests/tests/kernel/private/classes/commands/expSetupFilesAndConfigPhpCommandsTest.php` (no database).
- Generated reference: [exp:setupfiles](../../examples/commands/setupfiles.md), [exp:configphp](../../examples/commands/configphp.md).

## Related

- [Installation: getting the code](../../install/03-getting-the-code.md), [Serving the site](../../install/08-serving-the-site.md)
- [Change settings from the command line: exp:ini](exp-ini-command.md), for the INI files in `settings/`
- [The static cache generator](static-cache-generator.md), which `.htaccess_root_static` serves
- [File modes](../../bc/6.0/file-modes.md)
