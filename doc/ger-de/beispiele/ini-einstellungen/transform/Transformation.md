---
title: transform.ini [Transformation]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Transformation] in transform.ini: Repository, Files, Extensions, Groups, Charsets"
keywords: Einstellung, transform.ini, Transformation, INI, setting, Repository, Files, Extensions, Groups, Charsets
type: generated
generated: exp:help:examples
---
# transform.ini [Transformation]

Der Block `[Transformation]` von `settings/transform.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/transform.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/transform.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/transform.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/transform.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Repository

> Where to look for transformation files.  
> The files all end in .tr  

Standard: `share/transformations`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[Transformation]
Repository=share/transformations
*/ ?>
```

## Files

> The standard transformation files to load  

Standard: eine Liste mit 2: `basic.tr`, `ascii.tr`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[Transformation]
# ein leeres "Files[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Files[]
Files[]=basic.tr
Files[]=ascii.tr
*/ ?>
```

## Extensions

> Extensions that have transformation files  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[Transformation]
# ein leeres "Extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Extensions[]
*/ ?>
```

## Groups

> The available transformation groups  
> each group can define the rules it uses  

Standard: eine Liste mit 7: `search`, `urlalias_iri`, `urlalias`, `urlalias_compat`, `identifier`, `uppercase`, `lowercase`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[Transformation]
# ein leeres "Groups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Groups[]
Groups[]=search
Groups[]=urlalias_iri
Groups[]=urlalias
Groups[]=urlalias_compat
Groups[]=identifier
Groups[]=uppercase
Groups[]=lowercase
*/ ?>
```

## Charsets

> Maps from a charset to the unicode group  
> The group identifier can be used to load certain  
> extra transformation files when that charset is used.  
> Each entry consists of:  
> <charset>;<group>  
> The latin group  
> The cyrrilic group  
> The greek group  
> The hebrew group  

Standard: eine Liste mit 53: `iso-8859-1;latin`, `iso-8859-2;latin`, `iso-8859-3;latin`, `iso-8859-4;latin`, `iso-8859-9;latin`, `iso-8859-10;latin`, `iso-8859-13;latin`, `iso-8859-14;latin`, `iso-8859-15;latin`, `cp437;latin`, `cp775;latin`, `cp850;latin` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[Transformation]
# ein leeres "Charsets[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Charsets[]
Charsets[]=iso-8859-1;latin
Charsets[]=iso-8859-2;latin
Charsets[]=iso-8859-3;latin
Charsets[]=iso-8859-4;latin
Charsets[]=iso-8859-9;latin
Charsets[]=iso-8859-10;latin
Charsets[]=iso-8859-13;latin
Charsets[]=iso-8859-14;latin
Charsets[]=iso-8859-15;latin
Charsets[]=cp437;latin
Charsets[]=cp775;latin
Charsets[]=cp850;latin
Charsets[]=cp852;latin
Charsets[]=cp857;latin
Charsets[]=cp860;latin
Charsets[]=cp861;latin
Charsets[]=cp862;latin
Charsets[]=cp863;latin
Charsets[]=cp865;latin
Charsets[]=cp932;latin
# ... 33 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von transform.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/transform.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
