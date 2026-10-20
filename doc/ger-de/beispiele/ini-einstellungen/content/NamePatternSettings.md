---
title: content.ini [NamePatternSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [NamePatternSettings] in content.ini: ResolverClasses"
keywords: Einstellung, content.ini, NamePatternSettings, INI, setting, ResolverClasses
type: generated
generated: exp:help:examples
---
# content.ini [NamePatternSettings]

Der Block `[NamePatternSettings]` von `settings/content.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ResolverClasses

> PHP classes a content class's object name pattern or URL alias name pattern  
> may name in braces at its start, for example {myNameResolver} or  
> {myNameResolver} <title>: the class then makes the name. A listed class must  
> implement expNamePatternResolverInterface; a name that is not listed here is  
> ignored (and logged), and the pattern is resolved from its attribute tokens.  
> Empty by default.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[NamePatternSettings]
# ein leeres "ResolverClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ResolverClasses[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
