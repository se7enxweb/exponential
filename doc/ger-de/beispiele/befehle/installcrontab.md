---
title: exp:installcrontab
audience: developer, admin
category: examples
summary: "Zeigt den Cronjob der Installation an, fügt ihn hinzu oder entfernt ihn: die Crontab-Zeilen, die ihre cronjob-Teile ausführen (--dry-run sagt, was sich ändern würde)."
keywords: Konsole, Befehl, exp:installcrontab, installcrontab, console, command, CLI, installcrontab.php
type: generated
generated: exp:help:examples
---
# exp:installcrontab

Zeigt den Cronjob der Installation an, fügt ihn hinzu oder entfernt ihn: die Crontab-Zeilen, die ihre cronjob-Teile ausführen (--dry-run sagt, was sich ändern würde).

Skript `bin/php/installcrontab.php`, Code in `kernel/private/classes/commands/installcrontab.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:installcrontab --help
php bin/php/installcrontab.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Show, add or remove the cron job of this installation: the crontab lines that run its cronjob
parts (runcronjobs.php) with the PHP binary and the siteaccess each needs, in the crontab of the
system user that owns the installation directory, between the lines
"# BEGIN Exponential cronjobs: <root>" and "# END Exponential cronjobs: <root>".
The parts are cronjob.ini [CrontabSettings] Parts[] that the installation defines (an extension's
when it is active), the schedules [AdminSettings] CrontabSchedule_<part>. --add replaces the block
or appends it and keeps every other line; a part another line of the crontab runs already is left
out. Run it as the owner of the installation or as root (crontab -u <owner>); otherwise, or without a
crontab command, it prints the lines to add by hand. Prints PASS or FAIL at the end.

Example: php bin/php/installcrontab.php --show
         php bin/php/installcrontab.php --add --dry-run
         php bin/php/installcrontab.php --add
         php bin/php/installcrontab.php --remove
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--show` |  | Die geplanten Zeilen, der Block in der Crontab und die letzten Läufe (der Standard) |
| `--add` |  | Fügt den Block hinzu oder aktualisiert ihn |
| `--remove` |  | Entfernt den Block |
| `--dry-run` |  | Mit --add oder --remove: zeigt, was sich ändern würde, ändert nichts |
| `--admin-siteaccess` | erforderlich | Der Siteaccess der Admin-Teile (Standard: [CrontabSettings] AdminSiteAccess, sonst admin) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/installcrontab.php`, `kernel/private/classes/commands/installcrontab.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
