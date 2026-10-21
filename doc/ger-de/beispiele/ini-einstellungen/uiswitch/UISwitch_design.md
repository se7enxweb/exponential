---
title: uiswitch.ini [UISwitch_design]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UISwitch_design] in uiswitch.ini: Class, Enabled, AllowedValues, DesignChain, DesignLabel, RememberPreference"
keywords: Einstellung, uiswitch.ini, UISwitch_design, INI, setting, Class, Enabled, AllowedValues, DesignChain, DesignLabel, RememberPreference
type: generated
generated: exp:help:examples
---
# uiswitch.ini [UISwitch_design]

Der Block `[UISwitch_design]` von `settings/uiswitch.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/uiswitch.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/uiswitch.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/uiswitch.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/uiswitch.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Das Design der Administrationsoberfläche: site.ini \[DesignSettings\] SiteDesign und  
> AdditionalSiteDesignList\[\] für die Anfragen dieses Benutzers. Richtlinie: uiswitch/design.  

Standard: `expUISwitchDesignProvider`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_design]
Class=expUISwitchDesignProvider
*/ ?>
```

## Enabled

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_design]
Enabled=true
*/ ?>
```

## AllowedValues

> Die Designs, die ein Benutzer wählen darf, in der Reihenfolge der Werkzeugleiste. Ein Design wird nur angeboten, wenn es unten einen  
> Eintrag DesignChain\[\] hat und das Design existiert (design/<design> oder design/<design> einer aktiven  
> Design-Erweiterung); das eigene SiteDesign des Siteaccess wird immer angeboten, und seine Wahl hebt den  
> Schalter auf. adminui braucht die im Siteaccess aktive Erweiterung exp\_adminui (ihr Design ist dann eine  
> Design-Erweiterung), admin\_classic die Erweiterung sevenx\_themes\_admin\_classic. Jedes angebotene Design  
> muss die rechte Werkzeugleiste rendern (toolbar/full/admin\_ui\_switch.tpl),  
> sonst kann ein Benutzer, der es gewählt hat, nicht erneut wählen. Die aktuellen Designs kommen zuerst, die älteren  
> (AdminUI und die klassischen Designs, als „(älter)“ beschriftet) zuletzt.  

Standard: eine Liste mit 6: `admin4l`, `admin4`, `admin3`, `adminui`, `admin`, `admin_classic`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_design]
# ein leeres "AllowedValues[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedValues[]
AllowedValues[]=admin4l
AllowedValues[]=admin4
AllowedValues[]=admin3
AllowedValues[]=adminui
AllowedValues[]=admin
AllowedValues[]=admin_classic
*/ ?>
```

## DesignChain

> Die Ausweich-Designs, mit denen jede Wahl läuft (AdditionalSiteDesignList\[\]), durch ";" getrennt,  
> das spezifischste zuerst. Leer: nur das Design und das Standard-Design.  

Standard: ein Hash mit 6: `admin4l=admin4;admin3;admin2;admin`, `admin4=admin3;admin2;admin`, `adminui=admin4l;admin4;admin3;admin2;admin`, `admin3=admin2;admin`, `admin=`, `admin_classic=admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_design]
# ein leeres "DesignChain[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DesignChain[]
DesignChain[admin4l]=admin4;admin3;admin2;admin
DesignChain[admin4]=admin3;admin2;admin
DesignChain[adminui]=admin4l;admin4;admin3;admin2;admin
DesignChain[admin3]=admin2;admin
DesignChain[admin]=
DesignChain[admin_classic]=admin
*/ ?>
```

## DesignLabel

> Die in der Werkzeugleiste angezeigten Namen (übersetzt im Kontext kernel/uiswitch).  

Standard: ein Hash mit 6: `admin4l=Admin 4 (layouts)`, `admin4=Admin 4`, `adminui=AdminUI (older)`, `admin3=Admin 3`, `admin=Classic (older)`, `admin_classic=Classic, server-rendered (older)`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_design]
# ein leeres "DesignLabel[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DesignLabel[]
DesignLabel[admin4l]=Admin 4 (layouts)
DesignLabel[admin4]=Admin 4
DesignLabel[adminui]=AdminUI (older)
DesignLabel[admin3]=Admin 3
DesignLabel[admin]=Classic (older)
DesignLabel[admin_classic]=Classic, server-rendered (older)
*/ ?>
```

## RememberPreference

> enabled: Die Wahl wird auch in den Einstellungen des Benutzers gehalten und kehrt bei der nächsten Anmeldung zurück.  
> disabled (Standard): Sie endet mit der Sitzung, beim Abmelden.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_design]
RememberPreference=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von uiswitch.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/uiswitch.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
