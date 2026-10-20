---
title: exp:mailstatus
audience: developer, admin
category: examples
summary: "Show the e-mail preference system: tables, gate, footer, categories, suppression, consent log, the gate's last decisions and problems."
keywords: Konsole, Befehl, exp:mailstatus, mailstatus, console, command, CLI, mailstatus.php, exp:mail:status
type: generated
generated: exp:help:examples
---
# exp:mailstatus

Show the e-mail preference system: tables, gate, footer, categories, suppression, consent log, the gate's last decisions and problems.

Skript `bin/php/mailstatus.php`, Code in `kernel/private/classes/commands/mailstatus.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:mailstatus --help
./console exp:mail:status --help
php bin/php/mailstatus.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
E-mail preferences status
The tables, the mail gate, the footer settings, the categories with their counts, the
suppression list, the consent log, what the gate decided in the last 24 hours and 7 days,
and what looks wrong. Nothing is changed. No address is shown.

./console exp:mail:status
./console exp:mail:status --json

Ends with PASS, or FAIL (exit 1) when a problem of level error is found.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--json` |  | Ein JSON-Objekt statt des Textes |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/mailstatus.php`, `kernel/private/classes/commands/mailstatus.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
