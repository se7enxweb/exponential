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

> Das Geheimnis, das die Adressen der Belege signiert. Lassen Sie es leer, damit bei der ersten Verwendung eines  
> für diese Installation erzeugt und in <VarDir>/secrets/orderreceipt.key gehalten wird  
> (Modus 0600, außerhalb der vom Webserver ausgelieferten Pfade). Setzen Sie es (32 Zeichen oder  
> mehr), um Belege zwischen Installationen zu teilen, die sich eine Datenbank teilen. Setzen Sie es  
> in einer Override-Datei, nie hier. Eine Änderung ändert jede Belegadresse.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[OrderReceiptSettings]
Secret=
*/ ?>
```

## BcryptCost

> bcrypt-Aufwand für die Signatur des Belegs, 4 bis 6: genug Arbeit, um Raten  
> teuer zu machen, wenig genug, dass sich ein Beleg sofort öffnet. Belege, die mit einem  
> früheren Aufwand erstellt wurden, funktionieren weiter.  

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
