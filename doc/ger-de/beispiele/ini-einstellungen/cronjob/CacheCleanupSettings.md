---
title: cronjob.ini [CacheCleanupSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CacheCleanupSettings] in cronjob.ini: MaxAge, IterationSleep, RenameAfterClear"
keywords: Einstellung, cronjob.ini, CacheCleanupSettings, INI, setting, MaxAge, IterationSleep, RenameAfterClear
type: generated
generated: exp:help:examples
---
# cronjob.ini [CacheCleanupSettings]

Der Block `[CacheCleanupSettings]` von `settings/cronjob.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxAge

> Sekunden, nach denen eine Datei unabhängig von ihrem Ablauf entfernt wird. Ein kalter Eintrag, der  
> noch gültig war, kostet eine Neuerzeugung. 0 entfernt nur abgelaufene Dateien.  

Standard: `172800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CacheCleanupSettings]
MaxAge=172800
*/ ?>
```

## IterationSleep

> Mikrosekunden Pause nach je 1000 Dateien, um die Festplatte zu schonen. 0 = keine.  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CacheCleanupSettings]
IterationSleep=0
*/ ?>
```

## RenameAfterClear

> Nach dem globalen Leeren eines Caches sein ganzes Verzeichnis mit einer Umbenennung beiseiteschieben  
> und dort löschen, statt jede Datei anzusehen. Einträge, die zwischen  
> dem Leeren und dem Lauf gespeichert wurden, gehen verloren und werden neu erzeugt. disabled entfernt  
> Datei für Datei. Ein Leeren in der Administrationsoberfläche oder mit  
> bin/php/ezcache.php schiebt das Verzeichnis schon selbst beiseite (site.ini  
> \[FileSettings\] RenameExpiredCaches) und vermerkt das hier, sodass dieser Lauf es  
> nicht ein zweites Mal verschiebt.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[CacheCleanupSettings]
RenameAfterClear=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
