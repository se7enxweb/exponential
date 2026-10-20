---
title: shop.ini [BasketInfoSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [BasketInfoSettings] in shop.ini: Handler, RepositoryDirectories, ExtensionDirectories"
keywords: Einstellung, shop.ini, BasketInfoSettings, INI, setting, Handler, RepositoryDirectories, ExtensionDirectories
type: generated
generated: exp:help:examples
---
# shop.ini [BasketInfoSettings]

Der Block `[BasketInfoSettings]` von `settings/shop.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/shop.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shop.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shop.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shop.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handler

> Specifies basketinfo handler class/file name. For example, if the value is  
> "ezcustom", then the handler class should be named  
> eZCustomBasketInfoHandler (case does not matter) and placed to file  
> ezcustombasketinfohandler.php residing in a directory specified by  
> "RepositoryDirectories" and "ExtensionDirectories" settings.  
> ezdefault is the default basket info handler, that provides basic  
> calculated information about basket items.  

Standard: `ezdefault`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[BasketInfoSettings]
Handler=ezdefault
*/ ?>
```

## RepositoryDirectories

> Directories where basketinfo handlers should be searched for.  

Standard: eine Liste mit 1: `kernel/classes/basketinfohandlers`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[BasketInfoSettings]
# ein leeres "RepositoryDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryDirectories[]
RepositoryDirectories[]=kernel/classes/basketinfohandlers
*/ ?>
```

## ExtensionDirectories

> If you are going to implement your basketinfo handler in an extension  
> then you should add the extension name to the list below.  
> In that case basketinfo handlers will be searched for  
> in the following (hardcoded) directory:  
> extension/<your\_extension>/basketinfohandlers  
> beside directories specified in 'RepositoryDirectories' setting.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[BasketInfoSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shop.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shop.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
