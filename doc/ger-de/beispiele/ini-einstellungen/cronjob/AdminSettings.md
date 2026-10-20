---
title: cronjob.ini [AdminSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdminSettings] in cronjob.ini: ForbiddenParts, PhpCliPath, LogFile, ErrorFile"
keywords: Einstellung, cronjob.ini, AdminSettings, INI, setting, ForbiddenParts, PhpCliPath, LogFile, ErrorFile
type: generated
generated: exp:help:examples
---
# cronjob.ini [AdminSettings]

Der Block `[AdminSettings]` von `settings/cronjob.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ForbiddenParts

> Einstellungen der Cronjob-Konsole unter Setup > Cronjobs, die einen  
> Cronjob-Teil aus der Administrationsoberfläche startet.  
> Teile, die sich nicht aus einem Browser starten lassen dürfen. Beide Standards sind  
> destruktiv und gehören in die Hand eines Administrators auf einer Shell: cluster\_maintenance  
> löscht Cluster-Speicher, und unlock gibt Sperren frei, die andere Prozesse halten.  

Standard: eine Liste mit 2: `cluster_maintenance`, `unlock`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[AdminSettings]
# ein leeres "ForbiddenParts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ForbiddenParts[]
ForbiddenParts[]=cluster_maintenance
ForbiddenParts[]=unlock
*/ ?>
```

## PhpCliPath

> Vollständiger Pfad zur PHP-Kommandozeilen-Binärdatei, mit der runcronjobs.php ausgeführt wird. Bleibt er  
> leer, werden die üblichen Orte versucht, was in einer normalen Installation genügt.  
> Es muss eine Kommandozeilen-Binärdatei sein: Die, die die Web-Anfrage ausführt, kann kein  
> Skript ausführen.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[AdminSettings]
PhpCliPath=
*/ ?>
```

## LogFile

> Wohin Ausgabe und Fehler eines gestarteten Cronjobs geschrieben werden, relativ zum var-  
> Verzeichnis des Siteaccess. Die Konsole liest sie und verfolgt sie, während sie  
> wachsen, solange ein Auftrag läuft.  

Standard: `cronjobs/output.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[AdminSettings]
LogFile=cronjobs/output.log
*/ ?>
```

## ErrorFile

Standard: `cronjobs/error.log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[AdminSettings]
ErrorFile=cronjobs/error.log
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
