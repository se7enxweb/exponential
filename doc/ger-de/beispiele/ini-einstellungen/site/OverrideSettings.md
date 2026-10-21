---
title: site.ini [OverrideSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [OverrideSettings] in site.ini: Cache"
keywords: Einstellung, site.ini, OverrideSettings, INI, setting, Cache
type: generated
generated: exp:help:examples
---
# site.ini [OverrideSettings]

Der Block `[OverrideSettings]` von `settings/site.ini`, mit 1 Einstellung(en).

> Seit 5.3 lässt sich eine Liste der Datentypen definieren, die Preise enthalten  
> Damit können Sie eigene Preis-Datentypen definieren und mit ihnen  
> im Modul shop arbeiten  
> Die Einstellung ist standardmäßig auskommentiert  
> ProductDatatypeStringList\[\]  
> ProductDatatypeStringList\[\]=ezprice  
> ProductDatatypeStringList\[\]=ezmultiprice  
> ProductDatatypeStringList\[\]=customprice  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Cache

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[OverrideSettings]
Cache=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
