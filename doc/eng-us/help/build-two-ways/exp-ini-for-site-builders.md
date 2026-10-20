---
title: exp:ini for site builders
audience: admin, developer, website-creator, designer, hosting
category: build-two-ways
order: 420
summary: The exp:ini commands a site builder needs for menus and theme settings - where, get, add, rem, clear, set, scopes, dry runs and backups - with the scopes of an installation.
keywords: exp:ini, console, ini.php, where, get, add, rem, clear, set, scopes, dry-run, backup, siteaccess scope, global, extension scope, menu.ini
type: help
---
# exp:ini for site builders

`./console exp:ini` (the script `bin/php/ini.php`) reads and changes INI settings in every scope. It is the fastest
way to make the settings changes these pages describe, and it is safe to try: `--dry-run` shows the diff and writes
nothing. Its full guide is `doc/eng-us/bc/6.0/console-exp-ini.md`.

## The commands used on these pages

| Task | Command |
|---|---|
| Where is it set, what is in effect | `./console exp:ini where menu.ini/SiteInfo/MainMenuID site` |
| The value in effect | `./console exp:ini get menu.ini/SiteInfo/MainMenuID -s site` |
| Add a menu item (append) | `./console exp:ini add menu.ini/SiteInfo/MainMenuID[] 192 siteaccess:site` |
| Remove one item | `./console exp:ini rem menu.ini/SiteInfo/MainMenuID[] 192 siteaccess:site` |
| Empty a list in one file (keeps the reset line) | `./console exp:ini clear menu.ini/SiteInfo/MainMenuID[] siteaccess:site` |
| Set a plain value | `./console exp:ini set menu.ini/SiteInfo/FooterLinksRemoteID my-footer-links siteaccess:site` |
| List the blocks of a file | `./console exp:ini list menu.ini site` |
| List the scopes | `./console exp:ini scopes` |

Add `--dry-run` to any writing command to see the change first, and `--allow-root-user` when running as root.

## Setting syntax

The setting is written `<file>/<Block>/<Variable>`; the `.ini` suffix may be left out. Arrays end in `[]`, hash
entries in `[key]`. The help also accepts `menu.ini:SiteInfo.MainMenuID` and `"menu.ini [SiteInfo] MainMenuID"`.

## Scopes

| Scope | File |
|---|---|
| `global` (= `override`) | `settings/override/<file>.ini.append.php` |
| `siteaccess:<sa>` (or bare `<sa>`) | `settings/siteaccess/<sa>/<file>.ini.append.php` |
| `extension:<ext>` | `extension/<ext>/settings/<file>.ini.append.php` |
| `extension:<ext>:siteaccess:<sa>` | `extension/<ext>/settings/siteaccess/<sa>/<file>.ini.append.php` |
| `default` | `settings/<file>.ini`, refused unless `--allow-default` |

## What a write does

- The file is written line by line: comments, blank lines and order stay; owner, group and mode are kept.
- The write is atomic, after a backup copy to `var/backup/ini/<timestamp>/` (`--no-backup` skips it).
- The INI cache is cleared (`--no-clear-cache` skips it).
- Exit codes: 0 done (or nothing to change), 1 usage error, 2 not found, 3 refused, 4 write failed.

On the test copy one `exp:ini add` took 1.3 to 2.1 seconds ([Measured steps and times](measured-steps-and-times.md)).

## After a write

Settings read by templates with `ezini()` are compiled into the templates: run
`./console exp:cache clear --tag=template,content`. On Exponential Velocity, `./console exp:velocity restart`, as the
command's own hint says.

## Moving theme settings into your extension

`exp:ini move` and `exp:ini move-all` move a block or a whole scope into another scope, for example a siteaccess's
settings into your design extension (`--create-extension --activate`). That keeps a site's menus and theme settings in
version control with the theme. Try it with `--dry-run` first.

## Related pages

- [Header menu by command line](header-menu-by-cli.md)
- [Ini settings in the administration](ini-settings-in-the-admin.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
