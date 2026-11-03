---
title: exp:expviewcountupdate
audience: developer, admin
category: examples
summary: Zählt die Seitenaufrufe aus den Zugriffsprotokollen des Webservers in den Aufrufzähler (ezview_counter), für einen Siteaccess oder mehrere.
keywords: Konsole, Befehl, exp:expviewcountupdate, expviewcountupdate, console, command, CLI, expviewcountupdate.php, exp:viewcount:update
type: generated
generated: exp:help:examples
---
# exp:expviewcountupdate

Zählt die Seitenaufrufe aus den Zugriffsprotokollen des Webservers in den Aufrufzähler (ezview_counter), für einen Siteaccess oder mehrere.

Skript `bin/php/expviewcountupdate.php`, Code in `kernel/private/classes/commands/expviewcountupdate.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expviewcountupdate --help
./console exp:viewcount:update --help
php bin/php/expviewcountupdate.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Count the page views of the web server's access logs into the view counter (ezview_counter)
Reads each siteaccess's access log (logfile.ini [AccessLogFileSettings]) from where the last run
stopped, keeps the page views (logfile.ini [ViewCountSettings]) and adds them to the nodes they
showed. Several siteaccesses sharing one log are told apart by the kernel's siteaccess matching.

./console exp:viewcount:update --dry-run --top=20          what a run would count, nothing written
./console exp:viewcount:update                              count (as the cronjob updateviewcount.php)
./console exp:viewcount:update --all-siteaccesses           every siteaccess with a log, not the administrations
./console exp:viewcount:update --siteaccess-list=site,bold  these siteaccesses
./console exp:viewcount:update --dry-run --from-start       the whole log again, as a preview only
./console exp:viewcount:update --skip-to-end                start counting from now: mark the logs as read
./console exp:viewcount:update --reset-state                forget where the runs stopped (the next run reads
                                                            the logs from their first line)
./console exp:viewcount:update --dry-run --format=json      the report as JSON (exp-viewcount/1)
./console exp:viewcount:update --all-siteaccesses --dry-run --log-file=/var/log/httpd/access_log.1 \
      --state-dir=var/tmp/viewcount-trial                    a trial on another log, with a state of its own

Exit code: 0 fine, 1 an error (a log missing, the database refused the counts), 2 usage error,
3 another run held the lock of a log.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--dry-run` |  | Lesen und berichten, weder die Zahlen noch den Zustand schreiben |
| `--all-siteaccesses` |  | Jeder Siteaccess aus AvailableSiteAccessList mit einem Zugriffsprotokoll, außer den Administrationen |
| `--siteaccess-list` | erforderlich | Diese Siteaccesses, durch Kommas getrennt (Standard: logfile.ini [ViewCountSettings] SiteAccessList[], sonst der Siteaccess von -s) |
| `--log-file` | erforderlich | Dieses Zugriffsprotokoll für jeden gezählten Siteaccess lesen, statt logfile.ini [AccessLogFileSettings] |
| `--state-dir` | erforderlich | Den Zustand hier ablegen, statt in logfile.ini [ViewCountSettings] StateDir |
| `--from-start` |  | Mit --dry-run: die Protokolle ab ihrer ersten Zeile lesen, ohne den Zustand zu beachten |
| `--skip-to-end` |  | Nichts zählen; den Zustand auf das Ende der Protokolle setzen, damit der nächste Lauf nur neuere Zeilen zählt |
| `--reset-state` |  | Nichts zählen; den Zustand der Siteaccesses entfernen, damit der nächste Lauf die Protokolle ab ihrer ersten Zeile liest |
| `--top` | erforderlich | Die meistbesuchten Knoten des Laufs auflisten (Standard 0, keine) |
| `--format` | erforderlich | text (Standard) oder json |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expviewcountupdate.php`, `kernel/private/classes/commands/expviewcountupdate.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
