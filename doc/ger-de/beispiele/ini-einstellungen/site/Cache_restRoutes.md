---
title: site.ini [Cache_restRoutes]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Cache_restRoutes] in site.ini: name, id, tags, class, purgeClass"
keywords: Einstellung, site.ini, Cache_restRoutes, INI, setting, name, id, tags, class, purgeClass
type: generated
generated: exp:help:examples
---
# site.ini [Cache_restRoutes]

Der Block `[Cache_restRoutes]` von `settings/site.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## name

Standard: `REST Routes memory cache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_restRoutes]
name=REST Routes memory cache
*/ ?>
```

## id

Standard: `rest-routes`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_restRoutes]
id=rest-routes
*/ ?>
```

## tags

Standard: eine Liste mit 1: `rest`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_restRoutes]
# ein leeres "tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
tags[]
tags[]=rest
*/ ?>
```

## class

Standard: `ezpRestRoutesCacheClear`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_restRoutes]
class=ezpRestRoutesCacheClear
*/ ?>
```

## purgeClass

Standard: `ezpRestRoutesCacheClear`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_restRoutes]
purgeClass=ezpRestRoutesCacheClear
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
