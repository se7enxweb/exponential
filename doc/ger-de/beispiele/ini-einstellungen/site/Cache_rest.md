---
title: site.ini [Cache_rest]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Cache_rest] in site.ini: name, id, tags, path, isClustered"
keywords: Einstellung, site.ini, Cache_rest, INI, setting, name, id, tags, path, isClustered
type: generated
generated: exp:help:examples
---
# site.ini [Cache_rest]

Der Block `[Cache_rest]` von `settings/site.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## name

Standard: `REST Application cache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_rest]
name=REST Application cache
*/ ?>
```

## id

Standard: `rest`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_rest]
id=rest
*/ ?>
```

## tags

Standard: eine Liste mit 2: `content`, `rest`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_rest]
# ein leeres "tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
tags[]
tags[]=content
tags[]=rest
*/ ?>
```

## path

Standard: `rest`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_rest]
path=rest
*/ ?>
```

## isClustered

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_rest]
isClustered=true
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
