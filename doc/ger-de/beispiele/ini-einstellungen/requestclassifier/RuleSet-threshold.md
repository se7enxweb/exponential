---
title: requestclassifier.ini [RuleSet-threshold]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RuleSet-threshold] in requestclassifier.ini: Class"
keywords: Einstellung, requestclassifier.ini, RuleSet-threshold, INI, setting, Class
type: generated
generated: exp:help:examples
---
# requestclassifier.ini [RuleSet-threshold]

Der Block `[RuleSet-threshold]` von `settings/requestclassifier.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/requestclassifier.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/requestclassifier.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/requestclassifier.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/requestclassifier.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Die Grenzen stehen oben in \[ThresholdSettings\]. Regel: 404-rate.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestclassifier.ini.append.php
[RuleSet-threshold]
Class=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von requestclassifier.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestclassifier.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
