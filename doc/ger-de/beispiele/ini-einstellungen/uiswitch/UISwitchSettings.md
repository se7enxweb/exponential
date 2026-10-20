---
title: uiswitch.ini [UISwitchSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UISwitchSettings] in uiswitch.ini: Enabled, SiteAccessList, ProviderList"
keywords: Einstellung, uiswitch.ini, UISwitchSettings, INI, setting, Enabled, SiteAccessList, ProviderList
type: generated
generated: exp:help:examples
---
# uiswitch.ini [UISwitchSettings]

Der Block `[UISwitchSettings]` von `settings/uiswitch.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/uiswitch.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/uiswitch.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/uiswitch.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/uiswitch.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> true | false. Off: no choice is offered or applied anywhere.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitchSettings]
Enabled=true
*/ ?>
```

## SiteAccessList

> The siteaccesses where the choices work. Empty: every siteaccess whose design list has an  
> administration design (admin, admin2, admin3, admin4, admin4l, adminui; the editor and adminui  
> siteaccesses fall back to them). Never a public site.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitchSettings]
# ein leeres "SiteAccessList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SiteAccessList[]
*/ ?>
```

## ProviderList

> The providers, in toolbar order. Each needs a \[UISwitch\_<identifier>\] group with Class=, and gets  
> the policy function uiswitch/<identifier>.  

Standard: eine Liste mit 2: `language`, `design`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitchSettings]
# ein leeres "ProviderList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ProviderList[]
ProviderList[]=language
ProviderList[]=design
*/ ?>
```

## Siehe auch

- [Alle Blöcke von uiswitch.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/uiswitch.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
