---
title: subitems.ini [Defaults_media]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Defaults_media] in subitems.ini: Subtree, ParentClassIdentifiers, Columns"
keywords: Einstellung, subitems.ini, Defaults_media, INI, setting, Subtree, ParentClassIdentifiers, Columns
type: generated
generated: exp:help:examples
---
# subitems.ini [Defaults_media]

Der Block `[Defaults_media]` von `settings/subitems.ini`, mit 3 Einstellung(en).

> Standards pro Ort: die Spalten, die unter manchen Elternknoten standardmäßig angezeigt werden (bevor der Benutzer  
> eine eigene Wahl speichert). Der erste passende Block gewinnt, in der Reihenfolge dieser Datei:  
> \[Defaults\_<id>\]  
> Subtree\[\]                 Knoten-IDs: passt, wenn der Elternknoten einer davon ist oder darunter liegt  
> ParentClassIdentifiers\[\]  Klassenbezeichner: passt, wenn der Elternknoten zu einer davon gehört  
> Columns\[\]                 die sichtbaren Spalten, in Reihenfolge  
> Die Medienbibliothek (content.ini \[NodeSettings\] MediaRootNode=43); die früheren Medienstandards der Liste  

Bearbeiten Sie `settings/subitems.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitems.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitems.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitems.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Subtree

Standard: eine Liste mit 1: `43`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[Defaults_media]
# ein leeres "Subtree[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Subtree[]
Subtree[]=43
*/ ?>
```

## ParentClassIdentifiers

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[Defaults_media]
# ein leeres "ParentClassIdentifiers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ParentClassIdentifiers[]
*/ ?>
```

## Columns

Standard: eine Liste mit 4: `thumbnail`, `name`, `visibility`, `type`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[Defaults_media]
# ein leeres "Columns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Columns[]
Columns[]=thumbnail
Columns[]=name
Columns[]=visibility
Columns[]=type
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitems.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitems.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
