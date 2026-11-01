---
title: exp:expwtmigratefromezwt
audience: developer, admin
category: examples
summary: "Ersetzt ezwt (die Website-Werkzeugleiste) durch expwt (die Website-Seitenleiste): Einstellungen, Richtlinien, Aktivierung, Zeitplan (Probelauf ohne --apply)."
keywords: Konsole, Befehl, exp:expwtmigratefromezwt, expwtmigratefromezwt, console, command, CLI, expwtmigratefromezwt.php, exp:wt:migrate-from-ezwt
type: generated
generated: exp:help:examples
---
# exp:expwtmigratefromezwt

Ersetzt ezwt (die Website-Werkzeugleiste) durch expwt (die Website-Seitenleiste): Einstellungen, Richtlinien, Aktivierung, Zeitplan (Probelauf ohne --apply).

Skript `bin/php/expwtmigratefromezwt.php`, Code in `kernel/private/classes/commands/expwtmigratefromezwt.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expwtmigratefromezwt --help
./console exp:wt:migrate-from-ezwt --help
php bin/php/expwtmigratefromezwt.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
Replaces ezwt, the website toolbar, by expwt, the website sidebar: copies ezwt's hidden classes
(websitetoolbar.ini) to expwt.ini in settings/override and every siteaccess, gives every
websitetoolbar/use policy as expwt/use, edit, create and structure with the same limitations, the
Editor role those four and the Administrator role expwt/*, and puts expwt in ezwt's place in
ActiveExtensions[] and ActiveAccessExtensions[]. A changed file is kept as <file>.before-expwt.
The sidebar itself stays off until switched on (expwt.ini [ExpwtSettings] Enabled).
It also moves the schedule of earlier expwt versions (publish later, take down) to the kernel's
schedule: the wait-until-date workflow and the hide cronjob. An entry whose class has no date field
the kernel reads stays and is listed as a note; run the command again once it has one.
Without --apply nothing is written. The upgrade step 6.0.15-expwt-enable does the same.

Example: php bin/php/expwtmigratefromezwt.php
         php bin/php/expwtmigratefromezwt.php --apply
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--apply` |  | Die Änderungen schreiben (Standard ist ein Probelauf, der sie auflistet und nichts ändert) |
| `--dry-run` |  | Die Voreinstellung: auflisten, was sich ändern würde, nichts ändern |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expwtmigratefromezwt.php`, `kernel/private/classes/commands/expwtmigratefromezwt.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
