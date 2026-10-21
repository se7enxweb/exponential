---
title: exp:notificationrun
audience: developer, admin
category: examples
summary: "Führt den Benachrichtigungsfilter aus: sendet die Benachrichtigungen der anstehenden Ereignisse und die fälligen Zusammenfassungen (--dry-run: nur auflisten)."
keywords: Konsole, Befehl, exp:notificationrun, notificationrun, console, command, CLI, notificationrun.php, exp:notification:run, exp:notify:run
type: generated
generated: exp:help:examples
---
# exp:notificationrun

Führt den Benachrichtigungsfilter aus: sendet die Benachrichtigungen der anstehenden Ereignisse und die fälligen Zusammenfassungen (--dry-run: nur auflisten).

Skript `bin/php/notificationrun.php`, Code in `kernel/private/classes/commands/notificationrun.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:notificationrun --help
./console exp:notification:run --help
./console exp:notify:run --help
php bin/php/notificationrun.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Run the notifications
One pass of the notification cronjob part: a time event is made, every pending event goes through
every handler (subtree, collaboration, general digest), messages are sent or kept for a digest,
events nothing waits for are removed. The run is locked (a second one is refused while one runs),
recorded (exp:notification:status shows it) and audited.

--dry-run lists what would be sent (subject, number of recipients, masked addresses) and changes
nothing: the pass runs in a transaction that is rolled back and no mail is handed to the transport.
--mail-file-dir=<dir> forces the file transport for this process: the mail is written to <dir>
instead of being sent, whatever site.ini says. Use it for every trial.
--at=<time> sets the time of the time event (a date or timestamp), to try digest windows.

./console exp:notification:run --dry-run
./console exp:notification:run --mail-file-dir=var/tmp/notification-mail
./console exp:notification:run
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | Auflisten, was gesendet würde, und nichts ändern |
| `--at` | erforderlich | Der Zeitpunkt des Zeitereignisses: ein Datum (2026-10-05 08:00) oder ein Zeitstempel, Standard jetzt |
| `--event` | erforderlich | Nur diese Ereignisse (IDs, durch Kommas getrennt); dann wird kein Zeitereignis erzeugt |
| `--no-time-event` |  | Kein Zeitereignis erzeugen: nur die anstehenden Ereignisse werden verarbeitet (durch diesen Lauf wird keine Zusammenfassung fällig) |
| `--mail-file-dir` | erforderlich | Die Mail in Dateien in diesem Verzeichnis schreiben, statt sie zu senden (nur dieser Prozess) |
| `--addresses` |  | Im Probelauf die ganzen Empfängeradressen zeigen (standardmäßig maskiert) |
| `--json` |  | Ein JSON-Objekt statt des Textes |
| `--job` | erforderlich | Die ID eines Hintergrundauftrags, dessen Fortschrittsdatei geschrieben wird (die Statusseite verwendet das) |
| `--source` | erforderlich | console (Standard) oder web; was der Datensatz des Laufs angibt |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/notificationrun.php`, `kernel/private/classes/commands/notificationrun.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
