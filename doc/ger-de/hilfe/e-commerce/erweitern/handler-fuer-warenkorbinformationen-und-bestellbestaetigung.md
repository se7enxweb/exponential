---
title: Handler für Warenkorbinformationen und Bestellbestätigung
audience: ecommerce, app-creator, developer
category: e-commerce
order: 65
summary: Zwei kleine Handler des Exponential-Shops - der Handler für Warenkorbinformationen, der den Versand je Steuersatz zu den Summen des Warenkorbs hinzufügt, und der Handler für die Bestellbestätigung, der die Bestellmail versendet -, was jeder tut, wann Sie ihn ersetzen und wie Sie einen Ersatz registrieren.
keywords: Handler für Warenkorbinformationen, eZDefaultBasketInfoHandler, updatePriceInfo, price_info, total_price_info, BasketInfoSettings, Handler für Bestellbestätigung, eZDefaultConfirmOrderHandler, sendOrderEmail, ConfirmOrderSettings, Bestellmail, Bestellannahme, basket info handler, confirm order handler, order mail, order desk
type: help
---
# Handler für Warenkorbinformationen und Bestellbestätigung

Diese Seite ist für Entwickler, die anpassen, wie die Summen des Warenkorbs berechnet werden oder wie die Bestellmail
versendet wird. Beides sind kleine Handler mit je einer Aufgabe, ausgewählt durch eine Einstellung und aus einer
Erweiterung ersetzbar.

## Inhalt

1. [Der Handler für Warenkorbinformationen](#der-handler-für-warenkorbinformationen)
2. [Der Handler für die Bestellbestätigung](#der-handler-für-die-bestellbestätigung)
3. [Ersatz registrieren](#ersatz-registrieren)
4. [Testen](#testen)

## Der Handler für Warenkorbinformationen

| | |
|---|---|
| Einstellung | `shop.ini [BasketInfoSettings] Handler` (ausgeliefert `ezdefault`) |
| Mitgelieferte Klasse | `eZDefaultBasketInfoHandler`, `kernel/classes/basketinfohandlers/ezdefaultbasketinfohandler.php` |
| Methode | `updatePriceInfo( $productCollectionID, &$basketInfo )` |

Der mitgelieferte Handler fragt den Versand-Handler nach seinen Versandinformationen, wandelt sie mit
`eZShippingManager::vatPriceInfo()` in Beträge je Steuersatz um und addiert diese Beträge zu
`$basketInfo['price_info'][<VAT rate>]` und zu `$basketInfo['total_price_info']` (ohne USt., inklusive USt. und
USt.). Templates, die die Umsatzsteuer je Satz anzeigen, lesen diese Arrays.

Ersetzen Sie ihn, wenn die Summen des Warenkorbs je Steuersatz etwas anderes enthalten müssen, zum Beispiel eine
Bearbeitungsgebühr oder ein Pfand, das ein Versand-Handler nicht tragen soll. Erweitern Sie die mitgelieferte Klasse
und rufen Sie zuerst die Elternklasse auf, damit der Versand weiter funktioniert.

## Der Handler für die Bestellbestätigung

| | |
|---|---|
| Einstellung | `shopaccount.ini [ConfirmOrderSettings] Handler` (ausgeliefert `ezdefault`) |
| Mitgelieferte Klasse | `eZDefaultConfirmOrderHandler`, `kernel/classes/confirmorderhandlers/ezdefaultconfirmorderhandler.php` |
| Methoden | `execute( $params )`, `sendOrderEmail( $params )` |

`execute()` versendet die Mail nur, wenn `site.ini [ShopSettings] SendOrderEmail=enabled` gesetzt ist.
`sendOrderEmail()` rendert `shop/orderemail.tpl` einmal und sendet sie an die E-Mail-Adresse der Bestellung und an
`[MailSettings] AdminEmail`, von `EmailSender` (oder `AdminEmail`), mit der Mail-Kategorie `orders`.

Ersetzen Sie ihn, wenn:

| Bedarf | Änderung |
|---|---|
| Die Kopie für den Shop soll an eine Bestellannahme gehen, nicht an den Administrator der Website | Senden Sie die zweite Mail an eine Adresse aus Ihrer eigenen Einstellung |
| Die Kopie für den Shop soll sich von der des Kunden unterscheiden (interne Notizen, Kommissionierliste) | Rendern Sie ein zweites Template für den Shop |
| Eine Kopie soll an einen Fulfillment-Partner gehen | Fügen Sie einen dritten Empfänger hinzu |
| Bestelldaten sollen im selben Moment an ein anderes System gehen | Bevorzugen Sie ein Ereignis am After-Trigger von `shop_checkout` ([Workflows an Shop-Triggern](../kasse/workflows-an-shop-triggern.md)) |

## Ersatz registrieren

```ini
# extension/mystore_shop/settings/shop.ini.append.php
[BasketInfoSettings]
Handler=mystore
ExtensionDirectories[]=mystore_shop

# extension/mystore_shop/settings/shopaccount.ini.append.php
[HandlerSettings]
ExtensionRepositories[]=mystore_shop

[ConfirmOrderSettings]
Handler=mystore
```

Dateien: `basketinfohandlers/mystorebasketinfohandler.php` (Klasse `mystoreBasketInfoHandler`) und
`confirmorderhandlers/mystoreconfirmorderhandler.php` (Klasse `mystoreConfirmOrderHandler`). Der RAD-Handler-Assistent
hat ein Rezept für Handler für Warenkorbinformationen ([Pfad für Entwickler](../rollen/pfad-fuer-entwickler.md#generatoren-die-zeit-sparen)).

## Testen

- [ ] Die Summen je Steuersatz in Warenkorb und Bestätigung ergeben die Gesamtsumme.
- [ ] Bestellmail: Der Kunde und jeder weitere Empfänger erhalten die richtige Fassung.
- [ ] `SendOrderEmail=disabled` versendet wie bisher nichts.
- [ ] Test-Mailadressen gehören keinen echten Kunden ([Einen Shop testen](../betrieb/einen-shop-testen.md)).

## Verwandte Seiten

- [Den Shop erweitern](README.md)
- [Bestellmails und Belege für Betreiber](../bestellungen/bestellmails-und-belege-fuer-betreiber.md)
- [Benachrichtigungen und Mail](../../ersteller/anwendungsentwickler/benachrichtigungen-und-mail.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
