---
title: site.ini [AppendNodeIDFilterSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AppendNodeIDFilterSettings] in site.ini: ApplyOnClass"
keywords: Einstellung, site.ini, AppendNodeIDFilterSettings, INI, setting, ApplyOnClass
type: generated
generated: exp:help:examples
---
# site.ini [AppendNodeIDFilterSettings]

Der Block `[AppendNodeIDFilterSettings]` von `settings/site.ini`, mit 1 Einstellung(en).

> Diese Konfigurationsgruppe gehört  
> zum URL-Filter eZURLAliasFilterAppendNodeID  
> Damit lässt sich filtern, an welche Art von  
> Inhalten die Knoten-ID angehängt wird.  
>
> Wenn Sie zum Beispiel die Knoten-ID  
> für Inhaltsobjekte vom Typ folder und article anhängen wollen, könnten Sie  
> die folgende Konfiguration verwenden  
>
> ApplyOnClass\[\]  
> ApplyOnClass\[\]=folder  
> ApplyOnClass\[\]=article  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ApplyOnClass

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AppendNodeIDFilterSettings]
# ein leeres "ApplyOnClass[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ApplyOnClass[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
