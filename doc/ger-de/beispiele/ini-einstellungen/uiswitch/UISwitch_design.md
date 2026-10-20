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

> The design of the administration interface: site.ini \[DesignSettings\] SiteDesign and  
> AdditionalSiteDesignList\[\] for this user's requests. Policy: uiswitch/design.  

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

> The designs a user may choose, in toolbar order. A design is offered only when it has a  
> DesignChain\[\] entry below and the design exists (design/<design>, or design/<design> of an active  
> design extension); the siteaccess's own SiteDesign is always offered and choosing it clears the  
> switch. adminui needs the exp\_adminui extension active on the siteaccess (its design is then a  
> design extension), admin\_classic the sevenx\_themes\_admin\_classic extension. Every design offered  
> must render the right toolbar (toolbar/full/admin\_ui\_switch.tpl),  
> or a user who chose it cannot choose again. The current designs come first, the older ones  
> (AdminUI and the classic designs, labelled "(older)") last.  

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

> The fallback designs each choice runs with (AdditionalSiteDesignList\[\]), separated by ";",  
> most specific first. Empty: the design and the standard design only.  

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

> The names shown in the toolbar (translated in the context kernel/uiswitch).  

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

> enabled: the choice is also kept in the user's preferences and comes back at the next sign-in.  
> disabled (default): it ends with the session, at logout.  

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
