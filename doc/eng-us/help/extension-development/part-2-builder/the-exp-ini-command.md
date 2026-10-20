---
title: Changing settings with exp:ini
audience: developer, designer, app-creator
category: extension-development
order: 50
summary: Every verb of the settings command - get, set, add, rem, clear, toggle, copy, move, move-all, where, list, scopes - with its scopes, dry runs, backups, JSON output and exit codes, and recipes for common builder tasks.
keywords: exp:ini, ini command, settings command line, get, set, add, rem, clear, toggle, copy, move, move-all, where, list, scopes, dry-run, backup, json
type: help
---
# Changing settings with exp:ini

Part 2, chapter 5. `./console exp:ini` (the script `bin/php/ini.php`) reads and changes settings in every scope from
the command line. It writes files line by line, keeping comments, blank lines and order; it writes atomically, keeps a
backup, and clears the INI cache afterwards. It is the builder's main tool, and everything on this page comes from its
own `--help`.

## The syntax

```text
./console exp:ini <action> <arguments> [options]
```

A setting is written `file/Block/Variable` (the `.ini` suffix is optional); arrays end in `[]`, hash entries in
`[key]`. Two other spellings are accepted: `site.ini:SiteSettings.SiteName` and `"site.ini [SiteSettings] SiteName"`.

## Reading

| Action | Example | Shows |
|---|---|---|
| `get` | `exp:ini get site.ini/SiteSettings/SiteName` | the value in effect (or, with a scope, the value one scope's file gives) |
| `where` | `exp:ini where site.ini/SiteSettings/SiteName` | every file that sets it, in load order, and the result |
| `list` | `exp:ini list site.ini/DebugSettings` | the variables of a block, or the blocks of a file |
| `scopes` | `exp:ini scopes extension` | the scopes of this installation and the directory each writes |
| `actions` | `exp:ini actions` | the built-in actions and those extensions registered |

`-s <siteaccess>` evaluates `get`, `where` and `list` as that siteaccess sees them. Values of variables named like
`Password`, `Secret`, `Token`, `Salt` or ending in `Key` are masked unless you pass `--show-secrets`.

## Writing

| Action | Example | Does |
|---|---|---|
| `set` | `exp:ini set site.ini/SiteSettings/SiteName "My site" global` | sets a variable or a hash entry |
| `add` | `exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_settings global` | appends to an array |
| `rem` | `exp:ini rem site.ini/SiteSettings/SiteName global` | removes a variable, one array value or one hash entry |
| `clear` | `exp:ini clear 'site.ini/RegionalSettings/SiteLanguageList[]' siteaccess:mysite` | writes the reset line, so earlier files' values are dropped |
| `toggle` | `exp:ini toggle site.ini/DebugSettings/DebugOutput global` | flips enabled/disabled, true/false, yes/no, 1/0 |
| `copy` | `exp:ini copy site.ini/DesignSettings/SiteDesign siteaccess:site siteaccess:ger` | copies a setting between scopes |
| `move` | `exp:ini move site.ini/DesignSettings siteaccess:mysite extension:mysite_settings` | moves a block or a whole file |
| `move-all` | `exp:ini move-all siteaccess:mysite extension:mysite_settings:siteaccess:mysite --create-extension` | moves every file of a scope |

Put `--` before a value that starts with a dash: `exp:ini set site.ini/MySettings/Offset -- -1 global`.

## Scopes

| Scope word | File written |
|---|---|
| `global` (or `override`) | `settings/override/<file>.ini.append.php` |
| `siteaccess:<sa>` (or just `<sa>`) | `settings/siteaccess/<sa>/<file>.ini.append.php` |
| `extension:<ext>` | `extension/<ext>/settings/<file>.ini.append.php` |
| `extension:<ext>:siteaccess:<sa>` | `extension/<ext>/settings/siteaccess/<sa>/<file>.ini.append.php` |
| `default` | `settings/<file>.ini`; refused unless `--allow-default` (do not use it) |

Extensions can register more scopes in `ini.ini [IniCommandSettings] ScopeProviders[]`.

## Options that keep you safe

| Option | Default | Effect |
|---|---|---|
| `--dry-run` | off | show the diff, write nothing |
| `--backup` / `--no-backup` | backup | copy the file to `var/backup/ini/<timestamp>/` first |
| `--clear-cache` / `--no-clear-cache` | clear | clear the INI cache after a write |
| `--create` / `--no-create` | create for set, add, clear, toggle, copy | create a missing file and block |
| `--json` | off | one JSON object: ok, code, action, message, warnings, data |
| `--root=<dir>` | the installation | work on another installation's settings tree (a checkout, a copy) |

Exit codes: `0` done or nothing to change, `1` usage error, `2` not found, `3` refused, `4` write failed. Scripts can
rely on them.

## Recipes

**Turn on debug output for your own address only:**

```bash
./console exp:ini set site.ini/DebugSettings/DebugOutput enabled global
./console exp:ini set site.ini/DebugSettings/DebugByIP enabled global
./console exp:ini add 'site.ini/DebugSettings/DebugIPList[]' 192.0.2.10 global
```

(`192.0.2.10` is a documentation address; use yours.)

**Give one site its own list of languages:**

```bash
./console exp:ini clear 'site.ini/RegionalSettings/SiteLanguageList[]' siteaccess:mysite
./console exp:ini add 'site.ini/RegionalSettings/SiteLanguageList[]' eng-US siteaccess:mysite
./console exp:ini add 'site.ini/RegionalSettings/SiteLanguageList[]' ger-DE siteaccess:mysite
```

**Find out why an extension's setting is ignored:**

```bash
./console exp:ini where design.ini/ExtensionSettings/DesignExtensions -s mysite
```

**Check a change in a checkout before deploying it:**

```bash
./console exp:ini where site.ini/DesignSettings/SiteDesign --root=/path/to/checkout
```

## From a script

Because of `--json` and the exit codes, `exp:ini` is safe to use in deployment scripts:

```bash
./console exp:ini get site.ini/SiteSettings/SiteName --json
```

Read the `ok` and `data` fields instead of parsing text.

## Related pages

- Previous: [Settings that live in an extension](extension-settings-for-builders.md)
- Next: [Settings in the administration](settings-in-the-admin.md)
- [Change settings from the command line](../../../features/6.0/exp-ini-command.md)
- [Console: exp:ini](../../../bc/6.0/console-exp-ini.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
