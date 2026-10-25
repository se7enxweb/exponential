---
title: exp:installcrontab
audience: developer, admin
category: examples
summary: "Show, add or remove the cron job of the installation: the crontab lines that run its cronjob parts (--dry-run says what would change)."
keywords: Konsole, Befehl, exp:installcrontab, installcrontab, console, command, CLI, installcrontab.php
type: generated
generated: exp:help:examples
---
# exp:installcrontab

Show, add or remove the cron job of the installation: the crontab lines that run its cronjob parts (--dry-run says what would change).

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
| `--show` |  | The planned lines, the block in the crontab and the last runs (the default) |
| `--add` |  | Add or update the block |
| `--remove` |  | Remove the block |
| `--dry-run` |  | With --add or --remove: show what would change, change nothing |
| `--admin-siteaccess` | erforderlich | The siteaccess of the admin parts (default: [CrontabSettings] AdminSiteAccess, else admin) |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/installcrontab.php`, `kernel/private/classes/commands/installcrontab.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
