---
title: transform.ini [lowercase]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [lowercase] in transform.ini: Files, Extensions, Commands"
keywords: Einstellung, transform.ini, lowercase, INI, setting, Files, Extensions, Commands
type: generated
generated: exp:help:examples
---
# transform.ini [lowercase]

Der Block `[lowercase]` von `settings/transform.ini`, mit 3 Einstellung(en).

> Regeln, die Zeichen in Kleinbuchstaben umwandeln  
> Diese Regeln arbeiten ähnlich wie strtolower in PHP, funktionieren aber  
> unabhängig von der verwendeten Locale.  

Bearbeiten Sie `settings/transform.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/transform.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/transform.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/transform.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Files

> Zusätzliche Umwandlungsdateien für Bezeichner  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[lowercase]
# ein leeres "Files[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Files[]
*/ ?>
```

## Extensions

> Erweiterungen, die Umwandlungsdateien für Bezeichner haben  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[lowercase]
# ein leeres "Extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Extensions[]
*/ ?>
```

## Commands

> Die für Bezeichner verwendeten Befehle  

Standard: eine Liste mit 1: `lowercase`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[lowercase]
# ein leeres "Commands[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Commands[]
Commands[]=lowercase
*/ ?>
```

## Siehe auch

- [Alle Blöcke von transform.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/transform.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
