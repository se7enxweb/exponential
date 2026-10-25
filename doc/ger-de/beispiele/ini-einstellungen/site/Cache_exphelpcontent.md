---
title: site.ini [Cache_exphelpcontent]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Cache_exphelpcontent] in site.ini: name, id, tags, class, purgeClass"
keywords: Einstellung, site.ini, Cache_exphelpcontent, INI, setting, name, id, tags, class, purgeClass
type: generated
generated: exp:help:examples
---
# site.ini [Cache_exphelpcontent]

Der Block `[Cache_exphelpcontent]` von `settings/site.ini`, mit 5 Einstellung(en).

> The rows of help maintained as content (var/<site>/cache/exphelp/content/repository-\*.cache).  
> It checks itself against the help subtree on every request and reads again only  
> the objects that changed, so it needs no clearing after a publish; clearing it  
> makes the next help page read every help object (a few seconds).  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## name

Standard: `Help content cache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_exphelpcontent]
name=Help content cache
*/ ?>
```

## id

Standard: `exphelpcontent`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_exphelpcontent]
id=exphelpcontent
*/ ?>
```

## tags

Standard: eine Liste mit 1: `exphelp`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_exphelpcontent]
# ein leeres "tags[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
tags[]
tags[]=exphelp
*/ ?>
```

## class

Standard: `expHelpContentRepository`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_exphelpcontent]
class=expHelpContentRepository
*/ ?>
```

## purgeClass

Standard: `expHelpContentRepository`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[Cache_exphelpcontent]
purgeClass=expHelpContentRepository
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
