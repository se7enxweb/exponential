---
title: exp:audit
audience: developer, admin
category: examples
summary: "Audit-Log: die letzten Einträge, ein Ereignis anzeigen, die Hash-Ketten prüfen, die Kanäle auflisten."
keywords: Konsole, Befehl, exp:audit, audit, console, command, CLI, audit.php
type: generated
generated: exp:help:examples
---
# exp:audit

Audit-Log: die letzten Einträge, ein Ereignis anzeigen, die Hash-Ketten prüfen, die Kanäle auflisten.

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
| `--channel` | erforderlich | Nur dieser Kanal (content, access, system, commerce, read) |
| `--name` | erforderlich | tail, search, export: nur Namen, die auf dieses Muster passen: *, access.*, access.session.*, content.node.move |
| `--lines` | erforderlich | tail: wie viele Einträge (Standard 20) |
| `--follow` |  | tail: neue Einträge weiter ausgeben, sobald sie geschrieben werden (Strg-C beendet) |
| `--json` |  | Einträge und Ergebnisse als JSON ausgeben |
| `--date` | erforderlich | verify: nur die Dateien dieses Tages; restore: der archivierte Tag (YYYY-MM-DD, UTC) |
| `--archives` |  | verify: auch die Archive (Manifeste, HMAC, Prüfsummen, die Kette durch sie hindurch); reindex: auch die Archive |
| `--dry-run` |  | rotate, archive, purge, import, pseudonymise: nur sagen, was getan würde |
| `--before` | erforderlich | archive: die Tage vor diesem Datum (Standard: älter als LiveDays) |
| `--format` | erforderlich | archive: der Format-Handler; export: jsonl, csv oder bundle |
| `--to` | erforderlich | restore: das Verzeichnis (Standard <LogDir>/restored); search, export: bis einschließlich zu diesem Zeitpunkt (wie --from) |
| `--incremental` |  | reindex: nur das Neue (Standard: neu aufbauen) |
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
| `--from` | erforderlich | search, export: ab diesem Zeitpunkt (YYYY-MM-DD[THH:MM[:SS]], UTC, außer er endet auf Z oder +HH:MM) |
| `--query` | erforderlich | search, export: Volltextsuche über Namen, Objektnamen und Werte vorher/nachher |
| `--legacy-file` | erforderlich |  |
| `--limit` | erforderlich | search: wie viele Einträge (Standard 50); export: höchstens MaxExportRecords |
| `--files` |  | search: die Dateien lesen, nicht den Index |
| `--out` | erforderlich | export: die Datei (jsonl, csv) oder das Verzeichnis (bundle) |
| `--subject-user` | erforderlich | export: jeder Eintrag von diesem oder über diesen Benutzer (eine Auskunftsanfrage) |
| `--dir` | erforderlich | import: das Verzeichnis der Audit-Dateien von 4.x (Standard: das LogDir des Audits) |
| `--file` | erforderlich | import: nur diese Datei von 4.x (login.log) |
| `--keep-originals` |  | import: die Dateien von 4.x nach dem Archivieren an ihrem Platz lassen |
| `--pseudonym` |  | key rotate: den Pseudonym-Schlüssel statt des Signaturschlüssels ersetzen |
| `--replay` | erforderlich | alerts test: die Regel über die Einträge seit diesem Datum laufen lassen, ohne auszulösen |
| `--daily` |  | cron: die täglichen Aufgaben jetzt ausführen |
| `--rule` | erforderlich | alerts recipients: nur diese Regel |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

Argumente: `[action][id][extra]`.

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/audit.php`, `kernel/private/classes/commands/audit.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
