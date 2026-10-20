---
title: exp:explogs
audience: developer, admin
category: examples
summary: "The log viewer on the command line: list the log files, follow one, search them and their rotations, find an error reference and the lines of its request (masked by default); rotate logs and prune their archives."
keywords: exp:explogs, explogs, console, command, CLI, explogs.php, exp:logs
type: generated
generated: exp:help:examples
---
# exp:explogs

The log viewer on the command line: list the log files, follow one, search them and their rotations, find an error reference and the lines of its request (masked by default); rotate logs and prune their archives.

Script `bin/php/explogs.php`, code in `kernel/private/classes/commands/explogs.php`. Run it from the installation root, as the user the web server runs as:

```bash
./console exp:explogs --help
./console exp:logs --help
php bin/php/explogs.php --help
```

## Help text

```text
The log viewer on the command line (read only; e-mail and IP addresses and secrets masked unless --unmask)

./console exp:logs --list                                  the log files: group, size, time, lines a minute
./console exp:logs --tail=var/log/error.log --lines=50     the last 50 entries
./console exp:logs --tail=var/log/error.log --follow       follow it (Ctrl-C ends)
./console exp:logs --search=timeout --file=var/log/error.log --rotations --level=error,warning
./console exp:logs --search='/content/view/\d+' --regex --scope=kernel --from='2026-10-17 01:00' --to='2026-10-17 02:00'
./console exp:logs --reference=ERR-0123456789          where an error page's reference is, and its request
./console exp:logs --related=var/log/error.log:123456      the lines of the request of the entry at that offset

Log management (doc/eng-us/features/6.0/log-rotation.md):
./console exp:logs --archives                              each log with its archives and their sizes
./console exp:logs --rotate=error,debug                    rotate these logs now (keys as in --archives, or ids)
./console exp:logs --rotate-all                            rotate every log that is not empty
./console exp:logs --prune --dry-run                       the archives beyond site.ini [FileSettings] limits
./console exp:logs --prune=error                           remove them (all logs, or these)
./console exp:logs --compress                              compress the archives from .2 on
./console exp:logs --truncate=debug [--in-place]           empty a log (rotated, or in place)

Exit code: 0 something listed or found, 1 nothing found, 2 error.
```

## Options

| Option | Value | Help |
|---|---|---|
| `--list` |  | List the log files the viewer may open, by group, with size, time and lines a minute |
| `--archives` |  | List the logs that can be rotated, with their archives (.1 .. .N, .gz) and sizes, and the limits |
| `--rotate` | required | Rotate these logs now, whatever their size: keys (error,debug,site-storage) or ids (var/log/error.log), comma separated |
| `--rotate-all` |  | Rotate every log over its MaxSize (with --force: every log that is not empty) |
| `--force` |  | With --rotate-all: also the logs under their MaxSize |
| `--prune` | optional | Remove the archives beyond the limits (Keep, MaxAgeDays): of every log, or of these keys |
| `--days` | required | With --prune: remove archives older than this many days instead of MaxAgeDays |
| `--compress` | optional | Compress the plain archives from .2 on: of every log, or of these keys |
| `--truncate` | required | Empty these logs: rotate them (the content stays in .1), or with --in-place empty the file itself |
| `--in-place` |  | With --truncate: empty the file in place, keeping no copy |
| `--dry-run` |  | With --rotate, --rotate-all, --prune, --compress or --truncate: only print the plan |
| `--tail` | required | The last entries of a file (its path relative to the root, e.g. var/log/error.log) |
| `--lines` | required | With --tail: how many entries (default 20) |
| `--follow` |  | With --tail: go on printing what the file gains, polling with backoff (Ctrl-C ends) |
| `--interval` | required | With --follow: the shortest poll interval in milliseconds (default [TailSettings] PollInterval) |
| `--search` | required | Search for a text (literal, or a regular expression with --regex) |
| `--regex` |  | With --search: the text is a regular expression (bounded by [SearchSettings] BacktrackLimit and TimeLimit) |
| `--case` |  | With --search: case sensitive |
| `--file` | required | With --search: the file (default: [LogViewerSettings] DefaultFile) |
| `--scope` | required | With --search: file (default), kernel (every kernel log) or all (every file listed) |
| `--rotations` |  | With --search: the rotations (.1 .. .N, .gz) too |
| `--level` | required | Only these levels: error,warning,notice,debug |
| `--from` | required | Only entries at or after this time (e.g. 2026-10-17 01:00) |
| `--to` | required | Only entries at or before this time (a date alone means the whole day) |
| `--sa` | required | Only entries of this siteaccess |
| `--uri` | required | Only entries whose request contains this text |
| `--ip` | required | Only entries whose client address starts with this |
| `--cursor` | required | With --search: go on from where an earlier search stopped (printed as "More: --cursor=...") |
| `--limit` | required | With --search: print at most this many entries |
| `--reference` | required | Find an error reference (ERR-XXXXXXXXXX) in the kernel logs and their rotations, with its request |
| `--related` | required | The lines of the same request as the entry at <file>:<offset> |
| `--unmask` |  | Show e-mail addresses, IP addresses and secrets unmasked (the values of secret settings stay hidden) |
| `--json` |  | One JSON object instead of the text |
| `--color` | required | Color the levels: auto (default: when the output is a terminal), always, never |

Read from the option definitions in the code (`startup()`, `getOptions()`, `options()` or `ezcConsoleOption`). Every script also takes the standard options of the script framework (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Related

- [All console commands](README.md)

---

Generated by `./console exp:help:examples` from `bin/php/explogs.php`, `kernel/private/classes/commands/explogs.php`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
