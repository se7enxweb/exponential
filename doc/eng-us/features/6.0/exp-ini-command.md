# Change settings from the command line: exp:ini

This page is for administrators and developers who change INI settings. One command reads and changes settings in
every scope: the global override, a siteaccess, an extension, an extension siteaccess. It edits files line by line, so
comments, blank lines and order stay as you wrote them (unlike `eZINI::save()`, which rewrites the whole file). Added
2026-10-02. Full reference with every action's output: [console exp:ini](../../bc/6.0/console-exp-ini.md).

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

## Five commands that cover most of a day

```bash
./console exp:ini set site.ini/SiteSettings/SiteName "My site" global --allow-root-user
./console exp:ini get site.ini/SiteSettings/SiteName --allow-root-user
./console exp:ini toggle site.ini/ContentSettings/ViewCaching global --allow-root-user
./console exp:ini where cronjob.ini/CronjobPart-publishing/Scripts --allow-root-user
./console exp:ini rem site.ini/SiteSettings/SiteName siteaccess:admin --allow-root-user
```

- `php bin/php/ini.php ...` is the same command.
- `exp:ini --help` lists the actions; `exp:ini help <action>` explains one.
- Add `--dry-run` to see what would happen without writing.

## Scopes

| Scope | File written |
|---|---|
| `global` (or `override`) | `settings/override/<file>.ini.append.php` |
| `siteaccess:<sa>` or `<sa>` | `settings/siteaccess/<sa>/<file>.ini.append.php` |
| `extension:<ext>` | `extension/<ext>/settings/<file>.ini.append.php` |
| `extension:<ext>:siteaccess:<sa>` | `extension/<ext>/settings/siteaccess/<sa>/<file>.ini.append.php` |
| `default` | `settings/<file>.ini`; refused unless `--allow-default` |

## Actions

| Action | What it does |
|---|---|
| `get` | Print a value |
| `set` | Write a value |
| `add` | Add to an array, without duplicates |
| `rem` | Remove a value |
| `clear` | Write the reset line `Variable[]` |
| `toggle` | Flip enabled/disabled, true/false, yes/no, on/off, 1/0, keeping your spelling |
| `copy`, `move`, `move-all` | Copy or move settings, for example into an extension |
| `where` | Every file that sets a variable, in load order, and the value in effect; lists the files of inactive extensions too |
| `list`, `scopes`, `actions` | List settings, scopes and actions |

## What it does for safety

- A backup of each changed file goes to `var/backup/ini/<timestamp>/`.
- Saving as the site user keeps the file's group (it rewrites in place when the group cannot be set).
- Settings writes are recorded in the [audit trail](audit-trail.md) as `system.setting.write`; secrets are recorded as
  `[secret]`.

## Extending

Actions and scope providers are classes registered in `settings/ini.ini` `[IniCommandSettings]`. **Setup > RAD** at
`/setup/radsurvey/(show)/inicommand` lists each with its owner. The same editor class (`expIniEditor`) is behind the
[debug bar's](exp-debug-bar.md) settings tab.

## See also: config.php

`exp:ini` handles the INI files in `settings/` and extensions. The constants in `config.php` (file modes, INI checks,
cluster storage, log rotation), which are read before any INI file, are handled by their own commands:
`exp:config:list`, `exp:config:get`, `exp:config:set` and `exp:config:unset`, with `exp:setup:config` to put the file
into place. They follow the same rules: a dry run without `--apply`, a backup of each changed file (to
`var/backup/config/<time>/`) and masked secrets. See
[Setting up .htaccess and config.php from the console](../../guides/setup-files-from-the-console.md) and
[Console commands for .htaccess and config.php](setup-files-and-config-php-commands.md).

## Related pages

- [Console](../../bc/6.0/console.md)
- [Setting up .htaccess and config.php from the console](../../guides/setup-files-from-the-console.md): `exp:config:*` for `config.php`
- [INI override placements](../../specifications/6.0/ini-override-placements.md)
- [Audit event model](../../specifications/6.0/audit-event-model.md)
- [eZINI preserves comments on save](../../bc/6.0/eZINI_PRESERVES_COMMENTS.md)
- [Multi-site INI overrides](multi-site-ini-overrides.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
- History: [June 2026, second half](../../history/2026/2026-06b.md), [October 2026](../../history/2026/2026-10.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
