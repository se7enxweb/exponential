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

> true | false. Aus: Nirgends wird eine Wahl angeboten oder angewendet.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitchSettings]
Enabled=true
*/ ?>
```

## SiteAccessList

> Die Siteaccesses, in denen die Auswahlen wirken. Leer: jeder Siteaccess, dessen Design-Liste ein  
> Administrations-Design enthält (admin, admin2, admin3, admin4, admin4l, adminui; die Siteaccesses editor und adminui  
> fallen auf sie zurück). Nie eine öffentliche Website.  

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

> Die Provider, in der Reihenfolge der Werkzeugleiste. Jeder braucht eine Gruppe \[UISwitch\_<identifier>\] mit Class= und erhält  
> die Richtlinienfunktion uiswitch/<identifier>.  

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
