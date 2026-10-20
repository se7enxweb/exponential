---
title: exp:notificationrun
audience: developer, admin
category: examples
summary: "Run the notification filter: send the notifications of the pending events and the due digests (--dry-run: only list them)."
keywords: Konsole, Befehl, exp:notificationrun, notificationrun, console, command, CLI, notificationrun.php, exp:notification:run, exp:notify:run
type: generated
generated: exp:help:examples
---
# exp:notificationrun

Run the notification filter: send the notifications of the pending events and the due digests (--dry-run: only list them).

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
| `--dry-run` |  | List what would be sent and change nothing |
| `--at` | erforderlich | The time of the time event: a date (2026-10-05 08:00) or a timestamp, default now |
| `--event` | erforderlich | Only these events (ids, comma separated); no time event is made then |
| `--no-time-event` |  | Make no time event: only the pending events are handled (no digest is due because of this run) |
| `--mail-file-dir` | erforderlich | Write the mail to files in this directory instead of sending it (this process only) |
| `--addresses` |  | Show whole recipient addresses in the dry run (they are masked by default) |
| `--json` |  | One JSON object instead of the text |
| `--job` | erforderlich | The id of a background job whose progress file is written (the status page uses this) |
| `--source` | erforderlich | console (default) or web; what the record of the run says |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/notificationrun.php`, `kernel/private/classes/commands/notificationrun.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
