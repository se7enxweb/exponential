---
title: exp:audit
audience: developer, admin
category: examples
summary: "Audit log: tail, show one event, verify the hash chains, list the channels."
keywords: Konsole, Befehl, exp:audit, audit, console, command, CLI, audit.php
type: generated
generated: exp:help:examples
---
# exp:audit

Audit log: tail, show one event, verify the hash chains, list the channels.

Skript `bin/php/audit.php`, Code in `kernel/private/classes/commands/audit.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:audit --help
php bin/php/audit.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Audit log: the records of the audit channels and their hash chains

  exp:audit status|channels
  exp:audit tail [--channel=<c>] [--name=<pattern>] [--lines=20] [--follow] [--json]
  exp:audit show <event id> [--json]
  exp:audit search [filters] [--files] [--limit=50] [--json]
  exp:audit verify [--channel=<c>] [--date=YYYY-MM-DD] [--archives] [--json]
  exp:audit rotate [--channel=<c>] [--dry-run]
  exp:audit archive [--channel=<c>] [--before=YYYY-MM-DD] [--format=gzip|bzip2|xz|zstd|zip] [--dry-run]
  exp:audit restore --channel=<c> --date=YYYY-MM-DD [--to=<dir>]
  exp:audit purge [--channel=<c>] [--dry-run]
  exp:audit reindex [--incremental] [--archives] [--channel=<c>]
  exp:audit pseudonymise [--dry-run]
  exp:audit export [filters] [--subject-user=<id>] [--format=jsonl|csv|bundle] --out=<path>
  exp:audit import [--dir=<old log dir>] [--file=login.log] [--dry-run] [--keep-originals]
  exp:audit key list|rotate|fingerprint [--pseudonym]
  exp:audit checkpoint
  exp:audit sinks list|test <name>|flush [<name>]
  exp:audit alerts list|test <rule> [--replay=YYYY-MM-DD]|recipients [--rule=<rule>]
  exp:audit cron [--daily]           one run of the cronjob part (--daily: the daily tasks now)

Filters (search, export): --name=<pattern> --user=<id> --login= --object=<type:id> --target=<type:id>
  --result=success|refused|failed --severity=<min> --request= --job= --run= --ip=<network>
  --from=YYYY-MM-DD[THH:MM[:SS]][Z|+HH:MM] --to=... (UTC unless an offset is given; --to includes that day,
  minute or second) --query=<text> --legacy-file=<4.x file name> --channel=
Name patterns: * | <domain>.* | a prefix ending in .* (access.session.*) | a whole name; anything else
  is refused (exit 2).
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--channel` | erforderlich | Only this channel (content, access, system, commerce, read) |
| `--name` | erforderlich | tail, search, export: only names matching this pattern: *, access.*, access.session.*, content.node.move |
| `--lines` | erforderlich | tail: how many records (default 20) |
| `--follow` |  | tail: keep printing new records as they are written (Ctrl-C ends) |
| `--json` |  | Print records and results as JSON |
| `--date` | erforderlich | verify: only that day's files; restore: the archived day (YYYY-MM-DD, UTC) |
| `--archives` |  | verify: also the archives (manifests, HMAC, checksums, the chain through them); reindex: also the archives |
| `--dry-run` |  | rotate, archive, purge, import, pseudonymise: only say what would be done |
| `--before` | erforderlich | archive: the days before this date (default: older than LiveDays) |
| `--format` | erforderlich | archive: the format handler; export: jsonl, csv or bundle |
| `--to` | erforderlich | restore: the directory (default <LogDir>/restored); search, export: up to and including this time (as --from) |
| `--incremental` |  | reindex: only what is new (default: rebuild) |
| `--user` | erforderlich |  |
| `--login` | erforderlich |  |
| `--object` | erforderlich |  |
| `--target` | erforderlich |  |
| `--result` | erforderlich |  |
| `--severity` | erforderlich |  |
| `--request` | erforderlich |  |
| `--job` | erforderlich |  |
| `--run` | erforderlich |  |
| `--ip` | erforderlich |  |
| `--from` | erforderlich | search, export: from this time (YYYY-MM-DD[THH:MM[:SS]], UTC unless it ends in Z or +HH:MM) |
| `--query` | erforderlich | search, export: full-text search over names, object names and before/after values |
| `--legacy-file` | erforderlich |  |
| `--limit` | erforderlich | search: how many records (default 50); export: at most MaxExportRecords |
| `--files` |  | search: read the files, not the index |
| `--out` | erforderlich | export: the file (jsonl, csv) or directory (bundle) |
| `--subject-user` | erforderlich | export: every record by or about this user id (an access request) |
| `--dir` | erforderlich | import: the directory of the 4.x audit files (default: the audit LogDir) |
| `--file` | erforderlich | import: only this 4.x file (login.log) |
| `--keep-originals` |  | import: leave the 4.x files in place after archiving them |
| `--pseudonym` |  | key rotate: replace the pseudonym key instead of the signing key |
| `--replay` | erforderlich | alerts test: run the rule over the records since this date, without firing |
| `--daily` |  | cron: run the daily tasks now |
| `--rule` | erforderlich | alerts recipients: only this rule |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[action][id][extra]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/audit.php`, `kernel/private/classes/commands/audit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
