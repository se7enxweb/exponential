---
title: site.ini [ShopSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ShopSettings] in site.ini: ClearBasketOnCheckout, ClearBasketOnLogout, RedirectAfterAddToBasket, SendOrderEmail"
keywords: Einstellung, site.ini, ShopSettings, INI, setting, ClearBasketOnCheckout, ClearBasketOnLogout, RedirectAfterAddToBasket, SendOrderEmail
type: generated
generated: exp:help:examples
---
# site.ini [ShopSettings]

Der Block `[ShopSettings]` von `settings/site.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ClearBasketOnCheckout

> Diese Einstellung steuert, wann der Warenkorb geleert wird.  
> Sie kann folgende Werte enthalten:  
> - disabled - Der Warenkorb wird geleert, wenn der Auslöser shop/checkout  
>              fertig ist. In der Praxis also, wenn ein Benutzer  
>              das Produkt bezahlt hat und das Bezahlsystem fertig ist.  
>              Das ist der Standard, weil der Benutzer dann die  
>              Bestellung abbrechen und mit intaktem Warenkorb  
>              zum Shop zurückkehren kann.  
> - enabled - Der Warenkorb wird geleert, sobald der Benutzer  
>             im Auslöser shop/confirmorder auf Bestätigen klickt. Das kann  
>             für manche Bezahlsysteme nötig sein; prüfen Sie in der Dokumentation  
>             des Systems, ob dies aktiviert werden muss.  
>             Für die Benutzer bedeutet das, dass der Warenkorb nicht mehr  
>             verfügbar ist, wenn die Zahlung abgebrochen wird.  
>
> Zur Einordnung: Der ganze Bezahlvorgang besteht aus diesen Auslösern:  
> - shop/confirmorder - Dem Benutzer wird der Gesamtpreis mit Versand  
>                       und anderen Berechnungen gezeigt. Klickt der Benutzer  
>                       auf Bestätigen, wird der Auslöser shop/checkout gestartet.  
> - shop/checkout     - Startet eine neue vorläufige Bestellung und führt die Bezahl-  
>                       methoden (oder andere Workflows) aus. Wenn das fertig ist,  
>                       wird die Bestellung aktiviert und der Warenkorb geleert.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ShopSettings]
ClearBasketOnCheckout=disabled
*/ ?>
```

## ClearBasketOnLogout

> Diese Einstellung wird verwendet, wenn sich ein Benutzer abmeldet.  
> Sie kann folgende Werte enthalten:  
> - disabled - Der Warenkorb wird beim Abmelden eines Benutzers NICHT geleert.  
> - enabled  - Der Warenkorb wird beim Abmelden eines Benutzers geleert.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ShopSettings]
ClearBasketOnLogout=disabled
*/ ?>
```

## RedirectAfterAddToBasket

> Steuert, was geschieht, nachdem ein Artikel in den Warenkorb gelegt wurde  
> Sie kann einen dieser Einträge enthalten:  
> - basket - zurück zum Warenkorb weiterleiten, um den neu hinzugefügten Artikel zu zeigen  
> - reload - dorthin zurückleiten, wo der Benutzer zuvor war, damit  
>            er weiter einkaufen kann.  

Standard: `basket`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ShopSettings]
RedirectAfterAddToBasket=basket
*/ ?>
```

## SendOrderEmail

> Steuert, ob eine Bestellbestätigung per E-Mail an Administrator und Benutzer gesendet wird,  
> wenn eine Bestellung abgeschlossen und bestätigt ist.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ShopSettings]
SendOrderEmail=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
