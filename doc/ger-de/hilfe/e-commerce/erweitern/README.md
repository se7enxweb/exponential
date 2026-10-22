---
title: Den Shop erweitern
audience: ecommerce, app-creator, developer
category: e-commerce
order: 60
summary: Die Erweiterungspunkte des Exponential-Shops an einem Ort - Handler für Versand, Umsatzsteuer, Warenkorbinformationen, Shop-Konten, Bestellbestätigung und Wechselkurse, Zahlungs-Gateways, Preis-Datentypen und Workflow-Ereignisse -, mit der Art, wie jeder ausgewählt wird, wohin seine Datei gehört und wie Sie ihn paketieren und testen.
keywords: Shop erweitern, Handler, Versand-Handler, USt-Handler, Handler für Warenkorbinformationen, Shop-Konto-Handler, Handler für Bestellbestätigung, Wechselkurs-Handler, Zahlungs-Gateway, Preis-Datentyp, Workflow-Ereignis, Erweiterung, RAD-Assistent, extending the shop, handler, shipping handler, VAT handler, basket info handler, shop account handler, confirm order handler, exchange rate handler, payment gateway, price datatype, workflow event, extension, RAD wizard
type: help
---
# Den Shop erweitern

Dieser Teil ist für Entwickler, die den Shop etwas tun lassen müssen, was er ab Werk nicht tut. Der Exponential-Shop
ist um austauschbare **Handler** herum gebaut: Eine Einstellung benennt einen Handler, der Kernel findet dessen Datei
über eine Namenskonvention und ruft ihn anstelle seines eigenen auf. Alles auf diesen Seiten gehört in eine eigene
Erweiterung, nie nach `kernel/`.

## Inhalt

1. [Alle Erweiterungspunkte](#alle-erweiterungspunkte)
2. [Das Handler-Muster](#das-handler-muster)
3. [Paketierung](#paketierung)
4. [Eine Erweiterung des Shops testen](#eine-erweiterung-des-shops-testen)

## Alle Erweiterungspunkte

| Erweiterungspunkt | Ausgewählt durch | Datei in Ihrer Erweiterung | Seite |
|---|---|---|---|
| Versand-Handler | `shop.ini [ShippingSettings] Handler`, `ExtensionDirectories[]` | `shippinghandlers/<name>shippinghandler.php` | [Eigener Versand-Handler](eigener-versand-handler.md) |
| USt-Handler | `shop.ini [VATSettings] Handler`, `ExtensionDirectories[]` | `vathandlers/<name>vathandler.php` | [Eigener USt-Handler](eigener-ust-handler.md) |
| Zahlungs-Gateway | eine aktive Erweiterung oder `paymentgateways.ini` | `classes/<extension>gateway.php` | [Entwicklung von Zahlungs-Gateways](entwicklung-von-zahlungs-gateways.md) |
| Shop-Konto-Handler | `shopaccount.ini [AccountSettings] Handler`, `[HandlerSettings] ExtensionRepositories[]` | `shopaccounthandlers/<name>shopaccounthandler.php` | [Shop-Konto-Handler](shop-konto-handler.md) |
| Handler für Warenkorbinformationen | `shop.ini [BasketInfoSettings] Handler`, `ExtensionDirectories[]` | `basketinfohandlers/<name>basketinfohandler.php` | [Handler für Warenkorbinformationen und Bestellbestätigung](handler-fuer-warenkorbinformationen-und-bestellbestaetigung.md) |
| Handler für Bestellbestätigung | `shopaccount.ini [ConfirmOrderSettings] Handler` | `confirmorderhandlers/<name>confirmorderhandler.php` | ebenda |
| Wechselkurs-Handler | `shop.ini [ExchangeRatesSettings] ExchangeRatesUpdateHandler`, `ExtensionDirectories[]` | `exchangeratehandlers/<name>/<name>handler.php` | [Wechselkurs-Handler](wechselkurs-handler.md) |
| Preis-Datentyp | `site.ini [ShopSettings] ProductDatatypeStringList[]` | `datatypes/<name>/<name>type.php` | [Eigene Preis-Datentypen](eigene-preis-datentypen.md) |
| Workflow-Ereignis | `workflow.ini [EventSettings]` | `workflowtypes/event/<name>/<name>type.php` | [Workflows an Shop-Triggern](../kasse/workflows-an-shop-triggern.md#eigene-ereignisse) |

## Das Handler-Muster

1. Eine Einstellung benennt den Handler, zum Beispiel `Handler=mystore`.
2. Der Kernel durchsucht die Repository-Verzeichnisse und die aufgeführten Erweiterungen nach `<name><suffix>.php`.
3. Er bindet die Datei ein und instanziiert `<name><Suffix>`, zum Beispiel `mystoreShippingHandler` (Klassennamen in
   PHP unterscheiden nicht zwischen Groß- und Kleinschreibung, also funktioniert auch `MyStoreShippingHandler`).
4. Fehlt die Datei, wird ein Fehler in das Debug-Log geschrieben, und der Kernel fällt zurück oder tut nichts; er
   bricht die Anfrage nicht ab. Prüfen Sie das Log nach jeder Änderung.

Das RAD-Werkzeug **Setup > RAD** hat einen Handler-Assistenten (`setup/handlerextension/<kind>`) mit Rezepten für
Zahlungs-Gateways, USt-, Versand-, Warenkorbinformations- und Wechselkurs-Handler; er schreibt die Klasse, die
Einstellung und eine Erweiterung drumherum ([Erweiterungen](../../../anleitungen/extensions.md#63-der-handler-assistent-schritt-für-schritt)).
Ein vom Assistenten erzeugtes Zahlungs-Gateway wird nach `extension/<name>/paymentgateways/<alias>/classes/<alias>gateway.php`
geschrieben, den Pfad, den der Gateway-Loader über `paymentgateways.ini [PaymentGatewaySettings] GatewaysDirectories[]`
liest. Ein vor 6.0.15 erzeugtes Gateway liegt eine Ebene höher und wird nicht geladen: Verschieben Sie es, wie der
[Hinweis zu den RAD-Erweiterungspunkten](../../../bc/6.0/rad-erweiterungspunkte.md) erklärt.

## Paketierung

- Eine Erweiterung je Shop oder je Integration, zum Beispiel `mystore_shop`, mit eigenen `settings/*.ini.append.php`
  ([Als Erweiterung paketieren](../../ersteller/anwendungsentwickler/als-erweiterung-paketieren.md)).
- Erzeugen Sie die Autoloads nach dem Hinzufügen von Klassen neu (`php bin/php/ezpgenerateautoloads.php -e`) und
  prüfen Sie, dass jede Klasse lädt (`php bin/php/checkclasses.php --quiet-ok`).
- Halten Sie Zugangsdaten von Anbietern aus der Erweiterung heraus: Lesen Sie sie aus `settings/override/`
  ([Umgebungseinstellungen und Geheimnisse](../../ersteller/anwendungsentwickler/umgebungseinstellungen-und-geheimnisse.md)).
- Wenn die Erweiterung anderen nützt, überlegen Sie, [sie zurückzugeben](../../erweiterungen-zurueckgeben.md).

## Eine Erweiterung des Shops testen

- Unit-Tests für Berechnungen (Versand, Umsatzsteuer) ohne Datenbank: [PHPUnit-Tests ohne Datenbank](../../ersteller/anwendungsentwickler/phpunit-tests-ohne-datenbank.md).
- Eine vollständige Testbestellung für jeden Pfad, den die Erweiterung ändert: [Einen Shop testen](../betrieb/einen-shop-testen.md).
- Nach dem Ausrollen „So funktioniert Ihr Shop“ im Shop-Dashboard: Es nennt die wirksamen Handler und Workflows.

## Verwandte Seiten

- [Pfad für Entwickler](../rollen/pfad-fuer-entwickler.md)
- [Aufbau einer Erweiterung](../../ersteller/anwendungsentwickler/aufbau-einer-erweiterung.md)
- [Einstellungen und INI-Dateien](../../ersteller/anwendungsentwickler/einstellungen-und-ini-dateien.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
