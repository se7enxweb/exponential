---
title: shop.ini [VATSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [VATSettings] in shop.ini: ProductCategoryAttribute, UserCountryAttribute, RequireUserCountry, DynamicVatTypeName, RepositoryDirectories, ExtensionDirectories"
keywords: Einstellung, shop.ini, VATSettings, INI, setting, ProductCategoryAttribute, UserCountryAttribute, RequireUserCountry, DynamicVatTypeName, RepositoryDirectories, ExtensionDirectories
type: generated
generated: exp:help:examples
---
# shop.ini [VATSettings]

Der Block `[VATSettings]` von `settings/shop.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/shop.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shop.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shop.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shop.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ProductCategoryAttribute

> Specifies VAT handler class/file name. For example, if the value is  
> "ezdefault", then the handler class should be named  
> eZDefaultVATHandler (case does not matter) and placed to file  
> ezdefaultvathandler.php residing in a directory specified by  
> "RepositoryDirectories" and "ExtensionDirectories" settings.  
> Handler=ezdefault  
> Identifier of the product category content attribute.  
> E.g. "ProductCategoryAttribute=category".  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[VATSettings]
ProductCategoryAttribute=
*/ ?>
```

## UserCountryAttribute

> Identifier of the user country content attribute.  
> E.g. "UserCountryAttribute=country".  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[VATSettings]
UserCountryAttribute=
*/ ?>
```

## RequireUserCountry

> If set to false, no errors will be displayed in case if user country  
> is not specified. One might want this when using a custom VAT handler.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[VATSettings]
RequireUserCountry=true
*/ ?>
```

## DynamicVatTypeName

> Name of the dynamic VAT type displayed in class/object view/edit GUI.  

Standard: `Determined by VAT charging rules`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[VATSettings]
DynamicVatTypeName=Determined by VAT charging rules
*/ ?>
```

## RepositoryDirectories

> Directories where VAT handlers should be searched for.  

Standard: eine Liste mit 1: `kernel/classes/vathandlers`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[VATSettings]
# ein leeres "RepositoryDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryDirectories[]
RepositoryDirectories[]=kernel/classes/vathandlers
*/ ?>
```

## ExtensionDirectories

> If you are going to implement your VAT handler in an extension  
> then you should add the extension name to the list below.  
> In that case VAT handlers will be searched for in the following directory:  
> extension/<your\_extension>/vathandlers  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[VATSettings]
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
