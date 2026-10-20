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

> Since 4.6 the following script is not activated by default  
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
> Extension directory for cronjobs.  

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

> No more than one instance of a cronjob script can be run at any given time.  
> If a script uses more time than MaxScriptExecutionTime, the next instance of  
> it will try to gracefully steal the cronjob script mutex. If the process has  
> been running for more than two times MaxScriptExecutionTime, the original  
> process will be killed. The default is 12 hours. MaxScriptExecutionTime can  
> also be set per cronjob part, that will override this global setting.  

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
