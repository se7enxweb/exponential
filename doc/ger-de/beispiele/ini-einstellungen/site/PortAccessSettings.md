---
title: site.ini [PortAccessSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PortAccessSettings] in site.ini: 1337, 1338"
keywords: Einstellung, site.ini, PortAccessSettings, INI, setting, 1337, 1338
type: generated
generated: exp:help:examples
---
# site.ini [PortAccessSettings]

Der Block `[PortAccessSettings]` von `settings/site.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## 1337

> MatchOrder : port  
> Fügen Sie hier Einträge hinzu, wenn Sie port in MatchOrder haben  
> Jeder Eintrag besteht aus port=accessname  

Standard: `user`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PortAccessSettings]
1337=user
*/ ?>
```

## 1338

Standard: `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[PortAccessSettings]
1338=admin
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
