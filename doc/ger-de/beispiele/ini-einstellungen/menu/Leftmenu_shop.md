---
title: menu.ini [Leftmenu_shop]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Leftmenu_shop] in menu.ini: Name, Links, LinkNames"
keywords: Einstellung, menu.ini, Leftmenu_shop, INI, setting, Name, Links, LinkNames
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_shop]

Der Block `[Leftmenu_shop]` von `settings/menu.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `shop`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_shop]
Name=shop
*/ ?>
```

## Links

Standard: ein Hash mit 13: `dashboard=shop/dashboard`, `orders=shop/orderlist`, `products_overview=shop/productsoverview`, `product_statistics=shop/statistics`, `product_categories=shop/productcategories`, `customers=shop/customerlist`, `discounts=shop/discountgroup`, `archive=shop/archivelist`, `order_status=shop/status`, `vat_types=shop/vattype`, `vat_rules=shop/vatrules`, `currencies=shop/currencylist` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_shop]
# ein leeres "Links[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Links[]
Links[dashboard]=shop/dashboard
Links[orders]=shop/orderlist
Links[products_overview]=shop/productsoverview
Links[product_statistics]=shop/statistics
Links[product_categories]=shop/productcategories
Links[customers]=shop/customerlist
Links[discounts]=shop/discountgroup
Links[archive]=shop/archivelist
Links[order_status]=shop/status
Links[vat_types]=shop/vattype
Links[vat_rules]=shop/vatrules
Links[currencies]=shop/currencylist
Links[preferred_currency]=shop/preferredcurrency
*/ ?>
```

## LinkNames

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_shop]
# ein leeres "LinkNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkNames[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
