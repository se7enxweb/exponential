---
title: content.ini [ContentOverrideSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentOverrideSettings] in content.ini: EnableClassGroupOverride"
keywords: Einstellung, content.ini, ContentOverrideSettings, INI, setting, EnableClassGroupOverride
type: generated
generated: exp:help:examples
---
# content.ini [ContentOverrideSettings]

Der Block `[ContentOverrideSettings]` von `settings/content.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## EnableClassGroupOverride

> Set this to true if you would like to do an override  
> based on the class group of the content  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[ContentOverrideSettings]
EnableClassGroupOverride=false
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
