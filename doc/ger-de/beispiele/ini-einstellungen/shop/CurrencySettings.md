---
title: shop.ini [CurrencySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CurrencySettings] in shop.ini: PreferredCurrency"
keywords: Einstellung, shop.ini, CurrencySettings, INI, setting, PreferredCurrency
type: generated
generated: exp:help:examples
---
# shop.ini [CurrencySettings]

Der Block `[CurrencySettings]` von `settings/shop.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/shop.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shop.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shop.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shop.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PreferredCurrency

> Standardwährung für den Einkauf.  

Standard: `USD`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[CurrencySettings]
PreferredCurrency=USD
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shop.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shop.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
