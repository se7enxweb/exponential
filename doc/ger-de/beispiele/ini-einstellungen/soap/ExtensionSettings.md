---
title: soap.ini [ExtensionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ExtensionSettings] in soap.ini: SOAPExtensions"
keywords: Einstellung, soap.ini, ExtensionSettings, INI, setting, SOAPExtensions
type: generated
generated: exp:help:examples
---
# soap.ini [ExtensionSettings]

Der Block `[ExtensionSettings]` von `settings/soap.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/soap.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/soap.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/soap.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/soap.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SOAPExtensions

> Erweiterungen für SOAP-Funktionen.  
> SOAP-Funktionen müssen unter soap/initialize.php registriert sein  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/soap.ini.append.php
[ExtensionSettings]
# ein leeres "SOAPExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SOAPExtensions[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von soap.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/soap.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
