---
title: exphelp.ini [TypeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TypeSettings] in exphelp.ini: PathRules, TypeNames"
keywords: Einstellung, exphelp.ini, TypeSettings, INI, setting, PathRules, TypeNames
type: generated
generated: exp:help:examples
---
# exphelp.ini [TypeSettings]

Der Block `[TypeSettings]` von `settings/exphelp.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PathRules

> Welche Art von Seite eine Datei ist: ihr Front Matter (type: guide), sonst die erste passende Regel.  
> <pattern>;<type>, der erste Treffer gewinnt  

Standard: eine Liste mit 16: `doc/eng-us/help/*;help`, `doc/eng-us/guides/*;guide`, `doc/eng-us/features/*;feature`, `doc/eng-us/bc/*;bc`, `doc/changelogs/*;changelog`, `doc/eng-us/install/*;install`, `doc/eng-us/INSTALL.md;install`, `doc/eng-us/UPDATE.md;install`, `doc/eng-us/UNINSTALL.md;install`, `doc/eng-us/specifications/*;specification`, `doc/eng-us/history/*;history`, `doc/eng-us/release-overview;history` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[TypeSettings]
# ein leeres "PathRules[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PathRules[]
PathRules[]=doc/eng-us/help/*;help
PathRules[]=doc/eng-us/guides/*;guide
PathRules[]=doc/eng-us/features/*;feature
PathRules[]=doc/eng-us/bc/*;bc
PathRules[]=doc/changelogs/*;changelog
PathRules[]=doc/eng-us/install/*;install
PathRules[]=doc/eng-us/INSTALL.md;install
PathRules[]=doc/eng-us/UPDATE.md;install
PathRules[]=doc/eng-us/UNINSTALL.md;install
PathRules[]=doc/eng-us/specifications/*;specification
PathRules[]=doc/eng-us/history/*;history
PathRules[]=doc/eng-us/release-overview;history
PathRules[]=doc/eng-us/examples/*;example
PathRules[]=doc/eng-us/standards/patterns/*;catalogue
PathRules[]=extension/*;extension
PathRules[]=*;reference
*/ ?>
```

## TypeNames

Standard: ein Hash mit 14: `help=Help`, `guide=Guide`, `feature=Feature`, `bc=Upgrade note`, `install=Installation`, `changelog=Changelog`, `specification=Specification`, `history=History`, `example=Example`, `generated=Generated reference`, `reference=Reference`, `catalogue=Generated catalogue` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[TypeSettings]
# ein leeres "TypeNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TypeNames[]
TypeNames[help]=Help
TypeNames[guide]=Guide
TypeNames[feature]=Feature
TypeNames[bc]=Upgrade note
TypeNames[install]=Installation
TypeNames[changelog]=Changelog
TypeNames[specification]=Specification
TypeNames[history]=History
TypeNames[example]=Example
TypeNames[generated]=Generated reference
TypeNames[reference]=Reference
TypeNames[catalogue]=Generated catalogue
TypeNames[extension]=Extension documentation
TypeNames[project]=Project file
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
