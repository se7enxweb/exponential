---
title: workflow.ini [OperationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [OperationSettings] in workflow.ini: AvailableOperationList"
keywords: Einstellung, workflow.ini, OperationSettings, INI, setting, AvailableOperationList
type: generated
generated: exp:help:examples
---
# workflow.ini [OperationSettings]

Der Block `[OperationSettings]` von `settings/workflow.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/workflow.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/workflow.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/workflow.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/workflow.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AvailableOperationList

> List of available trigger operations.  
> Other extra available operations  
> Note that the code which is used to  
> implement them may be changed if needed  
> we will not apply BC rules for these ones  
> AvailableOperationList\[\]=content\_sort  
> AvailableOperationList\[\]=content\_move  
> AvailableOperationList\[\]=content\_swap  
> AvailableOperationList\[\]=content\_updatemainassignment  
> AvailableOperationList\[\]=content\_addlocation  
> AvailableOperationList\[\]=content\_removelocation  
> AvailableOperationList\[\]=content\_updatepriority  
> AvailableOperationList\[\]=content\_hide  
> AvailableOperationList\[\]=content\_delete  
> AvailableOperationList\[\]=content\_updatesection  
> AvailableOperationList\[\]=content\_read  
> AvailableOperationList\[\]=content\_updateinitiallanguage  
> AvailableOperationList\[\]=content\_updatealwaysavailable  
> AvailableOperationList\[\]=content\_removetranslation  
> AvailableOperationList\[\]=content\_updateobjectstate  
> AvailableOperationList\[\]=content\_createnodefeed  
> AvailableOperationList\[\]=content\_removenodefeed  
> AvailableOperationList\[\]=user\_activation  
> AvailableOperationList\[\]=user\_password  
> AvailableOperationList\[\]=user\_forgotpassword  
> AvailableOperationList\[\]=user\_preferences  
> AvailableOperationList\[\]=user\_setsettings  

Standard: eine Liste mit 5: `content_publish`, `before_shop_confirmorder`, `shop_checkout`, `shop_addtobasket`, `shop_updatebasket`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/workflow.ini.append.php
[OperationSettings]
# ein leeres "AvailableOperationList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AvailableOperationList[]
AvailableOperationList[]=content_publish
AvailableOperationList[]=before_shop_confirmorder
AvailableOperationList[]=shop_checkout
AvailableOperationList[]=shop_addtobasket
AvailableOperationList[]=shop_updatebasket
*/ ?>
```

## Siehe auch

- [Alle Blöcke von workflow.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/workflow.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
