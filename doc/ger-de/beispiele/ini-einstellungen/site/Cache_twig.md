---
title: site.ini [Cache_twig]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Cache_twig] in site.ini: name, id, tags, path"
keywords: Einstellung, site.ini, Cache_twig, INI, setting, name, id, tags, path
type: generated
generated: exp:help:examples
---
# site.ini [Cache_twig]

Der Block `[Cache_twig]` von `settings/site.ini`, mit 4 Einstellung(en).

> Die kompilierten .twig-Templates von Twig (lib/twig), in <Cache-Verzeichnis>/twig/. Geleert mit  
> den Template-Caches.  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## name

Standard: `Twig compiled templates`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_twig]
name=Twig compiled templates
*/ ?>
```

## id

Standard: `twig`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_twig]
id=twig
*/ ?>
```

## tags

Standard: eine Liste mit 1: `template`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_twig]
# ein leeres "tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
tags[]
tags[]=template
*/ ?>
```

## path

Standard: `twig`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_twig]
path=twig
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
