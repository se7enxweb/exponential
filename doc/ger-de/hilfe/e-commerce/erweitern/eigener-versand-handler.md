---
title: Eigener Versand-Handler
audience: ecommerce, app-creator, developer
category: e-commerce
order: 61
summary: Einen Versand-Handler für einen Exponential-Shop schreiben - die drei Methoden, die eZShippingManager aufruft, das Array, das getShippingInfo mit mehreren Versandposten und ihrer Umsatzsteuer zurückgibt, wie der Handler für Warenkorbinformationen den Versand zu den Summen addiert, die Registrierung in shop.ini und Modelle wie kostenloser Versand ab einem Betrag oder Tarife nach Land.
keywords: Versand-Handler, eZShippingManager, getShippingInfo, updateShippingInfo, purgeShippingInfo, shipping_items, vat_value, is_vat_inc, management_link, vatPriceInfo, ShippingSettings, shippinghandlers, kostenloser Versand, Versand nach Land, Versand nach Gewicht, shipping handler, free shipping, shipping by country, shipping by weight
type: help
---
# Eigener Versand-Handler

Diese Seite ist für Entwickler, die Versandregeln jenseits des pauschalen Betrags des
[einfachen Versand-Ereignisses](../kasse/das-ereignis-einfacher-versand.md) brauchen. Im Kernel wird kein Versand-Handler
mitgeliefert; der Vertrag ist in `kernel/classes/ezshippingmanager.php` dokumentiert, und diese Seite macht daraus ein
Rezept.

## Inhalt

1. [Der Vertrag](#der-vertrag)
2. [Was getShippingInfo zurückgibt](#was-getshippinginfo-zurückgibt)
3. [Den Handler registrieren](#den-handler-registrieren)
4. [Wie der Versand in die Summen gelangt](#wie-der-versand-in-die-summen-gelangt)
5. [Übliche Modelle](#übliche-modelle)
6. [Testen](#testen)

## Der Vertrag

`eZShippingManager` lädt den in `shop.ini [ShippingSettings] Handler` genannten Handler und ruft drei Methoden auf,
jeweils mit der ID der Produktsammlung des Warenkorbs:

| Methode | Aufgerufen | Gibt zurück |
|---|---|---|
| `getShippingInfo( $productCollectionID )` | Immer wenn Warenkorb, Bestätigung oder Summen den Versand brauchen | Ein Array (unten) oder nichts für keinen Versand |
| `updateShippingInfo( $productCollectionID )` | Wenn ein Artikel hinzugefügt wird (der Schritt `update-shipping-info` von `addtobasket`) | Was immer Ihr Handler braucht; nutzen Sie ihn, um neu zu berechnen oder Auswahlen zu speichern |
| `purgeShippingInfo( $productCollectionID )` | Wenn Versandinformationen entfernt werden sollen | Nichts |

Der Klassenname ist `<name>ShippingHandler` in `<name>shippinghandler.php`.

## Was getShippingInfo zurückgibt

Eine Versandzeile:

```php
return array( 'description' => 'Standard delivery',
              'cost'        => 6.50,
              'vat_value'   => 20,
              'is_vat_inc'  => 1 );
```

Mehrere Zeilen, zum Beispiel wenn Pakete unterschiedliche Steuersätze tragen:

```php
return array( 'shipping_items' => array(
                  array( 'description' => 'Parcel, standard VAT', 'cost' => 5.00, 'vat_value' => 20, 'is_vat_inc' => 1 ),
                  array( 'description' => 'Parcel, reduced VAT', 'cost' => 3.00, 'vat_value' => 5, 'is_vat_inc' => 1 ) ),
              'description' => 'Total shipping',
              'cost'        => 8.00,
              'vat_value'   => false,
              'is_vat_inc'  => 1 );
```

| Schlüssel | Bedeutung |
|---|---|
| `description` | Dem Kunden angezeigter Text |
| `cost` | Betrag als Float |
| `vat_value` | Steuersatz in Prozent als Zahl oder `false`, wenn die Summe mehrere Sätze kombiniert |
| `is_vat_inc` | `1`, wenn `cost` die Umsatzsteuer enthält, `0`, wenn nicht |
| jeder andere Schlüssel | Wird an Templates übergeben, zum Beispiel `management_link` zu einer Seite, auf der der Kunde eine Versandart wählt |

## Den Handler registrieren

In der `settings/shop.ini.append.php` Ihrer Erweiterung:

```ini
[ShippingSettings]
Handler=mystore
ExtensionDirectories[]=mystore_shop
```

und die Klasse in `extension/mystore_shop/shippinghandlers/mystoreshippinghandler.php`. Wird die Datei nicht
gefunden, meldet das Debug-Log „Shipping handler 'mystore' not found“ mit den durchsuchten Verzeichnissen. Entfernen
Sie den einfachen Versand-Workflow vom Trigger `confirmorder`, wenn der Handler live geht.

## Wie der Versand in die Summen gelangt

`eZShippingManager::vatPriceInfo()` wandelt Ihr Array in Beträge je Steuersatz um, und der mitgelieferte Handler für
Warenkorbinformationen (`ezdefault`) addiert sie zu den Preisinformationen des Warenkorbs je Steuersatz und zur Summe
([Handler für Warenkorbinformationen und Bestellbestätigung](handler-fuer-warenkorbinformationen-und-bestellbestaetigung.md)). Der Schritt
`handle-shipping` der Operation `confirmorder` wendet den Versand an, wenn die Bestellung bestätigt wird
([Die Kassenoperationen Schritt für Schritt](../kasse/die-kassenoperationen-schritt-fuer-schritt.md#schritt-4-die-bestellung-bestätigen)).

## Übliche Modelle

| Modell | Was der Handler liest |
|---|---|
| Kostenloser Versand ab einem Betrag | Die Artikel der Produktsammlung und ihre Preise |
| Tarife nach Land oder Zone | Das Land des Kunden (`eZVATManager::getUserCountry()`) oder die Kontodaten |
| Tarife nach Gewicht | Ein Gewichtsfeld in den Produktklassen, mal der Anzahl der Artikel |
| Der Kunde wählt eine Versandart | Eine Auswahl, die Ihre eigene View speichert (verlinkt mit `management_link`) und `getShippingInfo()` zurückliest |
| Tarifdienst eines Versanddienstleisters | Ein Aufruf beim Versanddienstleister mit Adresse und Paketdaten; speichern Sie die Antwort je Warenkorb im Cache |

Ob der Versand Umsatzsteuer trägt und zu welchem Satz, ist eine Frage für Ihren Steuerberater.

## Testen

- [ ] Unit-Tests für die Berechnung mit festen Warenkörben ([PHPUnit-Tests ohne Datenbank](../../ersteller/anwendungsentwickler/phpunit-tests-ohne-datenbank.md)).
- [ ] Warenkorb, Bestätigung, Bestellansicht und Mail zeigen denselben Versand.
- [ ] Geänderte Mengen ändern den Versand, wenn sie es sollen.
- [ ] Jedes Land oder jede Zone, die Sie unterstützen, und eines, das Sie nicht unterstützen.
- [ ] Keine zweite Versandzeile aus einem alten einfachen Versand-Workflow.

## Verwandte Seiten

- [Den Shop erweitern](README.md)
- [Versand und Bearbeitung (Unternehmen)](../../unternehmen/shops/versand-und-bearbeitung.md)
- [shop.ini durchgegangen](../einstellungen/shop-ini-durchgegangen.md#shippingsettings)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
