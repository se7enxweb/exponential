---
title: exp:notificationstatus
audience: developer, admin
category: examples
summary: "Zeigt das Benachrichtigungssystem: anstehende Ereignisse, Einträge der Zusammenfassungen, Abonnements, die letzten Läufe und Probleme."
keywords: Konsole, Befehl, exp:notificationstatus, notificationstatus, console, command, CLI, notificationstatus.php, exp:notification:status, exp:notify:status
type: generated
generated: exp:help:examples
---
# exp:notificationstatus

Zeigt das Benachrichtigungssystem: anstehende Ereignisse, Einträge der Zusammenfassungen, Abonnements, die letzten Läufe und Probleme.

Skript `bin/php/notificationstatus.php`, Code in `kernel/private/classes/commands/notificationstatus.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:notificationstatus --help
./console exp:notification:status --help
./console exp:notify:status --help
php bin/php/notificationstatus.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Notification status
Pending events, the messages waiting for a digest, the subscriptions, the last runs of the
notification cronjob and what looks wrong. Nothing is changed.

./console exp:notification:status
./console exp:notification:status --json

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

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/notificationstatus.php`, `kernel/private/classes/commands/notificationstatus.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
