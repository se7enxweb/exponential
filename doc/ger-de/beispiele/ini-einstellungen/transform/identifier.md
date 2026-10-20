---
title: transform.ini [identifier]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [identifier] in transform.ini: Files, Extensions, Commands"
keywords: Einstellung, transform.ini, identifier, INI, setting, Files, Extensions, Commands
type: generated
generated: exp:help:examples
---
# transform.ini [identifier]

Der Block `[identifier]` von `settings/transform.ini`, mit 3 Einstellung(en).

> Rules used by identifiers in Exponential in general.  
> The rules ensures that the text is transformed to  
> ascii equivalent characters.  
> The most important rules are the transliteration lowercase and  
> diacritical removal.  

Bearbeiten Sie `settings/transform.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/transform.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/transform.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/transform.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Files

> Extra transformation files for identifiers  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[identifier]
# ein leeres "Files[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Files[]
*/ ?>
```

## Extensions

> Extensions that have transformation files when identifiers are used  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[identifier]
# ein leeres "Extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Extensions[]
*/ ?>
```

## Commands

> The commands to use for identifiers  

Standard: eine Liste mit 7: `normalize`, `transform`, `decompose`, `transliterate`, `diacritical`, `lowercase`, `identifier_cleanup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[identifier]
# ein leeres "Commands[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Commands[]
Commands[]=normalize
Commands[]=transform
Commands[]=decompose
Commands[]=transliterate
Commands[]=diacritical
Commands[]=lowercase
Commands[]=identifier_cleanup
*/ ?>
```

## Siehe auch

- [Alle Blöcke von transform.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/transform.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
