---
title: Console commands for developers
audience: developer
category: extension-development
order: 310
summary: The ./console commands extension developers use most - settings, caches, autoloads, class and template checks, help, logs, content jobs - and how an extension's scripts appear as ext: commands.
keywords: console, ./console, exp:ini, exp:cache, exp:ezpgenerateautoloads, exp:checkclasses, exp:eztemplatecheck, exp:exphelp, exp:explogs, exp:ezexec, exp:systeminfo, ext: commands, bin/php
type: help
---
# Console commands for developers

Part 8, reference 27. `./console` at the installation's root runs every command line script of the kernel and of the
active extensions under one name. `./console list` shows them all (about 300 on an installation with many
extensions); `./console help <command>` shows one command's options. The scripts are also reachable directly as
`php bin/php/<script>.php`. How an extension adds its own commands is in
[CLI scripts and console commands](../part-3-extension-developer/cli-scripts-and-console-commands.md).

## The ones you will use every day

| Command | Does |
|---|---|
| `exp:ini` | read and change settings in every scope: `get`, `set`, `add`, `rem`, `clear`, `toggle` ([The exp:ini command](../part-2-builder/the-exp-ini-command.md)) |
| `exp:cache` | clear and inspect caches by tag, id or all ([Cache control from the console](../../../bc/6.0/cache-console.md)) |
| `exp:ezcache` | the classic cache script: `--clear-tag`, `--clear-id`, `--clear-all`, `--list-tags` |
| `exp:ezpgenerateautoloads` | regenerate the autoload arrays; `-e` for extensions after adding or renaming a class |
| `exp:checkclasses` | load every declared class and report the ones PHP refuses to load: finds signature mismatches at once |
| `exp:eztemplatecheck` | check the syntax of the templates of a siteaccess or a design directory |
| `exp:ezexec` | run a PHP file with the kernel loaded: `./console exp:ezexec path/to/script.php` |
| `exp:systeminfo` | the facts and health checks of the system information page, as text or JSON |

## Help, logs and content

| Command | Does |
|---|---|
| `exp:exphelp` | the help system: build the index, search, check links (`check`) ([The help system for extensions](help-system-for-extensions.md)) |
| `exp:explogs` | list, follow and search the log files |
| `exp:audit` | the audit log: tail, show one event, verify |
| `exp:expcontentjob` | run, list, resume and cancel content jobs (large removes and copies in batches) |
| `exp:ezsubtreecopy`, `exp:ezsubtreeremove` | copy or remove subtrees |
| `exp:updatesearchindex` | reindex all content |
| `exp:ezcsvimport`, `exp:ezcsvexport` | content from and to CSV |

## Database and schema

| Command | Does |
|---|---|
| `exp:ezsqldumpschema` | dump the database schema to a file (`.dba` or SQL) |
| `exp:ezsqlinsertschema` | insert a schema and data from a `.dba` file |
| `exp:ezsqldiff` | the differences between two schemas |
| `exp:checkdbfiles` | verify the database update files against the upgrade path |

Your extension's schema workflow is in [Database schema and .dba files](../part-3-extension-developer/database-schema-and-dba-files.md).

## Quality

| Command | Does |
|---|---|
| `exp:checkcodestyle` | changed PHP files against the code style |
| `exp:checkmanifest` | `share/filelist.md5` against the files |
| `exp:ezcheckphptag` | characters before `<?php` or after `?>` (which break headers) |

## Your extension's commands

Every script in an active extension's `bin/php/` (and its command classes) appears as
`ext:<extension>:<script>`, for example `ext:cjw_newsletter:status`. The first line of the script's docblock
(`@description`) is the text `./console list` shows.

## Running commands

- Run them as the web server's user, so the files they write belong to it. As root they refuse unless
  `--allow-root-user` is given.
- `-s <siteaccess>` runs a command in a siteaccess's settings.
- Most commands that change data have `--dry-run`; use it first.

**Demo mode and the console.** With `EXP_DEMO_MODE=1` in the shell, every command prints `Demo mode is on (EXP_DEMO_MODE=1) ...` on standard error and records `demo/mode ... sapi=cli` once; `exp:resetuserpassword` refuses administrators, and `exp:install` and the kickstarter keep a well-known or short administrator password. [Demo mode for developers](demo-mode-for-developers.md).

## Related pages

- Previous: [INI files an extension author meets](ini-files-for-extension-authors.md)
- Next: [Directories under var/](directories-under-var.md)
- [eZScript and eZCLI](ezscript-and-ezcli.md)
- [Console](../../../bc/6.0/console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
