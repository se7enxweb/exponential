---
title: cronjob.ini [CronjobPart-frequent]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CronjobPart-frequent] in cronjob.ini: Scripts"
keywords: Einstellung, cronjob.ini, CronjobPart-frequent, INI, setting, Scripts
type: generated
generated: exp:help:examples
---
# cronjob.ini [CronjobPart-frequent]

Der Block `[CronjobPart-frequent]` von `settings/cronjob.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Scripts

> Inhalts-Jobs, deren Worker gestorben ist oder die niemand gestartet hat (kernel/classes/contentjob/)  
> Die fälligen Synchronisationen der Datentabellen (doc/eng-us/features/6.0/data-tables.md; ohne sie nichts)  
> Das Audit: Sink-Spools, Alarmregeln; einmal am Tag Rotation, Archive, Aufbewahrung und der  
> Checkpoint (audit.ini \[AuditRotationSettings\] RotateAfter). Hier, damit eine Installation,  
> die nur die Gruppe frequent ausführt, keine zusätzliche Crontab-Zeile braucht.  
> Das Bounce-Postfach (mailpreferences.ini \[BounceSettings\]; tut nichts, bis es eingerichtet ist)  

Standard: eine Liste mit 6: `notification.php`, `workflow.php`, `contentjobs.php`, `datatablesync.php`, `audit.php`, `mailbounces.php`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CronjobPart-frequent]
# ein leeres "Scripts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Scripts[]
Scripts[]=notification.php
Scripts[]=workflow.php
Scripts[]=contentjobs.php
Scripts[]=datatablesync.php
Scripts[]=audit.php
Scripts[]=mailbounces.php
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
