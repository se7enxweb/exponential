---
title: Pfad für Entwickler
audience: ecommerce, app-creator, website-creator
category: e-commerce
order: 13
summary: Ein Pfad für Entwickler, die einen Exponential-Shop bauen oder erweitern - die Code-Übersicht des Shops, die Erweiterungspunkte mit ihren Einstellungen, die RAD-Assistenten, die Gerüste für Handler erzeugen, und die Reihenfolge, in der Sie die technischen Seiten lesen.
keywords: E-Commerce-Entwickler, Shop-Code, kernel/shop, eZOrder, eZBasket, eZVatType, Operationen, Workflow-Ereignisse, Handler, Zahlungs-Gateway, Versand-Handler, USt-Handler, RAD, Handler-Assistent, expservices, e-commerce developer, shop code, operations, workflow events, handlers, payment gateway, shipping handler, VAT handler, handler wizard
type: help
---
# Pfad für Entwickler

Diese Seite ist für Entwickler, die einen Exponential-Shop bauen oder den Shop erweitern: interne Entwickler und die
Entwickler einer Agentur. Sie enthält die Code-Übersicht des Shops, jeden Erweiterungspunkt mit der Einstellung, die ihn
auswählt, und eine Lesereihenfolge durch die technischen Seiten.

## Inhalt

1. [Die Code-Übersicht](#die-code-übersicht)
2. [Erweiterungspunkte auf einen Blick](#erweiterungspunkte-auf-einen-blick)
3. [Generatoren, die Zeit sparen](#generatoren-die-zeit-sparen)
4. [Lesereihenfolge](#lesereihenfolge)
5. [Faustregeln](#faustregeln)

## Die Code-Übersicht

| Was | Wo |
|---|---|
| Shop-Modul: Views, Richtlinienfunktionen, Einschränkungen | `kernel/shop/module.php` |
| Der Code der Views (Einstiegsdateien rufen Klassen auf) | `kernel/shop/*.php`, `kernel/private/classes/views/shop/` |
| Shop-Operationen (welche Trigger es gibt und was dazwischen läuft) | `kernel/shop/operation_definition.php`, `kernel/shop/ezshopoperationcollection.php` |
| Fetch-Funktionen für Templates (`fetch( 'shop', ... )`) | `kernel/shop/function_definition.php`, `kernel/shop/ezshopfunctioncollection.php` |
| Bestellungen, Posten, Status, Statusverlauf | `kernel/classes/ezorder.php`, `ezorderitem.php`, `ezorderstatus.php`, `ezorderstatushistory.php` |
| Warenkörbe und Produktsammlungen | `kernel/classes/ezbasket.php`, `ezproductcollection.php`, `ezproductcollectionitem.php`, `ezproductcollectionitemoption.php` |
| USt. | `kernel/classes/ezvattype.php`, `ezvatrule.php`, `ezvatmanager.php`, `vathandlers/` |
| Versand und Warenkorbinformationen | `kernel/classes/ezshippingmanager.php`, `basketinfohandlers/` |
| Shop-Konten und die Bestellmail | `kernel/classes/ezshopaccounthandler.php`, `shopaccounthandlers/`, `ezconfirmorderhandler.php`, `confirmorderhandlers/` |
| Rabatte und Wunschliste | `kernel/classes/ezdiscount*.php`, `ezuserdiscountrule.php`, `ezwishlist.php` |
| Basisklassen für Zahlungen | `kernel/shop/classes/ezpaymentgateway.php`, `ezredirectgateway.php`, `ezpaymentobject.php`, `ezpaymentcallbackchecker.php` |
| Vom Shop verwendete Workflow-Ereignistypen | `kernel/classes/workflowtypes/event/ezpaymentgateway/`, `ezsimpleshipping/`, `ezmultiplexer/` |
| Währungen und Wechselkurse | `kernel/shop/classes/ezcurrencydata.php`, `ezcurrencyconverter.php`, `exchangeratehandlers/` |
| Shop-Templates des Standarddesigns | `design/standard/templates/shop/` |

## Erweiterungspunkte auf einen Blick

| Sie möchten | Erweiterungspunkt | Ausgewählt durch | Seite |
|---|---|---|---|
| Versand nach eigenen Regeln berechnen | Versand-Handler | `shop.ini [ShippingSettings] Handler` | [Eigener Versand-Handler](../erweitern/eigener-versand-handler.md) |
| Die USt. nach eigenen Regeln bestimmen | USt-Handler | `shop.ini [VATSettings] Handler` | [Eigener USt-Handler](../erweitern/eigener-ust-handler.md) |
| Zahlungen über einen anderen Anbieter annehmen | Zahlungs-Gateway | `paymentgateways.ini`, ein Ereignis Zahlungs-Gateway | [Entwicklung von Zahlungs-Gateways](../erweitern/entwicklung-von-zahlungs-gateways.md) |
| Andere Kundendaten erfassen | Shop-Konto-Handler | `shopaccount.ini [AccountSettings] Handler` | [Shop-Konto-Handler](../erweitern/shop-konto-handler.md) |
| Die Bestellmail ändern | Handler für die Bestellbestätigung | `shopaccount.ini [ConfirmOrderSettings] Handler` | [Handler für Warenkorbinformationen und Bestellbestätigung](../erweitern/handler-fuer-warenkorbinformationen-und-bestellbestaetigung.md) |
| Die Summen des Warenkorbs ändern | Handler für Warenkorbinformationen | `shop.ini [BasketInfoSettings] Handler` | ebenda |
| Wechselkurse anderswo abrufen | Handler für Wechselkurs-Aktualisierung | `shop.ini [ExchangeRatesSettings] ExchangeRatesUpdateHandler` | [Wechselkurs-Handler](../erweitern/wechselkurs-handler.md) |
| Ein eigenes Preisfeld | Preis-Datentyp | `site.ini [ShopSettings] ProductDatatypeStringList[]` | [Eigene Preis-Datentypen](../erweitern/eigene-preis-datentypen.md) |
| Code bei einem Shop-Schritt ausführen | Workflow-Ereignistyp an einem Shop-Trigger | `workflow.ini` | [Workflows an Shop-Triggern](../kasse/workflows-an-shop-triggern.md) |

## Generatoren, die Zeit sparen

Die RAD-Werkzeuge unter **Setup** schreiben funktionierende Gerüste in eine Erweiterung:

- `setup/handlerextension/<kind>` (Richtlinie `setup/setup`) hat Rezepte für ein Zahlungs-Gateway (Weiterleitung und
  transparent), einen USt-Handler, einen Versand-Handler, einen Handler für Warenkorbinformationen und einen
  Wechselkurs-Handler, jeweils mit den zu schreibenden Methoden und der nötigen Einstellung
  ([Erweiterungen](../../../anleitungen/extensions.md#63-der-handler-assistent-schritt-für-schritt)).
- `setup/workflowevent` schreibt einen Workflow-Ereignistyp
  ([Einen Workflow-Ereignistyp schreiben](../../ersteller/anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md)).

Prüfen Sie eine erzeugte Datei gegen die Ladepfade, die auf jeder Erweiterungsseite beschrieben sind, bevor Sie sich
darauf verlassen, und führen Sie danach `php bin/php/checkclasses.php --quiet-ok` aus.

## Lesereihenfolge

1. [Einen Shop betreiben: Überblick](../../ersteller/website-ersteller/shop-ueberblick.md) und
   [Warenkorb, Kasse und Bestellungen](../../ersteller/website-ersteller/warenkorb-kasse-und-bestellungen.md): die Teile.
2. [Die Kassenoperationen Schritt für Schritt](../kasse/die-kassenoperationen-schritt-fuer-schritt.md) und
   [Temporäre Bestellungen und Aktivierung](../kasse/temporaere-bestellungen-und-aktivierung.md): was wann läuft.
3. [Die Shop-Einstellungen durchgegangen](../settings/README.md): jede Einstellung.
4. [Workflows an Shop-Triggern](../kasse/workflows-an-shop-triggern.md) und
   [Das Ereignis Zahlungs-Gateway](../kasse/das-ereignis-zahlungs-gateway.md).
5. Die Seite des Erweiterungspunkts, den Sie brauchen, in [Den Shop erweitern](../erweitern/README.md).
6. [Einen Shop testen](../betrieb/einen-shop-testen.md) und [Fehlersuche an der Kasse](../betrieb/fehlersuche-an-der-kasse.md).

## Faustregeln

- Legen Sie Shop-Einstellungen in `settings/override/` oder einen Siteaccess-Ordner, nie in die ausgelieferten
  `settings/*.ini` ([Die Shop-Einstellungen durchgegangen](../settings/README.md#wohin-shop-einstellungen-gehören)).
- Legen Sie Handler und Gateways in eine eigene Erweiterung, nicht in `kernel/`
  ([Als Erweiterung paketieren](../../ersteller/anwendungsentwickler/als-erweiterung-paketieren.md)).
- Protokollieren oder speichern Sie nie Kartendaten; der Prüfpfad zeichnet Zahlungen ohne sie auf, und Ihr Code sollte
  das ebenso tun ([Sicherheit für Anwendungen](../../ersteller/anwendungsentwickler/sicherheit-fuer-anwendungen.md)).
- Testen Sie jede Änderung mit einer vollständigen Bestellung im Testmodus des Anbieters ([Einen Shop testen](../betrieb/einen-shop-testen.md)).

## Verwandte Seiten

- [Pfade nach Rolle](README.md)
- [Anwendungsentwickler: hier beginnen](../../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md)
- [Workflows, Trigger und Ereignisse](../../ersteller/anwendungsentwickler/workflows-und-ereignisse.md)
- [Headless Commerce mit expservices](../betrieb/headless-commerce-mit-expservices.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
