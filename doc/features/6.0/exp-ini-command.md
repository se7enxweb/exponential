# Change settings from the command line: exp:ini

One command reads and changes INI settings in every scope: the global override, a siteaccess, an extension, an
extension siteaccess. It edits files line by line, so comments, blank lines and order stay as you wrote them
(unlike `eZINI::save()`, which rewrites the whole file).

Added 2026-10-02. Full reference with every action's output:
[doc/bc/6.0/console-exp-ini.md](../../bc/6.0/console-exp-ini.md).

## Five commands that cover most of a day

```bash
./console exp:ini set site.ini/SiteSettings/SiteName "My site" global --allow-root-user
./console exp:ini get site.ini/SiteSettings/SiteName --allow-root-user
./console exp:ini toggle site.ini/ContentSettings/ViewCaching global --allow-root-user
./console exp:ini where cronjob.ini/CronjobPart-publishing/Scripts --allow-root-user
./console exp:ini rem site.ini/SiteSettings/SiteName siteaccess:admin --allow-root-user
```

`php bin/php/ini.php ...` is the same command. `exp:ini --help` lists the actions; `exp:ini help <action>` explains
one. Use `--dry-run` to see what would happen without writing.

## Scopes

| Scope | File written |
|---|---|
| `global` (or `override`) | `settings/override/<file>.ini.append.php` |
| `siteaccess:<sa>` or `<sa>` | `settings/siteaccess/<sa>/<file>.ini.append.php` |
| `extension:<ext>` | `extension/<ext>/settings/<file>.ini.append.php` |
| `extension:<ext>:siteaccess:<sa>` | `extension/<ext>/settings/siteaccess/<sa>/<file>.ini.append.php` |
| `default` | `settings/<file>.ini`; refused unless `--allow-default` |

## Actions

`get`, `set`, `add` (arrays, no duplicates), `rem`, `clear` (writes the reset line `Variable[]`), `toggle`
(enabled/disabled, true/false, yes/no, on/off, 1/0, keeping your spelling), `copy`, `move` and `move-all` (for
example into an extension), `where` (every file that sets a variable, in load order, and the value in effect;
it lists the files of inactive extensions too), `list`, `scopes`, `actions`.

A backup of each changed file goes to `var/backup/ini/<timestamp>/`. Saving as the site user keeps the file's
group (it rewrites in place when the group cannot be set). Settings writes are recorded in the
[audit trail](audit-trail.md) as `system.setting.write`; secrets are recorded as `[secret]`.

## Extending

Actions and scope providers are classes registered in `settings/ini.ini` `[IniCommandSettings]`; Setup > RAD at
`/setup/radsurvey/(show)/inicommand` lists each with its owner. The same editor class (`expIniEditor`) is behind
the [debug bar's](exp-debug-bar.md) settings tab.

Related: [console](../../bc/6.0/console.md), [October 2026 chronicle](../../history/2026/2026-10.md).

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [audit event model](../../specifications/6.0/audit-event-model.md), [INI override placements](../../specifications/6.0/ini-override-placements.md).
