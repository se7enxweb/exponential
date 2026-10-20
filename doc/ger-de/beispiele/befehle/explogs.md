---
title: exp:explogs
audience: developer, admin
category: examples
summary: "The log viewer on the command line: list the log files, follow one, search them and their rotations, find an error reference and the lines of its request (masked by default); rotate logs and prune their archives."
keywords: Konsole, Befehl, exp:explogs, explogs, console, command, CLI, explogs.php, exp:logs
type: generated
generated: exp:help:examples
---
# exp:explogs

The log viewer on the command line: list the log files, follow one, search them and their rotations, find an error reference and the lines of its request (masked by default); rotate logs and prune their archives.

Skript `bin/php/explogs.php`, Code in `kernel/private/classes/commands/explogs.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:explogs --help
./console exp:logs --help
php bin/php/explogs.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

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

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--list` |  | List the log files the viewer may open, by group, with size, time and lines a minute |
| `--archives` |  | List the logs that can be rotated, with their archives (.1 .. .N, .gz) and sizes, and the limits |
| `--rotate` | erforderlich | Rotate these logs now, whatever their size: keys (error,debug,site-storage) or ids (var/log/error.log), comma separated |
| `--rotate-all` |  | Rotate every log over its MaxSize (with --force: every log that is not empty) |
| `--force` |  | With --rotate-all: also the logs under their MaxSize |
| `--prune` | optional | Remove the archives beyond the limits (Keep, MaxAgeDays): of every log, or of these keys |
| `--days` | erforderlich | With --prune: remove archives older than this many days instead of MaxAgeDays |
| `--compress` | optional | Compress the plain archives from .2 on: of every log, or of these keys |
| `--truncate` | erforderlich | Empty these logs: rotate them (the content stays in .1), or with --in-place empty the file itself |
| `--in-place` |  | With --truncate: empty the file in place, keeping no copy |
| `--dry-run` |  | With --rotate, --rotate-all, --prune, --compress or --truncate: only print the plan |
| `--tail` | erforderlich | The last entries of a file (its path relative to the root, e.g. var/log/error.log) |
| `--lines` | erforderlich | With --tail: how many entries (default 20) |
| `--follow` |  | With --tail: go on printing what the file gains, polling with backoff (Ctrl-C ends) |
| `--interval` | erforderlich | With --follow: the shortest poll interval in milliseconds (default [TailSettings] PollInterval) |
| `--search` | erforderlich | Search for a text (literal, or a regular expression with --regex) |
| `--regex` |  | With --search: the text is a regular expression (bounded by [SearchSettings] BacktrackLimit and TimeLimit) |
| `--case` |  | With --search: case sensitive |
| `--file` | erforderlich | With --search: the file (default: [LogViewerSettings] DefaultFile) |
| `--scope` | erforderlich | With --search: file (default), kernel (every kernel log) or all (every file listed) |
| `--rotations` |  | With --search: the rotations (.1 .. .N, .gz) too |
| `--level` | erforderlich | Only these levels: error,warning,notice,debug |
| `--from` | erforderlich | Only entries at or after this time (e.g. 2026-10-17 01:00) |
| `--to` | erforderlich | Only entries at or before this time (a date alone means the whole day) |
| `--sa` | erforderlich | Only entries of this siteaccess |
| `--uri` | erforderlich | Only entries whose request contains this text |
| `--ip` | erforderlich | Only entries whose client address starts with this |
| `--cursor` | erforderlich | With --search: go on from where an earlier search stopped (printed as "More: --cursor=...") |
| `--limit` | erforderlich | With --search: print at most this many entries |
| `--reference` | erforderlich | Find an error reference (ERR-XXXXXXXXXX) in the kernel logs and their rotations, with its request |
| `--related` | erforderlich | The lines of the same request as the entry at <file>:<offset> |
| `--unmask` |  | Show e-mail addresses, IP addresses and secrets unmasked (the values of secret settings stay hidden) |
| `--json` |  | One JSON object instead of the text |
| `--color` | erforderlich | Color the levels: auto (default: when the output is a terminal), always, never |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/explogs.php`, `kernel/private/classes/commands/explogs.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
