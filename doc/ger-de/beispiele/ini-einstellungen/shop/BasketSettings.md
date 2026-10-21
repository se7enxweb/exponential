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

> Der Ansichtsname, den der Shop für die Warenkorbseite verwendet, und für jede Weiterleitung,  
> jede Formularaktion und jeden Brotkrumenpfad, der darauf zielt. Jedes einfache Token funktioniert (Buchstaben,  
> Ziffern, Bindestrich und Unterstrich), und der Shop ist dann unter /shop/<name>/ erreichbar:  
>
>   BasketViewName=basket   -> /shop/basket/   (Standard)  
>   BasketViewName=cart     -> /shop/cart/  
>   BasketViewName=buyer    -> /shop/buyer/  
>
> /shop/basket/ bleibt unabhängig von der Einstellung registriert, vorhandene Links und  
> Lesezeichen funktionieren also weiter; die Einstellung entscheidet, wohin der Shop die Leute schickt.  
>
> Ein Design kann einen Namen gestalten, indem es shop/<name>.tpl hinzufügt; shop/cart.tpl wird als  
> Beispiel mitgeliefert. Ohne eines wird das gewöhnliche shop/basket.tpl verwendet, ein neuer Name  
> braucht also kein eigenes Template. Alles außerhalb der erlaubten Zeichen fällt  
> auf basket zurück, statt eine URL zu erzeugen, die nirgendwohin führt.  

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
