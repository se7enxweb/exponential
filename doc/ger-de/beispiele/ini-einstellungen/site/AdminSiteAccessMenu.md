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

> The site menu of the administration (designs admin4 and admin4l): the globe  
> button in the header opens a list of this installation's siteaccesses with  
> their addresses. Its first entry opens the site, as the globe did; the  
> others are grouped into sites and administrations, and the siteaccess in use  
> is marked. Signed-in users only. The addresses are worked out as  
> siteaccess\_url() does (MatchOrder, URIMatchType, HostMatchMapItems, SiteURL),  
> with the scheme, host and port of the request.  
> Read from the settings of the administration siteaccess.  
>
> enabled or disabled; disabled leaves the globe a plain link to the site  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminSiteAccessMenu]
Show=enabled
*/ ?>
```

## List

> related: \[SiteAccessSettings\] RelatedSiteAccessList\[\] of this siteaccess (the  
> siteaccesses sharing its database), or this siteaccess alone where it is  
> empty; available: AvailableSiteAccessList\[\]. Only siteaccesses of  
> AvailableSiteAccessList\[\] are listed either way. An installation that serves  
> several sites sets RelatedSiteAccessList\[\] for each administration to list  
> its own site's siteaccesses.  

Standard: `related`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[AdminSiteAccessMenu]
List=related
*/ ?>
```

## HiddenSiteAccessList

> Siteaccesses left out of the menu. The site the globe opens (FrontSiteAccess,  
> else DefaultAccess) is always its first entry.  

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

> The siteaccesses that count as administrations. Empty: those whose  
> \[DesignSettings\] SiteDesign begins with "admin", or that set a  
> \[SiteSettings\] FrontSiteAccess.  

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

> disabled: the menu lists sites only; enabled: the administrations too, as a  
> group of their own  

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
