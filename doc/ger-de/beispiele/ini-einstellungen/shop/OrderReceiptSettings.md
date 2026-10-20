---
title: shop.ini [OrderReceiptSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [OrderReceiptSettings] in shop.ini: Secret, BcryptCost"
keywords: Einstellung, shop.ini, OrderReceiptSettings, INI, setting, Secret, BcryptCost
type: generated
generated: exp:help:examples
---
# shop.ini [OrderReceiptSettings]

Der Block `[OrderReceiptSettings]` von `settings/shop.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/shop.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shop.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shop.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shop.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Secret

> The secret that signs receipt addresses. Leave empty to have one generated  
> for this installation on first use and kept in <VarDir>/secrets/orderreceipt.key  
> (mode 0600, outside the web root's served paths). Set it (32 characters or  
> more) to share receipts between installations that share a database. Set it  
> in an override file, never here. Changing it changes every receipt address.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[OrderReceiptSettings]
Secret=
*/ ?>
```

## BcryptCost

> bcrypt cost for the receipt signature, 4 to 6: enough work to make guessing  
> expensive, little enough that a receipt opens instantly. Receipts made at an  
> earlier cost keep working.  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[OrderReceiptSettings]
BcryptCost=5
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shop.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shop.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
