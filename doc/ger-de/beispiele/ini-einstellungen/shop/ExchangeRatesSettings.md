---
title: shop.ini [ExchangeRatesSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ExchangeRatesSettings] in shop.ini: ExchangeRatesUpdateHandler, RepositoryDirectories, ExtensionDirectories, BaseCurrency"
keywords: Einstellung, shop.ini, ExchangeRatesSettings, INI, setting, ExchangeRatesUpdateHandler, RepositoryDirectories, ExtensionDirectories, BaseCurrency
type: generated
generated: exp:help:examples
---
# shop.ini [ExchangeRatesSettings]

Der Block `[ExchangeRatesSettings]` von `settings/shop.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/shop.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shop.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shop.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shop.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ExchangeRatesUpdateHandler

> eZECB - european central bank  

Standard: `eZECB`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[ExchangeRatesSettings]
ExchangeRatesUpdateHandler=eZECB
*/ ?>
```

## RepositoryDirectories

Standard: eine Liste mit 1: `kernel/shop/classes/exchangeratehandlers`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[ExchangeRatesSettings]
# ein leeres "RepositoryDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryDirectories[]
RepositoryDirectories[]=kernel/shop/classes/exchangeratehandlers
*/ ?>
```

## ExtensionDirectories

> A list of extensions containing exchange rates update handlers  
> These handlers must be placed in : <extension>/<your\_extension>/exchangeratehandlers/<handler\_name>/<handler\_name>handler.php  
> And contain a class called : <handler\_name>Handler  
> This class must implement eZExchangeRatesUpdateHandler ( kernel/shop/classes/exchangeratehandlers/ezexchangeratesupdatehandler.php )  
> It's common to create a settings/shop.ini.append file  
> in your extension and define 'ExtensionDirectories' in it to automatically  
> get handler from the extension when it's turned on.  

Standard: eine Liste mit 1: ``.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[ExchangeRatesSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
ExtensionDirectories[]=
*/ ?>
```

## BaseCurrency

> Base currency for rates: USD/<base>, NOK/<base>, UAH/<base>, ...  

Standard: `EUR`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[ExchangeRatesSettings]
BaseCurrency=EUR
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shop.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shop.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
