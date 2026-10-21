---
title: site.ini [AdminSiteAccessMenu]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AdminSiteAccessMenu] in site.ini: Show, List, HiddenSiteAccessList, AdminSiteAccessList, ListAdministrations"
keywords: Einstellung, site.ini, AdminSiteAccessMenu, INI, setting, Show, List, HiddenSiteAccessList, AdminSiteAccessList, ListAdministrations
type: generated
generated: exp:help:examples
---
# site.ini [AdminSiteAccessMenu]

Der Block `[AdminSiteAccessMenu]` von `settings/site.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Show

> Das Website-Menü der Administration (Designs admin4 und admin4l): Die Globus-  
> Schaltfläche im Kopf öffnet eine Liste der Siteaccesses dieser Installation mit  
> ihren Adressen. Ihr erster Eintrag öffnet die Website, wie es der Globus tat; die  
> anderen sind in Websites und Administrationen gruppiert, und der verwendete Siteaccess  
> ist markiert. Nur für angemeldete Benutzer. Die Adressen werden so ermittelt wie  
> von siteaccess\_url() (MatchOrder, URIMatchType, HostMatchMapItems, SiteURL),  
> mit Schema, Host und Port der Anfrage.  
> Aus den Einstellungen des Administrations-Siteaccess gelesen.  
>
> enabled oder disabled; disabled lässt den Globus einen einfachen Link zur Website sein  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminSiteAccessMenu]
Show=enabled
*/ ?>
```

## List

> related: \[SiteAccessSettings\] RelatedSiteAccessList\[\] dieses Siteaccess (die  
> Siteaccesses, die seine Datenbank teilen), oder dieser Siteaccess allein, wo sie  
> leer ist; available: AvailableSiteAccessList\[\]. In beiden Fällen werden nur Siteaccesses aus  
> AvailableSiteAccessList\[\] aufgeführt. Eine Installation, die  
> mehrere Websites bedient, setzt RelatedSiteAccessList\[\] für jede Administration, damit sie  
> die Siteaccesses ihrer eigenen Website auflistet.  

Standard: `related`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminSiteAccessMenu]
List=related
*/ ?>
```

## HiddenSiteAccessList

> Siteaccesses, die im Menü ausgelassen werden. Die Website, die der Globus öffnet (FrontSiteAccess,  
> sonst DefaultAccess), ist immer sein erster Eintrag.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminSiteAccessMenu]
# ein leeres "HiddenSiteAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HiddenSiteAccessList[]
*/ ?>
```

## AdminSiteAccessList

> Die Siteaccesses, die als Administrationen gelten. Leer: die, deren  
> \[DesignSettings\] SiteDesign mit "admin" beginnt oder die eine  
> \[SiteSettings\] FrontSiteAccess setzen.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminSiteAccessMenu]
# ein leeres "AdminSiteAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdminSiteAccessList[]
*/ ?>
```

## ListAdministrations

> disabled: Das Menü listet nur Websites auf; enabled: auch die Administrationen, als  
> eigene Gruppe  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminSiteAccessMenu]
ListAdministrations=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
