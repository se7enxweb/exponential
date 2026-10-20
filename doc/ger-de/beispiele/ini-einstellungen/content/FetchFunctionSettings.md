---
title: content.ini [FetchFunctionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FetchFunctionSettings] in content.ini: InstClassListFilter"
keywords: Einstellung, content.ini, FetchFunctionSettings, INI, setting, InstClassListFilter
type: generated
generated: exp:help:examples
---
# content.ini [FetchFunctionSettings]

Der Block `[FetchFunctionSettings]` von `settings/content.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## InstClassListFilter

> Setting used by 'can\_instantiate\_class\_list' fetch function to hide  
> certain content classes. Supported are only content class identifiers  
> Filtering is only available when 'group\_by\_class\_group' param is used  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[FetchFunctionSettings]
# ein leeres "InstClassListFilter[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
InstClassListFilter[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
