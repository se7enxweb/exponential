---
title: cronjob.ini [CronjobSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CronjobSettings] in cronjob.ini: ScriptDirectories, Scripts, ExtensionDirectories, MaxScriptExecutionTime"
keywords: Einstellung, cronjob.ini, CronjobSettings, INI, setting, ScriptDirectories, Scripts, ExtensionDirectories, MaxScriptExecutionTime
type: generated
generated: exp:help:examples
---
# cronjob.ini [CronjobSettings]

Der Block `[CronjobSettings]` von `settings/cronjob.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ScriptDirectories

Standard: eine Liste mit 1: `cronjobs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobSettings]
# ein leeres "ScriptDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ScriptDirectories[]
ScriptDirectories[]=cronjobs
*/ ?>
```

## Scripts

> Seit 4.6 ist das folgende Skript standardmäßig nicht aktiviert  
> Scripts\[\]=subtreeexpirycleanup.php  

Standard: eine Liste mit 5: `unpublish.php`, `rssimport.php`, `indexcontent.php`, `hide.php`, `internal_drafts_cleanup.php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobSettings]
# ein leeres "Scripts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scripts[]
Scripts[]=unpublish.php
Scripts[]=rssimport.php
Scripts[]=indexcontent.php
Scripts[]=hide.php
Scripts[]=internal_drafts_cleanup.php
*/ ?>
```

## ExtensionDirectories

> Scripts\[\]=unlock.php  
> Scripts\[\]=staticcache\_cleanup.php  
> Scripts\[\]=updateviewcount.php  
> Verzeichnis der Erweiterung für Cronjobs.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## MaxScriptExecutionTime

> Von einem Cronjob-Skript kann jederzeit höchstens eine Instanz laufen.  
> Braucht ein Skript länger als MaxScriptExecutionTime, versucht die nächste Instanz,  
> den Mutex des Cronjob-Skripts sauber zu übernehmen. Läuft der Prozess schon länger  
> als zweimal MaxScriptExecutionTime, wird der ursprüngliche  
> Prozess beendet. Der Standard ist 12 Stunden. MaxScriptExecutionTime lässt sich  
> auch pro Cronjob-Teil setzen, was diese globale Einstellung überschreibt.  

Standard: `43200`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobSettings]
MaxScriptExecutionTime=43200
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
