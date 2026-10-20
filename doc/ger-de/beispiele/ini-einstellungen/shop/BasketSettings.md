---
title: shop.ini [BasketSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [BasketSettings] in shop.ini: BasketViewName"
keywords: Einstellung, shop.ini, BasketSettings, INI, setting, BasketViewName
type: generated
generated: exp:help:examples
---
# shop.ini [BasketSettings]

Der Block `[BasketSettings]` von `settings/shop.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/shop.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shop.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shop.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shop.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## BasketViewName

> The view name the shop uses for the basket page, and for every redirect,  
> form action and breadcrumb that targets it. Any simple token works - letters,  
> digits, dash and underscore - and the shop is then served at /shop/<name>/:  
>
>   BasketViewName=basket   -> /shop/basket/   (default)  
>   BasketViewName=cart     -> /shop/cart/  
>   BasketViewName=buyer    -> /shop/buyer/  
>
> /shop/basket/ stays registered whatever the setting, so existing links and  
> bookmarks keep resolving; the setting decides where the shop sends people.  
>
> A design may theme a name by adding shop/<name>.tpl - shop/cart.tpl ships as  
> an example. Without one, the ordinary shop/basket.tpl is used, so a new name  
> needs no template of its own. Anything outside the allowed characters falls  
> back to basket rather than producing a URL that routes nowhere.  

Standard: `basket`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[BasketSettings]
BasketViewName=basket
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shop.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shop.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
