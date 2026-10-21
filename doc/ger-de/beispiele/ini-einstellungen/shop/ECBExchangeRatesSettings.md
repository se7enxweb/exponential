---
title: shop.ini [ECBExchangeRatesSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ECBExchangeRatesSettings] in shop.ini: ServerName, ServerPort, RatesURI"
keywords: Einstellung, shop.ini, ECBExchangeRatesSettings, INI, setting, ServerName, ServerPort, RatesURI
type: generated
generated: exp:help:examples
---
# shop.ini [ECBExchangeRatesSettings]

Der Block `[ECBExchangeRatesSettings]` von `settings/shop.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/shop.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shop.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shop.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shop.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ServerName

Standard: `http://www.ecb.int`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[ECBExchangeRatesSettings]
ServerName=http://www.ecb.int
*/ ?>
```

## ServerPort

Standard: `80`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[ECBExchangeRatesSettings]
ServerPort=80
*/ ?>
```

## RatesURI

Standard: `stats/eurofxref/eurofxref-daily.xml`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[ECBExchangeRatesSettings]
RatesURI=stats/eurofxref/eurofxref-daily.xml
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shop.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shop.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
