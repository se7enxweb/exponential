---
title: datatype.ini [ResultSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ResultSettings] in datatype.ini: GroupedInput"
keywords: Einstellung, datatype.ini, ResultSettings, INI, setting, GroupedInput
type: generated
generated: exp:help:examples
---
# datatype.ini [ResultSettings]

Der Block `[ResultSettings]` von `settings/datatype.ini`, mit 1 Einstellung(en).

> Einstellungen zu Ergebnissen (nach der Informationssammlung) und zu Datentypen  

Bearbeiten Sie `settings/datatype.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/datatype.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/datatype.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/datatype.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## GroupedInput

> Eine Liste der Datentypen, die ihre Daten gruppieren müssen,  
> wenn sie nach der Informationssammlung angezeigt werden  
> HINWEIS: Der Datentyp kann dies per PHP-Code überschreiben  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatype.ini.append.php
[ResultSettings]
# ein leeres "GroupedInput[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupedInput[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von datatype.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/datatype.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
