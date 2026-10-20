---
title: exp:mailconsent
audience: developer, admin
category: examples
summary: "The consent log of the e-mail preferences: list, export as CSV, retention cleanup."
keywords: Konsole, Befehl, exp:mailconsent, mailconsent, console, command, CLI, mailconsent.php, exp:mail:consent
type: generated
generated: exp:help:examples
---
# exp:mailconsent

The consent log of the e-mail preferences: list, export as CSV, retention cleanup.

Skript `bin/php/mailconsent.php`, Code in `kernel/private/classes/commands/mailconsent.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:mailconsent --help
./console exp:mail:consent --help
php bin/php/mailconsent.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
E-mail consent log
  list                      the newest rows (time, person, category, action, old -> new, source)
  export                    the rows as CSV (--output, or the screen); the CSV holds addresses and IPs
  cleanup                   retention: remove the rows of people who are gone after RetentionDays, expired
                            confirmations and old suppression entries (--dry-run counts)
Filters: --user, --email, --category, --action, --source, --from, --to (YYYY-MM-DD).

./console exp:mail:consent list --category=newsletter --limit=20
./console exp:mail:consent export --from=2026-01-01 --output=var/tmp/consent.csv
./console exp:mail:consent cleanup --dry-run
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--user` | erforderlich | a login or user id |
| `--email` | erforderlich | an address |
| `--category` | erforderlich | a category identifier |
| `--action` | erforderlich | on, off, pending, confirm, ... |
| `--source` | erforderlich | page, link, admin, import, signup, bridge, confirm, system |
| `--from` | erforderlich | from this day |
| `--to` | erforderlich | up to this day |
| `--limit` | erforderlich | list: how many (default 50) |
| `--offset` | erforderlich | list: skip this many |
| `--output` | erforderlich | export: the file (mode 0600) |
| `--dry-run` |  | cleanup: count only |
| `--addresses` |  | list: show addresses instead of hiding them |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/mailconsent.php`, `kernel/private/classes/commands/mailconsent.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
