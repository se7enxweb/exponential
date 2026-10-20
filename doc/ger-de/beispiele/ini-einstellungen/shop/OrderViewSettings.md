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

> Wohin das System eine aufgegebene Bestellung verlinkt: nach dem Bezahlvorgang, in der Bestellliste und dem  
> Verlauf des Kunden und in der Bestätigungsmail der Bestellung.  
>   orderview    - shop/orderview/<id>: die klassische Bestellansicht. Für eine Bestellung,  
>                  die ohne Konto aufgegeben wurde, funktioniert sie nur in der Sitzung, die  
>                  sie aufgegeben hat, und nicht mehr, sobald diese Sitzung abläuft.  
>   orderreceipt - shop/orderreceipt/<token>: der dauerhafte Beleg der Bestellung.  
>                  Er läuft nie ab, lässt sich als Lesezeichen speichern, drucken und herunterladen  
>                  und öffnet sich für jeden, der den Link hat, von jedem Browser oder  
>                  Gerät, mit oder ohne Konto, ohne Anmeldung. Das  
>                  signierte Token ist der Schlüssel, der Link ist also so privat wie der  
>                  Beleg. Shop-Administratoren und ein angemeldeter Kunde für seine  
>                  eigenen Bestellungen können ihn ohne den Link öffnen.  
> shop/orderreceipt/<token> funktioniert in beiden Fällen; dies wählt nur, was verlinkt wird.  
> Die Administrationsoberfläche verlinkt Bestellungen weiterhin auf shop/orderview, wo  
> sie verwaltet werden. shop/orderreciept/<token> leitet auf shop/orderreceipt weiter.  

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
