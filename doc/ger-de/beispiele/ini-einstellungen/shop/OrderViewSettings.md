---
title: shop.ini [OrderViewSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [OrderViewSettings] in shop.ini: OrderLinkView"
keywords: Einstellung, shop.ini, OrderViewSettings, INI, setting, OrderLinkView
type: generated
generated: exp:help:examples
---
# shop.ini [OrderViewSettings]

Der Block `[OrderViewSettings]` von `settings/shop.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/shop.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shop.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shop.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shop.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## OrderLinkView

> Where the system links a placed order: after checkout, in the customer's  
> order list and history, and in the order confirmation e-mail.  
>   orderview    - shop/orderview/<id>: the classic order view. For an order  
>                  placed without an account it works only in the session that  
>                  placed it, and stops working once that session expires.  
>   orderreceipt - shop/orderreceipt/<token>: the order's permanent receipt.  
>                  It never expires, can be bookmarked, printed and downloaded,  
>                  and opens for whoever holds the link -- from any browser or  
>                  device, with or without an account, no sign-in needed. The  
>                  signed token is the key, so the link is as private as the  
>                  receipt. Shop administrators, and a signed-in customer for  
>                  their own orders, can open it without the link.  
> shop/orderreceipt/<token> works either way; this only picks what is linked.  
> The administration interface keeps linking orders to shop/orderview, where  
> they are managed. shop/orderreciept/<token> redirects to shop/orderreceipt.  

Standard: `orderview`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[OrderViewSettings]
OrderLinkView=orderview
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shop.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shop.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
