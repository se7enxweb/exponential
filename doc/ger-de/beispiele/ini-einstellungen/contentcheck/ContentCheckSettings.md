---
title: contentcheck.ini [ContentCheckSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ContentCheckSettings] in contentcheck.ini: Enabled, SiteAccessList"
keywords: Einstellung, contentcheck.ini, ContentCheckSettings, INI, setting, Enabled, SiteAccessList
type: generated
generated: exp:help:examples
---
# contentcheck.ini [ContentCheckSettings]

Der Block `[ContentCheckSettings]` von `settings/contentcheck.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/contentcheck.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/contentcheck.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/contentcheck.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/contentcheck.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> true | false. With false the checks answer 404 on every siteaccess.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[ContentCheckSettings]
Enabled=true
*/ ?>
```

## SiteAccessList

> The siteaccesses the checks answer on. Empty (the default): every siteaccess whose design  
> list (\[DesignSettings\] SiteDesign, AdditionalSiteDesignList\[\]) has an administration design  
> (a design whose name starts with admin). Every other siteaccess answers 404.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[ContentCheckSettings]
# ein leeres "SiteAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SiteAccessList[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von contentcheck.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentcheck.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
