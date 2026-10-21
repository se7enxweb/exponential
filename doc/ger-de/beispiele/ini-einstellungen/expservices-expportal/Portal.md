---
title: expportal.ini [Portal]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Portal] in expportal.ini (expservices): SiteTitle, NewsNode, ShopNode, ForumsNode, MediaNode, PageSize"
keywords: Einstellung, expportal.ini, Portal, INI, setting, SiteTitle, NewsNode, ShopNode, ForumsNode, MediaNode, PageSize
type: generated
generated: exp:help:examples
---
# expportal.ini [Portal]

Der Block `[Portal]` von `extension/expservices/settings/expportal.ini` (Erweiterung expservices), mit 6 Einstellung(en).

> Einstellungen der Portal-Frontends (Designs expportal\_jquery und expportal\_reactive).  
> Knoten-IDs der Inhaltswurzeln, die jede Seite liest. Ändern Sie sie pro Installation; die Standardwerte sind die üblichen Wurzeln.  

Bearbeiten Sie `extension/expservices/settings/expportal.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/expportal.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/expportal.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/expportal.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SiteTitle

Standard: `Exponential Portal`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expportal.ini.append.php
[Portal]
SiteTitle=Exponential Portal
*/ ?>
```

## NewsNode

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expportal.ini.append.php
[Portal]
NewsNode=2
*/ ?>
```

## ShopNode

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expportal.ini.append.php
[Portal]
ShopNode=2
*/ ?>
```

## ForumsNode

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expportal.ini.append.php
[Portal]
ForumsNode=2
*/ ?>
```

## MediaNode

Standard: `43`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expportal.ini.append.php
[Portal]
MediaNode=43
*/ ?>
```

## PageSize

Standard: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/expportal.ini.append.php
[Portal]
PageSize=12
*/ ?>
```

## Siehe auch

- [Alle Blöcke von expportal.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/expservices/settings/expportal.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
