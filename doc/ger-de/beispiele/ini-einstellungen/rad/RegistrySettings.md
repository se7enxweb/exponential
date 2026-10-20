---
title: rad.ini [RegistrySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RegistrySettings] in rad.ini: Registries"
keywords: Einstellung, rad.ini, RegistrySettings, INI, setting, Registries
type: generated
generated: exp:help:examples
---
# rad.ini [RegistrySettings]

Der Block `[RegistrySettings]` von `settings/rad.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/rad.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rad.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rad.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rad.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Registries

> The keys of the registries: \[Registry\_<key>\] for each. A key listed without its block is shown as broken.  

Standard: eine Liste mit 3: `eventlisteners`, `cronjobparts`, `auditsources`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[RegistrySettings]
# ein leeres "Registries[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Registries[]
Registries[]=eventlisteners
Registries[]=cronjobparts
Registries[]=auditsources
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rad.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rad.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
