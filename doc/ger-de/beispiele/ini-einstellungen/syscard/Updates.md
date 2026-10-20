---
title: syscard.ini [Updates]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Updates] in syscard.ini: AskUpdateInventory, CacheTime"
keywords: Einstellung, syscard.ini, Updates, INI, setting, AskUpdateInventory, CacheTime
type: generated
generated: exp:help:examples
---
# syscard.ini [Updates]

Der Block `[Updates]` von `settings/syscard.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/syscard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/syscard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/syscard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/syscard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AskUpdateInventory

> enabled: count the packages that do not agree between composer.json, composer.lock and vendor/ (ezupdate's  
> inventory, local files only, when the ezupdate extension is active)  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Updates]
AskUpdateInventory=enabled
*/ ?>
```

## CacheTime

> Seconds the count is kept in the cache directory (var/<site>/cache/syscard/); 0 keeps nothing  

Standard: `3600`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/syscard.ini.append.php
[Updates]
CacheTime=3600
*/ ?>
```

## Siehe auch

- [Alle Blöcke von syscard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/syscard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
